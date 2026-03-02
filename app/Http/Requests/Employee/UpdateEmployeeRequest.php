<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
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
        info($this->all());
        return [
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:employees,email,' . $this->route('employee')->id],
            'phone'         => ['required', 'string', 'max:255'],
            'birth_date'    => ['required'],
            'position'      => ['required', 'string', 'max:255'],
            'photo'         => ['nullable'],
            'address'       => ['required', 'string', 'max:255'],
        ];
    }
}
