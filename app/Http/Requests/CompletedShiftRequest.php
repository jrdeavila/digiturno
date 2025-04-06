<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompletedShiftRequest extends FormRequest
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
            'services' => 'sometimes|array',
            'services.*' => 'required|exists:services,id',
        ];
    }

    public function completedShift(\App\Models\Shift $shift)
    {

        \Illuminate\Support\Facades\DB::beginTransaction();
        $shift->update([
            'state' => \App\Enums\ShiftState::Completed,
        ]);

        $shift->services()->sync($this->services);

        \Illuminate\Support\Facades\DB::commit();

        return $shift;
    }
}
