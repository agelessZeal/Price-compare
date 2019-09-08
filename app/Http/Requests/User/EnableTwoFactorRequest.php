<?php

namespace Vanguard\Http\Requests\User;

use Vanguard\Http\Requests\Request;
use Vanguard\User;

class EnableTwoFactorRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'country_code' => 'required|numeric|integer',
            'phone_number' => 'required|numeric',
        ];
    }
}
