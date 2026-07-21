<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatbotFaq extends Model
{
    use HasFactory;

    protected $table = 'chatbot_faqs';

    protected $fillable = [
        'question',
        'answer',
        'keywords',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
