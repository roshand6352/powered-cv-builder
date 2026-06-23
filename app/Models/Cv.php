<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Cv extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'template_id',
        'title',
        'cv_data',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'cv_data' => 'array',
            'status' => 'integer',
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getJsonDataAttribute(): ?array
    {
        return $this->cv_data;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cvTemplate(): BelongsTo
    {
        return $this->belongsTo(CvTemplate::class, 'template_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CvSection::class)->orderBy('sort_order');
    }

    public function coverLetters(): HasMany
    {
        return $this->hasMany(CoverLetter::class);
    }

    public function pdfExports(): MorphMany
    {
        return $this->morphMany(PdfExport::class, 'exportable');
    }
}
