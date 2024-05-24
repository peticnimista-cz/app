<?php

namespace App\Router;

use Nette\Application\Routers\RouteList;

class WebRouter
{
    const MODULE_NAME = "app";
    const PREFIX = "";

    /**
     * @return RouteList
     */
    public static function createRouter(): RouteList{
        $applicationRouter = new RouteList();
        $applicationRouter = $applicationRouter->withModule(self::MODULE_NAME)->withPath(self::PREFIX);
        $applicationRouter->addRoute("das", "Front:Main");

        return $applicationRouter;
    }
}