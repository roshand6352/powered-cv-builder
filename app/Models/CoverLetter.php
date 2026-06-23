<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoverLetter extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'cv_id',
        'template_id',
        'title',
        'company_name',
        'job_title',
        'hiring_manager_name',
        'company_address',
        'subject',
        'greeting',
        'introduction',
        'body',
        'closing_paragraph',
        'signature_name',
        'slug',
        'json_data',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'json_data' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(CvTemplate::class, 'template_id');
    }

    public function pdfExports(): MorphMany
    {
        return $this->morphMany(PdfExport::class, 'exportable');
    }

    /**
     * Editable content fields for the cover letter body (excludes meta columns).
     *
     * @return array<string, string|null>
     */
    public function contentFields(): array
    {
        return [
            'company_name' => $this->company_name,
            'job_title' => $this->job_title,
            'hiring_manager_name' => $this->hiring_manager_name,
            'company_address' => $this->company_address,
            'subject' => $this->subject,
            'greeting' => $this->greeting,
            'introduction' => $this->introduction,
            'body' => $this->body,
            'closing_paragraph' => $this->closing_paragraph,
            'signature_name' => $this->signature_name,
        ];
    }
}
