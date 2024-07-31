<?php declare(strict_types=1);

namespace SledovaniTV\Lib\Database;

use Doctrine\DBAL\ArrayParameterType;
use IteratorAggregate;
use Traversable;

class QueryBuilder extends \Doctrine\ORM\QueryBuilder implements IteratorAggregate
{
	private array $criteriaJoins = [];

	public function whereCriteria(array $criteria): QueryBuilder
	{
		foreach ($criteria as $key => $val) {
			$alias = $this->autoJoin($key);

			$operator = '=';
			if (preg_match('~(?P<key>[^\\s]+)\\s+(?P<operator>.+)\\s*~', $key, $m)) {
				$key = $m['key'];
				$operator = strtr(strtolower($m['operator']), [
					'neq' => '!=',
					'eq' => '=',
					'lt' => '<',
					'lte' => '<=',
					'gt' => '>',
					'gte' => '>=',
				]);
			}

			$not = $operator[0] === '!';
			if (str_starts_with($operator, 'not')) {
				$operator = substr($operator, 4);
				$not = TRUE;
			}

			$paramName = 'param_' . (count($this->getParameters()) + 1);

			if (is_array($val)) {
				$this->andWhere("$alias.$key " . ($not ? 'NOT ' : '') . "IN (:$paramName)");
				$this->setParameter($paramName, $val, is_int(reset($val)) ? ArrayParameterType::INTEGER : ArrayParameterType::STRING);

			} elseif ($val === NULL) {
				$this->andWhere("$alias.$key IS " . ($not ? 'NOT ' : '') . 'NULL');

			} else {
				$this->andWhere(sprintf('%s.%s %s :%s', $alias, $key, strtoupper($operator), $paramName));
				$this->setParameter($paramName, $val);
			}
		}

		return $this;
	}

	public function getIterator(): Traversable
	{
		return $this->getQuery()->iterate();
	}


	/**
	 * @internal
	 */
	public function autoJoinOrderBy(string|array $sort, string|null $order = NULL): \Doctrine\ORM\QueryBuilder
	{
		if (is_array($sort)) {
			foreach (func_get_arg(0) as $sort => $order) {
				if (!is_string($sort)) {
					$sort = $order;
					$order = NULL;
				}
				$this->autoJoinOrderBy($sort, $order);
			}

			return $this;
		}

		if (is_string($sort)) {
			$reg = '~[^()]+(?=\))~';
			if (preg_match($reg, $sort, $matches)) {
				$sortMix = $sort;
				$sort = $matches[0];
				$alias = $this->autoJoin($sort, 'leftJoin');
				$hiddenAlias = $alias . $sort . count($this->getDQLPart('orderBy'));

				$this->addSelect(preg_replace($reg, $alias . '.' . $sort, $sortMix) . ' as HIDDEN ' . $hiddenAlias);
				$rootAliases = $this->getRootAliases();
				$this->addGroupBy(reset($rootAliases) . '.id');
				$sort = $hiddenAlias;

			} else {
				$alias = $this->autoJoin($sort);
				$sort = $alias . '.' . $sort;
			}
		}

		return $this->addOrderBy($sort, $order);
	}

	private function autoJoin(&$key, $methodJoin = "innerJoin")
	{
		$rootAliases = $this->getRootAliases();
		$alias = reset($rootAliases);

		if (($i = strpos($key, '.')) === FALSE || !in_array(substr($key, 0, $i), $rootAliases)) {
			// there is no root alias to join from, assume first root alias
			$key = $alias . '.' . $key;
		}

		while (preg_match('~([^\\.]+)\\.(.+)~', $key, $m)) {
			$key = $m[2];
			$property = $m[1];

			if (in_array($property, $rootAliases)) {
				$alias = $property;
				continue;
			}

			if (isset($this->criteriaJoins[$alias][$property])) {
				$alias = $this->criteriaJoins[$alias][$property];
				continue;
			}

			$j = 0;
			do {
				$joinAs = $property[0] . (string) $j++;
			} while (isset($this->criteriaJoins[$joinAs]));
			$this->criteriaJoins[$joinAs] = [];

			$this->{$methodJoin}("$alias.$property", $joinAs);
			$this->criteriaJoins[$alias][$property] = $joinAs;
			$alias = $joinAs;
		}

		return $alias;
	}

}