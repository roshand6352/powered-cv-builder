<style>
    html, body {
        margin: 0;
        padding: 0;
        font-family: Figtree, ui-sans-serif, system-ui, sans-serif;
        font-size: 9px;
        color: #94a3b8;
    }

    .pdf-footer {
        width: 100%;
        padding: 0 12mm;
        box-sizing: border-box;
    }

    .pdf-footer__inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-top: 1px solid #e2e8f0;
        padding-top: 4px;
    }

    .pdf-footer__brand {
        font-weight: 600;
        color: #64748b;
    }

    .pdf-footer__pages {
        font-variant-numeric: tabular-nums;
    }
</style>

<div class="pdf-footer">
    <div class="pdf-footer__inner">
        <span class="pdf-footer__brand">{{ config('app.name') }}</span>
        <span class="pdf-footer__pages">Page <span class="pageNumber"></span> of <span class="totalPages"></span></span>
    </div>
</div>
