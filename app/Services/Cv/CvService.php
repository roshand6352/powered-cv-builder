<?php

namespace App\Services\Cv;

use App\Contracts\Repositories\CvRepositoryInterface;
use App\DTOs\Cv\CvDocumentData;
use App\Models\Cv;
use App\Models\CvTemplate;
use App\Models\User;
use App\Support\Cv\CvDocumentMigrator;
use App\Support\Cv\CvTemplateLayout;
use App\Services\BaseService;
use Illuminate\Support\Collection;

class CvService extends BaseService
{
    public function __construct(
        protected CvRepositoryInterface $cvRepository,
    ) {}

    public function listForUser(User $user): Collection
    {
        return $this->cvRepository->getByUser($user);
    }

    public function createDraft(User $user, ?string $title = null): Cv
    {
        return $this->createFromDocument(
            $user,
            $this->buildEmptyDocument($user),
            $title,
            null,
        );
    }

    public function assignTemplate(Cv $cv, CvTemplate $template): Cv
    {
        $document = CvDocumentData::fromArray($cv->cv_data ?? []);
        $layout = CvTemplateLayout::normalize($template->layout_json);

        $document->template->id = $template->id;
        $document->template->key = $template->slug;
        $document->template->slug = $template->slug;
        $document->template->settings = $layout['settings'] ?? [];
        $document->meta->sectionOrder = CvDocumentMigrator::migrateSectionKeys(
            $layout['section_order'] ?? $document->meta->sectionOrder,
        );
        $document->meta->enabledSections = CvDocumentMigrator::migrateSectionKeys(
            $layout['enabled_sections'] ?? $document->meta->enabledSections,
        );

        return $this->cvRepository->update($cv, [
            'cv_data' => $document->toArray(),
            'template_id' => $template->id,
        ]);
    }

    public function createFromTemplate(User $user, CvTemplate $template, ?string $title = null): Cv
    {
        $document = $this->buildEmptyDocument($user);
        $layout = CvTemplateLayout::normalize($template->layout_json);

        $document->template->id = $template->id;
        $document->template->key = $template->slug;
        $document->template->slug = $template->slug;
        $document->template->settings = $layout['settings'] ?? [];
        $document->meta->sectionOrder = CvDocumentMigrator::migrateSectionKeys(
            $layout['section_order'] ?? $document->meta->sectionOrder,
        );
        $document->meta->enabledSections = CvDocumentMigrator::migrateSectionKeys(
            $layout['enabled_sections'] ?? $document->meta->enabledSections,
        );

        return $this->createFromDocument($user, $document, $title, $template->id);
    }

    protected function buildEmptyDocument(User $user): CvDocumentData
    {
        $document = CvDocumentData::empty();

        if ($user->name && empty($document->personalDetails->fullName)) {
            $document->personalDetails->fullName = $user->name;
        }

        if ($user->email && empty($document->personalDetails->email)) {
            $document->personalDetails->email = $user->email;
        }

        return $document;
    }

    protected function createFromDocument(
        User $user,
        CvDocumentData $document,
        ?string $title = null,
        ?int $templateId = null,
    ): Cv {
        return $this->cvRepository->createForUser($user, [
            'title' => $title ?: config('cv.default_title'),
            'cv_data' => $document->toArray(),
            'status' => 1,
            'template_id' => $templateId ?? $document->template->id,
        ]);
    }
}
