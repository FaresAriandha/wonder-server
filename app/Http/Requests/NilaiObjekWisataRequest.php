<?php

namespace App\Http\Requests;

use App\Http\Controllers\NilaiObjekWisataController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class NilaiObjekWisataRequest extends FormRequest
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
        $criterias = NilaiObjekWisataController::$req;
        $rules = [
            $criterias[0] => "required|numeric|min:20|max:100|digits_between:2,3",
            $criterias[1] => "required|numeric|min:20|max:100|digits_between:2,3",
            $criterias[2] => "required|numeric|min:1|max:10|digits_between:1,2",
            $criterias[3] => "required|numeric|min:0|max:100|digits_between:1,3",
        ];
        if (request()->isMethod('post')) {
            $rules['id_objek_wisata'] = "required|numeric";
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
