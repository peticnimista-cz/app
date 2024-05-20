<?php

namespace App\Controllers\System\User;

use App\Controllers\CRUDBaseController;
use App\Model\API\Action;
use App\Model\API\Exceptions\BadRequestException;
use App\Model\API\Responses\JsonResponse;
use App\Model\Database\DatabaseManager;
use App\Model\Database\Entity\System\User;
use App\Model\Database\Exceptions\EntityNotFoundException;
use App\Model\Database\Manager\Logging\HistoryManager;
use App\Model\Database\Manager\PermissionManager;
use App\Model\Security\Auth\AdminAuthenticator;
use App\Model\Security\Auth\Exceptions\AuthorizationFailedException;
use App\Model\Security\Auth\Permissions;
use JetBrains\PhpStorm\NoReturn;
use Nette\Application\AbortException;

// todo: fix to not viewing passwords in get (somethingl iek forbidden columns)
class UserController extends CRUDBaseController
{

    /**
     * @param DatabaseManager $databaseManager
     * @param AdminAuthenticator $adminAuthenticator
     * @param HistoryManager $historyManager
     * @param PermissionManager $permissionManager
     */
    public function __construct(DatabaseManager $databaseManager, private AdminAuthenticator $adminAuthenticator, private HistoryManager $historyManager, private PermissionManager $permissionManager)
    {
        parent::__construct($databaseManager->getEntityRepository(User::class));
    }

    /**
     * @throws AbortException
     */
    #[NoReturn] public function actionHistory(int $id): void
    {
        $this->triggerResponse([
            Action::GET => function () use($id) {
                $user = $this->adminAuthenticator->getUser();
                $userPermissions = $this->permissionManager->getUserPermissionsList($user->id);
                if($user === $id || $this->permissionManager->containsPermission($userPermissions, Permissions::SYSTEM_USER_MANAGEMENT)) {
                    return new JsonResponse($this->historyManager->getUserRows($user->id), 200, "History list of user " . $user->id);
                }
                throw new AuthorizationFailedException();
            }
        ]);
    }

    /**
     * @throws AbortException
     */
    #[NoReturn] public function actionLogin(): void
    {
        $this->triggerResponse([
            Action::POST => (function() {
                $rawBody = $this->getRawBody();
                if(!isset($rawBody["email"]) || !isset($rawBody["password"])) throw new BadRequestException();
                $email = $rawBody["email"];
                $password = $rawBody["password"];
                $this->adminAuthenticator->login([$email, $password]);
                $user = $this->adminAuthenticator->getUser();
                return new JsonResponse([
                    "logged" => (bool)$user,
                    "id" => $user->id,
                ], 200, "You was successfully logged into app.");
            })
        ]);
    }

    /**
     * @throws EntityNotFoundException
     * @throws AbortException
     */
    #[NoReturn] public function actionInfo(): void
    {
        $user = $this->adminAuthenticator->getUser();
        $this->triggerResponse([
            Action::GET => fn() => new JsonResponse([
                "logged" => (bool)$user,
                "id" => $user?->id,
            ])
        ]);
    }

    /**
     * @throws AbortException
     */
    #[NoReturn] public function actionLogout(): void
    {
        $this->triggerResponse([
            Action::POST => (function() {
                    $this->adminAuthenticator->logout();
                    return new JsonResponse("", 200, "You was successfully logged out.");
            })
        ]);
    }
}