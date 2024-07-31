<?php declare(strict_types=1);

namespace SledovaniTV\Lib\Database;

use Doctrine;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\AbstractQuery;
use RuntimeException;

interface Exception
{

}

class InvalidStateException extends RuntimeException implements Exception
{

}

class InvalidArgumentException extends \InvalidArgumentException implements Exception
{

}

class NotSupportedException extends \LogicException implements Exception
{

}

class StaticClassException extends \LogicException implements Exception
{

}

class NotImplementedException extends \LogicException implements Exception
{

}

class MissingClassException extends \LogicException implements Exception
{

}

class UnexpectedValueException extends \UnexpectedValueException implements Exception
{

}

class DBALException extends RuntimeException implements Exception
{

	public string|null $query;

	public array $params = [];

	public Connection|null $connection;

	/**
	 * @param \Exception|\Throwable $previous
	 * @param string|null $query
	 * @param array $params
	 * @param Connection|null $connection
	 * @param string|null $message
	 */
	public function __construct($previous, $query = null, $params = [], Connection $connection = null, $message = null)
	{
		parent::__construct($message ?: $previous->getMessage(), $previous->getCode(), $previous);
		$this->query = $query;
		$this->params = $params;
		$this->connection = $connection;
	}

	
	public function __sleep(): array
	{
		return ['message', 'code', 'file', 'line', 'errorInfo', 'query', 'params'];
	}

}

class DuplicateEntryException extends DBALException
{

	public array $columns;

	/**
	 * @param \Exception|\Throwable $previous
	 * @param array $columns
	 * @param string $query
	 * @param array $params
	 * @param Connection $connection
	 */
	public function __construct($previous, $columns = [], $query = null, $params = [], Connection $connection = null)
	{
		parent::__construct($previous, $query, $params, $connection);
		$this->columns = $columns;
	}

	public function __sleep(): array
	{
		return array_merge(parent::__sleep(), ['columns']);
	}

}


class QueryException extends RuntimeException implements Exception
{

	public AbstractQuery|null $query;

	public function __construct(\Exception|\Throwable $previous, AbstractQuery|null $query = null, string|null $message = null)
	{
		parent::__construct($message ?: $previous->getMessage(), 0, $previous);
		$this->query = $query;
	}

}
