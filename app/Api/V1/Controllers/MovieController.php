<?php declare(strict_types = 1);

namespace App\Api\V1\Controllers;

use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RequestBody;
use Apitte\Core\Annotation\Controller\RequestParameter;
use Apitte\Core\Annotation\Controller\Tag;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use SledovaniTV\Model\Movie\Movie;
use SledovaniTV\Repository\Movie\MovieRepository;

#[Path('/movies'), Tag(name: 'Movies')]
class MovieController extends BaseController
{

	public function __construct(private MovieRepository $movieRepository)
	{
	}

	#[
		Path('/'),
		Method('GET')
	]
	public function list(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$result = $this->movieRepository->findAll();

		$response = $response->writeJsonBody([
			'test',
		]);

		return $response;
	}

	#[
		Path('/{id}'),
		Method('GET'),
		RequestParameter(name: 'id', type: 'integer')
	]
	public function detail(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$response = $response->writeJsonBody([
			'test',
		]);

		return $response;
	}

	#[
		Path('/'),
		Method('POST'),
		RequestBody(entity: Movie::class)
	]
	public function create(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		return $response;
	}

	#[
		Path('/{id}'),
		Method('PUT'),
		RequestParameter(name: 'id', type: 'integer')
	]
	public function update(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		return $response;
	}

	#[
		Path('/{id}'),
		Method('DELETE'),
		RequestParameter(name: 'id', type: 'integer')
	]
	public function delete(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$response = $response->writeJsonBody([
			'test',
		]);
		return $response;
	}

}
