<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class EnMmUnicode implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // English (a-zA-Z), Numbers (0-9), Spaces (\s) နှင့် Myanmar Unicode Range
        $pattern = '/^[a-zA-Z0-9\s\x{1000}-\x{109F}\x{AA60}-\x{AA7F}\-_\.,\(\)]+$/u';

        return (bool) preg_match($pattern, (string) $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute တွင် အင်္ဂလိပ်စာလုံး သို့မဟုတ် မြန်မာစာ Unicode များကိုသာ ခွင့်ပြုပါသည်။';
    }
}