<?php

namespace Vanguard\Http\Requests\Auth;

use Vanguard\Http\Requests\Request;

class LoginRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];

        if (settings('captcha.enabled')) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }

        return $rules;
    }

}
