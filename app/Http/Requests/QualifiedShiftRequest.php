<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QualifiedShiftRequest extends FormRequest
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
            'qualification' => 'required|integer|between:0,4',
        ];
    }


    public function createQualification()
    {
        $moduleAssignation = $this->route('shift')->moduleAssignations()->first();
        \throw_unless($moduleAssignation, \App\Exceptions\ShiftDontHaveModuleAssignedException::class);
        $qualification = $this->getQualificationOption($this->input('qualification'));
        $moduleAssignation->qualifications()->create([
            'qualification' => $qualification->value,
        ]);
        $moduleAssignation->update([
            'state' => \App\Enums\ShiftModuleAssignationState::Completed,
        ]);
        $this->route('shift')->update([
            'state' => \App\Enums\ShiftState::Qualified,
        ]);
    }

    private function getQualificationOption(int $qualification): \App\Enums\QualificationOption
    {
        echo $qualification;
        switch ($qualification) {
            case 0:
                return \App\Enums\QualificationOption::NotQualified;
            case 1:
                return \App\Enums\QualificationOption::Bad;
            case 2:
                return \App\Enums\QualificationOption::Regular;
            case 3:
                return \App\Enums\QualificationOption::Good;
            case 4:
                return \App\Enums\QualificationOption::Excellent;
            default:
                return \App\Enums\QualificationOption::NotQualified;
        }
    }
}
