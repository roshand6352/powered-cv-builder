<?php

namespace Database\Seeders;

use App\Enums\CouponStatus;
use App\Enums\CouponType;
use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::query()->updateOrCreate(
            ['code' => 'WELCOME20'],
            [
                'discount_type' => CouponType::Percentage,
                'discount_value' => 20,
                'status' => CouponStatus::Active,
                'description' => '20% off any paid plan',
                'max_uses' => 1000,
                'per_user_limit' => 1,
                'starts_at' => now()->subDay(),
                'expires_at' => now()->addYear(),
            ],
        );
    }
}
