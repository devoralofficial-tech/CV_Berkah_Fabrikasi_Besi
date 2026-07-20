<?php

namespace App\Exports;

use App\Models\Sale;
use App\Models\SaleItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesReportExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    public function __construct(private string $from, private string $to) {}

    public function collection()
    {
        return Sale::with(['items.product'])
            ->where('status', 'completed')
            ->whereDate('created_at', '>=', $this->from)
            ->whereDate('created_at', '<=', $this->to)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($sale) => [
                $sale->sale_number,
                $sale->created_at->format('d/m/Y H:i'),
                $sale->source_label,
                $sale->customer_name,
                $sale->payment_method === 'cash' ? 'Tunai' : 'Transfer',
                number_format($sale->total, 2, ',', '.'),
                $sale->status,
            ]);
    }

    public function headings(): array
    {
        return ['No. Nota', 'Tanggal', 'Sumber', 'Pelanggan', 'Metode Bayar', 'Total (Rp)', 'Status'];
    }

    public function title(): string
    {
        return "Penjualan {$this->from} s.d. {$this->to}";
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
