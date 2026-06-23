<?php

namespace App\Services\Cv;

use App\Contracts\Repositories\CvRepositoryInterface;
use App\DTOs\Cv\CvDocumentData;
use App\Enums\CvStatus;
use App\Models\Cv;
use App\Services\BaseService;

class CvDocumentSaveService extends BaseService
{
    public function __construct(
        protected CvRepositoryInterface $cvRepository,
        protected CvDocumentNormalizer $normalizer,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public function save(Cv $cv, array $payload, bool $publish = false): Cv
    {
        $document = $this->normalizer->normalize($payload['json_data'] ?? []);
        $title = trim($payload['title'] ?? $cv->title) ?: config('cv.default_title');

        return $this->persist($cv, $title, $document, $publish);
    }

    public function saveDocument(Cv $cv, CvDocumentData $document, bool $publish = false, ?string $title = null): Cv
    {
        $title = trim($title ?? $cv->title) ?: config('cv.default_title');

        return $this->persist($cv, $title, $document, $publish);
    }

    protected function persist(Cv $cv, string $title, CvDocumentData $document, bool $publish): Cv
    {
        $attributes = [
            'title' => $title,
            'cv_data' => $document->toArray(),
            'status' => $publish ? CvStatus::Published->value : CvStatus::Draft->value,
            'template_id' => $document->template->id ?? $cv->template_id,
        ];

        return $this->cvRepository->update($cv, $attributes);
    }
}
