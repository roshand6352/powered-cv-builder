<?php

namespace App\Console\Commands;

use App\Models\CvTemplate;
use App\Services\Cv\CvDocumentNormalizer;
use App\Services\Cv\Templates\CvTemplateRenderer;
use App\Services\Pdf\BrowsershotEnvironment;
use App\Support\Cv\CvSampleDocument;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Spatie\Browsershot\Browsershot;

/**
 * Generates static preview images for the public /templates page by rendering
 * each active CV template with the same renderer and sample data used by the
 * in-app template preview (CvTemplatePreviewController).
 */
class GenerateCvTemplatePreviewsCommand extends Command
{
    protected $signature = 'cv-templates:generate-previews
        {--base-url= : Root URL used for template assets (defaults to APP_URL)}
        {--slug=* : Only generate these template slugs}';

    protected $description = 'Render preview images for CV templates into public/images/template-previews';

    public function handle(
        CvTemplateRenderer $renderer,
        CvDocumentNormalizer $normalizer,
        BrowsershotEnvironment $environment,
    ): int {
        if ($base = $this->option('base-url')) {
            URL::forceRootUrl(rtrim($base, '/'));
        }

        $directory = public_path('images/template-previews');
        File::ensureDirectoryExists($directory);

        $query = CvTemplate::query()->cvOnly()->active()->orderBy('name');

        if ($slugs = $this->option('slug')) {
            $query->whereIn('slug', $slugs);
        }

        $document = $normalizer->normalize(CvSampleDocument::data());
        $failed = 0;

        foreach ($query->get() as $template) {
            try {
                $html = $renderer->render($document, $template->slug, [
                    'mode' => 'preview',
                    'user' => null,
                    'template_id' => $template->id,
                ]);

                $path = $directory.DIRECTORY_SEPARATOR.$template->slug.'.jpg';

                $shot = Browsershot::html($html)
                    ->windowSize(794, 1123)
                    ->setScreenshotType('jpeg', 82)
                    ->waitUntilNetworkIdle()
                    ->timeout(120);

                $environment->applyToBrowsershot($shot);
                $shot->save($path);

                $this->info("Generated {$template->slug}.jpg");
            } catch (\Throwable $e) {
                $failed++;
                $this->error("Failed {$template->slug}: {$e->getMessage()}");
            }
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}