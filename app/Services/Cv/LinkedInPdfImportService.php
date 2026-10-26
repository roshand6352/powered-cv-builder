<?php

namespace App\Services\Cv;

use App\Jobs\ImportLinkedInPdfJob;
use App\Models\Cv;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class LinkedInPdfImportService extends BaseService
{
    public const STATUS_TTL_MINUTES = 60;

    public const STATUS_CACHE_PREFIX = 'linkedin_pdf_import.status.';

    public function __construct(
        protected CvService $cvService,
    ) {}

    protected function statusCacheKey(int $userId): string
    {
        return self::STATUS_CACHE_PREFIX.$userId;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function statusFor(User $user): ?array
    {
        $status = Cache::get($this->statusCacheKey((int) $user->id));

        return is_array($status) ? $status : null;
    }

    public function forgetStatus(User $user): void
    {
        Cache::forget($this->statusCacheKey((int) $user->id));
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function putStatus(User $user, array $payload): void
    {
        $this->putStatusForUserId((int) $user->id, $payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function putStatusForUserId(int $userId, array $payload): void
    {
        Cache::put(
            $this->statusCacheKey($userId),
            $payload,
            now()->addMinutes(self::STATUS_TTL_MINUTES),
        );
    }

    /**
     * Store the temporary PDF, mark processing, and dispatch the import job.
     */
    public function queueImport(User $user, UploadedFile $pdf): string
    {
        $temporaryPath = $pdf->storeAs(
            'tmp/linkedin-pdf-imports/'.$user->id,
            Str::uuid()->toString().'.'.$pdf->getClientOriginalExtension(),
        );

        $this->putStatus($user, [
            'state' => 'processing',
            'step' => 'uploading_pdf',
            'message' => null,
            'uploaded_pdf_path' => $temporaryPath,
            'mapped_cv_json' => null,
        ]);

        ImportLinkedInPdfJob::dispatch(
            user_id: (int) $user->id,
            uploaded_pdf_path: $temporaryPath,
        );

        return $temporaryPath;
    }

    /**
     * Status payload for full page renders (Blade bootstrap).
     * Failed import errors are one-shot: never reopen the error modal after a
     * refresh — consume and clear them here. Live polling still uses statusForClient().
     *
     * @return array<string, mixed>|null
     */
    public function statusForPageLoad(User $user): ?array
    {
        $status = $this->statusFor($user);

        if ($status === null) {
            return null;
        }

        if (($status['state'] ?? null) === 'ready_for_builder') {
            return null;
        }

        if (($status['state'] ?? null) === 'failed') {
            $this->forgetStatus($user);

            return null;
        }

        unset($status['mapped_cv_json']);

        return $status;
    }

    /**
     * Clear a persisted failure so the error modal cannot reopen on refresh.
     */
    public function consumeFailedStatus(User $user): void
    {
        $status = $this->statusFor($user);

        if (($status['state'] ?? null) === 'failed') {
            $this->forgetStatus($user);
        }
    }

    /**
     * Status payload safe for the browser modal/polling UI.
     * After the one-time template redirect is consumed, return null so refresh
     * does not trigger another redirect. Mapped CV data remains in cache for
     * the builder via pullCompletedDocument().
     *
     * @return array<string, mixed>|null
     */
    public function statusForClient(User $user): ?array
    {
        $status = $this->statusFor($user);

        if ($status === null) {
            return null;
        }

        if (($status['state'] ?? null) === 'ready_for_builder') {
            return null;
        }

        unset($status['mapped_cv_json']);

        return $status;
    }

    /**
     * Create (or reuse) a draft CV for a completed import, then consume the
     * one-time redirect trigger by moving state to ready_for_builder.
     * Imported field data stays in cache until the builder pulls it.
     */
    public function continueToTemplateSelection(User $user): Cv
    {
        $status = $this->statusFor($user);
        $state = $status['state'] ?? null;

        if (
            ! is_array($status)
            || ! is_array($status['mapped_cv_json'] ?? null)
            || ! in_array($state, ['completed', 'ready_for_builder'], true)
        ) {
            throw new RuntimeException('LinkedIn PDF import is not ready for template selection.');
        }

        $cv = $this->resolveDraftCv($user, $status);

        if (! $cv) {
            $cv = $this->cvService->createDraft(
                $user,
                $this->draftTitleFromMappedDocument($status['mapped_cv_json']),
            );
        }

        // Consume the one-time redirect: refresh must not see state=completed again.
        $this->putStatus($user, array_merge($status, [
            'state' => 'ready_for_builder',
            'step' => 'almost_done',
            'message' => null,
            'cv_id' => $cv->id,
            'uploaded_pdf_path' => null,
        ]));

        return $cv;
    }

    /**
     * Pull a completed import document once, then clear the cached status.
     *
     * @return array<string, mixed>|null
     */
    public function pullCompletedDocument(User $user): ?array
    {
        $status = $this->statusFor($user);
        $state = $status['state'] ?? null;

        if (
            ! in_array($state, ['completed', 'ready_for_builder'], true)
            || ! is_array($status['mapped_cv_json'] ?? null)
        ) {
            return null;
        }

        $document = $status['mapped_cv_json'];
        $this->forgetStatus($user);

        return $document;
    }

    /**
     * @param  array<string, mixed>  $status
     */
    protected function resolveDraftCv(User $user, array $status): ?Cv
    {
        $existingCvId = (int) ($status['cv_id'] ?? 0);

        if ($existingCvId <= 0) {
            return null;
        }

        return Cv::query()
            ->where('user_id', $user->id)
            ->find($existingCvId);
    }

    /**
     * @param  array<string, mixed>  $mappedDocument
     */
    protected function draftTitleFromMappedDocument(array $mappedDocument): string
    {
        $personalDetails = is_array($mappedDocument['personal_details'] ?? null)
            ? $mappedDocument['personal_details']
            : [];

        $fullName = trim((string) ($personalDetails['full_name'] ?? ''));

        if ($fullName === '') {
            $fullName = trim(implode(' ', array_filter([
                $personalDetails['first_name'] ?? null,
                $personalDetails['last_name'] ?? null,
            ])));
        }

        if ($fullName !== '') {
            return $fullName."'s CV";
        }

        return 'LinkedIn Import';
    }

    /**
     * Merge imported LinkedIn CV JSON into an existing builder document without
     * overwriting the active template selection.
     *
     * @param  array<string, mixed>  $currentDocument
     * @param  array<string, mixed>  $importedDocument
     * @return array<string, mixed>
     */
    public function mergeIntoDocument(array $currentDocument, array $importedDocument): array
    {
        $merged = $currentDocument;

        $merged['personal_details'] = array_merge(
            is_array($currentDocument['personal_details'] ?? null) ? $currentDocument['personal_details'] : [],
            is_array($importedDocument['personal_details'] ?? null) ? $importedDocument['personal_details'] : [],
        );

        foreach (['experience', 'education', 'skills', 'certifications', 'languages', 'projects'] as $section) {
            if (! array_key_exists($section, $importedDocument) || ! is_array($importedDocument[$section])) {
                continue;
            }

            $merged[$section] = $this->ensureItemIds($importedDocument[$section]);
        }

        if (is_array($importedDocument['meta'] ?? null)) {
            $merged['meta'] = array_merge(
                is_array($currentDocument['meta'] ?? null) ? $currentDocument['meta'] : [],
                $importedDocument['meta'],
            );
        }

        if (isset($currentDocument['template'])) {
            $merged['template'] = $currentDocument['template'];
        }

        return $merged;
    }

    public function deleteTemporaryPdf(string $path): void
    {
        if ($path !== '' && Storage::exists($path)) {
            Storage::delete($path);
        }
    }

    /**
     * @param  list<mixed>  $items
     * @return list<array<string, mixed>>
     */
    protected function ensureItemIds(array $items): array
    {
        $normalized = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            if (blank($item['id'] ?? null)) {
                $item['id'] = (string) Str::uuid();
            }

            $normalized[] = $item;
        }

        return $normalized;
    }
}
