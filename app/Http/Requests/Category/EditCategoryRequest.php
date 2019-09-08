<?php

namespace Vanguard\Http\Requests\Category;

use Vanguard\Http\Requests\Request;

class EditCategoryRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'parent' => 'required',
            'title' => 'required',
            'keyword' => 'required',
        ];
    }
}
