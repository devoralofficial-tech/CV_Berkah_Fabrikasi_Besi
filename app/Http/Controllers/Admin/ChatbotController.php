<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ChatbotFaq;
use App\Models\ChatbotUnansweredLog;

class ChatbotController extends Controller
{
    public function index()
    {
        $faqs = ChatbotFaq::orderBy('sort_order')->get();
        return view('admin.chatbot.index', compact('faqs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'keywords' => 'nullable|string',
            'sort_order' => 'integer',
            'is_active' => 'boolean'
        ]);

        ChatbotFaq::create($request->all());
        return redirect()->route('admin.chatbot.index')->with('success', 'FAQ berhasil ditambahkan');
    }

    public function update(Request $request, ChatbotFaq $faq)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'keywords' => 'nullable|string',
            'sort_order' => 'integer',
            'is_active' => 'boolean'
        ]);

        $faq->update($request->all());
        return redirect()->route('admin.chatbot.index')->with('success', 'FAQ berhasil diperbarui');
    }

    public function destroy(ChatbotFaq $faq)
    {
        $faq->delete();
        return redirect()->route('admin.chatbot.index')->with('success', 'FAQ berhasil dihapus');
    }

    public function unanswered()
    {
        $logs = ChatbotUnansweredLog::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.chatbot.unanswered', compact('logs'));
    }
}
