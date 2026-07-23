<?php

namespace App\Services\CoverLetter;

use App\Models\CoverLetter;
use App\Models\Cv;
use App\Models\CvTemplate;
use App\Models\User;
use App\Services\Ai\AiUsageService;
use App\Services\BaseService;
use App\Services\Subscription\SubscriptionService;
use App\Support\Html\CoverLetterRichTextCleaner;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CoverLetterService extends BaseService
{
    public function __construct(
        protected CoverLetterCvExtractor $cvExtractor,
        protected CoverLetterRenderer $renderer,
        protected CoverLetterTemplateRegistry $templateRegistry,
        protected AiUsageService $aiUsageService,
        protected SubscriptionService $subscriptionService,
    ) {}

    /**
     * @return Collection<int, CoverLetter>
     */
    public function listForUser(User $user): Collection
    {
        return $user->coverLetters()
            ->with(['cv:id,title', 'template:id,name,slug'])
            ->latest('updated_at')
            ->get();
    }

    /**
     * Create a draft cover letter linked to a specific CV + cover letter template.
     */
    public function createForCv(User $user, Cv $cv, CvTemplate $template, array $attributes = []): CoverLetter
    {
        $sender = $this->cvExtractor->senderBlock($cv);
        $companyName = $attributes['company_name'] ?? null;
        $title = $attributes['title'] ?? $this->cvExtractor->suggestedTitle($cv, $companyName);

        return $user->coverLetters()->create([
            'cv_id' => $cv->id,
            'template_id' => $template->id,
            'title' => $title,
            'slug' => $this->uniqueSlug($user, $title),
            'company_name' => $companyName,
            'job_title' => $attributes['job_title'] ?? null,
            'hiring_manager_name' => $attributes['hiring_manager_name'] ?? null,
            'company_address' => $attributes['company_address'] ?? null,
            'subject' => $attributes['subject'] ?? null,
            'greeting' => $attributes['greeting'] ?? 'Dear Hiring Manager,',
            'introduction' => $attributes['introduction'] ?? null,
            'body' => $attributes['body'] ?? null,
            'closing_paragraph' => $attributes['closing_paragraph'] ?? 'Sincerely,',
            'signature_name' => $attributes['signature_name'] ?? $sender['full_name'],
            'status' => 'draft',
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(CoverLetter $coverLetter, array $data): CoverLetter
    {
        $data = CoverLetterRichTextCleaner::cleanFields($data);

        $coverLetter->fill([
            'title' => $data['title'] ?? $coverLetter->title,
            'company_name' => $data['company_name'] ?? null,
            'job_title' => $data['job_title'] ?? null,
            'hiring_manager_name' => $data['hiring_manager_name'] ?? null,
            'company_address' => $data['company_address'] ?? null,
            'subject' => $data['subject'] ?? null,
            'greeting' => $data['greeting'] ?? null,
            'introduction' => $data['introduction'] ?? null,
            'body' => $data['body'] ?? null,
            'closing_paragraph' => $data['closing_paragraph'] ?? null,
            'signature_name' => $data['signature_name'] ?? null,
        ]);

        if (! empty($data['status'])) {
            $coverLetter->status = $data['status'];
        }

        if (! empty($data['template'])) {
            $template = $this->templateRegistry->findActiveBySlug((string) $data['template']);

            if ($template) {
                $coverLetter->template_id = $template->id;
            }
        }

        $coverLetter->save();

        return $coverLetter->fresh();
    }

    public function duplicate(CoverLetter $coverLetter): CoverLetter
    {
        $copy = $coverLetter->replicate([
            'slug',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $copy->title = Str::limit($coverLetter->title.' (Copy)', 250, '');
        $coverLetter->loadMissing('user');
        $copy->slug = $this->uniqueSlug($coverLetter->user, $copy->title);
        $copy->status = 'draft';
        $copy->save();

        return $copy;
    }

    /**
     * Payload consumed by the Alpine cover letter builder component.
     *
     * @return array<string, mixed>
     */
    public function builderPayload(CoverLetter $coverLetter): array
    {
        $coverLetter->loadMissing(['cv', 'template', 'user.plan']);
        $cv = $coverLetter->cv;
        $activePlan = $this->subscriptionService->resolveActivePlan($coverLetter->user);

        return [
            'coverLetter' => [
                'id' => $coverLetter->id,
                'slug' => $coverLetter->slug,
                'title' => $coverLetter->title,
                'fields' => $coverLetter->contentFields(),
                'status' => $coverLetter->status,
            ],
            'cv' => $cv ? $this->cvExtractor->extract($cv) : null,
            'template' => $coverLetter->template ? [
                'id' => $coverLetter->template->id,
                'name' => $coverLetter->template->name,
                'slug' => $coverLetter->template->slug,
            ] : null,
            'templates' => $this->templateRegistry->all()->map(fn (array $tpl) => [
                'key' => $tpl['key'] ?? $tpl['slug'] ?? '',
                'name' => $tpl['name'] ?? '',
                'thumbnail' => $tpl['thumbnail'] ?? null,
            ])->values()->all(),
            'aiUsage' => $this->aiUsageService->snapshot($coverLetter->user),
            'hasAiAccess' => ! $activePlan->isFree(),
            'urls' => [
                'save' => route('cover-letters.update', $coverLetter),
                'autosave' => route('cover-letters.autosave', $coverLetter),
                'render' => route('cover-letters.render', $coverLetter),
                'ai' => route('cover-letters.ai', $coverLetter),
                'aiLog' => route('ai.logs.show', ['aiLog' => '__ID__']),
                'aiUsage' => route('ai.usage'),
                'pdfDownload' => route('pdf-exports.cover-letter.download', $coverLetter),
                'list' => route('dashboard.cover-letters'),
                'changeTemplate' => $cv ? route('cvs.cover-letters.create', $cv) : null,
                'subscription' => route('dashboard.subscription'),
            ],
        ];
    }

    protected function uniqueSlug(User $user, string $title): string
    {
        $base = Str::slug($title) ?: 'cover-letter';
        $slug = $base;
        $suffix = 1;

        while ($user->coverLetters()->withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.(++$suffix);
        }

        return $slug;
    }
}
