<?php

namespace App\Services\Pdf;

use App\Services\BaseService;
use Illuminate\Support\Facades\View;

class PdfLayoutService extends BaseService
{
    /**
     * @param  array<string, mixed>  $meta
     */
    public function headerHtml(array $meta = []): ?string
    {
        if (! config('pdf.header.enabled', true)) {
            return null;
        }

        return View::make(config('pdf.header.template'), [
            'meta' => $meta,
        ])->render();
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    public function footerHtml(array $meta = []): ?string
    {
        if (! config('pdf.footer.enabled', true)) {
            return null;
        }

        return View::make(config('pdf.footer.template'), [
            'meta' => $meta,
        ])->render();
    }
}
