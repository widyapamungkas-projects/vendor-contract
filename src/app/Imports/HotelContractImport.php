<?php
namespace App\Imports;

use App\Models\HotelContract;
use App\Models\RoomRate;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class HotelContractImport implements ToCollection, WithHeadingRow {
    public array $errors = [];
    public int $success = 0;

    public function collection(Collection $rows) {
        // Group rows by contract_code
        $grouped = $rows->groupBy('contract_code');

        foreach ($grouped as $code => $contractRows) {
            try {
                $first = $contractRows->first();

                // Delete existing contract with same code
                HotelContract::where('contract_code', $code)->delete();

                $contract = HotelContract::create([
                    'contract_code'  => $code ?: HotelContract::generateCode(),
                    'hotel_name'     => $first['vendor_name'] ?? $first['hotel_name'] ?? '',
                    'hotel_city'     => $first['hotel_city'] ?? '',
                    'hotel_country'  => $first['hotel_country'] ?? 'Indonesia',
                    'pic_name'       => $first['pic_name'] ?? '',
                    'pic_phone'      => $first['pic_phone'] ?? null,
                    'pic_email'      => $first['pic_email'] ?? null,
                    'price_category' => $first['price_category'] ?? 'FIT',
                    'currency'       => $first['currency'] ?? 'IDR',
                    'valid_from'     => $this->parseDate($first['valid_from'] ?? ''),
                    'valid_until'    => $this->parseDate($first['valid_until'] ?? ''),
                    'notes'          => $first['notes'] ?? null,
                ]);

                foreach ($contractRows as $row) {
                    if (empty($row['room_type'])) continue;
                    RoomRate::create([
                        'hotel_contract_id' => $contract->id,
                        'room_type'         => $row['room_type'],
                        'meal_plan'         => $row['meal_plan'] ?? 'RO',
                        'adult_price'       => $row['adult_price'] ?? 0,
                        'child_price'       => $row['child_price'] ?? 0,
                        'extra_bed_price'   => $row['extra_bed_price'] ?? 0,
                    ]);
                }
                $this->success++;
            } catch (\Exception $e) {
                $this->errors[] = "Contract {$code}: " . $e->getMessage();
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
