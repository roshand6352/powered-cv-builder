<?php

namespace App\Services\CoverLetter;

use App\DTOs\Cv\CvDocumentData;
use App\Models\Cv;
use App\Services\BaseService;
use Illuminate\Support\Str;

/**
 * Extracts a clean, structured snapshot of a CV for cover letter auto-fill and AI prompts.
 *
 * Only data that belongs to the selected CV is ever returned here, which keeps the
 * "use ONLY the selected CV" requirement enforceable in a single place.
 */
class CoverLetterCvExtractor extends BaseService
{
    /**
     * @return array<string, mixed>
     */
    public function extract(Cv $cv): array
    {
        $document = CvDocumentData::fromArray($cv->cv_data ?? []);
        $personal = $document->personalDetails;

        return [
            'cv_id' => $cv->id,
            'cv_title' => $cv->title,
            'full_name' => $personal->fullName,
            'professional_title' => $personal->jobTitle,
            'summary' => $personal->summary,
            'contact' => [
                'email' => $personal->email,
                'phone' => $personal->phone,
                'location' => $personal->location,
                'website' => $personal->website,
                'linkedin' => $personal->linkedin,
            ],
            'skills' => array_values(array_filter(array_map(
                fn ($skill) => trim((string) $skill->name),
                $document->skills,
            ))),
            'experience' => array_map(fn ($item) => [
                'position' => $item->position,
                'company' => $item->company,
                'location' => $item->location,
                'start_date' => $item->startDate,
                'end_date' => $item->current ? 'Present' : $item->endDate,
                'description' => $item->description,
            ], array_slice($document->experience, 0, 6)),
            'education' => array_map(fn ($item) => [
                'institution' => $item->institution,
                'degree' => $item->degree,
                'field' => $item->field,
                'start_date' => $item->startDate,
                'end_date' => $item->inProgress ? 'Present' : $item->endDate,
            ], array_slice($document->education, 0, 4)),
        ];
    }

    /**
     * Build the payload expected by the AI prompt builder using only CV data.
     *
     * @return array<string, mixed>
     */
    public function toAiCandidatePayload(Cv $cv): array
    {
        $snapshot = $this->extract($cv);

        return [
            'personal_details' => [
                'full_name' => $snapshot['full_name'],
                'job_title' => $snapshot['professional_title'],
                'location' => $snapshot['contact']['location'],
                'summary' => $snapshot['summary'],
            ],
            'experience' => $snapshot['experience'],
            'education' => $snapshot['education'],
            'skills' => array_map(fn (string $name) => ['name' => $name], $snapshot['skills']),
        ];
    }

    /**
     * The sender block rendered at the top of the letter, sourced from the CV.
     *
     * @return array<string, string>
     */
    public function senderBlock(Cv $cv): array
    {
        $snapshot = $this->extract($cv);

        return [
            'full_name' => $snapshot['full_name'],
            'job_title' => $snapshot['professional_title'],
            'email' => $snapshot['contact']['email'],
            'phone' => $snapshot['contact']['phone'],
            'location' => $snapshot['contact']['location'],
            'website' => $snapshot['contact']['website'],
            'linkedin' => $snapshot['contact']['linkedin'],
        ];
    }

    public function suggestedTitle(Cv $cv, ?string $companyName = null): string
    {
        $companyName = trim((string) $companyName);

        if ($companyName !== '') {
            return Str::limit('Cover Letter – '.$companyName, 120, '');
        }

        return 'Cover Letter for '.($cv->title ?: 'My CV');
    }
}
