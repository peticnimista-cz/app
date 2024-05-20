<?php

namespace App\Controllers\Test;

use App\Controllers\BaseController;
use App\Model\API\Responses\JsonResponse;
use App\Model\Database\DatabaseManager;
use App\Model\Database\Entity\System\User\UserPermission;
use App\Model\Database\EntityRepository;
use App\Model\Database\Exceptions\EntityNotFoundException;
use App\Model\Security\Auth\AdminAuthenticator;
use App\Router\RouterFactory;
use JetBrains\PhpStorm\NoReturn;
use Nette\Application\AbortException;
use Nette\Security\Passwords;

class TestController extends BaseController
{
    private EntityRepository $userPermissionRepository;

    public function __construct(private AdminAuthenticator $adminAuthenticator, DatabaseManager $databaseManager)
    {
        parent::__construct();

        $this->userPermissionRepository = $databaseManager->getEntityRepository(UserPermission::class);
    }

    /**
     * @throws AbortException|EntityNotFoundException
     */
    #[NoReturn] public function actionTest(): void
    {
        $user = $this->adminAuthenticator->getUser();
        $this->sendResponse(new JsonResponse([
            "timestamp" => time(),
            "request" => [
                "method" => $this->getRequest()->getMethod(),
                "parameters" => $this->getParameters(),
                "http_code" => $this->getHttpResponse()->getCode()
            ],
            "user" => [
                "logged" => (bool)$user,
                "permissions" => $user ? $this->userPermissionRepository->findByColumn(UserPermission::user_id, $user->id) : [],
            ]
        ]));
    }

    /**
     * @return void
     * @throws AbortException
     */
    #[NoReturn] public function actionPost(): void
    {
        $this->sendResponse(new JsonResponse([
            "request" => [
                "method" => $this->getRequest()->getMethod(),
                "rawBody" => (array)json_decode($this->getHttpRequest()->getRawBody()),
                "parameters" => $this->getParameters(),
                "parameter_d" => $this->getParameter("d"),
            ]
        ]));
    }

    /**
     * @throws AbortException
     */
    #[NoReturn] public function actionPassword(): void
    {
        $password = "kolokolo";
        $this->sendResponse(new JsonResponse([
            "hashed" => (new Passwords())->hash($password),
            "password" => $password,
        ]));
    }

    #[NoReturn] public function actionSitemap(): void
    {
        $this->sendResponse(new JsonResponse(RouterFactory::$sitemap));
    }
}