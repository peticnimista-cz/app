<?php

namespace App\Model\Database\Manager;

use App\Model\Database\DatabaseManager;
use App\Model\Database\Entity\System\User\UserPermission;
use App\Model\Database\EntityRepository;
use App\Model\Security\Auth\Permissions;

class PermissionManager
{
    private EntityRepository $permissionRepository;

    public function __construct(DatabaseManager $databaseManager)
    {
        $this->permissionRepository = $databaseManager->getEntityRepository(UserPermission::class);
    }

    /**
     * Return permissions of user in node list
     * Ex. ["permission.subpermission", "permission2.subpermission"]
     * @param int $userId
     * @return array
     */
    public function getUserPermissionsList(int $userId): array
    {
        $permissionsRows = $this->permissionRepository->findByColumn(UserPermission::user_id, $userId)
                ->fetchAll();
        return Permissions::getNodeList($permissionsRows);
    }

    /**
     * @param $permissionList
     * @param $permissionNode
     * @return bool
     */
    public function containsPermission($permissionList, $permissionNode): bool
    {
        return in_array(Permissions::FULL_PERMISSIONS, $permissionList) || in_array($permissionNode, $permissionList);
    }
}