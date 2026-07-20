<?php

namespace App\Exports;

use App\Models\SaleItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProfitLossExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    public function __construct(private string $from, private string $to) {}

    public function collection()
    {
        return SaleItem::with(['product', 'sale'])
            ->whereHas('sale', fn($q) => $q->where('status', 'completed')
                ->whereDate('created_at', '>=', $this->from)
                ->whereDate('created_at', '<=', $this->to))
            ->get()
            ->map(fn($item) => [
                $item->sale?->sale_number ?? '-',
                $item->sale?->created_at->format('d/m/Y') ?? '-',
                $item->product?->name ?? '-',
                number_format($item->qty, 2, ',', '.'),
                number_format($item->unit_price_snapshot, 2, ',', '.'),
                number_format($item->subtotal, 2, ',', '.'),
                number_format(($item->product?->cost_price ?? 0) * $item->qty, 2, ',', '.'),
                number_format($item->subtotal - (($item->product?->cost_price ?? 0) * $item->qty), 2, ',', '.'),
            ]);
    }

    public function headings(): array
    {
        return ['No. Nota', 'Tanggal', 'Produk', 'Qty', 'Harga Jual', 'Total Jual', 'HPP', 'Margin'];
    }

    public function title(): string
    {
        return "Laba Rugi {$this->from} s.d. {$this->to}";
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
