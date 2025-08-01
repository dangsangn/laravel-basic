<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Mail\VerificationCodeMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {

        $request->authenticate();
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function loginWith2FA(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $verificationCode = random_int(100000, 999999);
            session(['verification_code' => $verificationCode, 'user_id' => $user->id]);

            Mail::to($user->email)->send(new VerificationCodeMail($verificationCode));


            // Log out the user after sending the verification code
            // Auth::logout();

            return redirect()->route('custom.verification.form')->with('status', 'Verification code send to your mail');
        }

        return redirect()->back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');

    }

    public function showVerificationForm(): View
    {
        return view('auth.verify');
    }

    public function handleVerificationForm(Request $request)
    {
        $request->validate(['code' => 'required|numeric']);
        if ($request->code == session('verification_code')) {

            Auth::loginUsingId(session('user_id'));
            session()->forget(['verification_code', 'user_id']);
            return redirect()->intended('/dashboard');
        }
        return back()->withError(['code' => 'Invalid verification code'])->onlyInput('code');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();


        return redirect('/login');

    }
}
