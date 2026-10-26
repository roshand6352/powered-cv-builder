<?php

namespace App\Jobs;

use App\Exceptions\Ai\AiGenerationException;
use App\Models\User;
use App\Services\Ai\AiService;
use App\Services\Cv\LinkedInPdfImportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;
use Throwable;

class ImportLinkedInPdfJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 1;

    public int $timeout = 180;

    public function __construct(
        public int $user_id,
        public string $uploaded_pdf_path,
    ) {}

    public function handle(AiService $aiService, LinkedInPdfImportService $importService): void
    {
        Log::info('LinkedIn PDF import started', [
            'user_id' => $this->user_id,
            'uploaded_pdf_path' => $this->uploaded_pdf_path,
        ]);

        $user = User::query()->find($this->user_id);

        if (! $user) {
            Log::warning('LinkedIn PDF import aborted: user not found', [
                'user_id' => $this->user_id,
            ]);
            $importService->deleteTemporaryPdf($this->uploaded_pdf_path);

            return;
        }

        try {
            $this->storeStatus($importService, $user, 'processing', 'reading_linkedin_profile');

            if (! Storage::exists($this->uploaded_pdf_path)) {
                throw new \RuntimeException('Uploaded PDF was not found.');
            }

            $absolutePath = Storage::path($this->uploaded_pdf_path);
            $parser = new Parser();
            $extractedText = trim($parser->parseFile($absolutePath)->getText());

            Log::info('LinkedIn PDF text extracted', [
                'user_id' => $this->user_id,
                'text_length' => strlen($extractedText),
            ]);

            if ($extractedText === '') {
                throw new \RuntimeException('Extracted PDF text was empty.');
            }

            $this->storeStatus($importService, $user, 'processing', 'extracting_information');
            $this->storeStatus($importService, $user, 'processing', 'analyzing_profile');

            Log::info('LinkedIn PDF OpenAI mapping started', [
                'user_id' => $this->user_id,
            ]);

            $mappedCvJson = $aiService->mapLinkedinPdfImport([
                'extracted_text' => $extractedText,
            ]);

            Log::info('LinkedIn PDF OpenAI mapping completed', [
                'user_id' => $this->user_id,
                'section_keys' => array_keys($mappedCvJson),
            ]);

            $this->storeStatus($importService, $user, 'processing', 'preparing_cv', mappedCvJson: $mappedCvJson);
            $this->storeStatus($importService, $user, 'processing', 'almost_done', mappedCvJson: $mappedCvJson);
            $this->storeStatus(
                $importService,
                $user,
                'completed',
                'almost_done',
                mappedCvJson: $mappedCvJson,
                retainUploadedPdfPath: false,
            );

            Log::info('LinkedIn PDF import completed', [
                'user_id' => $this->user_id,
            ]);
        } catch (Throwable $exception) {
            Log::error('LinkedIn PDF import failed during processing', [
                'user_id' => $this->user_id,
                'uploaded_pdf_path' => $this->uploaded_pdf_path,
                'exception' => $exception->getMessage(),
            ]);

            $this->storeFriendlyFailure($importService, $user, $exception);
        } finally {
            $importService->deleteTemporaryPdf($this->uploaded_pdf_path);
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::error('LinkedIn PDF import queue failure', [
            'user_id' => $this->user_id,
            'uploaded_pdf_path' => $this->uploaded_pdf_path,
            'exception' => $exception->getMessage(),
        ]);

        try {
            $importService = app(LinkedInPdfImportService::class);
            $user = User::query()->find($this->user_id);

            if ($user) {
                $this->storeQueueFailure($importService, $user);
            } else {
                $importService->putStatusForUserId($this->user_id, [
                    'state' => 'failed',
                    'step' => 'almost_done',
                    'message' => 'We could not finish importing your LinkedIn PDF because the background task failed. Please try again or enter your CV manually.',
                    'uploaded_pdf_path' => null,
                    'mapped_cv_json' => null,
                ]);
            }

            $importService->deleteTemporaryPdf($this->uploaded_pdf_path);
        } catch (Throwable $statusException) {
            Log::error('LinkedIn PDF import failed to persist failure status', [
                'user_id' => $this->user_id,
                'exception' => $statusException->getMessage(),
            ]);
        }
    }

    protected function storeFriendlyFailure(
        LinkedInPdfImportService $importService,
        User $user,
        ?Throwable $exception = null,
    ): void {
        [$step, $message] = $this->friendlyFailurePayload($exception);

        $this->storeStatus(
            $importService,
            $user,
            'failed',
            $step,
            $message,
            retainUploadedPdfPath: false,
        );
    }

    protected function storeQueueFailure(LinkedInPdfImportService $importService, User $user): void
    {
        $this->storeStatus(
            $importService,
            $user,
            'failed',
            'almost_done',
            'We could not finish importing your LinkedIn PDF because the background task failed. Please try again or enter your CV manually.',
            retainUploadedPdfPath: false,
        );
    }

    /**
     * @param  array<string, mixed>|null  $mappedCvJson
     */
    protected function storeStatus(
        LinkedInPdfImportService $importService,
        User $user,
        string $state,
        string $step,
        ?string $message = null,
        ?array $mappedCvJson = null,
        bool $retainUploadedPdfPath = true,
    ): void {
        $importService->putStatus($user, [
            'state' => $state,
            'step' => $step,
            'message' => $message,
            'uploaded_pdf_path' => $retainUploadedPdfPath ? $this->uploaded_pdf_path : null,
            'mapped_cv_json' => $mappedCvJson,
        ]);

        Log::info('LinkedIn PDF import status updated', [
            'user_id' => $user->id,
            'state' => $state,
            'step' => $step,
        ]);
    }

    /**
     * @return array{0: string, 1: string}
     */
    protected function friendlyFailurePayload(?Throwable $exception): array
    {
        $message = strtolower((string) $exception?->getMessage());

        if ($message === '') {
            return [
                'reading_linkedin_profile',
                "We couldn't read your LinkedIn PDF. Please upload another PDF or enter your CV manually.",
            ];
        }

        if (str_contains($message, 'empty')) {
            return [
                'extracting_information',
                'This LinkedIn PDF appears to be empty. Please upload another PDF or enter your CV manually.',
            ];
        }

        if (
            ($exception instanceof AiGenerationException && str_contains($message, 'timeout'))
            || str_contains($message, 'timeout')
            || str_contains($message, 'timed out')
        ) {
            return [
                'analyzing_profile',
                'The AI analysis took too long to respond. Please try again in a moment or enter your CV manually.',
            ];
        }

        if (
            str_contains($message, 'quota')
            || str_contains($message, 'api key')
            || str_contains($message, 'openai')
        ) {
            return [
                'analyzing_profile',
                'We could not analyze your LinkedIn profile right now. Please try again in a moment or enter your CV manually.',
            ];
        }

        if (str_contains($message, 'invalid pdf') || str_contains($message, 'not found')) {
            return [
                'reading_linkedin_profile',
                'Please upload a valid LinkedIn PDF file and try again.',
            ];
        }

        if (str_contains($message, 'parse') || str_contains($message, 'xref') || str_contains($message, 'corrupt')) {
            return [
                'reading_linkedin_profile',
                'This LinkedIn PDF appears to be corrupted or unreadable. Please export it again from LinkedIn and try once more.',
            ];
        }

        if (str_contains($message, 'invalid json') || str_contains($message, 'builder schema')) {
            return [
                'analyzing_profile',
                'We could not understand your LinkedIn profile details this time. Please try again or enter your CV manually.',
            ];
        }

        return [
            'extracting_information',
            "We couldn't extract readable information from your LinkedIn PDF. Please upload another PDF or enter your CV manually.",
        ];
    }
}
