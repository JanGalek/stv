<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$configurator = App\Bootstrap::boot();
$container = $configurator->createContainer();
//$application = $container->getByType(Nette\Application\Application::class);
$application = $container->getByType(\Apitte\Core\Application\IApplication::class);
$application->run();
