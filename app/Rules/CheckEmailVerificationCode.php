<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\AuthConfirmation;

class CheckEmailVerificationCode implements Rule
{
    public $error = false;
    public $errorMsg = 'Неверный код!';

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if($auth = AuthConfirmation::where('code', $value)->first())
        {
            $this->errorMsg = 'Вы уже верифицированы!';
            if(!$auth->is_confirmed){
                $this->error = true;
            }
        }

        return $this->error;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->errorMsg;
    }
}
