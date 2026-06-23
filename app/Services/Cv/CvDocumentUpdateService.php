<?php

namespace App\Services\Cv;

use App\Contracts\Repositories\CvRepositoryInterface;
use App\Enums\CvStatus;
use App\Models\Cv;
use App\Services\BaseService;

class CvDocumentUpdateService extends BaseService
{
    public function __construct(
        protected CvRepositoryInterface $cvRepository,
        protected CvDocumentNormalizer $normalizer,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public function update(Cv $cv, array $payload): Cv
    {
        $document = $this->normalizer->normalize($payload['json_data'] ?? []);
        $title = trim($payload['title'] ?? $cv->title) ?: $cv->title;

        $attributes = [
            'title' => $title,
            'cv_data' => $document->toArray(),
            'status' => CvStatus::Draft->value,
            'template_id' => $document->template->id ?? $cv->template_id,
        ];

        return $this->cvRepository->update($cv, $attributes);
    }
}
