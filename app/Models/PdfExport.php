<?php

namespace App\Models;

use App\Enums\PdfExportStatus;
use App\Enums\PdfExportType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PdfExport extends Model
{
    protected $fillable = [
        'user_id',
        'exportable_type',
        'exportable_id',
        'type',
        'filename',
        'disk',
        'path',
        'status',
        'error_message',
        'options',
        'file_size',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => PdfExportType::class,
            'status' => PdfExportStatus::class,
            'options' => 'array',
            'file_size' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exportable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isDownloadable(): bool
    {
        return $this->status === PdfExportStatus::Completed
            && $this->path !== null;
    }
}
