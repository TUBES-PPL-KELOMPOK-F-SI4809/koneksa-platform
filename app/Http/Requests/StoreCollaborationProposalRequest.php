<?php

namespace App\Http\Requests;

use App\Models\Business;
use App\Models\PartnerProfile;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreCollaborationProposalRequest extends FormRequest
{
    /**
     * Tentukan otorisasi request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->isEnabled();
    }

    /**
     * Aturan validasi input proposal.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();

        $rules = [
            'cooperation_type_id' => ['required', 'integer', 'exists:cooperation_types,id'],
            'title' => ['required', 'string', 'min:5', 'max:200'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'digital_offering_id' => ['nullable', 'integer', 'exists:digital_offerings,id'],
            'attachment_path' => ['nullable', 'string', 'max:255'],
        ];

        if ($user && $user->isMitra()) {
            $rules['business_id'] = ['required', 'integer', 'exists:businesses,id'];
        } elseif ($user && $user->isUmkm()) {
            $rules['partner_profile_id'] = ['required', 'integer', 'exists:partner_profiles,id'];
        }

        return $rules;
    }

    /**
     * Pesan kesalahan khusus bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'cooperation_type_id.required' => 'Jenis kerja sama wajib dipilih.',
            'cooperation_type_id.exists' => 'Jenis kerja sama yang dipilih tidak valid.',
            'title.required' => 'Judul proposal wajib diisi.',
            'title.min' => 'Judul proposal minimal 5 karakter.',
            'title.max' => 'Judul proposal maksimal 200 karakter.',
            'description.required' => 'Rincian deskripsi penawaran wajib diisi.',
            'description.min' => 'Rincian deskripsi penawaran minimal 10 karakter.',
            'business_id.required' => 'Target UMKM tujuan kerja sama wajib ditentukan.',
            'business_id.exists' => 'UMKM yang dipilih tidak ditemukan.',
            'partner_profile_id.required' => 'Target Mitra Industri tujuan kerja sama wajib ditentukan.',
            'partner_profile_id.exists' => 'Mitra Industri yang dipilih tidak ditemukan.',
        ];
    }

    /**
     * Validasi bisnis lanjutan: kepemilikan dan target yang sah.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $user = $this->user();
            if (! $user) {
                return;
            }

            if ($user->isMitra()) {
                if (! $user->partnerProfile) {
                    $validator->errors()->add('partner_profile_id', 'Akun Mitra Industri Anda belum memiliki profil organisasi.');
                }

                $businessId = $this->input('business_id');
                if ($businessId) {
                    $business = Business::find($businessId);
                    if ($business && $business->user_id === $user->id) {
                        $validator->errors()->add('business_id', 'Anda tidak dapat mengajukan proposal ke usaha milik sendiri.');
                    }
                }
            } elseif ($user->isUmkm()) {
                if (! $user->business) {
                    $validator->errors()->add('business_id', 'Akun UMKM Anda belum memiliki profil usaha.');
                }

                $partnerId = $this->input('partner_profile_id');
                if ($partnerId) {
                    $partner = PartnerProfile::find($partnerId);
                    if ($partner && $partner->user_id === $user->id) {
                        $validator->errors()->add('partner_profile_id', 'Anda tidak dapat mengajukan proposal ke profil mitra milik sendiri.');
                    }
                }
            } else {
                $validator->errors()->add('role', 'Hanya role UMKM atau Mitra Industri yang dapat mengajukan proposal kolaborasi.');
            }
        });
    }
}
