<?php

namespace App\Services\Subscription;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayFastService extends BaseService
{
    public function isConfigured(): bool
    {
        $merchantId = trim((string) config('payfast.merchant_id'));
        $merchantKey = trim((string) config('payfast.merchant_key'));

        if ($merchantId === '' || $merchantKey === '') {
            return false;
        }

        // Guard against placeholder values copied from .env.example.
        if (str_contains($merchantId, 'your_') || str_contains($merchantKey, 'your_')) {
            return false;
        }

        return true;
    }

    public function isSandbox(): bool
    {
        return config('payfast.mode', 'sandbox') === 'sandbox';
    }

    /**
     * @return array<string, string>
     */
    public function buildPaymentForm(Payment $payment, User $user, Plan $plan): array
    {
        $names = $this->splitName($user->name);

        $data = array_filter([
            'merchant_id' => config('payfast.merchant_id'),
            'merchant_key' => config('payfast.merchant_key'),
            'return_url' => route('subscription.success'),
            'cancel_url' => route('subscription.cancel'),
            'notify_url' => route('payfast.itn'),
            'name_first' => $names['first'],
            'name_last' => $names['last'],
            'email_address' => $user->email,
            'm_payment_id' => $payment->reference,
            'amount' => number_format((float) $plan->price, 2, '.', ''),
            'item_name' => $plan->name.' Plan',
            'item_description' => $plan->description ?? config('app.name').' subscription',
            'custom_str1' => (string) $user->id,
            'custom_str2' => (string) $plan->id,
            'custom_str3' => (string) $payment->id,
        ], fn ($value) => $value !== null && $value !== '');

        $data['signature'] = $this->generateSignature($data);

        return $data;
    }

    public function processUrl(): string
    {
        $mode = config('payfast.mode', 'sandbox');

        return config("payfast.{$mode}.process_url");
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function verifyItn(array $payload): bool
    {
        if (! $this->verifyItnSignature($payload)) {
            Log::warning('PayFast ITN signature mismatch.', ['reference' => $payload['m_payment_id'] ?? null]);

            return false;
        }

        if (! $this->validServerConfirmation($payload)) {
            Log::warning('PayFast ITN server confirmation failed.', ['reference' => $payload['m_payment_id'] ?? null]);

            return false;
        }

        $merchantId = (string) ($payload['merchant_id'] ?? '');
        $expectedMerchantId = (string) config('payfast.merchant_id');

        if ($merchantId !== $expectedMerchantId) {
            return false;
        }

        return true;
    }

    public function verifyReturnPayload(Request $request): bool
    {
        $payload = $request->query();

        if (! isset($payload['signature'])) {
            return false;
        }

        if (! $this->verifyReturnSignature($request)) {
            return false;
        }

        $merchantId = (string) ($payload['merchant_id'] ?? '');
        $expectedMerchantId = (string) config('payfast.merchant_id');

        return $merchantId !== '' && $merchantId === $expectedMerchantId;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function verifyItnSignature(array $payload): bool
    {
        if (! isset($payload['signature'])) {
            return false;
        }

        $paramString = $this->buildItnParamString($payload);
        $checkString = $paramString;

        if (filled(config('payfast.passphrase'))) {
            $checkString .= '&passphrase='.urlencode(trim((string) config('payfast.passphrase')));
        }

        $generated = md5($checkString);

        return hash_equals(strtolower($generated), strtolower((string) $payload['signature']));
    }

    public function verifyReturnSignature(Request $request): bool
    {
        $payload = $request->query();

        if (! isset($payload['signature'])) {
            return false;
        }

        $queryString = (string) $request->server('QUERY_STRING', '');

        if ($queryString !== '') {
            $paramString = preg_replace('/(^|&)signature=[^&]*/', '', $queryString);
            $paramString = ltrim((string) $paramString, '&');
        } else {
            $paramString = $this->buildItnParamString($payload);
        }

        $checkString = $paramString;

        if (filled(config('payfast.passphrase'))) {
            $checkString .= '&passphrase='.urlencode(trim((string) config('payfast.passphrase')));
        }

        $generated = md5($checkString);

        return hash_equals(strtolower($generated), strtolower((string) $payload['signature']));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function generateSignature(array $data): string
    {
        $payload = $data;
        unset($payload['signature']);

        return md5($this->buildQueryString($payload));
    }

    /**
     * Build the parameter string PayFast uses for ITN signatures and server validation.
     *
     * @param  array<string, mixed>  $payload
     */
    protected function buildItnParamString(array $payload): string
    {
        $pfParamString = '';

        foreach ($payload as $key => $value) {
            if ($key === 'signature') {
                break;
            }

            if (is_array($value)) {
                continue;
            }

            $pfParamString .= $key.'='.urlencode(stripslashes(trim((string) $value))).'&';
        }

        return rtrim($pfParamString, '&');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function validServerConfirmation(array $payload): bool
    {
        $mode = config('payfast.mode', 'sandbox');
        $validateUrl = config("payfast.{$mode}.validate_url");
        $paramString = $this->buildItnParamString($payload);

        if ($paramString === '') {
            return false;
        }

        try {
            $response = Http::withBody($paramString, 'application/x-www-form-urlencoded')
                ->timeout(30)
                ->post($validateUrl)
                ->body();

            return trim($response) === 'VALID';
        } catch (\Throwable $exception) {
            Log::error('PayFast validation request failed.', [
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function buildQueryString(array $data, bool $includePassphrase = true): string
    {
        $output = '';

        foreach ($data as $key => $value) {
            if ($key === 'signature' || $value === '' || $value === null) {
                continue;
            }

            $output .= $key.'='.urlencode(trim((string) $value)).'&';
        }

        $string = rtrim($output, '&');

        if ($includePassphrase && filled(config('payfast.passphrase'))) {
            $string .= '&passphrase='.urlencode(trim((string) config('payfast.passphrase')));
        }

        return $string;
    }

    /**
     * @return array{first: string, last: string}
     */
    protected function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];

        return [
            'first' => $parts[0] ?? 'Customer',
            'last' => count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : 'User',
        ];
    }
}
