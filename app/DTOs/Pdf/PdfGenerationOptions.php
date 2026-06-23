<?php

namespace App\DTOs\Pdf;

class PdfGenerationOptions
{
    /**
     * @param  array<string, mixed>  $meta
     */
    /**
     * @param  array<string, mixed>  $meta
     * @param  array{top?: int, right?: int, bottom?: int, left?: int}|null  $margins
     */
    public function __construct(
        public bool $watermark = false,
        public ?string $headerHtml = null,
        public ?string $footerHtml = null,
        public ?string $title = null,
        public array $meta = [],
        public ?array $margins = null,
        public bool $emulatePrint = false,
        public bool $preserveExternalResources = false,
        public bool $fromLivePreview = false,
        public bool $preferBrowsershot = false,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'watermark' => $this->watermark,
            'header_html' => $this->headerHtml,
            'footer_html' => $this->footerHtml,
            'title' => $this->title,
            'meta' => $this->meta,
        ];
    }
}
