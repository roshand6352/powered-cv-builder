@extends('web.layouts.marketing')

@section('content')
    @include('web.landing.partials.page-hero', [
        'title' => 'CV Templates',
        'description' => 'Choose a professional template and start building your CV in minutes.',
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
                            @php
                                $previewFile = 'images/template-previews/'.$template->slug.'.jpg';
                                $previewUrl = is_file(public_path($previewFile))
                                    ? asset($previewFile).'?v='.filemtime(public_path($previewFile))
                                    : null;
                            @endphp
                            <div class="template-preview">
                                @if ($previewUrl)
                                    <button type="button" class="template-preview-trigger" data-template-preview="{{ $previewUrl }}" data-template-name="{{ $template->name }}" data-template-target="template-use-{{ $template->id }}" aria-label="Enlarge preview of the {{ $template->name }} template">
                                        <img src="{{ $previewUrl }}" alt="Preview of the {{ $template->name }} CV template" width="794" height="1123" loading="lazy" decoding="async">
                                        <span class="template-preview-zoom" aria-hidden="true">View larger</span>
                                    </button>
                                @else
                                <div class="template-placeholder">
                                    <div class="template-placeholder-inner">
                                        <div style="height:10px;width:72px;background:var(--orange-mid);border-radius:4px;margin-bottom:10px"></div>
                                        <div style="height:7px;width:100%;background:#f0f0f0;border-radius:4px;margin-bottom:6px"></div>
                                        <div style="height:7px;width:85%;background:#f0f0f0;border-radius:4px;margin-bottom:6px"></div>
                                        <div style="height:7px;width:70%;background:#f0f0f0;border-radius:4px"></div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <div class="template-body" id="template-use-{{ $template->id }}">
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

    @if ($templates->isNotEmpty())
        <dialog class="template-lightbox" id="template-lightbox" aria-labelledby="template-lightbox-title">
            <div class="template-lightbox-head">
                <h2 id="template-lightbox-title"></h2>
                <button type="button" class="template-lightbox-close" data-template-lightbox-close aria-label="Close preview">&times;</button>
            </div>
            <div class="template-lightbox-body">
                <button type="button" class="template-lightbox-nav" data-template-lightbox-step="-1" aria-label="Previous template">&lsaquo;</button>
                <img src="" alt="" width="794" height="1123">
                <button type="button" class="template-lightbox-nav" data-template-lightbox-step="1" aria-label="Next template">&rsaquo;</button>
            </div>
            <div class="template-lightbox-actions"></div>
        </dialog>
    @endif

    @include('web.landing.partials.cta')
@endsection

@push('head')
    <style>
        .template-preview-trigger{display:block;position:relative;width:100%;height:100%;padding:0;border:0;background:none;cursor:zoom-in}
        .template-preview-trigger:focus-visible{outline:3px solid var(--orange);outline-offset:-3px}
        .template-preview-zoom{position:absolute;right:12px;bottom:12px;padding:6px 12px;border-radius:999px;background:rgba(23,45,64,0.85);color:#fff;font-size:13px;font-weight:600;opacity:0;transition:opacity .2s}
        .template-preview-trigger:hover .template-preview-zoom,.template-preview-trigger:focus-visible .template-preview-zoom{opacity:1}
        @media(hover:none){.template-preview-zoom{opacity:1}}
        .template-lightbox{margin:auto;width:min(640px,calc(100vw - 24px));max-height:calc(100dvh - 24px);padding:0;border:0;border-radius:var(--radius-lg);background:#fff;box-shadow:0 24px 64px rgba(23,45,64,0.35);overflow:hidden}
        .template-lightbox[open]{display:flex;flex-direction:column}
        .template-lightbox::backdrop{background:rgba(23,45,64,0.6)}
        .template-lightbox-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 18px;border-bottom:1px solid var(--border)}
        .template-lightbox-head h2{font-size:18px;font-weight:700;color:var(--navy);margin:0}
        .template-lightbox-close{border:0;background:none;font-size:28px;line-height:1;color:var(--navy);cursor:pointer;padding:0 4px}
        .template-lightbox-body{display:flex;align-items:center;gap:8px;padding:12px;background:var(--cream);min-height:0;flex:1}
        .template-lightbox-body img{flex:1;min-width:0;width:100%;height:auto;max-height:calc(100dvh - 190px);object-fit:contain;background:#fff;box-shadow:0 4px 16px rgba(23,45,64,0.12)}
        .template-lightbox-nav{flex:0 0 auto;width:36px;height:36px;border-radius:50%;border:1px solid var(--border);background:#fff;color:var(--navy);font-size:24px;line-height:1;cursor:pointer}
        .template-lightbox-actions{padding:14px 18px;border-top:1px solid var(--border)}
        .template-lightbox-close:focus-visible,.template-lightbox-nav:focus-visible{outline:3px solid var(--orange);outline-offset:2px}
    </style>
@endpush

@push('scripts')
    <script>
        (() => {
            const dialog = document.getElementById('template-lightbox');
            const triggers = Array.from(document.querySelectorAll('[data-template-preview]'));
            if (!dialog || !triggers.length || typeof dialog.showModal !== 'function') return;

            const title = dialog.querySelector('#template-lightbox-title');
            const img = dialog.querySelector('.template-lightbox-body img');
            const actions = dialog.querySelector('.template-lightbox-actions');
            let index = 0;
            let opener = null;

            const show = (i) => {
                index = (i + triggers.length) % triggers.length;
                const t = triggers[index];
                title.textContent = t.dataset.templateName;
                img.src = t.dataset.templatePreview;
                img.alt = 'Preview of the ' + t.dataset.templateName + ' CV template';
                actions.innerHTML = '';
                const source = document.getElementById(t.dataset.templateTarget);
                const action = source && source.querySelector('form, a.btn');
                if (action) actions.appendChild(action.cloneNode(true));
            };

            triggers.forEach((t, i) => t.addEventListener('click', () => {
                opener = t;
                show(i);
                dialog.showModal();
            }));
            dialog.querySelectorAll('[data-template-lightbox-step]').forEach((b) => b.addEventListener('click', () => show(index + Number(b.dataset.templateLightboxStep))));
            dialog.querySelector('[data-template-lightbox-close]').addEventListener('click', () => dialog.close());
            dialog.addEventListener('click', (e) => { if (e.target === dialog) dialog.close(); });
            dialog.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') show(index - 1);
                if (e.key === 'ArrowRight') show(index + 1);
            });
            dialog.addEventListener('close', () => { if (opener) opener.focus(); });
        })();
    </script>
@endpush
