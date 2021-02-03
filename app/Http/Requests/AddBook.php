<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddBook extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string',
            'subtitle' => 'nullable|string',
            'author' => 'required|string',
            'published_year' => 'nullable|integer',
            'ol_link' => 'nullable|string',
            'ol_cover' => 'nullable|integer',
        ];
    }
}
