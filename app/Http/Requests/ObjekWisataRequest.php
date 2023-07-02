<?php

namespace App\Http\Requests;

use App\Models\ObjekWisata;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class ObjekWisataRequest extends FormRequest
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
        // dd(request()->all());
        return [
            "nama" => "required|string|max:255",
            "deskripsi" => "required|string",
            "alamat_lengkap" => "required|string",
            "kab_kota" => "required|string|max:100",
            "provinsi" => "required|string|max:100",
            "fasilitas" => "required|string",
            "foto.*" => "required|max:2048|mimes:jpg,png",
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
