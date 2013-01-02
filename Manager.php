<?php
namespace SMGregsList;
class Manager
{
    protected $code;
    protected $name;

    function __set($name, $value)
    {
        if ($name == 'code') {
            $this->code = $value;
            return;
        }
        if ($name == 'name') {
            $this->name = $value;
            return;
        }
        throw new \Exception('Internal error: unknown manager variable: ' . $name);
    }

    function getName()
    {
        return $this->name;
    }

    function getCode()
    {
        return $this->code;
    }

    function generateCode()
    {
        $this->code = base_convert(strtoupper($this->name), 36, 35) . '-' .
            base_convert(bin2hex(openssl_random_pseudo_bytes(5)), 16, 36);
    }
}