<?php

namespace App\Model\Security\Auth;

use App\Model\Database\Entity\System\User\UserPermission;
use Nette\Database\Table\ActiveRow;

final class Permissions
{
    const FULL_PERMISSIONS = "*";

    const DATA_MATERIAL_EDIT = "data.material.edit";
    const DATA_MATERIAL_READ = "data.material.read";
    const DATA_PROJEKT_READ = "data.projekt.read";
    const DATA_PROJEKT_EDIT = "data.projekt.edit";

    const DATA_CISTIC_EDIT = "data.cistic.edit";
    const DATA_CISTIC_READ = "data.cistic.read";

    const DATA_REDIDLO_EDIT = "data.redidlo.edit";
    const DATA_REDIDLO_READ = "data.redidlo.read";

    const DATA_LEPIDLO_EDIT = "data.lepidlo.edit";
    const DATA_LEPIDLO_READ = "data.lepidlo.read";

    const DATA_GRANULAT_EDIT = "data.granulat.edit";
    const DATA_GRANULAT_READ = "data.granulat.read";

    const DATA_KLUZNY_LAK_EDIT = "data.kluzny_lak.edit";
    const DATA_KLUZNY_LAK_READ = "data.kluzny_lak.read";

    const DYN_GLOBAL_NOTES = "data.global_notes"; // global notes

    const SYSTEM_USER_MANAGEMENT = "system.user.management";

    public static function getDataBox(): array {
        return [
            self::DATA_MATERIAL_EDIT => "Správa materiálů",
            self::DATA_MATERIAL_READ => "Zobrazení materiálů",

            self::DATA_PROJEKT_EDIT => "Správa projektů",
            self::DATA_PROJEKT_READ => "Zobrazení projektů",

            self::DATA_CISTIC_EDIT => "Úprava čističů",
            self::DATA_CISTIC_READ => "Zobrazení čističů",

            self::DATA_REDIDLO_EDIT => "Úprava ředidel",
            self::DATA_REDIDLO_READ => "Zobrazení ředidel",

            self::DATA_LEPIDLO_EDIT => "Úprava lepidel",
            self::DATA_LEPIDLO_READ => "Zobrazení lepidel",

            self::DATA_GRANULAT_EDIT => "Úprava granulátů",
            self::DATA_GRANULAT_READ => "Zobrazení granulátů",

            self::DATA_KLUZNY_LAK_EDIT => "Úprava kluzných laků",
            self::DATA_KLUZNY_LAK_READ => "Zobrazení kluzných laků",

            self::DYN_GLOBAL_NOTES => "Nastavování globálních poznámek (pro všechny)",
        ];
    }

    /**
     * @param array|ActiveRow[] $permissionsRows
     * @return array
     */
    public static function getNodeList(array $permissionsRows): array
    {
        $nodeList = [];
        foreach ($permissionsRows as $row) {
            $nodeList[] = $row->{UserPermission::permission_node};
        }
        return $nodeList;
    }

    public static function hasPermission(string $node, string $permissionConstant): bool
    {
        return $node === self::FULL_PERMISSIONS || $node === $permissionConstant;
    }

    public static function getSystemBox(): array {
        return [
            self::SYSTEM_USER_MANAGEMENT => "Správa uživatelů"
        ];
    }
}