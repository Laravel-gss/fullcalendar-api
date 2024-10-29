<?php

namespace App\Rules\Api;

use App\Enums\Api\FullCalendarEventStatus;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidFullCalendarEventStatus implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $validValues = array_column(FullCalendarEventStatus::cases(), 'value');

        if (!in_array($value, $validValues, true)) {
            $fail(__('validation.enum_value', [
                'attribute' => $attribute,
                'values' => implode(', ', $validValues),
            ]));
        }
    }
}
