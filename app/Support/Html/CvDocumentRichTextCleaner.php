<?php

namespace App\Support\Html;

use App\DTOs\Cv\CvDocumentData;

final class CvDocumentRichTextCleaner
{
    public static function clean(CvDocumentData $document): CvDocumentData
    {
        $document->personalDetails->summary = RichTextSanitizer::sanitize($document->personalDetails->summary);

        foreach ($document->experience as $item) {
            $item->description = RichTextSanitizer::sanitize($item->description);
        }

        foreach ($document->education as $item) {
            $item->description = RichTextSanitizer::sanitize($item->description);
        }

        foreach ($document->projects as $item) {
            $item->description = RichTextSanitizer::sanitize($item->description);
        }

        foreach ($document->customSections as $section) {
            foreach ($section->items as $item) {
                $item->content = RichTextSanitizer::sanitize($item->content);
            }
        }

        return $document;
    }
}
