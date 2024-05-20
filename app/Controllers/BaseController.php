<?php

namespace App\Controllers;

use App\Model\API\Exceptions\MethodNotAllowed;
use App\Model\API\Responses\JsonResponse;
use App\Model\ApiException;
use JetBrains\PhpStorm\NoReturn;
use Nette\Application\AbortException;
use Nette\Application\UI\Presenter;

abstract class BaseController extends Presenter
{
    /**
     * @return void
     * @throws AbortException
     * @var array $responses ACTION (POST,GET...) => (fn() => IResponse)
     */
    #[NoReturn] protected function triggerResponse(array $responses): void
    {
        $action = $this->getRequest()->getMethod();
        // check if endpoint allowed that method/action
        try {
            if(!in_array($action, array_keys($responses))) throw new MethodNotAllowed($action, array_keys($responses));
            $this->sendResponse($responses[$action]()); // send final response
        } catch (ApiException $exception) { // dont user never ever AbortException
            $this->sendResponse(JsonResponse::error($exception));
        }
    }

    protected function getRawBody(): ?array
    {
        return (array)json_decode($this->getHttpRequest()->getRawBody());
    }
}