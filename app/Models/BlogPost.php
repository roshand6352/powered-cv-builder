<?php

namespace App\Models;

use App\Enums\BlogPostStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'author',
        'meta_title',
        'meta_description',
        'published_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'status' => BlogPostStatus::class,
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', BlogPostStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function isPublished(): bool
    {
        return $this->status === BlogPostStatus::Published
            && $this->published_at !== null
            && $this->published_at->lte(now());
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getReadTimeMinutesAttribute(): int
    {
        $words = str_word_count(strip_tags((string) $this->content));

        return max(1, (int) ceil($words / 200));
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        if (blank($this->featured_image)) {
            return null;
        }

        if (Str::startsWith($this->featured_image, ['http://', 'https://', '/'])) {
            return $this->featured_image;
        }

        return Storage::disk('public')->url($this->featured_image);
    }

    public function seoTitle(): string
    {
        return filled($this->meta_title) ? (string) $this->meta_title : (string) $this->title;
    }

    public function seoDescription(): string
    {
        if (filled($this->meta_description)) {
            return (string) $this->meta_description;
        }

        if (filled($this->excerpt)) {
            return (string) $this->excerpt;
        }

        return Str::limit(strip_tags((string) $this->content), 160);
    }

    /**
     * Latest published posts excluding this one (max 3).
     * Ready for category filtering when categories are added later.
     *
     * @return \Illuminate\Support\Collection<int, self>
     */
    public function relatedPosts(int $limit = 3)
    {
        return static::query()
            ->published()
            ->whereKeyNot($this->getKey())
            ->latest('published_at')
            ->limit($limit)
            ->get([
                'id',
                'title',
                'slug',
                'excerpt',
                'content',
                'featured_image',
                'author',
                'published_at',
            ]);
    }

    /**
     * @return array{twitter: string, linkedin: string, whatsapp: string}
     */
    public function shareUrls(): array
    {
        $url = route('blog.show', $this);
        $encodedUrl = rawurlencode($url);
        $encodedTitle = rawurlencode($this->title);

        return [
            'twitter' => "https://twitter.com/intent/tweet?url={$encodedUrl}&text={$encodedTitle}",
            'linkedin' => "https://www.linkedin.com/sharing/share-offsite/?url={$encodedUrl}",
            'whatsapp' => "https://api.whatsapp.com/send?text={$encodedTitle}%20{$encodedUrl}",
        ];
    }

    /**
     * Schema.org Article JSON-LD payload.
     *
     * @return array<string, mixed>
     */
    public function jsonLdArticle(): array
    {
        $url = route('blog.show', $this);
        $image = $this->featured_image_url ?? asset('images/og-default.jpg');
        $logo = asset(config('marketing.brand.logo', 'images/LOGO-02.png'));
        $siteName = config('app.name');

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $this->seoTitle(),
            'description' => $this->seoDescription(),
            'image' => [$image],
            'author' => [
                '@type' => 'Person',
                'name' => $this->author,
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => $siteName,
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $logo,
                ],
            ],
            'datePublished' => optional($this->published_at)->toAtomString(),
            'dateModified' => optional($this->updated_at)->toAtomString(),
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $url,
            ],
            'url' => $url,
        ];
    }
}
