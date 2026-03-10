<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EntranceTemplateExport implements FromArray, WithHeadings, WithTitle, WithStyles {
    public function array(): array {
        return [
            ['temple','Tanah Lot Temple','Bali','IDR',60000,30000,0,'Include: sarong'],
            ['cultural_show','Kecak Dance Uluwatu','Bali','IDR',150000,75000,0,'Show at 18:00'],
        ];
    }
    public function headings(): array {
        return ['attraction_type','attraction_name','destination','currency','adult_price','child_price','infant_price','notes'];
    }
    public function title(): string { return 'Entrance Fee'; }
    public function styles(Worksheet $sheet) {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1D4ED8']]]];
    }
}
