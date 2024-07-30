<?php

declare(strict_types=1);

use Apitte\Core\Application\IApplication as ApiApplication;
use App\Bootstrap;
use Nette\Application\Application as UIApplication;

require __DIR__ . '/../vendor/autoload.php';

$configurator = Bootstrap::boot();
$isApi = str_starts_with($_SERVER['REQUEST_URI'], '/api');
$container = $configurator->createContainer();

if ($isApi) {
	// Apitte application
	$container->getByType(ApiApplication::class)->run();
} else {
	// Nette application
	$container->getByType(UIApplication::class)->run();
}
