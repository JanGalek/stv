<?php declare(strict_types=1);

namespace SledovaniTV\Lib\Database;

use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Query;

interface Queryable
{
	function createQueryBuilder(string|null $alias = null, string|null $indexBy = null): QueryBuilder;

	function createQuery(string|null $dql = null): Query;

	function createNativeQuery(string $sql, Query\ResultSetMapping $rsm): NativeQuery;
}