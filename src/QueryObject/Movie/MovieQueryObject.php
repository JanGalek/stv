<?php

declare(strict_types=1);

namespace SledovaniTV\QueryObject\Movie;

use SledovaniTV\Lib\Database\Queryable;
use SledovaniTV\Lib\Database\QueryObject;
use SledovaniTV\Model\Movie\Movie;

class MovieQueryObject extends QueryObject
{

	protected function doCreateQuery(Queryable $repository): \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder
	{
		return $repository->createQueryBuilder()
			->select('movie')->from(Movie::class, 'movie')
			->innerJoin('movie.author', 'author')->addSelect('author');
	}

	public function postFetch(Queryable $repository, \Iterator $iterator): void
	{
		$ids = array_keys(iterator_to_array($iterator, true));

		$repository->createQueryBuilder()
			->select('partial movie.{ids}')->from(Movie::class, 'movie')
			->leftJoin('movie.genres', 'genres')->addSelect('genres')
			->andWhere('movie.id IN (:ids)')->setParameter('ids', $ids)
			->getQuery()->getResult();
	}
}