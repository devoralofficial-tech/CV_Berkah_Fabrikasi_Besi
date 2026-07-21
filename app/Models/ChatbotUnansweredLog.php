<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotUnansweredLog extends Model
{
    use HasFactory;

    protected $table = 'chatbot_unanswered_logs';

    protected $fillable = [
        'user_input',
        'is_resolved',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
    ];
}
