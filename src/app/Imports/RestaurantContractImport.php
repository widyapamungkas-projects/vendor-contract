<?php
namespace App\Imports;

use App\Models\RestaurantContract;
use App\Models\RestaurantMenu;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RestaurantContractImport implements ToCollection, WithHeadingRow {
    public array $errors = [];
    public int $success = 0;

    public function collection(Collection $rows) {
        $grouped = $rows->groupBy('vendor_name');

        foreach ($grouped as $vendorName => $contractRows) {
            try {
                $first = $contractRows->first();
                $contract = RestaurantContract::create([
                    'vendor_name'    => $vendorName,
                    'vendor_city'    => $first['vendor_city'] ?? '',
                    'vendor_country' => $first['vendor_country'] ?? 'Indonesia',
                    'pic_name'       => $first['pic_name'] ?? '',
                    'pic_phone'      => $first['pic_phone'] ?? null,
                    'price_category' => $first['price_category'] ?? 'FIT',
                    'currency'       => $first['currency'] ?? 'IDR',
                    'valid_from'     => $this->parseDate($first['valid_from'] ?? ''),
                    'valid_until'    => $this->parseDate($first['valid_until'] ?? ''),
                    'notes'          => $first['notes'] ?? null,
                ]);

                foreach ($contractRows as $row) {
                    if (empty($row['menu_name'])) continue;
                    $adultPrice = $row['adult_price'] ?? 0;
                    $contract->menus()->create([
                        'menu_name'     => $row['menu_name'],
                        'serving_style' => $row['serving_style'] ?? 'set_menu',
                        'adult_price'   => $adultPrice,
                        'child_price'   => round($adultPrice * 0.65),
                        'min_pax'       => $row['min_pax'] ?? null,
                        'menu_details'  => $row['menu_details'] ?? null,
                        'notes'         => $row['notes'] ?? null,
                    ]);
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
