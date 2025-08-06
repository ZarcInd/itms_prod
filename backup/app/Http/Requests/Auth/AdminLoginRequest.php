<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;

class AdminLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();
    
        $login = $this->input('email'); // can be email or phone
        $password = $this->input('password');
    
        $credentials = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? ['email' => $login, 'password' => $password]
            : ['phone' => $login, 'password' => $password];
    
        $stayLogin = $this->boolean('stay_login');
    
        if ($stayLogin) {
            // Set session + remember cookie to 7 days
            config(['session.lifetime' => 60 * 24 * 7]); // 7 days (in minutes)
            config(['auth.remember_cookie_expiration' => 60 * 24 * 7]); // 7 days
    
            // Auth attempt with remember me ON
            $remember = true;
        } else {
            // Set session to 1 day, no remember cookie
            config(['session.lifetime' => 60 * 24 * 1]); // 1 day
            config(['auth.remember_cookie_expiration' => 0]); // No remember cookie
    
            // Auth attempt with remember me OFF
            $remember = false;
        }
        
        if (!Auth::guard('admin')->attempt($credentials, $remember)) {
            RateLimiter::hit($this->throttleKey());
    
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }
        
    
        RateLimiter::clear($this->throttleKey());
    }
    
    



    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}