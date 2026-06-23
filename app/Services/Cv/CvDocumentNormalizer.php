<?php

namespace App\Services\Cv;

use App\DTOs\Cv\CvDocumentData;
use App\Support\Cv\CvDocumentMigrator;
use App\Support\Html\CvDocumentRichTextCleaner;
use App\Services\BaseService;

class CvDocumentNormalizer extends BaseService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function normalize(array $data): CvDocumentData
    {
        $migrated = CvDocumentMigrator::migrate($data);

        return CvDocumentRichTextCleaner::clean(CvDocumentData::fromArray($migrated));
    }
}
