{{--
    Subscription Expiry Reminder — branded HTML template (UI ready; wire via Mailable when implemented).
    Expected: $user, $planName, $expiresAt, $renewUrl (optional)
--}}
@php
    $userName = $user->name ?? __('there');
    $planName = $planName ?? __('your plan');
    $expiresAt = $expiresAt ?? null;
    $renewUrl = $renewUrl ?? url('/dashboard');
    $brandName = config('marketing.brand.name', config('app.name', 'ApplyDocs ZA'));
@endphp
<x-emails.layouts.branded :title="__('Subscription Expiring Soon') . ' — ' . $brandName">
    <h1 style="margin: 0 0 16px 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 24px; font-weight: bold; line-height: 1.3; color: #172d40;">
        {{ __('Your subscription is expiring soon') }}
    </h1>
    <p style="margin: 0 0 16px 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.5; color: #172d40;">
        {{ __('Hello :name,', ['name' => $userName]) }}
    </p>
    <p style="margin: 0 0 16px 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.5; color: #172d40;">
        {{ __('This is a reminder that your :plan subscription will expire soon.', ['plan' => $planName]) }}
        @if ($expiresAt)
            {{ __('Expiry date: :date.', ['date' => $expiresAt]) }}
        @endif
    </p>
    <p style="margin: 0 0 16px 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.5; color: #172d40;">
        {{ __('Renew now to keep uninterrupted access to Pro features.') }}
    </p>
    @include('emails.partials.cta-button', [
        'url' => $renewUrl,
        'label' => __('Renew Subscription'),
    ])
</x-emails.layouts.branded>
