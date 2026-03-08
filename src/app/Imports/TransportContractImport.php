<?php
namespace App\Imports;

use App\Models\TransportContract;
use App\Models\TransportVehicle;
use App\Models\TransportRate;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TransportContractImport implements ToCollection, WithHeadingRow {
    public array $errors = [];
    public int $success = 0;

    public function collection(Collection $rows) {
        $grouped = $rows->groupBy('vendor_name');

        foreach ($grouped as $vendorName => $contractRows) {
            try {
                $first = $contractRows->first();
                $contract = TransportContract::create([
                    'vendor_name'    => $vendorName,
                    'vendor_city'    => $first['vendor_city'] ?? '',
                    'vendor_country' => $first['vendor_country'] ?? 'Indonesia',
                    'pic_name'       => $first['pic_name'] ?? '',
                    'pic_phone'      => $first['pic_phone'] ?? null,
                    'pic_email'      => $first['pic_email'] ?? null,
                    'price_category' => $first['price_category'] ?? 'FIT',
                    'currency'       => $first['currency'] ?? 'IDR',
                    'valid_from'     => $this->parseDate($first['valid_from'] ?? ''),
                    'valid_until'    => $this->parseDate($first['valid_until'] ?? ''),
                    'notes'          => $first['notes'] ?? null,
                ]);

                $vehicleGroups = $contractRows->groupBy('vehicle_name');
                foreach ($vehicleGroups as $vehicleName => $vehicleRows) {
                    if (empty($vehicleName)) continue;
                    $vFirst = $vehicleRows->first();
                    $vehicle = $contract->vehicles()->create([
                        'vehicle_name' => $vehicleName,
                        'category'     => $vFirst['vehicle_category'] ?? 'car',
                        'capacity'     => $vFirst['capacity'] ?? null,
                        'brand'        => $vFirst['brand'] ?? null,
                    ]);
                    foreach ($vehicleRows as $row) {
                        if (empty($row['route_name'])) continue;
                        $vehicle->rates()->create([
                            'route_name' => $row['route_name'],
                            'route_type' => $row['route_type'] ?? 'airport_transfer',
                            'duration'   => $row['duration'] ?? null,
                            'price'      => $row['price'] ?? 0,
                        ]);
                    }
                }
                $this->success++;
            } catch (\Exception $e) {
                $this->errors[] = "Vendor {$vendorName}: " . $e->getMessage();
            }
        }
    }

    private function parseDate($value): string {
        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
        }
        return date('Y-m-d', strtotime($value));
    }
}
