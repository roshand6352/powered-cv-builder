<?php

namespace App\Http\Controllers;

use App\Support\Cv\SampleCvPdfData;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class CvController extends Controller
{
    public function download(): Response
    {
        return $this->renderTemplate('pdf.template-1', 'cv-professional-sidebar.pdf');
    }

    public function downloadTemplate1(): Response
    {
        return $this->renderTemplate('pdf.template-1', 'cv-professional-sidebar.pdf');
    }

    public function downloadTemplate2(): Response
    {
        return $this->renderTemplate('pdf.template-2', 'cv-executive-clean.pdf');
    }

    public function downloadTemplate3(): Response
    {
        return $this->renderTemplate('pdf.template-3', 'cv-premium-ats.pdf');
    }

    protected function renderTemplate(string $view, string $filename): Response
    {
        $data = SampleCvPdfData::make();

        return Pdf::loadView($view, $data)
            ->setPaper('a4', 'portrait')
            ->download($filename);
    }
}
