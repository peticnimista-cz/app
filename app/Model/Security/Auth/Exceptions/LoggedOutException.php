<?php

namespace App\Model\Security\Auth\Exceptions;

use App\Model\ApiException;

class LoggedOutException extends ApiException
{
    public function __construct(string $message = "You're not logged in.", int $code = 401, array $content = [])
    {
        parent::__construct($message, $code, $content);
    }
}