<?php
namespace App\Imports;

use App\Models\EntranceTicket;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EntranceTicketImport implements ToCollection, WithHeadingRow {
    public array $errors = [];
    public int $success = 0;

    public function collection(Collection $rows) {
        foreach ($rows as $row) {
            try {
                if (empty($row['attraction_name'])) continue;
                EntranceTicket::create([
                    'attraction_type' => $row['attraction_type'] ?? 'other',
                    'attraction_name' => $row['attraction_name'],
                    'currency'        => $row['currency'] ?? 'IDR',
                    'adult_price'     => $row['adult_price'] ?? 0,
                    'child_price'     => $row['child_price'] ?? 0,
                    'infant_price'    => $row['infant_price'] ?? 0,
                    'notes'           => $row['notes'] ?? null,
                ]);
                $this->success++;
            } catch (\Exception $e) {
                $this->errors[] = "Row {$row['attraction_name']}: " . $e->getMessage();
            }
        }
    }
}
