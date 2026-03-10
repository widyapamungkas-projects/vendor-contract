<?php
namespace App\Imports;

use App\Models\GuideLanguage;
use App\Models\GuideService;
use App\Models\GuideServiceTier;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class GuideImport implements ToCollection, WithHeadingRow
{
    public int   $success = 0;
    public array $errors  = [];

    public function collection(Collection $rows)
    {
        $validTypes = ['airport_transfer','full_day','half_day','overtime','tipping','package'];

        foreach ($rows as $i => $row) {
            $rowNum = $i + 2;
            try {
                $langName    = trim($row['language_name'] ?? '');
                $destination = trim($row['destination'] ?? '');
                $currency    = trim($row['currency'] ?? 'IDR');
                $langNotes   = trim($row['language_notes'] ?? '');
                $svcName     = trim($row['service_name'] ?? '');
                $svcType     = trim($row['service_type'] ?? '');
                $unitLabel   = trim($row['unit_label'] ?? 'per trip');
                $minPax      = intval($row['min_pax'] ?? 1);
                $maxPax      = ($row['max_pax'] !== '' && $row['max_pax'] !== null) ? intval($row['max_pax']) : null;
                $rate        = floatval($row['rate'] ?? 0);

                if (!$langName || !$svcName || !$svcType) {
                    $this->errors[] = "Row {$rowNum}: language_name, service_name, service_type wajib diisi.";
                    continue;
                }

                if (!in_array($svcType, $validTypes)) {
                    $this->errors[] = "Row {$rowNum}: service_type '{$svcType}' tidak valid.";
                    continue;
                }

                $lang = GuideLanguage::firstOrCreate(
                    ['language_name' => $langName, 'destination' => $destination ?: null],
                    ['currency' => $currency, 'notes' => $langNotes]
                );

                $svc = GuideService::firstOrCreate(
                    ['guide_language_id' => $lang->id, 'service_name' => $svcName],
                    ['service_type' => $svcType, 'unit_label' => $unitLabel]
                );

                GuideServiceTier::create([
                    'guide_service_id' => $svc->id,
                    'min_pax'          => $minPax,
                    'max_pax'          => $maxPax,
                    'rate'             => $rate,
                ]);

                $this->success++;
            } catch (\Exception $e) {
                $this->errors[] = "Row {$rowNum}: " . $e->getMessage();
            }
        }
    }
}
