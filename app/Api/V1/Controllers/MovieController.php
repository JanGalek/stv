<?php

declare(strict_types=1);

namespace App\Api\V1\Controllers;

use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

#[Path('/movies')]
class MovieController extends BaseController
{
	#[
		Path('/'),
		Method('GET')
	]
	public function list(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$response = $response->writeJsonBody([
			'test'
		]);

		return $response;
	}
}