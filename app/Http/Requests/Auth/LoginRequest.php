<?php

namespace App\Http\Requests\Auth;

use App\Models\School;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Enums\Status;
use Illuminate\Support\Facades\Lang;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'school_cd' => ['nullable', 'string', 'max:4'],
            'login_id' => ['required', 'string', 'max:16'],
            'password' => ['required', 'string', 'max:20'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate()
    {
        $this->ensureIsNotRateLimited();

        $query = ['school_id' => null, 'status' => Status::ENABLED];
        if ($this->school_cd) {
            $existsSchool = School::where('school_cd', $this->school_cd)->first();
            if (!$existsSchool) {
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'login_id' => Lang::get('messages.MSE00003'),
                ]);
            }
            $query['school_id'] = $existsSchool->id;
        }

        if (!Auth::attempt(
            array_merge($this->only('login_id', 'password'), $query),
            $this->boolean('remember')
        )) {

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login_id' => Lang::get('messages.MSE00003'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited()
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login_id' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::transliterate(Str::lower($this->input('login_id')) . '|' . $this->ip());
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'login_id' => 'ユーザー名',
            'password' => 'パスワード',
            'school_cd' => '教習所コード',
        ];
    }
}
