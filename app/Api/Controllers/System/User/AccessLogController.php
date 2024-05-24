<?php

namespace App\Controllers\System\User;

use App\Controllers\CRUDBaseController;
use App\Model\Database\DatabaseManager;
use App\Model\Database\Entity\System\AccessLog;

class AccessLogController extends CRUDBaseController
{
    public function __construct(DatabaseManager $databaseManager)
    {
        parent::__construct($databaseManager->getEntityRepository(AccessLog::class));
    }
}