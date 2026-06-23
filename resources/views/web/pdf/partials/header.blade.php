<style>
    html, body {
        margin: 0;
        padding: 0;
        font-family: Figtree, ui-sans-serif, system-ui, sans-serif;
        font-size: 9px;
        color: #64748b;
    }

    .pdf-header {
        width: 100%;
        padding: 0 12mm;
        box-sizing: border-box;
    }

    .pdf-header__inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 4px;
    }

    .pdf-header__title {
        font-weight: 600;
        color: #334155;
    }

    .pdf-header__meta {
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-size: 8px;
    }
</style>

<div class="pdf-header">
    <div class="pdf-header__inner">
        <span class="pdf-header__title">{{ $meta['title'] ?? config('app.name') }}</span>
        <span class="pdf-header__meta">{{ ucfirst(str_replace('_', ' ', $meta['type'] ?? 'document')) }}</span>
    </div>
</div>
