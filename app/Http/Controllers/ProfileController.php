<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('admin.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        $idUser = Auth::user()->id;
        $data = User::find($idUser);
        $data->name = $request->name;
        $data->email = $request->email;
        $data->address = $request->address;
        $data->phone = $request->phone;
        $oldPhoto = $data->photo;
        if ($request->hasFile(('photo'))) {
            // save photo into folder uploads
            $file = $request->file('photo');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('upload/user_images'), $filename);
            $data->photo = $filename;
            // delete old photo if exists
            if ($filename !== $oldPhoto && $oldPhoto) {
                $this->deleteOldPhoto($oldPhoto);
            }
        }

        $data->save();
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    private function deleteOldPhoto(string $oldPhoto): void
    {
        $fullPath = public_path('upload/user_images/'.$oldPhoto);
        if ($fullPath && file_exists($fullPath)) {
            unlink($fullPath);
        }
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

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
