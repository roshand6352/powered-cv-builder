@extends('web.layouts.marketing')

@php
    $pageUrl = route('blog.show', $post);
    $share = $shareUrls ?? $post->shareUrls();
@endphp

@push('head')
    <script type="application/ld+json">
        {!! json_encode($post->jsonLdArticle(), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) !!}
    </script>
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Home',
                    'item' => route('home'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'Blog',
                    'item' => route('blog.index'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $post->title,
                    'item' => $pageUrl,
                ],
            ],
        ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@section('content')
    <article class="blog-detail" itemscope itemtype="https://schema.org/Article">
        <div class="container">
            <nav class="blog-breadcrumb" aria-label="Breadcrumb">
                <ol class="blog-breadcrumb-list" itemscope itemtype="https://schema.org/BreadcrumbList">
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a itemprop="item" href="{{ route('home') }}"><span itemprop="name">Home</span></a>
                        <meta itemprop="position" content="1">
                    </li>
                    <li class="blog-breadcrumb-sep" aria-hidden="true">→</li>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a itemprop="item" href="{{ route('blog.index') }}"><span itemprop="name">Blog</span></a>
                        <meta itemprop="position" content="2">
                    </li>
                    <li class="blog-breadcrumb-sep" aria-hidden="true">→</li>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" aria-current="page">
                        <span itemprop="name">{{ $post->title }}</span>
                        <meta itemprop="position" content="3">
                    </li>
                </ol>
            </nav>
        </div>

        <header class="blog-detail-hero">
            <div class="container blog-detail-hero-inner">
                @if ($post->featured_image_url)
                    <figure class="blog-detail-hero-image">
                        <img
                            src="{{ $post->featured_image_url }}"
                            alt="{{ $post->title }}"
                            width="1200"
                            height="630"
                            loading="lazy"
                            decoding="async"
                            itemprop="image"
                        >
                    </figure>
                @endif

                <div class="blog-detail-hero-copy">
                    <h1 class="blog-detail-title" itemprop="headline">{{ $post->title }}</h1>

                    <div class="blog-detail-meta">
                        <span itemprop="author" itemscope itemtype="https://schema.org/Person">
                            By <span itemprop="name">{{ $post->author }}</span>
                        </span>
                        <span aria-hidden="true">·</span>
                        <time
                            datetime="{{ $post->published_at?->toAtomString() }}"
                            itemprop="datePublished"
                        >
                            {{ $post->published_at?->format('d M Y') }}
                        </time>
                        <meta itemprop="dateModified" content="{{ $post->updated_at?->toAtomString() }}">
                        <span aria-hidden="true">·</span>
                        <span>{{ $post->read_time_minutes }} min read</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="page-section page-section--white">
            <div class="container blog-article">
                @if ($post->excerpt)
                    <p class="blog-detail-lead" itemprop="description">{{ $post->excerpt }}</p>
                @endif

                <div class="prose-content blog-article-content" itemprop="articleBody">
                    {!! $post->content !!}
                </div>

                <footer class="blog-article-footer">
                    <div class="blog-share" aria-label="Share this article">
                        <h2 class="blog-share-heading">Share this article</h2>

                        <div class="blog-share-actions">
                            <a
                                href="{{ $share['twitter'] }}"
                                class="blog-share-btn"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="Share on X (Twitter)"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true" fill="currentColor">
                                    <path d="M18.244 2H21.5l-7.5 8.57L22.5 22h-6.59l-5.16-6.74L5.2 22H1.94l8.03-9.17L1.5 2h6.75l4.66 6.17L18.244 2zm-1.16 18h1.81L7.01 3.94H5.07L17.084 20z"/>
                                </svg>
                                <span>Share on X</span>
                            </a>
                            <a
                                href="{{ $share['linkedin'] }}"
                                class="blog-share-btn"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="Share on LinkedIn"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true" fill="currentColor">
                                    <path d="M4.98 3.5C4.98 4.88 3.86 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1s2.48 1.12 2.48 2.5zM.5 8.5h4V23h-4V8.5zM8.5 8.5h3.84v1.98h.05c.53-1 1.84-2.06 3.79-2.06 4.05 0 4.8 2.67 4.8 6.14V23h-4v-6.66c0-1.59-.03-3.63-2.21-3.63-2.21 0-2.55 1.73-2.55 3.51V23h-4V8.5z"/>
                                </svg>
                                <span>Share on LinkedIn</span>
                            </a>
                            <a
                                href="{{ $share['whatsapp'] }}"
                                class="blog-share-btn"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="Share on WhatsApp"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true" fill="currentColor">
                                    <path d="M20.52 3.48A11.86 11.86 0 0012.05 0C5.5 0 .16 5.34.16 11.9c0 2.1.55 4.15 1.6 5.96L0 24l6.3-1.65a11.9 11.9 0 005.74 1.46h.01c6.55 0 11.89-5.34 11.89-11.9 0-3.18-1.24-6.16-3.42-8.43zM12.05 21.2h-.01a9.3 9.3 0 01-4.74-1.3l-.34-.2-3.74.98 1-3.64-.22-.37a9.28 9.28 0 01-1.42-4.96c0-5.13 4.18-9.3 9.32-9.3a9.25 9.25 0 016.59 2.73 9.25 9.25 0 012.72 6.58c0 5.13-4.18 9.28-9.16 9.28zm5.1-6.96c-.28-.14-1.65-.81-1.9-.9-.26-.1-.44-.14-.63.14-.18.28-.72.9-.88 1.08-.16.18-.33.2-.6.07-.28-.14-1.17-.43-2.23-1.37-.82-.73-1.38-1.63-1.54-1.9-.16-.28-.02-.43.12-.57.13-.13.28-.33.42-.5.14-.16.18-.28.28-.47.09-.18.05-.35-.02-.49-.07-.14-.63-1.51-.86-2.07-.23-.55-.46-.47-.63-.48h-.54c-.18 0-.48.07-.73.35-.26.28-.96.94-.96 2.29s.98 2.65 1.12 2.83c.14.18 1.93 2.95 4.67 4.14.65.28 1.16.45 1.56.57.65.2 1.25.18 1.72.11.52-.08 1.65-.67 1.88-1.32.23-.65.23-1.2.16-1.32-.07-.11-.25-.18-.53-.32z"/>
                                </svg>
                                <span>Share on WhatsApp</span>
                            </a>
                        </div>
                    </div>

                    <div class="blog-article-actions">
                        <a href="{{ route('blog.index') }}" class="btn btn-outline blog-back-btn">
                            ← Back to Blog
                        </a>
                    </div>
                </footer>
            </div>
        </div>
    </article>

    @if ($relatedPosts->isNotEmpty())
        <section class="page-section page-section--cream" aria-labelledby="related-articles-heading">
            <div class="container">
                <div class="section-intro">
                    <span class="section-tag">Keep reading</span>
                    <h2 id="related-articles-heading">Related Articles</h2>
                </div>

                <div class="blog-grid">
                    @foreach ($relatedPosts as $related)
                        <article class="blog-card">
                            <a
                                href="{{ route('blog.show', $related) }}"
                                class="blog-card-link"
                                aria-label="Read article: {{ $related->title }}"
                            >
                                <div class="blog-card-media">
                                    @if ($related->featured_image_url)
                                        <img
                                            src="{{ $related->featured_image_url }}"
                                            alt="{{ $related->title }}"
                                            width="800"
                                            height="450"
                                            loading="lazy"
                                            decoding="async"
                                        >
                                    @else
                                        <div class="blog-card-placeholder" aria-hidden="true">
                                            <span>{{ strtoupper(\Illuminate\Support\Str::substr($related->title, 0, 1)) }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="blog-card-body">
                                    <div class="blog-card-meta">
                                        <time datetime="{{ $related->published_at?->toAtomString() }}">
                                            {{ $related->published_at?->format('d M Y') }}
                                        </time>
                                        <span aria-hidden="true">·</span>
                                        <span>{{ $related->read_time_minutes }} min read</span>
                                    </div>
                                    <h3 class="blog-card-title">{{ $related->title }}</h3>
                                    <span class="blog-card-cta">
                                        Read More
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M5 12h14" />
                                            <path d="M13 6l6 6-6 6" />
                                        </svg>
                                    </span>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @include('web.landing.partials.cta')
@endsection
