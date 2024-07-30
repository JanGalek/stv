<?php declare(strict_types=1);

namespace App\Api\V1\Controllers;

use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\UI\Controller\IController;
use Apitte\OpenApi\ISchemaBuilder;

#[Path('/openapi')]
final class OpenApi extends BaseController
{

	public function __construct(private ISchemaBuilder $schemaBuilder)
	{
	}

	#[Path('/'), Method('GET')]
	public function index(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$openApi = $this->schemaBuilder->build();
		return $response->writeJsonBody($openApi->toArray());
	}
}