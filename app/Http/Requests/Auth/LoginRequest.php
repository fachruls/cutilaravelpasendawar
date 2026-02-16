<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'nip' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $inputType = $this->input('nip'); // Input dari form (name="nip")

        // --- LOGIKA BARU: NIP vs USERNAME ---
        // Jika inputannya ANGKA SEMUA, kita anggap itu NIP.
        // Jika ada HURUF, kita anggap itu Username.
        $field = is_numeric($inputType) ? 'nip' : 'username';

        // Coba Login
        if (! Auth::attempt([$field => $inputType, 'password' => $this->input('password')], $this->boolean('remember'))) {
            
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'nip' => 'Login gagal. Periksa NIP/Username dan Password Anda.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'nip' => trans('auth.throttle', [
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
        return Str::transliterate(Str::lower($this->input('nip')).'|'.$this->ip());
    }
}