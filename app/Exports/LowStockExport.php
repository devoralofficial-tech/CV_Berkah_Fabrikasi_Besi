<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LowStockExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    public function collection()
    {
        return Product::with('category.parent')
            ->whereRaw('stock <= low_stock_threshold')
            ->orderBy('stock')
            ->get()
            ->map(fn($p) => [
                $p->name,
                $p->category?->parent?->name ?? '-',
                $p->category?->name ?? '-',
                $p->unit,
                number_format($p->stock, 2, ',', '.'),
                number_format($p->low_stock_threshold, 2, ',', '.'),
                $p->stock <= 0 ? 'Habis' : 'Menipis',
            ]);
    }

    public function headings(): array
    {
        return ['Produk', 'Kategori Induk', 'Kategori Anak', 'Satuan', 'Stok Saat Ini', 'Threshold', 'Status'];
    }

    public function title(): string
    {
        return 'Laporan Stok Menipis';
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
