<?php

namespace App\Model\Database;

use Nette\Database\Explorer;

class DatabaseManager
{
	public function __construct(private Explorer $explorer) {
	}

	/**
	 * @param $entityName
	 * @return EntityRepository
	 */
	public function getEntityRepository($entityName): EntityRepository
	{
		return new EntityRepository(DataStructure::entities()[$entityName], $this->explorer);
	}
}
