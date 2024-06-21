<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\DebitCard;
use Illuminate\Foundation\Http\FormRequest;

class DebitCardUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('debit_card'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'is_active' => 'required|boolean',
        ];
    }
}
