<?php

namespace App\Models;

use App\Enums\AiLogStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AiLog extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'feature',
        'status',
        'model',
        'tokens_used',
        'credits_used',
        'request',
        'response',
        'result',
        'error_message',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => AiLogStatus::class,
            'tokens_used' => 'integer',
            'credits_used' => 'integer',
            'request' => 'array',
            'response' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public function isComplete(): bool
    {
        return $this->status === AiLogStatus::Completed;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
