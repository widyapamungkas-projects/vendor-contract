<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ActivityTemplateExport implements FromArray, WithHeadings, WithTitle, WithStyles {
    public function array(): array {
        return [
            ['Mason Adventures','Ubud','Indonesia','Bali','Ayu','08123456789','FIT','IDR','2026-01-01','2026-12-31','rafting','Telaga Waja Rafting','2 jam','2','per_pax','adult',350000],
            ['Mason Adventures','Ubud','Indonesia','Bali','Ayu','08123456789','FIT','IDR','2026-01-01','2026-12-31','rafting','Telaga Waja Rafting','2 jam','2','per_pax','child',280000],
        ];
    }
    public function headings(): array {
        return ['vendor_name','vendor_city','vendor_country','destination','pic_name','pic_phone','price_category','currency','valid_from','valid_until','activity_type','activity_name','duration','min_pax','rate_type','pax_type','price'];
    }
    public function title(): string { return 'Activity Contracts'; }
    public function styles(Worksheet $sheet) {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1D4ED8']]]];
    }
}
