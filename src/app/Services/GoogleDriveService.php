<?php
namespace App\Services;

use Google\Client;
use Google\Service\Drive;

class GoogleDriveService
{
    protected Drive $drive;

    public function __construct()
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('app/google/service-account.json'));
        $client->addScope(Drive::DRIVE_READONLY);
        $this->drive = new Drive($client);
    }

    /** List subfolders inside a folder */
    public function listFolders(string $folderId): array
    {
        try {
            $response = $this->drive->files->listFiles([
                'q'        => "'{$folderId}' in parents and mimeType = 'application/vnd.google-apps.folder' and trashed = false",
                'fields'   => 'files(id, name, modifiedTime)',
                'orderBy'  => 'name',
                'pageSize' => 100,
            ]);
            return $response->getFiles();
        } catch (\Exception $e) {
            return [];
        }
    }

    /** List files (non-folder) inside a folder */
    public function listFiles(string $folderId, int $limit = 100): array
    {
        try {
            $response = $this->drive->files->listFiles([
                'q'        => "'{$folderId}' in parents and mimeType != 'application/vnd.google-apps.folder' and trashed = false",
                'fields'   => 'files(id, name, size, mimeType, createdTime, modifiedTime, webViewLink)',
                'orderBy'  => 'modifiedTime desc',
                'pageSize' => $limit,
            ]);
            return $response->getFiles();
        } catch (\Exception $e) {
            return [];
        }
    }

    /** Search files by name across entire subtree */
    public function searchFiles(string $folderId, string $query): array
    {
        try {
            $response = $this->drive->files->listFiles([
                'q'        => "name contains '{$query}' and mimeType != 'application/vnd.google-apps.folder' and trashed = false",
                'fields'   => 'files(id, name, size, mimeType, modifiedTime, webViewLink, parents)',
                'orderBy'  => 'modifiedTime desc',
                'pageSize' => 50,
            ]);
            return $response->getFiles();
        } catch (\Exception $e) {
            return [];
        }
    }

    /** Get folder metadata including parents */
    public function getFolderMeta(string $folderId): ?\Google\Service\Drive\DriveFile
    {
        try {
            return $this->drive->files->get($folderId, [
                'fields' => 'id, name, parents'
            ]);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getFile(string $fileId): ?\Google\Service\Drive\DriveFile
    {
        try {
            return $this->drive->files->get($fileId, [
                'fields' => 'id, name, size, mimeType, createdTime, modifiedTime, webViewLink'
            ]);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function downloadFile(string $fileId): ?string
{
    try {
        $response = $this->drive->files->get($fileId, [
            'alt' => 'media'
        ]);

        return $response->getBody()->getContents();

    } catch (\Exception $e) {
        \Log::error('Drive download error: '.$e->getMessage());
        return null;
    }
}
}
