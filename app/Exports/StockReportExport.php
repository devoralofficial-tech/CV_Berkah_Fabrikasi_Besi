<?php

namespace App\Exports;

use App\Models\InventoryLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockReportExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    public function __construct(private string $from, private string $to, private ?int $productId = null) {}

    public function collection()
    {
        $query = InventoryLog::with(['product', 'creator'])
            ->whereDate('created_at', '>=', $this->from)
            ->whereDate('created_at', '<=', $this->to);

        if ($this->productId) {
            $query->where('product_id', $this->productId);
        }

        return $query->orderBy('created_at')->get()->map(fn($log) => [
            $log->created_at->format('d/m/Y H:i'),
            $log->product?->name ?? '-',
            $log->type === 'in' ? 'Masuk' : 'Keluar',
            number_format($log->qty, 2, ',', '.'),
            $log->source,
            $log->note ?? '-',
            $log->creator?->name ?? '-',
        ]);
    }

    public function headings(): array
    {
        return ['Tanggal', 'Produk', 'Tipe', 'Qty', 'Sumber', 'Catatan', 'Oleh'];
    }

    public function title(): string
    {
        return "Stok {$this->from} s.d. {$this->to}";
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
