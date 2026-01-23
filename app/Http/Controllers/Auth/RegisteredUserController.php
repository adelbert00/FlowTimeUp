<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        $siteKey = config('services.recaptcha.site_key');
        
        // Debug: Log if key is missing (only in non-production)
        if (empty($siteKey) && app()->environment('local')) {
            \Log::warning('reCAPTCHA site key is not configured. Check RECAPTCHA_SITE_KEY in .env');
        }
        
        return Inertia::render('Auth/Register', [
            'recaptchaSiteKey' => $siteKey ?: null,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $hasRecaptcha = !empty(config('services.recaptcha.secret_key'));
        
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
        
        // Only require reCAPTCHA token if secret key is configured
        if ($hasRecaptcha) {
            $rules['recaptcha_token'] = 'required|string';
        }
        
        $request->validate($rules);

        // Verify reCAPTCHA v3
        if ($hasRecaptcha) {
            if (empty($request->recaptcha_token)) {
                throw ValidationException::withMessages([
                    'recaptcha' => 'reCAPTCHA token is required. Please refresh the page and try again.',
                ]);
            }
            
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $request->recaptcha_token,
                'remoteip' => $request->ip(),
            ]);

            $recaptchaData = $response->json();

            if (!$recaptchaData || !isset($recaptchaData['success']) || !$recaptchaData['success']) {
                \Log::warning('reCAPTCHA verification failed', [
                    'response' => $recaptchaData,
                    'errors' => $recaptchaData['error-codes'] ?? [],
                ]);
                
                throw ValidationException::withMessages([
                    'recaptcha' => 'reCAPTCHA verification failed. Please try again.',
                ]);
            }
            
            $score = $recaptchaData['score'] ?? 0;
            if ($score < 0.5) {
                \Log::warning('reCAPTCHA score too low', ['score' => $score]);
                
                throw ValidationException::withMessages([
                    'recaptcha' => 'reCAPTCHA verification failed. Please try again.',
                ]);
            }
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Redirect to email verification page
        return redirect()->route('verification.notice');
    }
}
