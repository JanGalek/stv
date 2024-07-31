<?php declare(strict_types=1);

namespace SledovaniTV\Lib\Database;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\SQL\Parser as SQLParserUtils;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Parameter;

final class NativeQueryWrapper extends AbstractQuery
{

	private int|null $firstResult = null;

	private int|null $maxResults = null;

	public function __construct(private NativeQuery $nativeQuery)
	{
		parent::__construct($this->nativeQuery->getEntityManager());
	}

	public function getFirstResult(): int|null
	{
		return $this->firstResult;
	}

	public function getMaxResults(): int|null
	{
		return $this->maxResults;
	}

	public function setMaxResults(int|null $maxResults): self
	{
		$this->maxResults = $maxResults;
		return $this;
	}

	/**
	 * @throws Exception
	 */
	protected function getLimitedQuery(): NativeQuery
	{
		$copy = clone $this->nativeQuery;
		$copy->setParameters([]);

		try {
			$params = $types = [];
			/** @var Query\Parameter $param */
			foreach ($this->nativeQuery->getParameters() as $param) {
				$params[$param->getName()] = $param->getValue();
				$types[$param->getName()] = $param->getType();
			}

			[$query, $params, $types] = SQLParserUtils::expandListParameters($copy->getSQL(), $params, $types);

			$copy->setSQL($query);
			foreach ($params as $i => $value) {
				$copy->setParameter($i, $value, $types[$i] ?? NULL);
			}

		} catch (SQLParserUtils\Exception $e) {
			$copy->setParameters(clone $this->nativeQuery->getParameters());
		}

		if ($this->maxResults !== NULL || $this->firstResult !== NULL) {
			$em = $this->nativeQuery->getEntityManager();
			$platform = $em->getConnection()->getDatabasePlatform();

			$copy->setSQL($platform->modifyLimitQuery($copy->getSQL(), $this->maxResults, $this->firstResult));
		}

		return $copy;
	}


	/**
	 * @throws Exception|Query\QueryException
	 */
	public function iterate($parameters = null, $hydrationMode = null): iterable
	{
		return $this->getLimitedQuery()->toIterable($parameters, $hydrationMode);
	}


	/**
	 * @throws Exception
	 */
	public function execute($parameters = null, $hydrationMode = null)
	{
		return $this->getLimitedQuery()->execute($parameters, $hydrationMode);
	}


	/**
	 * @throws Exception
	 */
	public function getResult($hydrationMode = self::HYDRATE_OBJECT)
	{
		return $this->getLimitedQuery()->getResult($hydrationMode);
	}


	/**
	 * @throws Exception
	 */
	public function getArrayResult(): array|float|int|string
	{
		return $this->getLimitedQuery()->getArrayResult();
	}


	/**
	 * @throws Exception
	 */
	public function getScalarResult(): array|float|int|string
	{
		return $this->getLimitedQuery()->getScalarResult();
	}


	/**
	 * @throws NonUniqueResultException
	 * @throws Exception
	 */
	public function getOneOrNullResult($hydrationMode = null)
	{
		return $this->getLimitedQuery()->getOneOrNullResult($hydrationMode);
	}


	/**
	 * @throws NonUniqueResultException
	 * @throws NoResultException
	 * @throws Exception
	 */
	public function getSingleResult($hydrationMode = null)
	{
		return $this->getLimitedQuery()->getSingleResult($hydrationMode);
	}


	/**
	 * @throws NonUniqueResultException
	 * @throws NoResultException
	 * @throws Exception
	 */
	public function getSingleScalarResult(): float|bool|int|string|null
	{
		return $this->getLimitedQuery()->getSingleScalarResult();
	}



	public function setSQL($sql): NativeQueryWrapper
	{
		$this->nativeQuery->setSQL($sql);
		return $this;
	}



	public function getSQL(): array|string
	{
		return $this->nativeQuery->getSQL();
	}



	public function setCacheable($cacheable): NativeQueryWrapper
	{
		$this->nativeQuery->setCacheable($cacheable);
		return $this;
	}



	public function isCacheable(): bool
	{
		return $this->nativeQuery->isCacheable();
	}



	public function setCacheRegion($cacheRegion): NativeQueryWrapper
	{
		$this->nativeQuery->setCacheRegion($cacheRegion);
		return $this;
	}



	public function getCacheRegion(): ?string
	{
		return $this->nativeQuery->getCacheRegion();
	}



	protected function isCacheEnabled(): bool
	{
		return $this->nativeQuery->isCacheEnabled();
	}



	public function getLifetime(): int
	{
		return $this->nativeQuery->getLifetime();
	}



	public function setLifetime($lifetime): NativeQueryWrapper
	{
		$this->nativeQuery->setLifetime($lifetime);
		return $this;
	}



	public function getCacheMode()
	{
		return $this->nativeQuery->getCacheMode();
	}



	public function setCacheMode($cacheMode): NativeQueryWrapper
	{
		$this->nativeQuery->setCacheMode($cacheMode);
		return $this;
	}



	public function getEntityManager(): EntityManagerInterface
	{
		return $this->nativeQuery->getEntityManager();
	}



	public function free(): void
	{
		$this->nativeQuery->free();
	}



	public function getParameters(): ArrayCollection|array
	{
		return $this->nativeQuery->getParameters();
	}



	public function getParameter($key): ?Parameter
	{
		return $this->nativeQuery->getParameter($key);
	}



	public function setParameters($parameters): NativeQueryWrapper
	{
		$this->nativeQuery->setParameters($parameters);
		return $this;
	}



	public function setParameter($key, $value, $type = null): NativeQueryWrapper
	{
		$this->nativeQuery->setParameter($key, $value, $type);
		return $this;
	}



	public function processParameterValue($value)
	{
		return $this->nativeQuery->processParameterValue($value);
	}



	public function setResultSetMapping(Query\ResultSetMapping $rsm): NativeQueryWrapper
	{
		$this->nativeQuery->setResultSetMapping($rsm);
		return $this;
	}



	protected function getResultSetMapping(): ?Query\ResultSetMapping
	{
		return $this->nativeQuery->getResultSetMapping();
	}



	public function setHydrationCacheProfile(QueryCacheProfile $profile = null): NativeQueryWrapper
	{
		$this->nativeQuery->setHydrationCacheProfile($profile);
		return $this;
	}



	public function getHydrationCacheProfile(): ?QueryCacheProfile
	{
		return $this->nativeQuery->getHydrationCacheProfile();
	}



	public function setResultCacheProfile(QueryCacheProfile $profile = null): NativeQueryWrapper
	{
		$this->nativeQuery->setResultCacheProfile($profile);
		return $this;
	}



	public function setResultCacheDriver($resultCacheDriver = null): NativeQueryWrapper
	{
		$this->nativeQuery->setResultCacheDriver($resultCacheDriver);
		return $this;
	}



	public function getResultCacheDriver()
	{
		return $this->nativeQuery->getResultCacheDriver();
	}



	public function useResultCache($bool, $lifetime = null, $resultCacheId = null)
	{
		$this->nativeQuery->useResultCache($bool, $lifetime, $resultCacheId);
		return $this;
	}



	public function setResultCacheLifetime($lifetime)
	{
		$this->nativeQuery->setResultCacheLifetime($lifetime);
		return $this;
	}



	public function getResultCacheLifetime()
	{
		return $this->nativeQuery->getResultCacheLifetime();
	}



	public function expireResultCache($expire = true)
	{
		$this->nativeQuery->expireResultCache($expire);
		return $this;
	}



	public function getExpireResultCache()
	{
		return $this->nativeQuery->getExpireResultCache();
	}



	public function getQueryCacheProfile()
	{
		return $this->nativeQuery->getQueryCacheProfile();
	}



	public function setFetchMode($class, $assocName, $fetchMode)
	{
		$this->nativeQuery->setFetchMode($class, $assocName, $fetchMode);
		return $this;
	}



	public function setHydrationMode($hydrationMode)
	{
		$this->nativeQuery->setHydrationMode($hydrationMode);
		return $this;
	}



	public function getHydrationMode()
	{
		return $this->nativeQuery->getHydrationMode();
	}



	public function setHint($name, $value)
	{
		$this->nativeQuery->setHint($name, $value);
		return $this;
	}



	public function getHint($name)
	{
		return $this->nativeQuery->getHint($name);
	}



	public function hasHint($name)
	{
		return $this->nativeQuery->hasHint($name);
	}



	public function getHints()
	{
		return $this->nativeQuery->getHints();
	}



	protected function getHydrationCacheId()
	{
		return $this->nativeQuery->getHydrationCacheId();
	}



	public function setResultCacheId($id)
	{
		$this->nativeQuery->setResultCacheId($id);
		return $this;
	}



	public function getResultCacheId()
	{
		return $this->nativeQuery->getResultCacheId();
	}



	protected function getHash()
	{
		return $this->nativeQuery->getHash();
	}



	protected function _doExecute()
	{
		throw new NotImplementedException;
	}



	public function __clone()
	{
		$this->nativeQuery = clone $this->nativeQuery;
	}
}