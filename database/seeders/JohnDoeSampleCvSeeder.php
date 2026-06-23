<?php

namespace Database\Seeders;

use App\DTOs\Cv\CvDocumentData;
use App\Models\Cv;
use App\Models\CvTemplate;
use App\Models\User;
use App\Services\Cv\CvService;
use App\Support\Cv\JohnDoeSampleCvDocuments;
use Illuminate\Database\Seeder;

class JohnDoeSampleCvSeeder extends Seeder
{
    public function run(): void
    {
        $john = User::query()->where('email', 'john@example.com')->first();

        if (! $john) {
            $this->command?->warn('John Doe (john@example.com) not found. Run RolesAndPermissionsSeeder first.');

            return;
        }

        Cv::query()->where('user_id', $john->id)->delete();

        $cvService = app(CvService::class);
        $created = 0;

        foreach (JohnDoeSampleCvDocuments::profiles() as $profile) {
            $template = CvTemplate::query()
                ->cvOnly()
                ->where('slug', $profile['slug'])
                ->first();

            if (! $template) {
                $this->command?->warn("CV template [{$profile['slug']}] not found — skipping.");

                continue;
            }

            $cv = $cvService->createFromTemplate($john, $template, $profile['title']);

            $document = CvDocumentData::fromArray(array_merge(
                $profile['content'],
                [
                    'schema_version' => $cv->cv_data['schema_version'] ?? null,
                    'template' => $cv->cv_data['template'] ?? [],
                    'meta' => $cv->cv_data['meta'] ?? [],
                ],
            ));

            $cv->update([
                'title' => $profile['title'],
                'cv_data' => $document->toArray(),
            ]);

            $created++;
        }

        $this->command?->info("Created {$created} sample CV(s) for John Doe.");
    }
}
