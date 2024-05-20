<?php

namespace App\Model\Database;

use App\Model\Security\Auth\PermissionSection;

class EntityTable
{
    /**
     * @param string $entityClass
     * @param string $tableName
     * @param PermissionSection $permissionSection
     * @param array $columns
     * @param array|null $hiddenColumns
     */
    public function __construct(private string $entityClass, private string $tableName, private PermissionSection $permissionSection, private array $columns, private ?array $hiddenColumns = [])
    {
        $this->columns = array_values($this->columns);
    }

    /**
     * @return array|null
     */
    public function getHiddenColumns(): ?array
    {
        return $this->hiddenColumns;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * @return PermissionSection
     */
    public function getPermissionSection(): PermissionSection
    {
        return $this->permissionSection;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function __toString(): string
    {
        return $this->getTableName();
    }
}