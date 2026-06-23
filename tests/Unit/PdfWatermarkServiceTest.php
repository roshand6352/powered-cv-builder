<?php

namespace Tests\Unit;

use App\Models\Plan;
use App\Models\User;
use App\Services\Pdf\PdfWatermarkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PdfWatermarkServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_free_plan_users_see_watermark(): void
    {
        $plan = Plan::query()->create([
            'name' => 'Free Test',
            'slug' => 'free-test',
            'price' => 0,
            'currency' => 'ZAR',
            'status' => 'active',
        ]);

        $user = User::factory()->create(['plan_id' => $plan->id]);

        $service = app(PdfWatermarkService::class);

        $this->assertTrue($service->showWatermarkForUser($user));
        $this->assertTrue($service->shouldApplyForUserId($user->id));
    }

    public function test_paid_plan_users_do_not_see_watermark(): void
    {
        $plan = Plan::query()->create([
            'name' => 'Pro Test',
            'slug' => 'pro-test',
            'price' => 99,
            'currency' => 'ZAR',
            'status' => 'active',
        ]);

        $user = User::factory()->create(['plan_id' => $plan->id]);

        $service = app(PdfWatermarkService::class);

        $this->assertFalse($service->showWatermarkForUser($user));
        $this->assertFalse($service->shouldApplyForUserId($user->id));
    }
}
