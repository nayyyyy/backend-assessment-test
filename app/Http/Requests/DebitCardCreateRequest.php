<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\DebitCard;
use Illuminate\Foundation\Http\FormRequest;

class DebitCardCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', DebitCard::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => 'required|string',
        ];
    }
}
