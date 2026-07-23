<?php

namespace App\Http\Controllers\Cv;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cv\StoreCvRequest;
use App\Models\Cv;
use App\Services\Cv\CvService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CvController extends Controller
{
    public function __construct(
        protected CvService $cvService,
    ) {}

    public function store(StoreCvRequest $request): RedirectResponse
    {
        $cv = $this->cvService->createDraft(
            $request->user(),
            $request->validated('title'),
        );

        return redirect()
            ->route('cvs.templates', $cv)
            ->with('status', 'cv-created');
    }

    public function destroy(Request $request, Cv $cv): RedirectResponse|JsonResponse
    {
        $this->authorize('delete', $cv);

        $this->cvService->delete($cv);

        if ($request->wantsJson()) {
            // Flash so the success banner shows after the client reloads the list.
            $request->session()->flash('status', 'cv-deleted');

            return response()->json(['message' => 'CV deleted successfully.']);
        }

        return redirect()
            ->route('dashboard.cvs')
            ->with('status', 'cv-deleted');
    }
}
