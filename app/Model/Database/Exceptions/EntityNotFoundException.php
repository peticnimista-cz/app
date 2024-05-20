<?php

namespace App\Model\Database\Exceptions;

use App\Model\Database\DatabaseException;
use Nette\Http\IResponse;
use Throwable;

class EntityNotFoundException extends DatabaseException
{
    public function __construct(string $message = "Row with that ID doesn't exists.", int $code = IResponse::S404_NotFound)
    {
        parent::__construct($message, $code);
    }
}