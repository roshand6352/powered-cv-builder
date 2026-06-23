<?php

namespace App\Services\Cv;

use App\DTOs\Cv\CvBuilderData;
use App\Models\Cv;
use App\Services\BaseService;
use App\Services\Cv\Templates\TemplateRegistry;

class CvBuilderService extends BaseService
{
    public function __construct(
        protected CvDocumentSaveService $saveService,
        protected CvDocumentUpdateService $updateService,
        protected CvPreviewTransformer $previewTransformer,
        protected TemplateRegistry $templateRegistry,
    ) {}

    public function getBuilderData(Cv $cv): CvBuilderData
    {
        $cv->loadMissing('cvTemplate');

        return CvBuilderData::fromModel(
            $cv,
            $this->previewTransformer,
            $this->templateRegistry->allForBuilder()->all(),
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function save(Cv $cv, array $payload, bool $publish = false): Cv
    {
        return $this->saveService->save($cv, $payload, $publish);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function autosave(Cv $cv, array $payload): Cv
    {
        return $this->updateService->update($cv, $payload);
    }
}
