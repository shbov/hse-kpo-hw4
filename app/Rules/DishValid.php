<?php

namespace App\Rules;

use App\Models\Dish;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class DishValid implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        foreach (['id', 'quantity'] as $key) {
            if (!array_key_exists($key, $value)) {
                $fail("Field `{$key}` is required");
                return;
            }

            if (!is_int($value[$key]) || $value[$key] < 0) {
                $fail("Field `{$key}` must be integer and non-negative");
                return;
            }
        }

        $dish = Dish::where('id', $value['id'])->first();
        if (!$dish) {
            $fail("Dish not found");
            return;
        }

        if (!$dish->is_available) {
            $fail('Dish is not available');
        }

        if ($dish->quantity < $value['quantity']) {
            $fail('Insufficient number of dish');
        }
    }
}
