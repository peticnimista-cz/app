<?php

namespace App\Controllers\Error;

use App\Model\API\Responses\JsonResponse;
use Nette;
use Tracy\ILogger;

final class ErrorController implements Nette\Application\IPresenter
{
    public function __construct(
        private ILogger $logger,
    ) {
    }


    public function run(Nette\Application\Request $request): Nette\Application\Response
    {
        $exception = $request->getParameter('exception');
        $this->logger->log($exception, ILogger::EXCEPTION);
        return JsonResponse::error($exception);
    }
}