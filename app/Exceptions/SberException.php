<?php

namespace App\Exceptions;


use Exception;
use Throwable;

class SberException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $message = 'Ошибка Сбербанка: '.$message;
        parent::__construct($message, $code, $previous);
    }
}
