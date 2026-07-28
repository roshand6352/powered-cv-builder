@extends('web.layouts.marketing')

@section('content')
    @include('web.landing.partials.page-hero', [
        'title' => 'Blog',
        'description' => 'Latest insights, guides and updates from ApplyDocs ZA.',
    ])

    <section class="page-section page-section--cream" aria-labelledby="blog-listing-heading">
        <div class="container">
            <h2 id="blog-listing-heading" class="sr-only">Latest blog articles</h2>

            @if ($posts->isEmpty())
                <div class="empty-state blog-empty" role="status">
                    <div class="blog-empty-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 016.5 17H20" />
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z" />
                            <path d="M8 7h8M8 11h8M8 15h5" />
                        </svg>
                    </div>
                    <h2>No blog posts available</h2>
                    <p>Please check back soon.</p>
                </div>
            @else
                <div class="blog-grid">
                    @foreach ($posts as $post)
                        <article class="blog-card">
                            <a
                                href="{{ route('blog.show', $post) }}"
                                class="blog-card-link"
                                aria-label="Read article: {{ $post->title }}"
                            >
                                <div class="blog-card-media">
                                    @if ($post->featured_image_url)
                                        <img
                                            src="{{ $post->featured_image_url }}"
                                            alt="{{ $post->title }}"
                                            width="800"
                                            height="450"
                                            loading="lazy"
                                            decoding="async"
                                        >
                                    @else
                                        <div class="blog-card-placeholder" aria-hidden="true">
                                            <span>{{ strtoupper(Str::substr($post->title, 0, 1)) }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="blog-card-body">
                                    <div class="blog-card-meta">
                                        <time datetime="{{ $post->published_at?->toAtomString() }}">
                                            {{ $post->published_at?->format('d M Y') }}
                                        </time>
                                        <span aria-hidden="true">·</span>
                                        <span>{{ $post->read_time_minutes }} min read</span>
                                    </div>

                                    <h3 class="blog-card-title">{{ $post->title }}</h3>

                                    @if ($post->excerpt)
                                        <p class="blog-card-excerpt">{{ $post->excerpt }}</p>
                                    @endif

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

                @if ($posts->hasPages())
                    <nav class="blog-pagination" aria-label="Blog pagination">
                        {{ $posts->onEachSide(1)->links('blog.pagination') }}
                    </nav>
                @endif
            @endif
        </div>
    </section>

    @include('web.landing.partials.cta')
@endsection
