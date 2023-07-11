<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LikeAndCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "like_user" => "boolean",
            "comment_user" => "string",
        ];
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
