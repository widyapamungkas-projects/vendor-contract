<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransportTemplateExport implements FromArray, WithHeadings, WithTitle, WithStyles {
    public function array(): array {
        return [
            ['Bali Transport Co','Kuta','Indonesia','Jane Doe','08123456789','FIT','IDR','2026-01-01','2026-12-31','car','Toyota Avanza','13','Toyota','Transfer Tuban/Kuta','airport_transfer','One Way',350000],
            ['Bali Transport Co','Kuta','Indonesia','Jane Doe','08123456789','FIT','IDR','2026-01-01','2026-12-31','car','Toyota Avanza','13','Toyota','Half Day Tour','half_day','6 jam',500000],
        ];
    }
    public function headings(): array {
        return ['vendor_name','vendor_city','vendor_country','pic_name','pic_phone','price_category','currency','valid_from','valid_until','vehicle_category','vehicle_name','capacity','brand','route_name','route_type','duration','price'];
    }
    public function title(): string { return 'Transport Contracts'; }
    public function styles(Worksheet $sheet) {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1D4ED8']]]];
    }
}
