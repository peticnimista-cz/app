<?php

namespace App\Router;

use App\Bootstrap;
use App\Model\API\Action;
use App\Model\Utils\Uri;
use Nette\Application\Routers\RouteList;

class ApiRouter
{
    const MODULE_NAME = "api";
    const PREFIX = "api";

    public static function createRouter(): RouteList {
        $router = new RouteList();
        $router = $router->withModule(self::MODULE_NAME)->withPath(self::PREFIX);
        $sitemap = [];
        if(Bootstrap::DEBUG_MODE) {
            $router->addRoute('test', 'Test:test');
            $router->addRoute("test/sitemap", "Test:sitemap");
            $router->addRoute("test/post", "Test:post");
            $router->addRoute("test/password", "Test:password");

            $sitemap["api/test"] = ["*"];
            $sitemap["api/test/sitemap"] = ["*"];
            $sitemap["api/test/post"] = ["*"];
            $sitemap["api/test/password"] = ["*"];
        }

        /**
         * Data module
         */
        $dataRouter = $router->withModule("Data")->withPath("data");
        self::generateRouterSection(submodules: ["Cistic", "Granulat", "KluznyLak", "Lepidlo", "Redidlo"],
            controllers: [self::SELF_MODULE_CONTROLLER, "SlozeniLatka", "SlozeniMnozstvi", "Vyrobce"],
            routeList: $dataRouter, sitemap: $sitemap);
        self::generateRouterSection(submodules: ["Granulat"], controllers: ["Typ"], routeList: $router);
        self::generateRouterSection(submodules: ["Projekt"], controllers: [self::SELF_MODULE_CONTROLLER, "Sklo", "Trh"], routeList: $router);
        self::generateRouterSection(submodules: ["Material"], controllers: [self::SELF_MODULE_CONTROLLER, "Typ"], routeList: $router);

        /**
         * Dynamic module
         */
        $dynamicRouter = $router->withModule("Dynamic")->withPath("dynamic");
        self::generateRouterSection(submodules: [""], controllers: ["RowNote", "ViewNote"], routeList: $dynamicRouter);

        /**
         * System module
         */
        $systemRouter = $router->withModule("System")->withPath("system");
        self::generateRouterSection(submodules: ["User"], controllers: ["AccessLog", self::SELF_MODULE_CONTROLLER], routeList: $systemRouter);
        self::generateRouterSection(submodules: [""], controllers: ["History"],routeList: $systemRouter);

        $router->withModule("System")->withPath("system")->withPath("user")
            ->addRoute("<id>/history", "User:history")
            ->addRoute("login", "User:login")
            ->addRoute("logout", "User:logout")
            ->addRoute("info", "User:info")
        ;
        $sitemap["api/system/user/<id>/history"] = [Action::GET];
        $sitemap["api/system/user/login"] = [Action::POST];
        $sitemap["api/system/user/logout"] = [Action::POST];
        $sitemap["api/system/user/info"] = [Action::GET];

        return $router;
    }


    /**
     *
     *
     * dev
     *
     *
     */

    const SELF_MODULE_CONTROLLER = "___SELF_MODULE_CONTROLLER___"; // used in generating

    public static array $sitemap = [];

    /**
     * Using self::SELF_MODULE_CONTROLLER as variable used in $controllers
     * @param array $submodules
     * @param array $controllers
     * @param RouteList $routeList
     * @param array $sitemap
     * @return void
     */
    private static function generateRouterSection(array $submodules, array $controllers, RouteList &$routeList, array &$sitemap = []): void
    {
        foreach ($submodules as $submodule) { // modules
            foreach ($controllers as $controller) {
                if($controller === self::SELF_MODULE_CONTROLLER) $controller = $submodule;
                $normalizedSubmodule =  Uri::toSnakeCase($submodule);
                $normalizedController = Uri::toSnakeCase($controller);
                if(empty($submodule)) {
                    $routeList
                        ->addRoute($normalizedController . "/<id>", $controller . ":one")
                        ->addRoute($normalizedController, $controller . ":all");
                } else {
                    $routeList
                        ->withPath($normalizedSubmodule)
                        ->addRoute($normalizedController . "/<id>", $controller . ":one")
                        ->addRoute($normalizedController, $controller . ":all");
                }
                $sitemap[self::PREFIX . "/" .
                $routeList->getPath(). (!empty($submodule) ? $normalizedSubmodule : "") . "/" . $normalizedController . "/<id>"] = [Action::GET, Action::DELETE, Action::PATCH];
                $sitemap[self::PREFIX . "/" .
                $routeList->getPath() . (!empty($submodule) ? $normalizedSubmodule : "") . "/" . $normalizedController] = [Action::GET, Action::POST];
            }
        }
    }
}