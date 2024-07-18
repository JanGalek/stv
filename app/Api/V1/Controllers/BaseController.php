<?php declare(strict_types = 1);

namespace App\Api\V1\Controllers;

use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\UI\Controller\IController;

#[Path('/api/v1')]
abstract class BaseController implements IController
{

}
