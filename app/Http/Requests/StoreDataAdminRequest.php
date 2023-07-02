<?php

namespace App\Http\Requests;

use App\Http\Controllers\AdminController;
use App\Models\KredensialAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreDataAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    static private $credentials;
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
        if (request()->isMethod('put')) {
            $admin_exists = KredensialAdmin::where('id', request()->segment(4))->first();
            if ($admin_exists) {
                StoreDataAdminRequest::$credentials = $admin_exists;
            }
        }
        return [
            "nama_lengkap" => "required|string|max:255",
            "jenis_kelamin" => "required|string",
            "alamat" => "required|string",
            "foto" => "required|max:2048|mimes:jpg,png",
            "nik" => request()->isMethod('post') || (StoreDataAdminRequest::$credentials && StoreDataAdminRequest::$credentials->nik != request()->get('nik')) ? "required|max:16|unique:kredensial_admins,nik|string" : 'required|max:16',
            "no_telepon" => request()->isMethod('post') || (StoreDataAdminRequest::$credentials && StoreDataAdminRequest::$credentials->no_telepon != request()->get('no_telepon')) ? "required|digits_between:0,16|unique:kredensial_admins,no_telepon|integer" : "required|digits_between:0,16",
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
