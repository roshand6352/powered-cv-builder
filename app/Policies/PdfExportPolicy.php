<?php

namespace App\Policies;

use App\Enums\PdfExportType;
use App\Models\PdfExport;
use App\Models\User;

class PdfExportPolicy
{
    public function view(User $user, PdfExport $pdfExport): bool
    {
        return $user->id === $pdfExport->user_id;
    }

    public function download(User $user, PdfExport $pdfExport): bool
    {
        if ($user->id !== $pdfExport->user_id || ! $pdfExport->isDownloadable()) {
            return false;
        }

        if ($pdfExport->type === PdfExportType::CoverLetter) {
            return $user->hasCoverLetterAccess();
        }

        return true;
    }
}
