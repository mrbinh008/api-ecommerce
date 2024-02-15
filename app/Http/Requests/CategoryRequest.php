<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'parent_id' => 'nullable|exists:categories,id',
                    'category_name' => 'required|unique:categories,category_name',
                    'category_description' => 'required',
                    'icon' => 'nullable',
                    'active' => 'required|boolean',
                ];
            case 'PUT':
                return [
                    'id' => 'required|exists:categories,id',
                    'parent_id' => 'nullable|exists:categories,id',
                    'category_name' => 'required|unique:categories,category_name,' . $this->id . ',id',
                    'category_description' => 'required',
                    'icon' => 'nullable',
                    'active' => 'required|boolean',
                ];
            default:
                return [];
        }
    }

    public function messages(): array
    {
        return [
            'parent_id.exists' => 'Parent category not found',
            'category_name.required' => 'Category name is required',
            'category_name.unique' => 'Category name already exists',
            'category_description.required' => 'Category description is required',
            'active.required' => 'Active is required',
            'active.boolean' => 'Active must be boolean',
            'id.required' => 'Id is required',
            'id.exists' => 'Id not found',
        ];
    }
}
