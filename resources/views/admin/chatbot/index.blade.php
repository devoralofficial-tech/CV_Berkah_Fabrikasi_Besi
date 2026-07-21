@extends('layouts.admin')
@section('title', 'Manajemen Chatbot FAQ')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800" style="font-family: 'Sora', sans-serif;">Chatbot FAQ</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola pertanyaan dan jawaban template untuk chatbot</p>
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <a href="{{ route('admin.chatbot.unanswered') }}" class="flex-1 sm:flex-none px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 transition text-sm font-medium text-center">
                Log Tdk Terjawab
            </a>
            <button x-data @click="$dispatch('open-modal', 'modal-faq-form')" class="flex-1 sm:flex-none px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition text-sm font-medium flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah FAQ
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 text-emerald-600 p-4 rounded-xl text-sm font-medium border border-emerald-100 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-sm">
                        <th class="p-4 font-bold text-slate-700">Pertanyaan / Keyword</th>
                        <th class="p-4 font-bold text-slate-700">Jawaban</th>
                        <th class="p-4 font-bold text-slate-700 w-24 text-center">Urutan</th>
                        <th class="p-4 font-bold text-slate-700 w-24 text-center">Status</th>
                        <th class="p-4 font-bold text-slate-700 w-24 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($faqs as $faq)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-4 align-top">
                            <div class="font-bold text-slate-800">{{ $faq->question }}</div>
                            <div class="text-xs text-slate-500 mt-1">Key: {{ $faq->keywords ?: '-' }}</div>
                        </td>
                        <td class="p-4 align-top">
                            <p class="text-slate-600 line-clamp-3">{{ $faq->answer }}</p>
                        </td>
                        <td class="p-4 align-top text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-bold text-xs">{{ $faq->sort_order }}</span>
                        </td>
                        <td class="p-4 align-top text-center">
                            @if($faq->is_active)
                            <span class="inline-flex px-2 py-1 bg-emerald-50 text-emerald-600 rounded-md text-xs font-bold">Aktif</span>
                            @else
                            <span class="inline-flex px-2 py-1 bg-slate-100 text-slate-500 rounded-md text-xs font-bold">Nonaktif</span>
                            @endif
                        </td>
                        <td class="p-4 align-top text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" x-data @click="$dispatch('edit-faq', {{ json_encode($faq) }})" class="p-2 text-amber-500 hover:bg-amber-50 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <form action="{{ route('admin.chatbot.destroy', $faq) }}" method="POST" class="inline" onsubmit="return confirm('Hapus FAQ ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-500">Belum ada FAQ.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Card List --}}
        <div class="md:hidden divide-y divide-slate-100">
            @forelse($faqs as $faq)
            <div class="p-4 space-y-3">
                <div class="flex justify-between items-start gap-3">
                    <div>
                        <div class="font-bold text-slate-800 text-sm">{{ $faq->question }}</div>
                        <div class="text-xs text-slate-500 mt-1">Key: {{ $faq->keywords ?: '-' }}</div>
                    </div>
                    @if($faq->is_active)
                    <span class="inline-flex shrink-0 px-2 py-1 bg-emerald-50 text-emerald-600 rounded-md text-[10px] font-bold">Aktif</span>
                    @else
                    <span class="inline-flex shrink-0 px-2 py-1 bg-slate-100 text-slate-500 rounded-md text-[10px] font-bold">Nonaktif</span>
                    @endif
                </div>
                <p class="text-slate-600 text-xs line-clamp-2">{{ $faq->answer }}</p>
                <div class="flex justify-between items-center pt-2 border-t border-slate-50">
                    <span class="text-xs text-slate-500 font-medium">Urutan: {{ $faq->sort_order }}</span>
                    <div class="flex gap-2">
                        <button type="button" x-data @click="$dispatch('edit-faq', {{ json_encode($faq) }})" class="px-3 py-1.5 text-xs font-medium text-amber-600 bg-amber-50 rounded-lg">Edit</button>
                        <form action="{{ route('admin.chatbot.destroy', $faq) }}" method="POST" class="inline" onsubmit="return confirm('Hapus FAQ ini?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-slate-500 text-sm">Belum ada FAQ.</div>
            @endforelse
        </div>
    </div>
</div>

{{-- FAQ Form Modal --}}
<div x-data="{
        show: false,
        isEdit: false,
        formAction: '{{ route('admin.chatbot.store') }}',
        data: { id: null, question: '', answer: '', keywords: '', sort_order: 0, is_active: true }
    }"
    @open-modal.window="if($event.detail === 'modal-faq-form') { isEdit = false; data = { question: '', answer: '', keywords: '', sort_order: 0, is_active: true }; formAction = '{{ route('admin.chatbot.store') }}'; show = true; }"
    @edit-faq.window="isEdit = true; data = $event.detail; formAction = '{{ url('admin/chatbot') }}/' + data.id; show = true;"
    x-show="show"
    style="display: none;"
    class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    
    <div x-show="show" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="show = false"></div>
    
    <div x-show="show" 
         x-transition.scale.95 
         class="bg-white rounded-2xl shadow-xl w-full max-w-lg relative z-10 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center sticky top-0 bg-white z-10">
            <h3 class="text-lg font-bold text-slate-800" x-text="isEdit ? 'Edit FAQ' : 'Tambah FAQ'"></h3>
            <button @click="show = false" class="text-slate-400 hover:text-slate-600 transition p-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form :action="formAction" method="POST" class="p-6 space-y-4">
            @csrf
            <template x-if="isEdit">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Pertanyaan (Judul Tombol)</label>
                <input type="text" name="question" x-model="data.question" required class="w-full border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Kata Kunci (pisahkan koma)</label>
                <input type="text" name="keywords" x-model="data.keywords" placeholder="jam, operasional, buka" class="w-full border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500">
                <p class="text-[11px] text-slate-400 mt-1">Kosongkan jika hanya ingin lewat tombol quick reply.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Jawaban</label>
                <textarea name="answer" x-model="data.answer" required rows="4" class="w-full border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500"></textarea>
            </div>

            <div class="flex gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Urutan</label>
                    <input type="number" name="sort_order" x-model="data.sort_order" class="w-full border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500">
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                    <select name="is_active" x-model="data.is_active" class="w-full border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" @click="show = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition">Batal</button>
                <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-sky-600 hover:bg-sky-700 rounded-xl transition shadow-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
