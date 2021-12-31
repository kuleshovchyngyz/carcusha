<?php

namespace App\Auth;

use Illuminate\Support\Facades\Cache;

class Code
{
    const VERIFICATION = 'verification';
    const PASSWORD_RESET = 'password_reset';

    protected $key;

    protected $currentType;

    public function __construct()
    {
        $this->key = rand(1000, 9999);
    }

    public function for($type)
    {
        $this->currentType = $type;

        return $this;
    }

    public function matches($code, $type = null)
    {
        return Cache::get($this->getKey($type)) === $code;
    }

    public function generate($type = null)
    {
        $code = rand(1000, 9999);

        Cache::put($this->getKey($type), $code, now()->addHour());

        return $code;
    }

    protected function getKey($type)
    {
        return ($type ?: $this->currentType).'_codes.'.$this->key;
    }
}
