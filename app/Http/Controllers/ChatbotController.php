<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ChatbotFaq;
use App\Models\ChatbotUnansweredLog;

class ChatbotController extends Controller
{
    public function getFaqs()
    {
        $faqs = ChatbotFaq::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'question']);

        return response()->json($faqs);
    }

    public function ask(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'faq_id' => 'nullable|integer|exists:chatbot_faqs,id'
        ]);

        if ($request->faq_id) {
            $faq = ChatbotFaq::find($request->faq_id);
            if ($faq) {
                return response()->json(['answer' => $faq->answer]);
            }
        }

        $input = strtolower($request->question);
        $faqs = ChatbotFaq::where('is_active', true)->get();

        foreach ($faqs as $faq) {
            if ($faq->keywords) {
                $keywords = array_map('trim', explode(',', strtolower($faq->keywords)));
                foreach ($keywords as $kw) {
                    if ($kw !== '' && str_contains($input, $kw)) {
                        return response()->json(['answer' => $faq->answer]);
                    }
                }
            }
        }

        ChatbotUnansweredLog::create(['user_input' => $request->question]);
        return response()->json(['answer' => null]);
    }
}
