<?php

namespace App\Model\CRUD;

use App\Model\API\Action;
use App\Model\API\Exceptions\BadRequestException;
use App\Model\API\Responses\JsonResponse;
use App\Model\Database\DatabaseManager;
use App\Model\Database\Entity;
use App\Model\Database\EntityRepository;
use App\Model\Database\EntityTable;
use App\Model\Database\Exceptions\EntityNotFoundException;
use App\Model\Database\Manager\Logging\Changelog;
use App\Model\Database\Manager\Logging\HistoryManager;
use App\Model\Security\Auth\AdminAuthenticator;
use App\Model\Security\Auth\Exceptions\AuthorizationFailedException;
use App\Model\Security\Auth\Exceptions\LoggedOutException;
use App\Model\Security\Auth\Permissions;
use Nette\Database\DriverException;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\JsonException;

/**
 * Validate and transform requests/responses (for example disable option for changing id or edited_by_user_id (generated cols) by patch
 */
class CrudTransformerd
{

    private EntityRepository $permissionRepository;

    public function __construct(private AdminAuthenticator $adminAuthenticator, DatabaseManager $databaseManager, private HistoryManager $historyManager)
    {
        $this->permissionRepository = $databaseManager->getEntityRepository(Entity\System\User\UserPermission::class);
    }

    /**
     * @throws AuthorizationFailedException|EntityNotFoundException|LoggedOutException
     */
    public function validateUserAuthentication(string $permissionNode): ActiveRow
    {
        $user = $this->adminAuthenticator->getUser();
        if($user) {
            $userPermissions = $this->permissionRepository->findByColumn(Entity\System\User\UserPermission::user_id, $user->id);
            /**
             * @var $userPermissions Entity\System\User\UserPermission[]
             */
            foreach ($userPermissions as $userPermission) {
                if(Permissions::hasPermission($userPermission->permission_node, $permissionNode)) return $user;
            }
            throw new AuthorizationFailedException();
        } else {
            throw new LoggedOutException();
        }
    }

    /**
     * Validating for POST/PATCH
     * @param EntityTable $entityTable
     * @param array $data
     * @return string Message (for warning or information)
     * @throws BadRequestException
     */
    private static function validateDataPayload(EntityTable $entityTable, array &$data): string
    {
        $message = "";
        $columns = $entityTable->getColumns(); // check if data contains valid columns for that entity
        foreach ($data as $column => $value) {
            if(!in_array($column, $columns)) throw new BadRequestException("Sent parameters in POST/PATCH are invalid");
            if(array_key_exists(Entity::id, $data)) {
                $message = " Don't use ID column in parameters of PATCH/POST action. ID cannot be updated.";
                unset($data[Entity::id]);
            }
        }
        return $message;
    }

    /**
     * @param EntityRepository $entityRepository
     * @param int $id
     * @return JsonResponse
     * @throws EntityNotFoundException|AuthorizationFailedException
     * @throws LoggedOutException
     */
    public function getByID(EntityRepository $entityRepository, int $id): JsonResponse {
        $table = $entityRepository->getTable();
        $readNode = $table->getPermissionSection()->getReadNode();
        $this->validateUserAuthentication($readNode);

        $activeRow = $entityRepository->findById($id);
        $arrayRow = $activeRow->toArray();
        $hiddenColumns = $table->getHiddenColumns();
        foreach ($hiddenColumns as $hiddenColumn) { // for example hide password in user
            unset($arrayRow[$hiddenColumn]);
        }

        return new JsonResponse($arrayRow, 200, "Entity with ID " . $id . " was successfully found");
    }

    /**
     * @param EntityRepository $entityRepository
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationFailedException
     * @throws EntityNotFoundException
     * @throws LoggedOutException|JsonException
     */
    public function deleteByID(EntityRepository $entityRepository, int $id): JsonResponse {
        $table = $entityRepository->getTable();
        $editNode = $entityRepository->getTable()->getPermissionSection()->getEditNode();
        $user = $this->validateUserAuthentication($editNode);
        $row = $entityRepository->findById($id);

        $deletedRow = $entityRepository->deleteById($id);
        $this->historyManager->log($table, $id, $user->id, Action::DELETE, new Changelog($row->toArray(), []));
        return new JsonResponse([], 200, "Entity with ID " . $id . " was successfully deleted");
    }

    /**
     * @param EntityRepository $entityRepository
     * @param int $id
     * @param array $data
     * @return JsonResponse
     * @throws AuthorizationFailedException
     * @throws EntityNotFoundException
     * @throws LoggedOutException
     * @throws JsonException
     * @throws BadRequestException
     */
    public function patchByID(EntityRepository $entityRepository, int $id, array $data): JsonResponse
    {
        $table = $entityRepository->getTable();
        $editNode = $table->getPermissionSection()->getEditNode();
        $user = $this->validateUserAuthentication($editNode);

        $msg = self::validateDataPayload($entityRepository->getTable(), $data);
        $oldRow = $entityRepository->findById($id);
        $entityRepository->updateById($id, $data);

        $newRow = $oldRow->toArray();
        foreach ($data as $column => $value) $newRow[$column] = $value;

        $this->historyManager->log($table, $id, $user->id, Action::PATCH, new Changelog($oldRow->toArray(), $newRow));
        return new JsonResponse([], 200, "Entity with ID " . $id . " was successfully updated." . $msg);
    }

    /**
     * @throws EntityNotFoundException
     * @throws LoggedOutException
     * @throws AuthorizationFailedException|JsonException
     * @throws BadRequestException
     */
    public function post(EntityRepository $entityRepository, array $data): JsonResponse
    {
        $table = $entityRepository->getTable();
        $editNode = $table->getPermissionSection()->getEditNode();
        $user = $this->validateUserAuthentication($editNode);

        self::validateDataPayload($entityRepository->getTable(), $data);

        try {
            $row = $entityRepository->insert($data);
        } catch (DriverException $driverException) {
            throw new BadRequestException($driverException->getDriverCode() . ": " . $driverException->getMessage());
        }

        $this->historyManager->log($table, $row->id, $user->id, Action::POST, new Changelog([], $data)); // todo: maybe problem must be tsted
        return new JsonResponse([], 201, "Row with ID" . $row->id . " was successfully created");
    }

    /**
     * @throws EntityNotFoundException
     * @throws LoggedOutException
     * @throws AuthorizationFailedException
     */
    public function getAll(EntityRepository $entityRepository): JsonResponse
    {
        $table = $entityRepository->getTable();
        $readNode = $table->getPermissionSection()->getReadNode();

        $this->validateUserAuthentication($readNode);

        $assoc = $entityRepository->findAll()->fetchAssoc("id");
        $hiddenColumns = $table->getHiddenColumns();
        if($hiddenColumns) {
            foreach (array_keys($assoc) as $id) {
                foreach ($hiddenColumns as $h_column) {
                    unset($assoc[$id][$h_column]);
                }
            }
        }
        return new JsonResponse($assoc, 200);
    }
}