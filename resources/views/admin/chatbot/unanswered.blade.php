@extends('layouts.admin')
@section('title', 'Log Pertanyaan Tidak Terjawab')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.chatbot.index') }}" class="p-2 text-slate-400 hover:text-slate-600 bg-white rounded-lg border border-slate-200 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800" style="font-family: 'Sora', sans-serif;">Log Tak Terjawab</h1>
                <p class="text-slate-500 text-sm mt-1">Daftar pertanyaan user yang tidak mengenali kata kunci</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-sm">
                        <th class="p-4 font-bold text-slate-700">Waktu</th>
                        <th class="p-4 font-bold text-slate-700">Pertanyaan User</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-4 whitespace-nowrap text-slate-500">
                            {{ $log->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="p-4">
                            <span class="text-slate-800 font-medium">{{ $log->user_input }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="p-8 text-center text-slate-500">
                            Belum ada log pertanyaan tidak terjawab. Bagus!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="p-4 border-t border-slate-200">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
