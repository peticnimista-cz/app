<?php

namespace App\Controllers\System\History;

use App\Controllers\CRUDBaseController;
use App\Model\Database\DatabaseManager;
use App\Model\Database\Entity\System\History;

class HistoryController extends CRUDBaseController
{
    /**
     * @param DatabaseManager $databaseManager
     */
    public function __construct(DatabaseManager $databaseManager)
    {
        parent::__construct($databaseManager->getEntityRepository(History::class));
    }
}