<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RespondCollaborationProposalRequest extends FormRequest
{
    /**
     * Tentukan otorisasi request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->isEnabled();
    }

    /**
     * Aturan validasi respon proposal.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'decision' => ['required', 'string', 'in:accept,reject'],
            'response_note' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Pesan validasi bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'decision.required' => 'Keputusan proposal wajib dipilih (terima atau tolak).',
            'decision.in' => 'Keputusan proposal harus berupa terima (accept) atau tolak (reject).',
            'response_note.max' => 'Catatan respon maksimal 2000 karakter.',
        ];
    }
}
