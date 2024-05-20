<?php

namespace App\Model\Database;

use App\Model\Database\Entity\Data\_Pripravky\Cistic\CisticVyrobce;
use App\Model\Database\Entity\Data\_Pripravky\Granulat\GranulatVyrobce;
use App\Model\Database\Entity\Data\_Pripravky\KluznyLak\KluznyLakSlozeniMnozstvi;
use App\Model\Database\Entity\Data\_Pripravky\Cistic\Cistic;
use App\Model\Database\Entity\Data\_Pripravky\Cistic\CisticSlozeniMnozstvi;
use App\Model\Database\Entity\Data\_Pripravky\Cistic\CisticSlozeniLatka;
use App\Model\Database\Entity\Data\_Pripravky\KluznyLak\KluznyLakVyrobce;
use App\Model\Database\Entity\Data\_Pripravky\Lepidlo\Lepidlo;
use App\Model\Database\Entity\Data\_Pripravky\Lepidlo\LepidloSlozeniLatka;
use App\Model\Database\Entity\Data\_Pripravky\Lepidlo\LepidloSlozeniMnozstvi;
use App\Model\Database\Entity\Data\_Pripravky\Lepidlo\LepidloVyrobce;
use App\Model\Database\Entity\Data\_Pripravky\Redidlo\Redidlo;
use App\Model\Database\Entity\Data\_Pripravky\Redidlo\RedidloSlozeniLatka;
use App\Model\Database\Entity\Data\_Pripravky\Redidlo\RedidloSlozeniMnozstvi;
use App\Model\Database\Entity\Data\_Pripravky\Redidlo\RedidloVyrobce;
use App\Model\Database\Entity\Data\Material\Material;
use App\Model\Database\Entity\Data\Material\MaterialTyp;
use App\Model\Database\Entity\Data\Projekt\Projekt;
use App\Model\Database\Entity\Data\Projekt\ProjektSklo;
use App\Model\Database\Entity\Data\Projekt\ProjektTrh;
use App\Model\Database\Entity\Dynamic\RowNote;
use App\Model\Database\Entity\Dynamic\ViewNote;
use App\Model\Database\Entity\Data\_Pripravky\Granulat\Granulat;
use App\Model\Database\Entity\Data\_Pripravky\KluznyLak\KluznyLak;
use App\Model\Database\Entity\Data\_Pripravky\KluznyLak\KluznyLakSlozeniLatka;
use App\Model\Database\Entity\Data\_Pripravky\Granulat\GranulatSlozeniMnozstvi;
use App\Model\Database\Entity\Data\_Pripravky\Granulat\GranulatSlozeniLatka;
use App\Model\Database\Entity\Data\_Pripravky\Granulat\GranulatTyp;
use App\Model\Database\Entity\System\AccessLog;
use App\Model\Database\Entity\System\History;
use App\Model\Database\Entity\System\User;
use App\Model\Security\Auth\Permissions;
use App\Model\Security\Auth\PermissionSection;

class DataStructure
{
	public static function entities(): array
    {
        $userSection = new PermissionSection("Uživatel", Permissions::SYSTEM_USER_MANAGEMENT, Permissions::SYSTEM_USER_MANAGEMENT);
        $notes = new PermissionSection("Poznámky", Permissions::DYN_GLOBAL_NOTES, Permissions::DYN_GLOBAL_NOTES);
        $cistic = new PermissionSection("Čistič", Permissions::DATA_CISTIC_READ, Permissions::DATA_CISTIC_EDIT);
        $granulat = new PermissionSection("Granulát", Permissions::DATA_GRANULAT_READ, Permissions::DATA_GRANULAT_EDIT);
        $kluzny_lak = new PermissionSection("Kluzný lak", Permissions::DATA_KLUZNY_LAK_READ, Permissions::DATA_KLUZNY_LAK_EDIT);
        $lepidlo = new PermissionSection("Lepidlo", Permissions::DATA_LEPIDLO_READ, Permissions::DATA_LEPIDLO_EDIT);
        $redidlo = new PermissionSection("Ředidlo", Permissions::DATA_REDIDLO_READ, Permissions::DATA_REDIDLO_EDIT);
        $material = new PermissionSection("Material", Permissions::DATA_MATERIAL_READ, Permissions::DATA_MATERIAL_EDIT);
        $projekt = new PermissionSection("Projekt", Permissions::DATA_PROJEKT_READ, Permissions::DATA_PROJEKT_EDIT);

        return
            [
                AccessLog::class => new EntityTable(AccessLog::class, "sys_access_log", $userSection, AccessLog::getColumns()),
                History::class => new EntityTable(History::class, "sys_history", $userSection, History::getColumns()),
                User::class => new EntityTable(User::class, "sys_user", $userSection, User::getColumns(), [User::password]),
                User\UserPermission::class => new EntityTable(User\UserPermission::class,"sys_user_permission", $userSection, User\UserPermission::getColumns()),

                RowNote::class => new EntityTable(RowNote::class,"dyn_row_note", $notes, RowNote::getColumns()),
                ViewNote::class => new EntityTable(ViewNote::class, "dyn_view_note", $notes, RowNote::getColumns()),

                Cistic::class => new EntityTable(Cistic::class, "data_cistic", $cistic, Cistic::getColumns()),
                CisticSlozeniMnozstvi::class => new EntityTable(CisticSlozeniMnozstvi::class, "data_cistic_slozeni_mnozstvi", $cistic, CisticSlozeniMnozstvi::getColumns()),
                CisticSlozeniLatka::class => new EntityTable(CisticSlozeniLatka::class, "data_cistic_slozeni_latka", $cistic, CisticSlozeniLatka::getColumns()),
                CisticVyrobce::class => new EntityTable(CisticVyrobce::class, "data_cistic_vyrobce", $cistic, CisticVyrobce::getColumns()),

                Granulat::class => new EntityTable(Granulat::class,"data_granulat", $granulat, Granulat::getColumns()),
                GranulatSlozeniMnozstvi::class => new EntityTable(GranulatSlozeniMnozstvi::class, "data_granulat_slozeni_mnozstvi", $granulat, GranulatSlozeniMnozstvi::getColumns()),
                GranulatTyp::class => new EntityTable(GranulatTyp::class, "data_granulat_typ", $granulat, GranulatTyp::getColumns()),
                GranulatSlozeniLatka::class => new EntityTable(GranulatSlozeniLatka::class, "data_granulat_slozeni_latka", $granulat, GranulatSlozeniLatka::getColumns()),
                GranulatVyrobce::class => new EntityTable(Entity\Data\_Pripravky\Granulat\GranulatVyrobce::class, "data_granulat_vyrobce", $granulat, GranulatVyrobce::getColumns()),

                KluznyLak::class => new EntityTable(KluznyLak::class, "data_kluzny_lak", $kluzny_lak, KluznyLak::getColumns()),
                KluznyLakSlozeniMnozstvi::class => new EntityTable(KluznyLakSlozeniMnozstvi::class, "data_kluzny_lak_slozeni_mnozstvi", $kluzny_lak, KluznyLakSlozeniMnozstvi::getColumns()),
                KluznyLakSlozeniLatka::class => new EntityTable(KluznyLakSlozeniLatka::class, "data_kluzny_lak_slozeni_latka", $kluzny_lak, KluznyLakSlozeniLatka::getColumns()),
                KluznyLakVyrobce::class => new EntityTable(KluznyLakVyrobce::class, "data_kluzny_lak_vyrobce", $kluzny_lak, KluznyLakVyrobce::getColumns()),

                Lepidlo::class => new EntityTable(Lepidlo::class, "data_lepidlo", $lepidlo, Lepidlo::getColumns()),
                LepidloSlozeniMnozstvi::class => new EntityTable(LepidloSlozeniMnozstvi::class, "data_lepidlo_slozeni_mnozstvi", $lepidlo, LepidloSlozeniMnozstvi::getColumns()),
                LepidloSlozeniLatka::class => new EntityTable(LepidloSlozeniLatka::class, "data_lepidlo_slozeni_latka", $lepidlo, LepidloSlozeniLatka::getColumns()),
                LepidloVyrobce::class => new EntityTable(LepidloVyrobce::class, "data_lepidlo_vyrobce", $lepidlo, LepidloVyrobce::getColumns()),

                Redidlo::class => new EntityTable(Redidlo::class, "data_redidlo", $redidlo, Redidlo::getColumns()),
                RedidloSlozeniMnozstvi::class => new EntityTable(RedidloSlozeniMnozstvi::class, "data_redidlo_slozeni_mnozstvi", $redidlo, RedidloSlozeniMnozstvi::getColumns()),
                RedidloSlozeniLatka::class => new EntityTable(RedidloSlozeniLatka::class, "data_redidlo_slozeni_latka", $redidlo, RedidloSlozeniLatka::getColumns()),
                RedidloVyrobce::class => new EntityTable(RedidloVyrobce::class, "data_redidlo_vyrobce", $redidlo, RedidloVyrobce::getColumns()),

                // materials, projects, technical lists
                Material::class => new EntityTable(Material::class, "data_material", $material, Material::getColumns()),
                MaterialTyp::class => new EntityTable(MaterialTyp::class,"data_material_typ", $material, MaterialTyp::getColumns()),

                Projekt::class => new EntityTable(Projekt::class, "data_projekt", $projekt, Projekt::getColumns()),
                ProjektSklo::class => new EntityTable(ProjektSklo::class, "data_projekt_sklo", $projekt, ProjektSklo::getColumns()),
                ProjektTrh::class => new EntityTable(ProjektTrh::class, "data_projekt_trh", $projekt, ProjektTrh::getColumns()),
            ];
    }
}
