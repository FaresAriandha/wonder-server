<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'username' => 'required|max:255|string|unique:users,username',
            'email' => 'required|email:rfc,dns|unique:users,email',
            'password' => 'required|string',
            'role' => 'required',
        ];
        if (request()->isMethod('put')) {
            $rules['old_password'] = "required|max:255|string";
            $user_exist = User::where('id', request()->segment(4))->first();
            if ($user_exist) {
                $user_exist->username == request()->get('username') ? $rules['username'] = "required|string" : "";
                $user_exist->email == request()->get('email') ? $rules['email'] = "required|email:rfc,dns" : "";
            }
        }
        return $rules;
    }

    protected function failedValidation(Validator $validator)
    {
        $response = [
            'status' => 400,
            'message' => 'There is wrong inputs',
            'data' => $validator->errors()
        ];
        throw new HttpResponseException(response()->json($response, 400));
    }
}
