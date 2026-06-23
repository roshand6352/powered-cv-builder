<?php

namespace App\Http\Controllers\Concerns;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

trait RespondsToPdfExportFailures
{
    protected function pdfExportErrorResponse(Throwable $exception): Response
    {
        report($exception);

        return response()->json([
            'message' => 'Unable to generate PDF.',
            'error' => config('app.debug') ? $exception->getMessage() : null,
        ], 500);
    }
}
