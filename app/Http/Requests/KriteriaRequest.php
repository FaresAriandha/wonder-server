<?php

namespace App\Http\Requests;

use App\Models\Kriteria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class KriteriaRequest extends FormRequest
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
        $kriteria_exist = Kriteria::where("id", request()->segment(4))->first();
        $rules = [
            "nama_kriteria" => "required|string|max:255",
            "tipe" => "required|string",
            "bobot" => "required|numeric|min:0|max:1",
        ];

        if ($kriteria_exist != null) {
            $max_bobot = 1 - (float)Kriteria::whereNot("id", request()->segment(4))->sum('bobot');
            $rules['bobot'] = "required|numeric|min:0|max:$max_bobot";
            if (strtolower($kriteria_exist->nama_kriteria) != strtolower(request()->get('nama_kriteria'))) {
                $rules['nama_kriteria'] = "required|string|max:255|unique:kriteria,nama_kriteria";
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
