<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Memastikan hanya pengguna yang sudah login yang bisa membuat pesanan.
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
            // Validasi sekarang berdasarkan room_type_id
            'room_type_id' => 'required|exists:room_types,id',

            // Aturan lain yang dikirim dari form modal
            "guest_name" => "required|min:3|max:255",
            'total_person' => "required|integer|min:1",
            'from_date' => 'required|date|after_or_equal:today',
            'to_date' => 'required|date|after:from_date'
        ];
    }
}
