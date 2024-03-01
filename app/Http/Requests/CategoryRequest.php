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
            'parent_id.exists' => 'Thể loại cha không tồn tại',
            'category_name.required' => 'Thể loại không được để trống',
            'category_name.unique' => 'Thể loại đã tồn tại',
            'category_description.required' => 'Mô tả thể loại không được để trống',
            'active.required' => 'Trạng thái không được để trống',
            'active.boolean' => 'Trạng thái phải là boolean',
        ];
    }
}
