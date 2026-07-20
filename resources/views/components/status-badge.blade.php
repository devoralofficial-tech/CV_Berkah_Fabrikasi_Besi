@props(['status'])

@php
$config = match($status) {
    'tersedia' => ['label' => 'Tersedia', 'class' => 'bg-emerald-100 text-emerald-700 border-emerald-200'],
    'menipis'  => ['label' => 'Stok Menipis', 'class' => 'bg-amber-100 text-amber-700 border-amber-200'],
    'habis'    => ['label' => 'Habis', 'class' => 'bg-red-100 text-red-700 border-red-200'],
    'pending'  => ['label' => 'Pending', 'class' => 'bg-slate-100 text-slate-600 border-slate-200'],
    'contacted'=> ['label' => 'Dihubungi', 'class' => 'bg-blue-100 text-blue-700 border-blue-200'],
    'completed'=> ['label' => 'Selesai', 'class' => 'bg-emerald-100 text-emerald-700 border-emerald-200'],
    'cancelled'=> ['label' => 'Dibatalkan', 'class' => 'bg-red-100 text-red-700 border-red-200'],
    'voided'   => ['label' => 'Voided', 'class' => 'bg-gray-100 text-gray-600 border-gray-200'],
    default    => ['label' => ucfirst($status), 'class' => 'bg-slate-100 text-slate-600 border-slate-200'],
};
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $config['class'] }}">
    {{ $config['label'] }}
</span>
