<?php declare(strict_types = 1);

namespace App\Model\Utils;

use Exception;

final class Caster
{

    /**
     * @throws Exception
     */
    public static function toInt(mixed $value): int
	{
		if (is_string($value) || is_int($value) || is_float($value)) {
			return intval($value);
		}

		throw new Exception('Cannot cast to integer');
	}

}
