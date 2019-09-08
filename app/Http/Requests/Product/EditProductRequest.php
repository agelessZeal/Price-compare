<?php

namespace Vanguard\Http\Requests\Product;

use Vanguard\Http\Requests\Request;

class EditProductRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'category' => 'required',
            'title' => 'required',
            'description' => 'required',
            'link' => 'required',
            'price' => 'required',
            'product_image_path' => 'required',
        ];
    }
}
