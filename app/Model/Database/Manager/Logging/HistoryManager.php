<?php

namespace App\Model\Database\Manager\Logging;

use App\Model\Database\DatabaseManager;
use App\Model\Database\Entity\System\History;
use App\Model\Database\EntityRepository;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\JsonException;

class HistoryManager
{
    private EntityRepository $entityRepository;

    public function __construct(DatabaseManager $databaseManager)
    {
        $this->entityRepository = $databaseManager->getEntityRepository(History::class);
    }

    /**
     * @throws JsonException
     */
    public function log(string $tableName, int $rowId, int $userId, string $type, Changelog $changelog): void
    {
        $this->entityRepository->insert([
            History::table => $tableName,
            History::row_id => $rowId,
            History::action_type => $type,
            History::changes => $changelog->json(),
            History::user_id => $userId,
            History::time => new \DateTime()
        ]);
    }

    /**
     * @param int $userId
     * @return array|ActiveRow[]
     */
    public function getUserRows(int $userId): array
    {
        $all = $this->entityRepository->findByColumn(History::user_id, $userId)->order("id DESC")->fetchAll();
        $object = [];
        foreach($all as $row) {
            $rowArray = $row->toArray();
            $rowArray[History::changes] = json_decode($rowArray[History::changes]);
            $object[] = $rowArray;
        }
        return $object;
    }
}