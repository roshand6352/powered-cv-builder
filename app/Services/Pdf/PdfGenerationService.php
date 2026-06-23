<?php



namespace App\Services\Pdf;



use App\DTOs\Pdf\PdfGenerationOptions;

use App\Services\BaseService;



/**

 * Backward-compatible facade — delegates to PdfService.

 *

 * Controllers and export services inject this class; all PDF logic lives in PdfService.

 */

class PdfGenerationService extends BaseService

{

    public function __construct(

        protected PdfService $pdfService,

    ) {}



    public function generate(string $html, ?PdfGenerationOptions $options = null, ?string $host = null): string

    {

        return $this->pdfService->generate($html, $options);

    }



    public function activeEngine(?string $host = null): string

    {

        return $this->pdfService->activeEngine();

    }



    public function isBrowsershotConfigured(): bool

    {

        return $this->pdfService->isBrowsershotConfigured();

    }

}

