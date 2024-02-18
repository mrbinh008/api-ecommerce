<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
                    'product_name' => 'required|string|max:255|unique:products',
                    'sku' => 'required|string|max:255|unique:products,sku',
                    'brand_id' => 'nullable|integer|exists:brands,id',
                    'description' => 'nullable|string',
                    'short_description' => 'nullable|string',
                    'product_weight' => 'nullable|numeric',
                    'is_published' => 'nullable|boolean',
                    'is_featured' => 'nullable|boolean',
                    'options' => 'nullable|array',
                    'options.*.option_name' => 'required|string|max:255',
                    'options.*.option_values' => 'required|array',
                    'options.*.option_values.*.value' => 'required|string|max:255',
                    'skus' => 'nullable|array',
                    'skus.*.sku' => [
                        'required',
                        'string',
                        'max:255',
                        Rule::unique('product_skus', 'sku'),
                    ],
                    'skus.*.price' => 'required|numeric',
                    'skus.*.quantity' => 'required|integer',
                    'skus.*.values' => 'required|array',
                    'skus.*.values.*.value_id' => 'required|integer|max:255',
                    'skus.*.values.*.option_id' => 'required|integer|max:255',
                ];
            case 'PUT':
                return [
                    'product_name' => 'string|max:255|unique:products,product_name,'.$this->id,
                    'brand_id' => 'nullable|integer|exists:brands,id',
                    'description' => 'nullable|string',
                    'short_description' => 'nullable|string',
                    'product_weight' => 'nullable|numeric',
                    'is_published' => 'nullable|boolean',
                    'is_featured' => 'nullable|boolean',
                    'sku' => 'string|max:255|unique:products,sku,'.$this->id,
                    'options' => 'nullable|array',
                    'options.*.option_name' => 'required|string|max:255',
                    'options.*.option_values' => 'required|array',
                    'options.*.option_values.*.value' => 'required|string|max:255',
                    'skus' => 'nullable|array',
                    'skus.*.sku' => [
                        'required',
                        'string',
                        'max:255',
                        function ($attribute, $value, $fail) {
                            $skuId = explode('.', $attribute)[1];
                            $uniqueRule = Rule::unique('product_skus', 'sku')->ignore($this->skus[$skuId]['id']);
                            $validator = Validator::make([$attribute => $value], [$attribute => $uniqueRule]);
                            if ($validator->fails()) {
                                $fail($validator->errors()->first($attribute));
                            }
                        },
                    ],
                    'skus.*.price' => 'required|numeric',
                    'skus.*.quantity' => 'required|integer',
                    'skus.*.values' => 'required|array',
                    'skus.*.values.*.value_id' => 'required|integer|max:255',
                    'skus.*.values.*.option_id' => 'required|integer|max:255',
                ];
            default:
                return [];
        }
    }
}
