<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RestaurantTemplateExport implements FromArray, WithHeadings, WithTitle, WithStyles {
    public function array(): array {
        return [
            ['Bawang Merah','Jimbaran','Indonesia','Ocha','08123456789','FIT','IDR','2026-01-01','2026-12-31','Seafood Mini','set_menu',225000,'','Soup: Vegetable Soup\nMain Course: Seafood Plater','Min order 2 jam sebelum kedatangan'],
            ['Bawang Merah','Jimbaran','Indonesia','Ocha','08123456789','FIT','IDR','2026-01-01','2026-12-31','Seafood Regular','buffet',350000,10,'Soup: Vegetable Soup\nMain: Grilled Fish\nDessert: Fruit Platter',''],
        ];
    }
    public function headings(): array {
        return ['vendor_name','vendor_city','vendor_country','pic_name','pic_phone','price_category','currency','valid_from','valid_until','menu_name','serving_style','adult_price','min_pax','menu_details','notes'];
    }
    public function title(): string { return 'Restaurant Contracts'; }
    public function styles(Worksheet $sheet) {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1D4ED8']]]];
    }
}
