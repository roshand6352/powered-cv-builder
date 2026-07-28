{{--
    Email Verification — branded HTML template.
    Live verification mail currently uses Laravel's VerifyEmail notification
    (markdown → vendor/mail). This Blade mirrors that layout for consistency
    when a custom mailable/view is wired later.
    Expected: $user, $verificationUrl
--}}
@php
    $userName = $user->name ?? __('there');
    $verificationUrl = $verificationUrl ?? ($url ?? '#');
    $brandName = config('marketing.brand.name', config('app.name', 'ApplyDocs ZA'));
@endphp
<x-emails.layouts.branded :title="__('Verify Email Address') . ' — ' . $brandName">
    <h1 style="margin: 0 0 16px 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 24px; font-weight: bold; line-height: 1.3; color: #172d40;">
        {{ __('Verify Email Address') }}
    </h1>
    <p style="margin: 0 0 16px 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.5; color: #172d40;">
        {{ __('Hello :name,', ['name' => $userName]) }}
    </p>
    <p style="margin: 0 0 16px 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.5; color: #172d40;">
        {{ __('Please click the button below to verify your email address.') }}
    </p>
    @include('emails.partials.cta-button', [
        'url' => $verificationUrl,
        'label' => __('Verify Email Address'),
    ])
    <p style="margin: 0 0 16px 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.5; color: #172d40;">
        {{ __('If you did not create an account, no further action is required.') }}
    </p>
    <p style="margin: 28px 0 0 0; padding: 24px 0 0 0; border-top: 1px solid #e8e4df; font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 1.5; color: #5c6b7a;">
        {{ __('If you\'re having trouble clicking the ":actionText" button, copy and paste the URL below into your web browser:', ['actionText' => __('Verify Email Address')]) }}
        <br>
        <a href="{{ $verificationUrl }}" style="color: #c1623d; word-break: break-all;">{{ $verificationUrl }}</a>
    </p>
</x-emails.layouts.branded>
