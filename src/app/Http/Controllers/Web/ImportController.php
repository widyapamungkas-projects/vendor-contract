<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Imports\HotelContractImport;
use App\Imports\TransportContractImport;
use App\Imports\ActivityContractImport;
use App\Imports\EntranceTicketImport;
use App\Imports\RestaurantContractImport;
use App\Exports\HotelTemplateExport;
use App\Exports\TransportTemplateExport;
use App\Exports\ActivityTemplateExport;
use App\Exports\EntranceTemplateExport;
use App\Exports\RestaurantTemplateExport;
use App\Models\ImportLog;
use App\Models\EntranceTicket;
use App\Models\HotelContract;
use App\Models\TransportContract;
use App\Models\ActivityContract;
use App\Models\RestaurantContract;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller {

    public function index() {
        $logs = ImportLog::latest()->limit(20)->get();
        return view('imports.index', compact('logs'));
    }

    public function downloadTemplate(string $module) {
        $exports = [
            'hotel'      => [new HotelTemplateExport,      'template_hotel_contracts.xlsx'],
            'transport'  => [new TransportTemplateExport,   'template_transport_contracts.xlsx'],
            'activity'   => [new ActivityTemplateExport,    'template_activity_contracts.xlsx'],
            'entrance'   => [new EntranceTemplateExport,    'template_entrance_fee.xlsx'],
            'restaurant' => [new RestaurantTemplateExport,  'template_restaurant_contracts.xlsx'],
        ];

        if (!isset($exports[$module])) abort(404);
        [$export, $filename] = $exports[$module];
        return Excel::download($export, $filename);
    }

    public function upload(Request $request) {
        $request->validate([
            'module' => 'required|in:hotel,transport,activity,entrance,restaurant',
            'file'   => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $module   = $request->module;
        $filename = $request->file('file')->getClientOriginalName();

        // Delete existing data if replace mode
        match($module) {
            'hotel'      => HotelContract::query()->delete(),
            'transport'  => TransportContract::query()->delete(),
            'activity'   => ActivityContract::query()->delete(),
            'entrance'   => EntranceTicket::query()->delete(),
            'restaurant' => RestaurantContract::query()->delete(),
        };

        // Run import
        $import = match($module) {
            'hotel'      => new HotelContractImport,
            'transport'  => new TransportContractImport,
            'activity'   => new ActivityContractImport,
            'entrance'   => new EntranceTicketImport,
            'restaurant' => new RestaurantContractImport,
        };

        try {
            Excel::import($import, $request->file('file'));

            $status = count($import->errors) === 0 ? 'success' : 'partial';

            ImportLog::create([
                'module'       => $module,
                'filename'     => $filename,
                'total_rows'   => $import->success + count($import->errors),
                'success_rows' => $import->success,
                'failed_rows'  => count($import->errors),
                'errors'       => count($import->errors) ? implode("\n", $import->errors) : null,
                'status'       => $status,
            ]);

            $msg = "Import berhasil! {$import->success} data berhasil diimport.";
            if (count($import->errors)) {
                $msg .= ' ' . count($import->errors) . ' data gagal.';
            }

            return redirect()->route('imports.index')->with('success', $msg);

        } catch (\Exception $e) {
            ImportLog::create([
                'module'   => $module,
                'filename' => $filename,
                'status'   => 'failed',
                'errors'   => $e->getMessage(),
            ]);
            return redirect()->route('imports.index')->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }
}
