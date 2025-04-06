<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendToInProgressRequest extends FormRequest
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
            'module_id' => ['required', 'exists:modules,id'],
        ];
    }

    public function sendToInProgress(
        \App\Models\Shift $shift
    ): \App\Models\Shift {
        // Verify if the module is busy

        $module = \App\Models\Module::find($this->input('module_id'));
        $hasShiftsInProgress = \App\Models\Shift::where('module_id', $module->id)
            ->whereIn('state', [\App\Enums\ShiftState::InProgress, \App\Enums\ShiftState::Completed])
            ->exists();

        if ($hasShiftsInProgress) {
            throw new \App\Exceptions\ModuleAlreadyInProgressException();
        }
        $shift->update([
            'state' => \App\Enums\ShiftState::InProgress,
            'module_id' => $module->id
        ]);

        $shift->module?->currentAttendant()?->update([
            'status' => \App\Enums\AttendantStatus::Busy,
        ]);

        return $shift;
    }
}
