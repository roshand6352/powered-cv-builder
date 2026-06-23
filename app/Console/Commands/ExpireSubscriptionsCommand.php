<?php

namespace App\Console\Commands;

use App\Services\Subscription\SubscriptionService;
use Illuminate\Console\Command;

class ExpireSubscriptionsCommand extends Command
{
    protected $signature = 'subscriptions:expire';

    protected $description = 'Downgrade users whose paid plans have expired';

    public function handle(SubscriptionService $subscriptionService): int
    {
        $count = $subscriptionService->expireDueSubscriptions();

        $this->info("Expired {$count} subscription(s).");

        return self::SUCCESS;
    }
}
