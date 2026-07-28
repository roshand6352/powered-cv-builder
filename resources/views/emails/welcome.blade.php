{{--
    Welcome Email — branded HTML template (UI ready; wire via Mailable when implemented).
    Expected: $user, $dashboardUrl (optional)
--}}
@php
    $userName = $user->name ?? __('there');
    $dashboardUrl = $dashboardUrl ?? url('/dashboard');
    $brandName = config('marketing.brand.name', config('app.name', 'ApplyDocs ZA'));
@endphp
<x-emails.layouts.branded :title="__('Welcome') . ' — ' . $brandName">
    <h1 style="margin: 0 0 16px 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 24px; font-weight: bold; line-height: 1.3; color: #172d40;">
        {{ __('Welcome to :app', ['app' => $brandName]) }}
    </h1>
    <p style="margin: 0 0 16px 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.5; color: #172d40;">
        {{ __('Hello :name,', ['name' => $userName]) }}
    </p>
    <p style="margin: 0 0 16px 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.5; color: #172d40;">
        {{ __('Thanks for joining :app. You can start building your CV and cover letter from your dashboard.', ['app' => $brandName]) }}
    </p>
    @include('emails.partials.cta-button', [
        'url' => $dashboardUrl,
        'label' => __('Go to Dashboard'),
    ])
    <p style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.5; color: #172d40;">
        {{ __('If you have any questions, our support team is happy to help.') }}
    </p>
</x-emails.layouts.branded>
