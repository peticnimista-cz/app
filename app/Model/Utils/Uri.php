<?php

namespace App\Model\Utils;

final class Uri
{
    /**
     * Convert string from CamelCase to snake_case
     * @param string $url
     * @return string
     */
    public static function toSnakeCase(string $url): string {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $url));
    }
}