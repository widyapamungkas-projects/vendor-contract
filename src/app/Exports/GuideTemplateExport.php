<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GuideTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'language_name',
            'destination',
            'currency',
            'language_notes',
            'service_name',
            'service_type',
            'unit_label',
            'min_pax',
            'max_pax',
            'rate',
        ];
    }

    public function array(): array
    {
        return [
            ['English', 'Bali', 'IDR', '', 'Airport Transfer', 'airport_transfer', 'per trip', 1,  4,  100000],
            ['English', 'Bali', 'IDR', '', 'Airport Transfer', 'airport_transfer', 'per trip', 5,  10, 150000],
            ['English', 'Bali', 'IDR', '', 'Airport Transfer', 'airport_transfer', 'per trip', 11, '',  200000],
            ['English', 'Bali', 'IDR', '', 'Full Day Tour',    'full_day',         'per hari',  1,  4,  1150000],
            ['English', 'Bali', 'IDR', '', 'Full Day Tour',    'full_day',         'per hari',  5,  10, 1500000],
            ['English', 'Bali', 'IDR', '', 'Full Day Tour',    'full_day',         'per hari',  11, '',  2000000],
            ['Mandarin','Bali', 'IDR', 'Chinese speaking', 'Full Day Tour', 'full_day', 'per hari', 1, 4, 1500000],
            ['English', 'Lombok', 'IDR', '', 'Full Day Tour',  'full_day',         'per hari',  1,  4,  1300000],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1e3a5f']],
            ],
        ];
    }
}
