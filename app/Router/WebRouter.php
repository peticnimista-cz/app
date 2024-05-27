<?php

namespace App\Router;

use Nette\Application\Routers\RouteList;

class WebRouter
{
    const MODULE_NAME = "Web";
    const PREFIX = "";

    /**
     * @return RouteList
     */
    public static function createRouter(): RouteList{
        $applicationRouter = new RouteList();
        $applicationRouter = $applicationRouter->withPath(self::PREFIX)->withModule(self::MODULE_NAME);

        $applicationRouter->withModule("Front")->addRoute("", "Main:home");

        return $applicationRouter;
    }
}