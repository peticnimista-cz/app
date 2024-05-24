<?php

namespace App\Model\API\Exceptions;

use App\Model\ApiException;
use Nette\Http\IResponse;

class MethodNotAllowed extends ApiException
{
    public function __construct(string $method, array $allowedMethods)
    {
        parent::__construct("Method " . $method . " is not allowed.", IResponse::S405_MethodNotAllowed);

        $this->content = [
            "allowedMethods" => $allowedMethods
        ];
    }
}