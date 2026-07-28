{{--
    Password Reset — branded HTML template.
    Live reset mail currently uses Laravel's ResetPassword notification
    (markdown → vendor/mail). This Blade mirrors that layout for consistency
    when a custom mailable/view is wired later.
    Expected: $user, $resetUrl
--}}
@php
    $userName = $user->name ?? __('there');
    $resetUrl = $resetUrl ?? ($url ?? '#');
    $brandName = config('marketing.brand.name', config('app.name', 'ApplyDocs ZA'));
    $expireMinutes = $expireMinutes ?? (config('auth.passwords.'.config('auth.defaults.passwords').'.expire') ?? 60);
@endphp
<x-emails.layouts.branded :title="__('Reset Password') . ' — ' . $brandName">
    <h1 style="margin: 0 0 16px 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 24px; font-weight: bold; line-height: 1.3; color: #172d40;">
        {{ __('Reset Password Notification') }}
    </h1>
    <p style="margin: 0 0 16px 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.5; color: #172d40;">
        {{ __('Hello :name,', ['name' => $userName]) }}
    </p>
    <p style="margin: 0 0 16px 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.5; color: #172d40;">
        {{ __('You are receiving this email because we received a password reset request for your account.') }}
    </p>
    @include('emails.partials.cta-button', [
        'url' => $resetUrl,
        'label' => __('Reset Password'),
    ])
    <p style="margin: 0 0 16px 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.5; color: #172d40;">
        {{ __('This password reset link will expire in :count minutes.', ['count' => $expireMinutes]) }}
    </p>
    <p style="margin: 0 0 16px 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.5; color: #172d40;">
        {{ __('If you did not request a password reset, no further action is required.') }}
    </p>
    <p style="margin: 28px 0 0 0; padding: 24px 0 0 0; border-top: 1px solid #e8e4df; font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 1.5; color: #5c6b7a;">
        {{ __('If you\'re having trouble clicking the ":actionText" button, copy and paste the URL below into your web browser:', ['actionText' => __('Reset Password')]) }}
        <br>
        <a href="{{ $resetUrl }}" style="color: #c1623d; word-break: break-all;">{{ $resetUrl }}</a>
    </p>
</x-emails.layouts.branded>
