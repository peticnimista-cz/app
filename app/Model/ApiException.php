<?php

namespace App\Model;

abstract class ApiException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, public array $content = [])
    {
        parent::__construct($message, $code);

        $this->message = $message;
        $this->code = $code;
        $this->content = $content;
    }
}