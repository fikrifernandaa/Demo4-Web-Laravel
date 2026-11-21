<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // POST -> semua field wajib; PATCH -> hanya field yang dikirim yang divalidasi.
        $r = $this->isMethod('PATCH') ? 'sometimes' : 'required';

        return [
            'title'    => [
                $r, 'string', 'max:255',
                // Saat update, abaikan judul milik media itu sendiri.
                Rule::unique('media', 'title')->ignore($this->route('media')),
            ],
            'type'     => [$r, Rule::in(['Anime', 'Film', 'Series'])],
            'genre'    => [$r, 'string', 'max:255'],
            'score'    => [$r, 'numeric', 'between:0,10'],
            'episodes' => [$r, 'integer', 'min:1'],
            'aura'     => [$r, 'string', 'max:255'],
            'synopsis' => [$r, 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'    => 'Judul media wajib diisi',
            'title.unique'      => 'Judul media sudah ada, tidak boleh duplikat',
            'type.required'     => 'Jenis media wajib diisi',
            'type.in'           => 'Jenis media harus Anime, Film, atau Series',
            'genre.required'    => 'Genre wajib diisi',
            'score.required'    => 'Rating wajib diisi',
            'score.numeric'     => 'Rating harus berupa angka',
            'score.between'     => 'Rating harus antara 0 sampai 10',
            'episodes.required' => 'Jumlah episode wajib diisi',
            'episodes.integer'  => 'Jumlah episode harus bilangan bulat',
            'episodes.min'      => 'Jumlah episode minimal 1',
            'aura.required'     => 'Aura wajib diisi',
            'synopsis.required' => 'Sinopsis wajib diisi',
        ];
    }
}
