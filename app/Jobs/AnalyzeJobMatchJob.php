<?php

namespace App\Jobs;

use App\Models\CoverLetter;
use App\Models\Cv;
use App\Models\JobMatchScore;
use App\Services\Ai\AiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class AnalyzeJobMatchJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(
        public int $job_match_score_id,
        public int $user_id,
        public int $cv_id,
        public ?int $cover_letter_id,
        public string $job_description,
    ) {}

    public function handle(AiService $aiService): void
    {
        $jobMatchScore = JobMatchScore::query()->findOrFail($this->job_match_score_id);
        $jobMatchScore->update(['status' => 'processing']);

        $cv = Cv::query()
            ->whereKey($this->cv_id)
            ->where('user_id', $this->user_id)
            ->firstOrFail();

        $coverLetter = $this->cover_letter_id
            ? CoverLetter::query()
                ->whereKey($this->cover_letter_id)
                ->where('user_id', $this->user_id)
                ->first()
            : null;

        $result = $aiService->analyzeJobMatch([
            'job_description' => $this->job_description,
            'cv_json' => $cv->cv_data ?? [],
            'cover_letter' => $coverLetter ? [
                'title' => $coverLetter->title,
                'company_name' => $coverLetter->company_name,
                'job_title' => $coverLetter->job_title,
                'subject' => $coverLetter->subject,
                'greeting' => $coverLetter->greeting,
                'introduction' => $coverLetter->introduction,
                'body' => $coverLetter->body,
                'closing_paragraph' => $coverLetter->closing_paragraph,
                'signature_name' => $coverLetter->signature_name,
            ] : null,
        ]);

        $jobMatchScore->update([
            'score' => $result['score'],
            'result_json' => $result,
            'status' => 'completed',
        ]);
    }

    public function failed(Throwable $exception): void
    {
        JobMatchScore::query()
            ->whereKey($this->job_match_score_id)
            ->update(['status' => 'failed']);
    }
}
