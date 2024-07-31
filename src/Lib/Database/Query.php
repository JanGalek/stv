<?php declare(strict_types=1);

namespace SledovaniTV\Lib\Database;

use Doctrine\ORM\AbstractQuery;

interface Query
{
	function count(Queryable $repository): int;

	function fetch(Queryable $repository, int $hydrationMode = AbstractQuery::HYDRATE_OBJECT): mixed;

	function fetchOne(Queryable $repository): object;
}