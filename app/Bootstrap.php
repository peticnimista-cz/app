<?php

declare(strict_types=1);

namespace App;

use Nette\Bootstrap\Configurator;


class Bootstrap
{
    const DEBUG_MODE = true;

	public static function boot(): Configurator
	{
		$configurator = new Configurator;
		$appDir = dirname(__DIR__);

		$configurator->setDebugMode(self::DEBUG_MODE);
		$configurator->enableTracy($appDir . '/log');

		$configurator->setTempDirectory($appDir . '/temp');

		$configurator->createRobotLoader()
			->addDirectory(__DIR__)
			->register();

        $configurator->addConfig($appDir . ($configurator->isDebugMode() ? "/config/dev.neon" : "/config/production.neon"));

		return $configurator;
	}
}
