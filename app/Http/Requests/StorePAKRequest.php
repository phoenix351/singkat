<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePAKRequest extends FormRequest
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
            'nomor_sk' => 'required',
            'tmt_sk' => 'required',
            'jenis_sk' => 'required',
            'predikat_id' => 'required',
            'bulan_mulai' => 'required|date',
            'bulan_selesai' => 'required|date',
            'file' => 'required',
            'angka_kredit' => 'required|numeric',
            'angka_kredit_akumulasi' => 'required|numeric'
        ];
    }
}
