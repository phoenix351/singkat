<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCapaianRequest extends FormRequest
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
        return [
            'pegawai_id' => 'required',
            'periode' => 'required',
            'tahun' => 'required',
            'predikat_id' => 'required',
            'bulan_mulai' => 'required',
            'bulan_akhir' => 'required',
            'file' => 'nullable',
        ];
    }
}
