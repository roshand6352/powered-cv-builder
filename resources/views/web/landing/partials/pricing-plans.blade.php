@props(['plans', 'currentPlan' => null, 'showAllPlansButton' => false])

<section class="pricing" id="pricing">
    <div class="container">
        <span class="section-tag" style="display:block;text-align:center">Pricing</span>
        <h2 class="section-title pricing-title">Start free. Upgrade when you are ready.</h2>
        <p class="pricing-sub">Choose Free to get started, or upgrade to Pro for premium templates, AI tools, and watermark-free PDFs.</p>

        <div class="pricing-grid">
            @foreach ($plans as $plan)
                @php
                    $badge = $plan->displayBadge();
                    $buttonClass = $plan->buttonCssClass();
                    $customUrl = route('subscription.checkout', $plan->id) . '?' . http_build_query([
                                 'plan_id'    => $plan->id,
                                 'plan_name'  => $plan->name,
                                 'plan_price' => $plan->price,
                                ]);
                    $cardStyle = $plan->cardStyleAttributes()['style'] ?? null;
                    $buttonStyle = $plan->buttonStyleAttributes()['style'] ?? 'width:100%;justify-content:center;padding:14px';
                    $isCurrentPlan = auth()->check() && $currentPlan && (int) $currentPlan->id === (int) $plan->id;
                @endphp

                <div @class(['price-card', 'pro' => $plan->is_featured]) @if ($cardStyle) style="{{ $cardStyle }}" @endif>
                    @if ($badge)
                        <div class="pro-badge" style="background: {{ $plan->badge_color ?: $plan->accent_color ?: 'var(--orange)' }}">
                            {{ $badge }}
                        </div>
                    @endif

                    <div class="price-tier">{{ $plan->displayTypeLabel() }}</div>
                    @if ($isCurrentPlan)
                        <div class="payment-note">Current active plan</div>
                    @endif

                    <div class="price-amount">
                        {{ $plan->formattedPrice() }}
                        @if ($suffix = $plan->displayPriceSuffix())
                            <span>{{ $suffix }}</span>
                        @endif
                    </div>

                    @if ($plan->description)
                        <p class="price-desc">{{ $plan->description }}</p>
                    @endif

                    <ul class="price-features">
                        @foreach ($plan->displayFeatures() as $feature)
                            <li @class(['na' => $feature['status'] === 'disabled'])>
                                <span class="{{ $plan->featureIconClass($feature['icon_type'], $feature['status']) }}">
                                    {!! $plan->featureIconMarkup($feature['icon_type'], $feature['status']) !!}
                                </span>
                                {{ $feature['title'] }}
                            </li>
                        @endforeach
                    </ul>

                    @auth
                        @if ($isCurrentPlan)
                            <button type="button" class="btn {{ $buttonClass }}" style="{{ $buttonStyle }}" disabled>
                                Current Plan
                            </button>
                        @elseif ($customUrl)
                            <a href="{{ $customUrl }}" class="btn {{ $buttonClass }}" style="{{ $buttonStyle }}">
                                {{ $plan->displayButtonText(true) }}
                            </a>
                        @else
                            <form method="GET" action="{{ route('subscription.checkout', $plan) }}">
                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                <input type="hidden" name="plan_name" value="{{ $plan->name }}">
                                <input type="hidden" name="plan_price" value="{{ $plan->price }}">
                                <button type="submit" class="btn {{ $buttonClass }}" style="{{ $buttonStyle }}">
                                    {{ $plan->displayButtonText(true) }}
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ $customUrl ?? route('register') }}" class="btn {{ $buttonClass }}" style="{{ $buttonStyle }}">
                            {{ $plan->displayButtonText(false) }}
                        </a>
                    @endauth

                    @if ($plan->footer_note)
                        <div class="watermark-note">{{ $plan->footer_note }}</div>
                    @endif

                    @if ($plan->paymentMethodLabels() !== [])
                        <div class="payment-note">
                            &#128179;
                            {{ str_replace('��', '•', implode(' • ', $plan->paymentMethodLabels())) }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        @if ($showAllPlansButton)
            <div style="margin-top:28px;text-align:center;">
                <a href="{{ route('pricing') }}" class="btn btn-outline" style="justify-content:center;padding:14px 28px;">
                    Show All Plans
                </a>
            </div>
        @endif
    </div>
</section>
