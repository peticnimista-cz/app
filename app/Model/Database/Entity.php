<?php

namespace App\Model\Database;

use App\Model\Database\Columns\TId;
use ReflectionClass;

abstract class Entity
{
    use TId; const id = "id"; // autoincrement

    public static string $METHOD__GET_COLUMNS = "getColumns";

    /**
     * not best practice in the world
     * @return array
     */
    public static function getColumns(): array
    {
        // "static::class" here does the magic
        $reflectionClass = new ReflectionClass(static::class);
        return $reflectionClass->getConstants();
    }
}
