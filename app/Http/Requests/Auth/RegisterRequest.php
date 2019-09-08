<?php

namespace Vanguard\Http\Requests\Auth;

use Vanguard\Http\Requests\Request;

class RegisterRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'email' => 'required|email|unique:users,email',
            'username' => 'required|unique:users,username',
            'password' => 'required|confirmed|min:6',
        ];

        if (settings('registration.captcha.enabled')) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }

        if (settings('tos')) {
            $rules['tos'] = 'accepted';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'tos.accepted' => trans('app.you_have_to_accept_tos')
        ];
    }
}
