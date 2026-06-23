<?php

namespace App\DTOs\Cv;

use Illuminate\Contracts\Support\Arrayable;

class CvPersonalDetailsData implements Arrayable
{
    public function __construct(
        public string $fullName = '',
        public string $jobTitle = '',
        public string $email = '',
        public string $phone = '',
        public string $location = '',
        public string $website = '',
        public string $linkedin = '',
        public string $summary = '',
        public string $photoPreview = '',
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $defaults = config('cv.schema.personal_details');

        $data = array_replace_recursive($defaults, $data);

        return new self(
            fullName: (string) ($data['full_name'] ?? ''),
            jobTitle: (string) ($data['job_title'] ?? ''),
            email: (string) ($data['email'] ?? ''),
            phone: (string) ($data['phone'] ?? ''),
            location: (string) ($data['location'] ?? ''),
            website: (string) ($data['website'] ?? ''),
            linkedin: (string) ($data['linkedin'] ?? ''),
            summary: (string) ($data['summary'] ?? ''),
            photoPreview: (string) ($data['photo_preview'] ?? $data['photo_url'] ?? ''),
        );
    }

    public function toArray(): array
    {
        return [
            'full_name' => $this->fullName,
            'job_title' => $this->jobTitle,
            'email' => $this->email,
            'phone' => $this->phone,
            'location' => $this->location,
            'website' => $this->website,
            'linkedin' => $this->linkedin,
            'summary' => $this->summary,
            'photo_preview' => $this->photoPreview,
        ];
    }
}
