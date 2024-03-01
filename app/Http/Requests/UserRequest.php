<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required|min:6',
                    'role' => 'required',
                    'is_active' => 'required|boolean',
                ];
            case 'PUT':
                return [
                    'name' => 'required',
                    'email' => [
                        'required',
                        'email',
                        Rule::unique('users', 'email')->ignore($this->id),
                    ],
                    'role' => 'required',
                    'is_active' => 'required|boolean',
                ];
            case 'DELETE':
            case 'PATCH':
                switch ($this->path()) {
                    case 'user/change-password':
                        return [
                            'old_password' => 'required|min:6',
                            'password' => 'required|confirmed|min:6|different:old_password',
                        ];
                    default:
                        return [
//                            'id' => 'required|exists:users,id'
                        ];
                }
            default:
                return [];
        }
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Id không được để trống',
            'id.exists' => 'Người dùng không tồn tại',
            'name.required' => 'Tên không được để trống',
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email đã tồn tại',
            'password.required' => 'Mật khẩu không được để trống',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'role.required' => 'Role không được để trống',
            'active.required' => 'Trạng thái không được để trống',
        ];
    }

}
