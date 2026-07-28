{{--
    Subscription Expired — branded HTML template (UI ready; wire via Mailable when implemented).
    Expected: $user, $planName, $renewUrl (optional)
--}}
@php
    $userName = $user->name ?? __('there');
    $planName = $planName ?? __('your plan');
    $renewUrl = $renewUrl ?? url('/dashboard');
    $brandName = config('marketing.brand.name', config('app.name', 'ApplyDocs ZA'));
@endphp
<x-emails.layouts.branded :title="__('Subscription Expired') . ' — ' . $brandName">
    <h1 style="margin: 0 0 16px 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 24px; font-weight: bold; line-height: 1.3; color: #172d40;">
        {{ __('Your subscription has expired') }}
    </h1>
    <p style="margin: 0 0 16px 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.5; color: #172d40;">
        {{ __('Hello :name,', ['name' => $userName]) }}
    </p>
    <p style="margin: 0 0 16px 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.5; color: #172d40;">
        {{ __('Your :plan subscription has expired. You can still use free features, and you can renew anytime to restore Pro access.', ['plan' => $planName]) }}
    </p>
    @include('emails.partials.cta-button', [
        'url' => $renewUrl,
        'label' => __('Renew Subscription'),
    ])
    <p style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.5; color: #172d40;">
        {{ __('We hope to see you back soon.') }}
    </p>
</x-emails.layouts.branded>
