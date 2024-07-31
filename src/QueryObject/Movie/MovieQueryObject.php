<?php

declare(strict_types=1);

namespace SledovaniTV\QueryObject\Movie;

use SledovaniTV\Lib\Database\Queryable;
use SledovaniTV\Lib\Database\QueryObject;

class MovieQueryObject extends QueryObject
{

	protected function doCreateQuery(Queryable $repository): \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder
	{
		return $repository->createQueryBuilder('m');
	}
}