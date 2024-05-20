<?php

namespace App\Model\Database;

use App\Model\ApiException;
use Nette\Http\IResponse;
use Throwable;

/**
 *
 */
class DatabaseException extends ApiException
{
    public function __construct(string        $message = "DatabaseError",
                                int           $code = IResponse::S500_InternalServerError, array $content = [])
    {
        parent::__construct($message, $code, $content);
    }
}