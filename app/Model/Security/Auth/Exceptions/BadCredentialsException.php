<?php

namespace App\Model\Security\Auth\Exceptions;

use App\Model\ApiException;

class BadCredentialsException extends ApiException
{
    public function __construct(string $message = "Authentication failed", int $code = 401, array $content = [])
    {
        parent::__construct($message, $code, $content);
    }
}