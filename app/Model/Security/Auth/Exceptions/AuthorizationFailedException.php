<?php

namespace App\Model\Security\Auth\Exceptions;

use App\Model\ApiException;

class AuthorizationFailedException extends ApiException
{
    public function __construct(string $message = "Authorization failed", int $code = 403, array $content = [])
    {
        parent::__construct($message, $code, $content);
    }
}