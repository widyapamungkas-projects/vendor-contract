<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HotelTemplateExport implements FromArray, WithHeadings, WithTitle, WithStyles {
    public function array(): array {
        return [
            ['HC-202601-0001','Grand Bali Hotel','Kuta','Indonesia', 'Bali', 'Kuta','John Doe','08123456789','john@hotel.com','FIT','IDR','2026-01-01','2026-12-31','','Deluxe Room','BB',800000,400000,200000],
            ['HC-202601-0001','Grand Bali Hotel','Kuta','Indonesia', 'Bali', 'Kuta','John Doe','08123456789','john@hotel.com','FIT','IDR','2026-01-01','2026-12-31','','Superior Room','RO',650000,325000,150000],
        ];
    }
    public function headings(): array {
        return ['contract_code','vendor_name','hotel_city','hotel_country', 'destination', 'area','pic_name','pic_phone','pic_email','price_category','currency','valid_from','valid_until','notes','room_type','meal_plan','adult_price','child_price','extra_bed_price'];
    }
    public function title(): string { return 'Hotel Contracts'; }
    public function styles(Worksheet $sheet) {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1D4ED8']]]];
    }
}
