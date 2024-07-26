<?php declare(strict_types=1);

namespace SledovaniTV\Lib\DotEnv;

use Nette\Bootstrap\Configurator;

class Loader
{
	public static function load(Configurator $configurator, string $env)
	{
		if (file_exists($env)) {
			$lines = file($env);
			$parameters = ['env' => []];
			foreach ($lines as $line) {
				if (trim($line) === '' || str_starts_with(trim($line), '#')) {
					continue;
				}

				[$name, $value] = explode('=', $line, 2);
				$name = trim($name);
				$value = trim($value);
				$parameters['env'][$name] = $value;

				if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
					putenv(sprintf('%s=%s', $name, $value));
					$_ENV[$name] = $value;
					$_SERVER[$name] = $value;
				}
			}

			$configurator->addStaticParameters($parameters);
		}

	}
}