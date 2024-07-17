<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendantAbsenceRequest extends FormRequest
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
            'absence_reason_id' => [
                'required',
                'integer',
                'exists:absence_reasons,id',
            ],
        ];
    }

    public function createAbsence(\App\Models\Attendant $attendant): \App\Models\AbsenceReason
    {
        \Illuminate\Support\Facades\DB::table('attendant_absence_reason')->insert([
            'attendant_id' => $attendant->id,
            'absence_reason_id' => $this->input('absence_reason_id'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $absence = $attendant->absences()->latest()->firstOrFail();
        return $absence;
    }
}
