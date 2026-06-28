<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class TopMenusExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths, WithEvents
{
    private int $totalRows;

    public function __construct(
        private int $month,
        private int $year
    ) {}

    public function collection()
    {
        $data = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->whereMonth('orders.created_at', $this->month)
            ->whereYear('orders.created_at', $this->year)
            ->whereIn('orders.status', ['paid', 'preparing', 'done'])
            ->select(
                'menus.name as Menu',
                DB::raw('SUM(order_items.qty) as Total_Terjual'),
                DB::raw('SUM(order_items.subtotal) as Total_Pendapatan')
            )
            ->groupBy('menus.name')
            ->orderBy('Total_Terjual', 'DESC')
            ->get();

        $this->totalRows = $data->count();

        return $data->values()->map(fn($row, $index) => [
            $index + 1,
            $row->Menu,
            (int) $row->Total_Terjual,
            (int) $row->Total_Pendapatan,
        ]);
    }

    public function headings(): array
    {
        // Baris 1: judul laporan (merge akan dilakukan di WithEvents)
        // Baris 2: sub judul periode
        // Baris 3: header kolom
        return [
            ['LAPORAN MENU TERLARIS', '', '', ''],
            [Carbon::createFromDate($this->year, $this->month, 1)->translatedFormat('F Y'), '', '', ''],
            ['No', 'Nama Menu', 'Total Terjual (Qty)', 'Total Pendapatan (Rp)'],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 35,
            'C' => 22,
            'D' => 25,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Baris 1 — Judul utama
            1 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e293b']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            // Baris 2 — Sub judul periode
            2 => [
                'font' => ['bold' => false, 'size' => 11, 'color' => ['rgb' => 'D4E971'], 'name' => 'Arial'],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e293b']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            // Baris 3 — Header kolom
            3 => [
                'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '334155']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastDataRow = 3 + $this->totalRows;

                // Merge cell judul & periode
                $sheet->mergeCells('A1:D1');
                $sheet->mergeCells('A2:D2');

                // Tinggi baris header
                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(20);

                // Format kolom C (qty) — angka biasa
                $sheet->getStyle("C4:C{$lastDataRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');

                // Format kolom D (pendapatan) — format rupiah
                $sheet->getStyle("D4:D{$lastDataRow}")
                    ->getNumberFormat()
                    ->setFormatCode('"Rp "#,##0');

                // Alignment data
                $sheet->getStyle("A4:A{$lastDataRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C4:D{$lastDataRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Font data rows
                $sheet->getStyle("A4:D{$lastDataRow}")
                    ->getFont()->setName('Arial')->setSize(10);

                // Zebra stripes — baris genap lebih terang
                for ($row = 4; $row <= $lastDataRow; $row++) {
                    if ($row % 2 === 0) {
                        $sheet->getStyle("A{$row}:D{$row}")
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('F8FAFC');
                    }
                }

                // Border seluruh tabel
                $sheet->getStyle("A3:D{$lastDataRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()->setRGB('E2E8F0');

                // Baris total di paling bawah
                $totalRow = $lastDataRow + 1;
                $sheet->setCellValue("B{$totalRow}", 'TOTAL');
                $sheet->setCellValue("C{$totalRow}", "=SUM(C4:C{$lastDataRow})");
                $sheet->setCellValue("D{$totalRow}", "=SUM(D4:D{$lastDataRow})");

                $sheet->getStyle("A{$totalRow}:D{$totalRow}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e293b']],
                    'font' => ['bold' => true, 'color' => ['rgb' => 'D4E971'], 'name' => 'Arial', 'size' => 10],
                ]);

                $sheet->getStyle("C{$totalRow}")
                    ->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("D{$totalRow}")
                    ->getNumberFormat()->setFormatCode('"Rp "#,##0');
                $sheet->getStyle("C{$totalRow}:D{$totalRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Freeze pane di baris data pertama
                $sheet->freezePane('A4');

                // Footer — generated at
                $footerRow = $totalRow + 2;
                $sheet->setCellValue("A{$footerRow}", 'Digenerate pada: ' . now()->format('d/m/Y H:i'));
                $sheet->getStyle("A{$footerRow}")
                    ->getFont()->setItalic(true)->setSize(9)->setColor(
                        (new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB('94A3B8')
                    );
                $sheet->mergeCells("A{$footerRow}:D{$footerRow}");
            },
        ];
    }

    public function title(): string
    {
        return 'Menu Terlaris';
    }
}