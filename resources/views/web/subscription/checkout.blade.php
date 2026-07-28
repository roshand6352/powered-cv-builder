@php
    $symbol = $plan->currency_symbol ?: ($plan->currency === 'ZAR' ? 'R' : '$');
    $planPrice = round((float) $plan->price, 2);
    $initialCoupon = is_array($appliedCoupon ?? null) ? $appliedCoupon : null;
@endphp

<x-dashboard.layout title="Checkout">
    <x-slot name="header">
        <x-dashboard.page-header
            title="Checkout"
            description="Review your plan and continue to secure payment with PayFast."
            class="mb-0"
        />
    </x-slot>

    <div
        class="mx-auto max-w-3xl space-y-6"
        x-data="checkoutCoupon(@js([
            'planId' => $plan->id,
            'planPrice' => $planPrice,
            'symbol' => $symbol,
            'validateUrl' => $couponValidateUrl,
            'removeUrl' => $couponRemoveUrl,
            'applied' => $initialCoupon,
        ]))"
    >
        @if (session('error'))
            <div class="dash-alert-error">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="dash-alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="dash-card p-6">
            <h2 class="text-lg font-semibold text-navy">Plan details</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="rounded-xl bg-brand-50 p-4">
                    <p class="text-xs uppercase tracking-wide text-muted">Selected plan</p>
                    <p class="mt-1 text-xl font-bold text-navy">{{ $plan->name }}</p>
                    <p class="mt-1 text-sm text-muted">{{ $plan->description }}</p>
                </div>
                <div class="rounded-xl bg-brand-50 p-4">
                    <p class="text-xs uppercase tracking-wide text-muted">Plan price</p>
                    <p class="mt-1 text-xl font-bold text-navy">{{ $plan->formattedPrice() }}</p>
                    <p class="mt-1 text-sm text-muted">{{ $plan->displayPriceSuffix() ?? '/ one-time' }}</p>
                </div>
            </div>
        </div>

        <div class="dash-card p-6">
            <h2 class="text-lg font-semibold text-navy">Discount Code</h2>
            <p class="mt-1 text-sm text-muted">Insert your discount code from one of our reward partners.</p>

            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-stretch">
                <label for="coupon-code" class="sr-only">Discount code</label>
                <input
                    id="coupon-code"
                    type="text"
                    x-model="code"
                    @keydown.enter.prevent="applyCoupon()"
                    :disabled="applied || loading"
                    autocomplete="off"
                    placeholder="Enter discount code"
                    class="block w-full min-w-0 flex-1 rounded-lg border-slate-300 uppercase shadow-sm focus:border-brand-500 focus:ring-brand-500 disabled:cursor-not-allowed disabled:bg-slate-100"
                >
                <button
                    type="button"
                    @click="applyCoupon()"
                    :disabled="applied || loading || !code.trim()"
                    class="inline-flex shrink-0 items-center justify-center rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <span x-show="!loading">Apply</span>
                    <span x-show="loading" x-cloak class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        Checking…
                    </span>
                </button>
            </div>

            <p
                x-show="errorMessage"
                x-cloak
                x-text="errorMessage"
                class="mt-3 text-sm font-medium text-red-600"
                role="alert"
            ></p>

            <div
                x-show="applied"
                x-cloak
                class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4"
                role="status"
            >
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-emerald-800" x-text="successMessage"></p>
                        <p class="mt-1 text-sm text-emerald-700">
                            Code <span class="font-semibold" x-text="appliedCode"></span>
                            · Discount <span class="font-semibold" x-text="formattedDiscount"></span>
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="removeCoupon()"
                        :disabled="loading"
                        class="text-sm font-semibold text-emerald-800 underline hover:text-emerald-900 disabled:opacity-60"
                    >
                        Remove coupon
                    </button>
                </div>
            </div>
        </div>

        <div class="dash-card p-6">
            <h2 class="text-lg font-semibold text-navy">Pricing summary</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-muted">Plan price</dt>
                    <dd class="font-semibold text-navy" x-text="formattedPlanPrice"></dd>
                </div>
                <div class="flex items-center justify-between gap-4" x-show="discountAmount > 0" x-cloak>
                    <dt class="text-muted">Discount</dt>
                    <dd class="font-semibold text-emerald-700" x-text="'-' + formattedDiscount"></dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-t border-slate-200 pt-3">
                    <dt class="text-base font-semibold text-navy">Total payable</dt>
                    <dd class="text-xl font-bold text-navy" x-text="formattedTotal"></dd>
                </div>
            </dl>
        </div>

        <div class="dash-card p-6">
            <h2 class="text-lg font-semibold text-navy">Account</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <p class="text-xs uppercase tracking-wide text-muted">Name</p>
                    <p class="mt-1 font-semibold text-navy">{{ $user->name }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-muted">Email</p>
                    <p class="mt-1 font-semibold text-navy">{{ $user->email }}</p>
                </div>
            </div>
            <div class="mt-6 space-y-3">
                <form
                    method="POST"
                    action="{{ route('subscription.pay', $plan) }}"
                    class="min-w-0"
                    x-data="{
                        acceptedTerms: false,
                        termsError: false,
                        submitCheckout() {
                            if (!this.acceptedTerms) {
                                this.termsError = true;
                                return;
                            }

                            this.termsError = false;
                            this.$el.submit();
                        },
                    }"
                    @submit.prevent="submitCheckout()"
                >
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                    <input type="hidden" name="plan_name" value="{{ $plan->name }}">
                    <input type="hidden" name="plan_price" value="{{ $plan->price }}">
                    <input type="hidden" name="coupon_code" :value="applied ? appliedCode : ''">
                    <label class="flex max-w-2xl items-start gap-3">
                        <input
                            type="checkbox"
                            x-model="acceptedTerms"
                            @change="if (acceptedTerms) termsError = false"
                            class="mt-1 h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500"
                        >
                        <span class="text-sm leading-6 text-muted">
                            I understand that I am paying for timed access to ApplyDocs ZA premium features, not per CV. When my access period expires, I will need to upgrade again to continue using paid features.
                        </span>
                    </label>
                    <p
                        x-show="termsError"
                        x-cloak
                        class="mt-2 text-sm font-medium text-red-600"
                        role="alert"
                    >
                        Please confirm that you understand the subscription terms before continuing.
                    </p>
                    <button
                        type="submit"
                        class="dash-btn-primary mt-4 disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="loading || !acceptedTerms"
                    >
                        Pay now with PayFast
                    </button>

                    <div class="mt-4 w-full min-w-0">
                        <p class="mb-2 text-xs font-medium text-muted">
                            Payment options available through PayFast:
                        </p>

                        @php
                            $paymentLogos = [
                                ['file' => 'Visa.svg', 'alt' => 'Visa'],
                                ['file' => 'mastercard.webp', 'alt' => 'Mastercard'],
                                ['file' => 'instant-eft.svg', 'alt' => 'EFT'],
                                ['file' => 'Capitec-Pay.svg', 'alt' => 'Capitec Pay'],
                                ['file' => 'ozow.png', 'alt' => 'Ozow'],
                                ['file' => 'SnapScan.svg', 'alt' => 'SnapScan'],
                                ['file' => 'Masterpass.webp', 'alt' => 'Masterpass'],
                                ['file' => 'mobicred.svg', 'alt' => 'Mobicred'],
                            ];
                        @endphp

                        <div class="checkout-pay-marquee" aria-label="Payment methods available through PayFast">
                            <div class="checkout-pay-marquee__track">
                                <div class="checkout-pay-marquee__group">
                                    @foreach ($paymentLogos as $logo)
                                        <img
                                            src="{{ asset('images/payment-method-logos/'.$logo['file']) }}"
                                            alt="{{ $logo['alt'] }}"
                                            loading="lazy"
                                            class="checkout-pay-marquee__logo"
                                        >
                                    @endforeach
                                </div>
                                <div class="checkout-pay-marquee__group" aria-hidden="true">
                                    @foreach ($paymentLogos as $logo)
                                        <img
                                            src="{{ asset('images/payment-method-logos/'.$logo['file']) }}"
                                            alt=""
                                            loading="lazy"
                                            class="checkout-pay-marquee__logo"
                                        >
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <style>
                            .checkout-pay-marquee {
                                overflow: hidden;
                                width: 100%;
                                max-width: 100%;
                                min-width: 0;
                            }

                            .checkout-pay-marquee__track {
                                display: flex;
                                align-items: center;
                                max-width: none;
                                animation: checkout-pay-marquee-scroll 7s linear infinite;
                                will-change: transform;
                            }

                            .checkout-pay-marquee:hover .checkout-pay-marquee__track {
                                animation-play-state: paused;
                            }

                            .checkout-pay-marquee__group {
                                display: flex;
                                flex-shrink: 0;
                                align-items: center;
                                gap: 1.125rem;
                                padding-right: 1.125rem;
                                min-width: 0;
                            }

                            .checkout-pay-marquee__logo {
                                display: block;
                                height: 28px;
                                width: auto;
                                max-width: 100%;
                                object-fit: contain;
                                flex-shrink: 0;
                            }

                            @media (max-width: 640px) {
                                .checkout-pay-marquee__group {
                                    gap: 0.875rem;
                                    padding-right: 0.875rem;
                                }

                                .checkout-pay-marquee__logo {
                                    height: 24px;
                                }
                            }

                            @keyframes checkout-pay-marquee-scroll {
                                from { transform: translateX(0); }
                                to { transform: translateX(-50%); }
                            }

                            @media (hover: none) {
                                .checkout-pay-marquee:hover .checkout-pay-marquee__track {
                                    animation-play-state: running;
                                }
                            }

                            @media (prefers-reduced-motion: reduce) {
                                .checkout-pay-marquee__track {
                                    animation: none;
                                }
                            }
                        </style>
                    </div>
                </form>
                <div>
                    <a href="{{ route('pricing') }}" class="dash-btn-secondary">
                        Back to pricing
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-dashboard.layout>
