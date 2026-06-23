<?php

namespace App\Models;

use App\Enums\TemplateType;
use App\Support\CoverLetter\CoverLetterTemplateLayout;
use App\Support\Cv\CvTemplateLayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CvTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'layout_json',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'type' => TemplateType::class,
            'layout_json' => 'array',
            'status' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (CvTemplate $template): void {
            if (! $template->type) {
                $template->type = TemplateType::Cv;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    public function scopeCvOnly(Builder $query): Builder
    {
        return $query->where('type', TemplateType::Cv->value);
    }

    public function scopeCoverLetterOnly(Builder $query): Builder
    {
        return $query->where('type', TemplateType::CoverLetter->value);
    }

    /**
     * @return array<string, mixed>
     */
    public function normalizedLayout(): array
    {
        return $this->type->isCoverLetter()
            ? CoverLetterTemplateLayout::normalize($this->layout_json)
            : CvTemplateLayout::normalize($this->layout_json);
    }

    public function isCv(): bool
    {
        return $this->type->isCv();
    }

    public function isCoverLetter(): bool
    {
        return $this->type->isCoverLetter();
    }

    public function scopeOfType(Builder $query, TemplateType $type): Builder
    {
        return $query->where('type', $type->value);
    }

    public function cvs(): HasMany
    {
        return $this->hasMany(Cv::class, 'template_id');
    }
}
