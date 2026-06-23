<?php

namespace App\Models;

use App\Enums\PlanSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Plan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'type_label',
        'description',
        'price',
        'currency',
        'currency_symbol',
        'price_suffix',
        'ai_credits_limit',
        'billing_interval',
        'duration_hours',
        'duration_days',
        'is_lifetime',
        'has_watermark',
        'is_featured',
        'show_badge',
        'badge_text',
        'accent_color',
        'card_border_color',
        'card_background_color',
        'button_background_color',
        'button_text_color',
        'title_color',
        'price_color',
        'badge_color',
        'feature_text_color',
        'button_style',
        'sort_order',
        'status',
        'show_on_website',
        'features',
        'excluded_features',
        'button_text',
        'button_link',
        'footer_note',
        'show_payment_methods',
        'payment_methods',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'ai_credits_limit' => 'integer',
            'duration_hours' => 'integer',
            'duration_days' => 'integer',
            'is_lifetime' => 'boolean',
            'has_watermark' => 'boolean',
            'is_featured' => 'boolean',
            'show_badge' => 'boolean',
            'show_on_website' => 'boolean',
            'show_payment_methods' => 'boolean',
            'sort_order' => 'integer',
            'features' => 'array',
            'excluded_features' => 'array',
            'payment_methods' => 'array',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function resolveRouteBinding($value, $field = null): ?self
    {
        return static::query()
            ->catalog()
            ->where($field ?? $this->getRouteKeyName(), $value)
            ->first();
    }

    public function isFree(): bool
    {
        return $this->slug === PlanSlug::Free->value || (float) $this->price <= 0;
    }

    public function isPro(): bool
    {
        return $this->slug === PlanSlug::Pro->value;
    }

    public function isLifetime(): bool
    {
        return (bool) $this->is_lifetime;
    }

    public function isPaid(): bool
    {
        return ! $this->isFree();
    }

    public function scopeCatalog(Builder $query): Builder
    {
        return $query->whereIn('slug', PlanSlug::catalogSlugs());
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeWebsite(Builder $query): Builder
    {
        return $query->where('show_on_website', true);
    }

    public function periodLabel(): string
    {
        if ($this->isLifetime()) {
            return 'lifetime';
        }

        if ($this->duration_hours) {
            return $this->duration_hours >= 24
                ? rtrim(rtrim(number_format($this->duration_hours / 24, 1), '0'), '.').' days'
                : $this->duration_hours.' hours';
        }

        if ($this->duration_days) {
            if ($this->duration_days === 7) {
                return 'week';
            }

            if ($this->duration_days === 30) {
                return 'month';
            }

            if ($this->duration_days === 365) {
                return 'year';
            }

            return $this->duration_days.' days';
        }

        return 'forever';
    }

    public function displayTypeLabel(): string
    {
        return filled($this->type_label)
            ? $this->type_label
            : strtoupper($this->name);
    }

    public function formattedPrice(): string
    {
        $symbol = $this->currency_symbol ?: ($this->currency === 'ZAR' ? 'R' : '$');
        $price = (float) $this->price;

        if ($price === floor($price)) {
            return trim($symbol.' '.number_format($price, 0));
        }

        return trim($symbol.' '.number_format($price, 2));
    }

    public function displayPriceSuffix(): ?string
    {
        if (filled($this->price_suffix)) {
            return $this->price_suffix;
        }

        if ($this->isFree()) {
            return '/ forever free';
        }

        if ($this->isLifetime()) {
            return '/ lifetime';
        }

        if (! $this->isFree()) {
            return '/ '.$this->periodLabel();
        }

        return null;
    }

    /**
     * @return Collection<int, array{title: string, status: string, icon_type: string, sort_order: int}>
     */
    public function displayFeatures(): Collection
    {
        $features = collect($this->features ?? []);

        if ($features->isNotEmpty() && is_array($features->first())) {
            return $features
                ->map(fn (array $feature): array => [
                    'title' => (string) ($feature['title'] ?? ''),
                    'status' => (string) ($feature['status'] ?? 'active'),
                    'icon_type' => (string) ($feature['icon_type'] ?? 'check'),
                    'sort_order' => (int) ($feature['sort_order'] ?? 0),
                ])
                ->filter(fn (array $feature): bool => filled($feature['title']))
                ->sortBy('sort_order')
                ->values();
        }

        $legacy = collect();

        foreach ($features as $index => $title) {
            if (! is_string($title) || blank($title)) {
                continue;
            }

            $legacy->push([
                'title' => $title,
                'status' => 'active',
                'icon_type' => 'check',
                'sort_order' => $index + 1,
            ]);
        }

        foreach ($this->excluded_features ?? [] as $index => $title) {
            if (! is_string($title) || blank($title)) {
                continue;
            }

            $legacy->push([
                'title' => $title,
                'status' => 'disabled',
                'icon_type' => 'cross',
                'sort_order' => 100 + $index,
            ]);
        }

        return $legacy->sortBy('sort_order')->values();
    }

    /**
     * @return list<string>
     */
    public function activeFeatureTitles(): array
    {
        return $this->displayFeatures()
            ->where('status', 'active')
            ->pluck('title')
            ->all();
    }

    public function displayBadge(): ?string
    {
        if (! $this->show_badge) {
            return null;
        }

        if (filled($this->badge_text)) {
            return $this->badge_text;
        }

        return $this->is_featured ? 'Most Popular' : null;
    }

    public function displayButtonText(?bool $authenticated = null): string
    {
        if (filled($this->button_text)) {
            return $this->button_text;
        }

        $authenticated ??= auth()->check();

        if ($this->isFree()) {
            return $authenticated ? 'Switch to Free' : 'Get started free';
        }

        return $authenticated ? 'Choose '.$this->name : 'Get Started';
    }

    public function resolvedButtonUrl(?bool $authenticated = null): ?string
    {
        if (blank($this->button_link)) {
            return null;
        }

        $link = trim($this->button_link);

        if (str_starts_with($link, 'http://') || str_starts_with($link, 'https://') || str_starts_with($link, '/')) {
            return $link;
        }

        return match ($link) {
            'register' => route('register'),
            'login' => route('login'),
            'pricing' => route('pricing'),
            'dashboard' => route('dashboard'),
            'cvs' => route('dashboard.cvs'),
            default => url($link),
        };
    }

    public function buttonCssClass(): string
    {
        return match ($this->normalizedButtonStyle()) {
            'primary' => 'btn-primary',
            'outline' => 'btn-outline',
            default => 'btn-ghost',
        };
    }

    public function normalizedButtonStyle(): string
    {
        if (in_array($this->button_style, ['primary', 'secondary', 'outline'], true)) {
            return $this->button_style === 'secondary' ? 'secondary' : $this->button_style;
        }

        if ($this->button_style === 'ghost') {
            return 'secondary';
        }

        return $this->is_featured ? 'primary' : 'secondary';
    }

    /**
     * @return list<string>
     */
    public function paymentMethodLabels(): array
    {
        if (! $this->show_payment_methods) {
            return [];
        }

        return collect($this->payment_methods ?? [])
            ->map(fn ($method) => is_array($method) ? ($method['label'] ?? null) : $method)
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public function cardStyleAttributes(): array
    {
        $styles = [];

        $borderColor = $this->card_border_color ?: $this->accent_color;
        $badgeColor = $this->badge_color ?: $this->accent_color;

        if ($borderColor) {
            $styles[] = 'border-color: '.$borderColor;
        }

        if ($this->card_background_color) {
            $styles[] = 'background: '.$this->card_background_color;
        }

        if ($badgeColor) {
            $styles[] = '--plan-badge-color: '.$badgeColor;
        }

        if ($this->title_color) {
            $styles[] = '--plan-title-color: '.$this->title_color;
        }

        if ($this->price_color) {
            $styles[] = '--plan-price-color: '.$this->price_color;
        }

        if ($this->feature_text_color) {
            $styles[] = '--plan-feature-color: '.$this->feature_text_color;
        }

        if ($styles === []) {
            return [];
        }

        return ['style' => implode('; ', $styles)];
    }

    /**
     * @return array<string, string>
     */
    public function buttonStyleAttributes(): array
    {
        $styles = [
            'width:100%',
            'justify-content:center',
            'padding:14px',
        ];

        if ($this->normalizedButtonStyle() === 'primary') {
            $styles[] = 'font-size:15px';
        }

        if ($this->button_background_color) {
            $styles[] = 'background: '.$this->button_background_color;
            $styles[] = 'border-color: '.$this->button_background_color;
        }

        if ($this->button_text_color) {
            $styles[] = 'color: '.$this->button_text_color;
        }

        return ['style' => implode('; ', $styles)];
    }

    public function featureIconMarkup(string $iconType, string $status): string
    {
        if ($status === 'disabled' || $iconType === 'cross') {
            return '&ndash;';
        }

        return match ($iconType) {
            'star' => '&#10024;',
            default => '&#10003;',
        };
    }

    public function featureIconClass(string $iconType, string $status): string
    {
        if ($status === 'disabled' || $iconType === 'cross') {
            return 'pf-cross';
        }

        return $iconType === 'star' ? 'pf-check orange' : 'pf-check';
    }
}
