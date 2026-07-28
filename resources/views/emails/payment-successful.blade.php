{{--
    Payment Successful — branded HTML template (UI ready; wire via Mailable when implemented).
    Expected: $user, $planName, $amount (optional), $dashboardUrl (optional), $invoiceUrl (optional)
--}}
@php
    $userName = $user->name ?? __('there');
    $planName = $planName ?? __('your plan');
    $amount = $amount ?? null;
    $ctaUrl = $invoiceUrl ?? $dashboardUrl ?? url('/dashboard');
    $ctaLabel = isset($invoiceUrl) ? __('View Invoice') : __('Go to Dashboard');
    $brandName = config('marketing.brand.name', config('app.name', 'ApplyDocs ZA'));
@endphp
<x-emails.layouts.branded :title="__('Payment Successful') . ' — ' . $brandName">
    <h1 style="margin: 0 0 16px 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 24px; font-weight: bold; line-height: 1.3; color: #172d40;">
        {{ __('Payment Successful') }}
    </h1>
    <p style="margin: 0 0 16px 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.5; color: #172d40;">
        {{ __('Hello :name,', ['name' => $userName]) }}
    </p>
    <p style="margin: 0 0 16px 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.5; color: #172d40;">
        {{ __('We have received your payment. Your :plan subscription is now active.', ['plan' => $planName]) }}
    </p>
    @if ($amount)
        <p style="margin: 0 0 16px 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.5; color: #172d40;">
            {{ __('Amount paid: :amount', ['amount' => $amount]) }}
        </p>
    @endif
    @include('emails.partials.cta-button', [
        'url' => $ctaUrl,
        'label' => $ctaLabel,
    ])
    <p style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.5; color: #172d40;">
        {{ __('Thank you for supporting :app.', ['app' => $brandName]) }}
    </p>
</x-emails.layouts.branded>
