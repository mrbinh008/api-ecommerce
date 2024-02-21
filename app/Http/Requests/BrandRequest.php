<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
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
                    'name' => 'required|string|max:255|unique:brands',
                    'logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'description' => 'required',
                    'is_active' => 'required|boolean',
                    'featured'=>'required|boolean',
                ];
            case 'PUT':
                return [
                    'name' => 'required|string|max:255|unique:brands,name,'.$this->id,
                    'logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'description' => 'required',
                    'is_active' => 'required|boolean',
                    'featured'=>'required|boolean',
                ];
            default:
                return [];
        }
    }
}
