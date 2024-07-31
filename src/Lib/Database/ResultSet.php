<?php declare(strict_types=1);

namespace SledovaniTV\Lib\Database;

use ArrayIterator;
use Countable;
use Doctrine\ORM;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\Tools\Pagination\Paginator as ResultPaginator;
use IteratorAggregate;
use Nette;
use Nette\Utils\Strings;
use Nette\Utils\Paginator as UIPaginator;

class ResultSet implements Countable, IteratorAggregate
{

	private int|null $totalCount;

	private AbstractQuery|\Doctrine\ORM\Query|NativeQuery $query;

	private bool $fetchJoinCollection = true;

	private bool|null $useOutputWalkers;

	private ArrayIterator|null $iterator;

	private bool $frozen = false;

	public function __construct(AbstractQuery $query, private readonly QueryObject|null $queryObject = null, private Queryable|null $repository = NULL)
	{
		$this->query = $query;

		if ($this->query instanceof NativeQueryWrapper || $this->query instanceof NativeQuery) {
			$this->fetchJoinCollection = false;
		}
	}

	/**
	 * @throws InvalidStateException
	 */
	public function setFetchJoinCollection(bool $fetchJoinCollection): ResultSet
	{
		$this->updating();

		$this->fetchJoinCollection = $fetchJoinCollection;
		$this->iterator = null;

		return $this;
	}

	/**
	 * @throws InvalidStateException
	 */
	public function setUseOutputWalkers(bool|null $useOutputWalkers): ResultSet
	{
		$this->updating();

		$this->useOutputWalkers = $useOutputWalkers;
		$this->iterator = null;

		return $this;
	}

	public function getUseOutputWalkers(): bool|null
	{
		return $this->useOutputWalkers;
	}

	public function getFetchJoinCollection(): bool
	{
		return $this->fetchJoinCollection;
	}

	/**
	 * @throws InvalidStateException
	 */
	public function clearSorting(): ResultSet
	{
		$this->updating();

		if ($this->query instanceof ORM\Query) {
			$dql = Strings::normalize((string) $this->query->getDQL());
			if (preg_match('~^(.+)\\s+(ORDER BY\\s+((?!FROM|WHERE|ORDER\\s+BY|GROUP\\sBY|JOIN).)*)\\z~si', $dql, $m)) {
				$dql = $m[1];
			}
			$this->query->setDQL(trim($dql));
		}

		return $this;
	}

	/**
	 * @throws InvalidStateException
	 */
	public function applySorting(string|array $columns): ResultSet
	{
		$this->updating();

		$sorting = [];
		foreach (is_array($columns) ? $columns : func_get_args() as $name => $column) {
			if (!is_numeric($name)) {
				$column = $name . ' ' . $column;
			}

			if (!preg_match('~\s+(DESC|ASC)\s*\z~i', $column = trim($column))) {
				$column .= ' ASC';
			}
			$sorting[] = $column;
		}

		if ($sorting && $this->query instanceof ORM\Query) {
			$dql = Strings::normalize((string) $this->query->getDQL());

			if (!preg_match('~^(.+)\\s+(ORDER BY\\s+((?!FROM|WHERE|ORDER\\s+BY|GROUP\\sBY|JOIN).)*)\\z~si', $dql, $m)) {
				$dql .= ' ORDER BY ';

			} else {
				$dql .= ', ';
			}

			$this->query->setDQL($dql . implode(', ', $sorting));
		}
		$this->iterator = null;

		return $this;
	}

	/**
	 * @throws InvalidStateException
	 */
	public function applyPaging(int|null $offset, int|null $limit): ResultSet
	{
		if ($this->query instanceof ORM\Query && ($this->query->getFirstResult() != $offset || $this->query->getMaxResults() != $limit)) {
			$this->query->setFirstResult($offset);
			$this->query->setMaxResults($limit);
			$this->iterator = null;
		}

		return $this;
	}

	public function applyPaginator(UIPaginator $paginator, int $itemsPerPage = null): ResultSet
	{
		if ($itemsPerPage !== null) {
			$paginator->setItemsPerPage($itemsPerPage);
		}

		$paginator->setItemCount($this->getTotalCount());
		$this->applyPaging($paginator->getOffset(), $paginator->getLength());

		return $this;
	}

	public function isEmpty(): bool
	{
		$count = $this->getTotalCount();
		$offset = $this->query instanceof ORM\Query ? $this->query->getFirstResult() : 0;

		return $count <= $offset;
	}

	/**
	 * @throws QueryException
	 */
	public function getTotalCount(): int
	{
		if ($this->totalCount !== null) {
			return $this->totalCount;
		}

		try {
			$paginatedQuery = $this->createPaginatedQuery($this->query);

			if ($this->queryObject !== null && $this->repository !== null) {
				$totalCount = $this->queryObject->count($this->repository, $this, $paginatedQuery);

			} else {
				$totalCount = $paginatedQuery->count();
			}

			$this->frozen = true;
			return $this->totalCount = $totalCount;

		} catch (ORMException $e) {
			throw new QueryException($e, $this->query, $e->getMessage());
		}
	}

	/**
	 * @throws QueryException
	 */
	public function getIterator(int $hydrationMode = AbstractQuery::HYDRATE_OBJECT): ArrayIterator
	{
		if ($this->iterator !== NULL) {
			return $this->iterator;
		}

		$this->query->setHydrationMode($hydrationMode);

		try {
			if ($this->fetchJoinCollection && $this->query instanceof ORM\Query && ($this->query->getMaxResults() > 0 || $this->query->getFirstResult() > 0)) {
				$iterator = $this->createPaginatedQuery($this->query)->getIterator();

			} else {
				$iterator = new ArrayIterator($this->query->getResult());
			}

			if ($this->queryObject !== NULL && $this->repository !== NULL) {
				$this->queryObject->postFetch($this->repository, $iterator);
			}

			$this->frozen = TRUE;
			return $this->iterator = $iterator;

		} catch (ORMException $e) {
			throw new QueryException($e, $this->query, $e->getMessage());
		}
	}

	public function toArray(int $hydrationMode = AbstractQuery::HYDRATE_OBJECT): array
	{
		return iterator_to_array(clone $this->getIterator($hydrationMode), TRUE);
	}

	public function count(): int
	{
		return $this->getIterator()->count();
	}

	private function createPaginatedQuery(AbstractQuery|ORM\Query|NativeQuery $query): ORM\Tools\Pagination\Paginator
	{
		if (!$query instanceof ORM\Query) {
			throw new InvalidArgumentException(sprintf('QueryObject pagination only works with %s', \Doctrine\ORM\Query::class));
		}

		$paginated = new ResultPaginator($query, $this->fetchJoinCollection);
		$paginated->setUseOutputWalkers($this->useOutputWalkers);

		return $paginated;
	}

	private function updating(): void
	{
		if ($this->frozen !== FALSE) {
			throw new InvalidStateException("Cannot modify result set, that was already fetched from storage.");
		}
	}

}
