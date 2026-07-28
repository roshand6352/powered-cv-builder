<?php

namespace App\Models;

use App\Enums\CouponStatus;
use App\Enums\CouponType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'status',
        'description',
        'starts_at',
        'expires_at',
        'max_uses',
        'used_count',
        'per_user_limit',
        'min_amount',
        'plan_ids',
    ];

    protected function casts(): array
    {
        return [
            'discount_type' => CouponType::class,
            'status' => CouponStatus::class,
            'discount_value' => 'decimal:2',
            'min_amount' => 'decimal:2',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'max_uses' => 'integer',
            'used_count' => 'integer',
            'per_user_limit' => 'integer',
            'plan_ids' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $coupon): void {
            $coupon->code = strtoupper(trim((string) $coupon->code));
        });
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(CouponRedemption::class);
    }

    public function isActive(): bool
    {
        return $this->status === CouponStatus::Active;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isFullyUsed(): bool
    {
        return $this->max_uses !== null && $this->used_count >= $this->max_uses;
    }

    public function displayStatus(): string
    {
        if ($this->isFullyUsed()) {
            return 'Fully Used';
        }

        if ($this->isExpired()) {
            return 'Expired';
        }

        return $this->status === CouponStatus::Active ? 'Active' : 'Inactive';
    }

    public function displayStatusColor(): string
    {
        return match ($this->displayStatus()) {
            'Active' => 'success',
            'Inactive' => 'gray',
            'Expired' => 'danger',
            'Fully Used' => 'warning',
            default => 'gray',
        };
    }

    public function canBeDeleted(): bool
    {
        return (int) $this->used_count === 0;
    }

    public function appliesToPlan(Plan $plan): bool
    {
        $planIds = $this->plan_ids;

        if (! is_array($planIds) || $planIds === []) {
            return true;
        }

        return in_array((int) $plan->id, array_map('intval', $planIds), true);
    }
}
