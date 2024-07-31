<?php declare(strict_types=1);

namespace SledovaniTV\Lib\Database;

use ArrayIterator;
use Doctrine;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Iterator;
use Nette\SmartObject;

/**
 * @method onPostFetch(QueryObject $self, Queryable $repository, Iterator $iterator)
 */
abstract class QueryObject implements Query
{

	use SmartObject;

	public array $onPostFetch = [];

	private \Doctrine\ORM\Query|NativeQueryWrapper|null $lastQuery;

	private ResultSet $lastResult;

	public function __construct()
	{

	}

	protected abstract function doCreateQuery(Queryable $repository): \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder;

	/**
	 * @throws UnexpectedValueException
	 */
	protected function getQuery(Queryable $repository): Doctrine\ORM\Query|NativeQueryWrapper|null
	{
		$query = $this->toQuery($this->doCreateQuery($repository));

		if ($this->lastQuery instanceof Doctrine\ORM\Query && $query instanceof Doctrine\ORM\Query &&
			$this->lastQuery->getDQL() === $query->getDQL()) {
			$query = $this->lastQuery;
		}

		if ($this->lastQuery !== $query) {
			$this->lastResult = new ResultSet($query, $this, $repository);
		}

		return $this->lastQuery = $query;
	}

	protected function doCreateCountQuery(Queryable $repository): \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder
	{

	}

	public function count(Queryable $repository, ResultSet $resultSet = null, Paginator $paginatedQuery = null): int
	{
		if ($query = $this->doCreateCountQuery($repository)) {
			return (int)$this->toQuery($query)->getSingleScalarResult();
		}

		if ($this->lastQuery && $this->lastQuery instanceof NativeQueryWrapper) {
			$class = static::class;
			throw new NotSupportedException("You must implement your own count query in $class::doCreateCountQuery(), Paginator from Doctrine doesn't support NativeQueries.");
		}

		if ($paginatedQuery !== null) {
			return $paginatedQuery->count();
		}

		$query = $this->getQuery($repository)
			->setFirstResult(null)
			->setMaxResults(null);

		$paginatedQuery = new Paginator($query, ($resultSet !== null) ? $resultSet->getFetchJoinCollection() : TRUE);
		$paginatedQuery->setUseOutputWalkers(($resultSet !== null) ? $resultSet->getUseOutputWalkers() : null);

		return $paginatedQuery->count();
	}

	public function fetch(Queryable $repository, int $hydrationMode = AbstractQuery::HYDRATE_OBJECT): ResultSet|array
	{
		$query = $this->getQuery($repository)
			->setFirstResult(null)
			->setMaxResults(null);

		return $hydrationMode !== AbstractQuery::HYDRATE_OBJECT
			? $query->execute(null, $hydrationMode)
			: $this->lastResult;
	}

	/**
	 * If You encounter a problem with the LIMIT 1 here,
	 * you should instead of fetching toMany relations just use postFetch.
	 *
	 * And if you really really need to hack it, just override this method and remove the limit.
	 */
	public function fetchOne(Queryable $repository): object
	{
		$query = $this->getQuery($repository)
			->setFirstResult(null)
			->setMaxResults(1);

		// getResult has to be called to have consistent result for the postFetch
		// this is the only way to main the INDEX BY value
		$singleResult = $query->getResult();

		if (!$singleResult) {
			throw new Doctrine\ORM\NoResultException(); // simulate getSingleResult()
		}

		$this->postFetch($repository, new ArrayIterator($singleResult));

		return array_shift($singleResult);
	}

	public function postFetch(Queryable $repository, Iterator $iterator): void
	{
		$this->onPostFetch($this, $repository, $iterator);
	}

	/**
	 * @internal For Debugging purposes only!
	 */
	public function getLastQuery(): \Doctrine\ORM\Query|NativeQueryWrapper|null
	{
		return $this->lastQuery;
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder|AbstractQuery|NativeQueryBuilder $query
	 * @return Doctrine\ORM\Query|NativeQueryWrapper
	 */
	private function toQuery(\Doctrine\ORM\QueryBuilder|AbstractQuery|Doctrine\DBAL\Query\QueryBuilder $query): Doctrine\ORM\Query|NativeQueryWrapper
	{
		if ($query instanceof Doctrine\ORM\QueryBuilder) {
			$query = $query->getQuery();

		} elseif ($query instanceof Doctrine\ORM\NativeQuery) {
			$query = new NativeQueryWrapper($query);

		} elseif ($query instanceof NativeQueryBuilder) {
			$query = $query->getQuery();
		}

		if (!$query instanceof Doctrine\ORM\Query && !$query instanceof NativeQueryWrapper) {
			throw new UnexpectedValueException(sprintf(
				"Method " . static::class . "::doCreateQuery must return " .
				"instanceof %s or %s, " .
				(is_object($query) ? 'instance of ' . get_class($query) : gettype($query)) . " given.",
				\Doctrine\ORM\Query::class,
				QueryBuilder::class
			));
		}

		return $query;
	}
}