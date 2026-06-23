<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CvSection extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'cv_id',
        'type',
        'title',
        'json_data',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'json_data' => 'array',
            'sort_order' => 'integer',
        ];
    }

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }
}
