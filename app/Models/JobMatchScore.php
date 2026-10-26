<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobMatchScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cv_id',
        'cover_letter_id',
        'job_description',
        'score',
        'result_json',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'result_json' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }

    public function coverLetter(): BelongsTo
    {
        return $this->belongsTo(CoverLetter::class);
    }
}
