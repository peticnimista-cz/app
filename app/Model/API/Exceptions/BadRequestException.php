<?php

namespace App\Model\API\Exceptions;

use App\Model\ApiException;


class BadRequestException extends ApiException
{
    public function __construct(string $message = "Bad request parameters (probably POST)", int $code = 400, array $content = [])
    {
        parent::__construct($message, $code, $content);
    }
}