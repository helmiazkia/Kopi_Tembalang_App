<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class OrdersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths, WithEvents
{
    private int $totalRows = 0;

    public function __construct(
        protected string $startDate,
        protected string $endDate,
    ) {}

    // ── Meta ──────────────────────────────────────────────────────────

    public function title(): string
    {
        return "Laporan {$this->startDate} sd {$this->endDate}";
    }

    // ── Data ──────────────────────────────────────────────────────────

    public function collection()
    {
        $data = Order::with(['payment', 'cashier', 'table'])
            ->whereBetween('created_at', [
                "{$this->startDate} 00:00:00",
                "{$this->endDate} 23:59:59",
            ])
            ->orderBy('created_at')
            ->get();

        $this->totalRows = $data->count();

        return $data;
    }

    public function headings(): array
    {
        $periode = Carbon::parse($this->startDate)->format('d M Y');
        if ($this->startDate !== $this->endDate) {
            $periode .= ' — ' . Carbon::parse($this->endDate)->format('d M Y');
        }

        // Baris 1: Judul
        // Baris 2: Periode
        // Baris 3: Header kolom
        return [
            ['LAPORAN TRANSAKSI — KOPI TEMBALANG', '', '', '', '', '', '', ''],
            [$periode, '', '', '', '', '', '', ''],
            [
                'Tanggal',
                'Jam',
                'Order ID',
                'Pelanggan',
                'Kasir',
                'Metode Bayar',
                'Status',
                'Total (Rp)',
            ],
        ];
    }

    public function map($order): array
    {
        return [
            $order->created_at->format('d/m/Y'),
            $order->created_at->format('H:i'),
            '#' . $order->id,
            strtoupper($order->customer_name),
            $order->cashier?->name ?? 'Self-Order',
            strtoupper($order->payment?->method ?? '-'),
            strtoupper($order->status),
            (int) $order->total_price,
        ];
    }

    // ── Column Widths ─────────────────────────────────────────────────

    public function columnWidths(): array
    {
        return [
            'A' => 14,
            'B' => 8,
            'C' => 10,
            'D' => 24,
            'E' => 22,
            'F' => 14,
            'G' => 14,
            'H' => 18,
        ];
    }

    // ── Styles ────────────────────────────────────────────────────────

    public function styles(Worksheet $sheet): array
    {
        return [
            // Baris 1 — Judul utama
            1 => [
                'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            // Baris 2 — Sub judul periode
            2 => [
                'font' => ['size' => 10, 'color' => ['rgb' => 'D4E971'], 'name' => 'Arial'],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
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

    // ── Events ────────────────────────────────────────────────────────

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet       = $event->sheet->getDelegate();
                $lastDataRow = 3 + $this->totalRows; // 3 baris header + data

                // Merge judul & periode
                $sheet->mergeCells('A1:H1');
                $sheet->mergeCells('A2:H2');

                // Tinggi baris header
                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(22);

                // Tinggi & styling baris data
                for ($row = 4; $row <= $lastDataRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(20);

                    // Zebra stripes
                    if ($row % 2 === 0) {
                        $sheet->getStyle("A{$row}:H{$row}")
                            ->getFill()->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('F8FAFC');
                    }
                }

                // Font data rows
                $sheet->getStyle("A4:H{$lastDataRow}")
                    ->getFont()->setName('Arial')->setSize(10);

                // Alignment kolom
                $sheet->getStyle("A4:B{$lastDataRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C4:C{$lastDataRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("H4:H{$lastDataRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Format angka kolom H (Total)
                $sheet->getStyle("H4:H{$lastDataRow}")
                    ->getNumberFormat()->setFormatCode('"Rp "#,##0');

                // Warna badge status (kolom G)
                for ($row = 4; $row <= $lastDataRow; $row++) {
                    $status = strtolower($sheet->getCell("G{$row}")->getValue());
                    $color  = match ($status) {
                        'paid'      => 'D1FAE5', // emerald-100
                        'preparing' => 'FEF3C7', // amber-100
                        'done'      => 'DBEAFE', // blue-100
                        'cancelled' => 'FEE2E2', // rose-100
                        default     => 'F1F5F9', // slate-100
                    };
                    $sheet->getStyle("G{$row}")
                        ->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($color);
                }

                // Warna badge metode bayar (kolom F)
                for ($row = 4; $row <= $lastDataRow; $row++) {
                    $method = strtolower($sheet->getCell("F{$row}")->getValue());
                    $color  = $method === 'cash' ? 'D1FAE5' : 'E0E7FF'; // emerald / indigo
                    $sheet->getStyle("F{$row}")
                        ->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($color);
                }

                // Border seluruh tabel
                $sheet->getStyle("A3:H{$lastDataRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => 'E2E8F0'],
                        ],
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color'       => ['rgb' => '94A3B8'],
                        ],
                    ],
                ]);

                // ── Baris Total ───────────────────────────────────────
                $totalRow = $lastDataRow + 1;
                $sheet->setCellValue("D{$totalRow}", 'TOTAL TRANSAKSI');
                $sheet->setCellValue("G{$totalRow}", $this->totalRows . ' Nota');
                $totalAmount = Order::with(['payment', 'cashier', 'table'])
                    ->whereBetween('created_at', [
                        "{$this->startDate} 00:00:00",
                        "{$this->endDate} 23:59:59",
                    ])
                    ->sum('total_price');

                $sheet->setCellValue("H{$totalRow}", $totalAmount);

                $sheet->getStyle("A{$totalRow}:H{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'D4E971'], 'name' => 'Arial', 'size' => 10],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
                ]);
                $sheet->getStyle("H{$totalRow}")
                    ->getNumberFormat()->setFormatCode('"Rp "#,##0');
                $sheet->getStyle("H{$totalRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("D{$totalRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("G{$totalRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getRowDimension($totalRow)->setRowHeight(22);

                // ── Footer timestamp ──────────────────────────────────
                $footerRow = $totalRow + 2;
                $sheet->mergeCells("A{$footerRow}:H{$footerRow}");
                $sheet->setCellValue("A{$footerRow}", 'Digenerate pada: ' . now()->format('d/m/Y H:i') . '  |  Kopi Tembalang');
                $sheet->getStyle("A{$footerRow}")->applyFromArray([
                    'font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '94A3B8'], 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);

                // Freeze header
                $sheet->freezePane('A4');
            },
        ];
    }
}
