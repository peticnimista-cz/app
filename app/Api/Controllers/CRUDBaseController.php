<?php

namespace App\Controllers;

use App\Model\API\Action;
use App\Model\CRUD\CrudTransformer;
use App\Model\Database\EntityRepository;
use JetBrains\PhpStorm\NoReturn;
use Nette\Application\AbortException;
use Nette\DI\Attributes\Inject;

abstract class CRUDBaseController extends BaseController
{
    #[Inject]
    public CrudTransformer $crudTransformer;

    public function __construct(protected EntityRepository $entityRepository)
    {
        parent::__construct();
    }

    /**
     * @throws AbortException
     */
    #[NoReturn] public function actionOne(int $id): void {
        $this->triggerResponse([
            Action::GET => fn() => $this->crudTransformer->getByID($this->entityRepository, $id),
            Action::DELETE => fn() => $this->crudTransformer->deleteByID($this->entityRepository, $id),
            Action::PATCH => fn() => $this->crudTransformer->patchByID($this->entityRepository, $id, (array)json_decode($this->getHttpRequest()->getRawBody())), // TODO: add data
        ]);
    }

    /**
     * @throws AbortException
     */
    #[NoReturn] public function actionAll(): void {
        $this->triggerResponse([
            Action::POST => fn() => $this->crudTransformer->post($this->entityRepository, (array)json_decode($this->getHttpRequest()->getRawBody())), // TODO: add data,
            Action::GET => fn() => $this->crudTransformer->getAll($this->entityRepository)
        ]);
    }
}