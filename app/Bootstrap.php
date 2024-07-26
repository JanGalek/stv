<?php declare(strict_types = 1);

namespace App;

use Contributte\Bootstrap\ExtraConfigurator;
use Nette\Bootstrap\Configurator;
use SledovaniTV\Lib\DotEnv\Loader;

class Bootstrap
{

	public static function boot(): Configurator
	{

		$configurator = new ExtraConfigurator();
		$appDir = dirname(__DIR__);
		$confDir = $appDir . '/config';
		$localConfig = $confDir . '/local.neon';

		Loader::load($configurator, $appDir . '/.env');

		$configurator->addEnvParameters();
		$configurator->setEnvDebugMode();

		$configurator->enableTracy($appDir . '/log');
		$configurator->setTempDirectory($appDir . '/temp');

		$configurator->addStaticParameters([
			'srcDir' => $appDir . '/src',
		]);

		$configurator->createRobotLoader()
			->addDirectory(__DIR__)
			->register();

		$configurator->addConfig($confDir . '/common.neon');
		$configurator->addConfig($confDir . '/services.neon');
		$configurator->addConfig($confDir . '/extensions.neon');

		if (file_exists($localConfig)) {
			$configurator->addConfig($localConfig);
		}

		return $configurator;
	}

}
