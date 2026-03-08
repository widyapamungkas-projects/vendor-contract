<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PaymentSlipNotification;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;

class PaymentSlipController extends Controller
{
    protected string $rootFolderId = '13uYGyzlldbziOm4k56oDI57iH2z9hDJK';

    public function index(Request $request)
    {
        $drive  = new GoogleDriveService();
        $search = $request->input('search');

        // Find latest folder
        $latestFolderPath = [];
        $latestFolder     = $this->findLatestFolder($drive, $this->rootFolderId, $latestFolderPath);
        $latestFiles      = $latestFolder ? $drive->listFiles($latestFolder) : [];

        // Get latest modified time from files
        $latestModified = null;
        foreach ($latestFiles as $f) {
            if ($f->getModifiedTime()) {
                $t = new \DateTime($f->getModifiedTime());
                if (!$latestModified || $t > $latestModified) $latestModified = $t;
            }
        }
        $latestUpdatedAt = $latestModified ? \Carbon\Carbon::instance($latestModified)->setTimezone(session('user_timezone', 'Asia/Makassar'))->format("d-M-Y h:i A") : now()->setTimezone(session('user_timezone', 'Asia/Makassar'))->format("d-M-Y h:i A");

        // Sync notifications
        foreach ($latestFiles as $file) {
            PaymentSlipNotification::firstOrCreate(
                ['file_id' => $file->getId()],
                [
                    'file_name'   => $file->getName(),
                    'folder_id'   => $latestFolder,
                    'file_size'   => $file->getSize(),
                    'mime_type'   => $file->getMimeType(),
                    'uploaded_at' => $file->getModifiedTime()
                        ? new \DateTime($file->getModifiedTime()) : now(),
                ]
            );
        }

        // Archive: year folders
        $yearFolders = $drive->listFolders($this->rootFolderId);

        // Search
        $searchResults = $search ? $drive->searchFiles($this->rootFolderId, $search) : [];

        $unreadCount = PaymentSlipNotification::where('is_read', false)->count();

        return view('payment-slips.index', compact(
            'latestFiles', 'latestFolderPath', 'yearFolders', 'latestUpdatedAt',
            'unreadCount', 'search', 'searchResults'
        ));
    }

    public function archive(Request $request)
    {
        $folderId = $request->input('folder', $this->rootFolderId);
        $drive    = new GoogleDriveService();

        $breadcrumbs = $this->buildBreadcrumbs($drive, $folderId);
        $folders     = $drive->listFolders($folderId);
        $files       = $drive->listFiles($folderId);

        foreach ($files as $file) {
            PaymentSlipNotification::firstOrCreate(
                ['file_id' => $file->getId()],
                [
                    'file_name'   => $file->getName(),
                    'folder_id'   => $folderId,
                    'file_size'   => $file->getSize(),
                    'mime_type'   => $file->getMimeType(),
                    'uploaded_at' => $file->getModifiedTime()
                        ? new \DateTime($file->getModifiedTime()) : now(),
                ]
            );
        }

        $unreadCount = PaymentSlipNotification::where('is_read', false)->count();

        return view('payment-slips.archive', compact(
            'folders', 'files', 'folderId', 'breadcrumbs', 'unreadCount'
        ));
    }

    /** Stream file content for in-app preview */
   public function preview(string $fileId)
{
    $drive = new GoogleDriveService();
    $file  = $drive->getFile($fileId);
    if (!$file) abort(404);

    $content = $drive->downloadFile($fileId);
    if (!$content) abort(404);

    PaymentSlipNotification::where('file_id', $fileId)
        ->update(['is_read' => true]);

    $mime = $file->getMimeType() ?? 'application/octet-stream';

    return response($content, 200, [
    'Content-Type'        => $mime,
    'Content-Disposition' => 'inline; filename="'.$file->getName().'"',
    'Content-Length'      => strlen($content),
    'X-Frame-Options'     => 'SAMEORIGIN',
    'Cache-Control'       => 'public, max-age=3600'
]);
}

    public function download(string $fileId)
    {
        $drive = new GoogleDriveService();
        $file  = $drive->getFile($fileId);
        if (!$file) abort(404);

        $content = $drive->downloadFile($fileId);
        if (!$content) abort(404);

        PaymentSlipNotification::where('file_id', $fileId)->update(['is_read' => true]);

        return response($content, 200, [
            'Content-Type'        => $file->getMimeType() ?? 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $file->getName() . '"',
        ]);
    }

    public function markAllRead()
    {
        PaymentSlipNotification::where('is_read', false)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function notifications()
    {
        $unread = PaymentSlipNotification::where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get(['id', 'file_name', 'file_size', 'uploaded_at', 'file_id']);

        return response()->json([
            'count' => PaymentSlipNotification::where('is_read', false)->count(),
            'items' => $unread,
        ]);
    }

    private function findLatestFolder(GoogleDriveService $drive, string $folderId, array &$path, int $depth = 0): ?string
    {
        if ($depth > 4) return $folderId;
        $subfolders = $drive->listFolders($folderId);
        if (empty($subfolders)) return $folderId;
        usort($subfolders, fn($a, $b) => strcmp($b->getName(), $a->getName()));
        $latest = $subfolders[0];
        $path[] = ['id' => $latest->getId(), 'name' => $latest->getName()];
        return $this->findLatestFolder($drive, $latest->getId(), $path, $depth + 1);
    }

    private function buildBreadcrumbs(GoogleDriveService $drive, string $folderId): array
    {
        $crumbs = [];
        $current = $folderId;
        $visited = [];
        while ($current && $current !== $this->rootFolderId && count($visited) < 6) {
            if (in_array($current, $visited)) break;
            $visited[] = $current;
            $meta = $drive->getFolderMeta($current);
            if (!$meta) break;
            array_unshift($crumbs, ['id' => $current, 'name' => $meta->getName()]);
            $parents = $meta->getParents();
            $current = $parents ? $parents[0] : null;
        }
        array_unshift($crumbs, ['id' => $this->rootFolderId, 'name' => 'Payment Slips']);
        return $crumbs;
    }
}
