<?php
namespace App\Imports;

use App\Models\ActivityContract;
use App\Models\ActivityItem;
use App\Models\ActivityRate;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ActivityContractImport implements ToCollection, WithHeadingRow {
    public array $errors = [];
    public int $success = 0;

    public function collection(Collection $rows) {
        $grouped = $rows->groupBy('vendor_name');

        foreach ($grouped as $vendorName => $contractRows) {
            try {
                $first = $contractRows->first();
                $contract = ActivityContract::create([
                    'vendor_name'    => $vendorName,
                    'vendor_city'    => $first['vendor_city'] ?? '',
                    'vendor_country' => $first['vendor_country'] ?? 'Indonesia',
                    'destination'    => $first['destination'] ?? null,
                    'area'           => $first['area'] ?? null,
                    'pic_name'       => $first['pic_name'] ?? '',
                    'pic_phone'      => $first['pic_phone'] ?? null,
                    'price_category' => $first['price_category'] ?? 'FIT',
                    'currency'       => $first['currency'] ?? 'IDR',
                    'valid_from'     => $this->parseDate($first['valid_from'] ?? ''),
                    'valid_until'    => $this->parseDate($first['valid_until'] ?? ''),
                    'notes'          => $first['notes'] ?? null,
                ]);

                $itemGroups = $contractRows->groupBy('activity_name');
                foreach ($itemGroups as $activityName => $itemRows) {
                    if (empty($activityName)) continue;
                    $iFirst = $itemRows->first();
                    $item = $contract->items()->create([
                        'activity_type' => $iFirst['activity_type'] ?? 'other',
                        'activity_name' => $activityName,
                        'duration'      => $iFirst['duration'] ?? null,
                        'min_pax'       => $iFirst['min_pax'] ?? 1,
                    ]);
                    foreach ($itemRows as $row) {
                        if (empty($row['price'])) continue;
                        $item->rates()->create([
                            'rate_type' => $row['rate_type'] ?? 'per_pax',
                            'pax_type'  => $row['pax_type'] ?? 'adult',
                            'price'     => $row['price'] ?? 0,
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
