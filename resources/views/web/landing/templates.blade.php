@extends('web.layouts.marketing')

@section('content')
    @include('web.landing.partials.page-hero', [
        'title' => 'CV Templates',
        'description' => 'Choose a professional template and start building your resume in minutes.',
    ])

    <section class="page-section page-section--cream">
        <div class="container">
            @if ($templates->isEmpty())
                <div class="empty-state">
                    <h2>No templates available</h2>
                    <p>Check back soon for new CV designs.</p>
                </div>
            @else
                <div class="template-grid">
                    @foreach ($templates as $template)
                        <article class="template-card">
                            <div class="template-preview">
                                <div class="template-placeholder">
                                    <div class="template-placeholder-inner">
                                        <div style="height:10px;width:72px;background:var(--orange-mid);border-radius:4px;margin-bottom:10px"></div>
                                        <div style="height:7px;width:100%;background:#f0f0f0;border-radius:4px;margin-bottom:6px"></div>
                                        <div style="height:7px;width:85%;background:#f0f0f0;border-radius:4px;margin-bottom:6px"></div>
                                        <div style="height:7px;width:70%;background:#f0f0f0;border-radius:4px"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="template-body">
                                <h3>{{ $template->name }}</h3>
                                <p>{{ $template->description ?: 'Professional CV template ready to customize.' }}</p>

                                @auth
                                    <form method="POST" action="{{ route('templates.use', $template) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-block">Use Template</button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-primary btn-block">Use Template</a>
                                @endauth
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @include('web.landing.partials.cta')
@endsection
