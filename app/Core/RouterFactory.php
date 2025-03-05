<?php

declare(strict_types=1);

namespace App\Core;

use App\Bootstrap;
use App\Model\API\Action;
use App\Model\Utils\Uri;
use Nette;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

    const API_PREFIX = "api";

	public static function createRouter(): RouteList
	{
        $router = new RouteList;
        $router->withModule("Front")->addRoute("/", "Home:default");
        return $router;
	}

}
