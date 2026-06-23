<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfilePhotoUpdateRequest;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class ProfileController extends Controller
{
    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('dashboard.profile')->with('status', 'profile-updated');
    }

    /**
     * Update the user's profile photo.
     */
    public function updatePhoto(ProfilePhotoUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->clearMediaCollection('avatar');
        $user->addMediaFromRequest('photo')
            ->toMediaCollection('avatar');

        return Redirect::route('dashboard.profile')->with('status', 'photo-updated');
    }

    /**
     * Remove the user's profile photo.
     */
    public function destroyPhoto(Request $request): RedirectResponse
    {
        $request->user()->clearMediaCollection('avatar');

        return Redirect::route('dashboard.profile')->with('status', 'photo-removed');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        $user->clearMediaCollection('avatar');

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
