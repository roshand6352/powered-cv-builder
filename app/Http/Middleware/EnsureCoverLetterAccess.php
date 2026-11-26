<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCoverLetterAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Unauthorized.');
        }

        if (! $user->hasCoverLetterAccess()) {
            $message = 'Cover letters are available on paid plans. Upgrade to unlock this feature.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ], 403);
            }

            return redirect()
                ->route('dashboard.subscription')
                ->with('error', $message);
        }

        return $next($request);
    }
}
