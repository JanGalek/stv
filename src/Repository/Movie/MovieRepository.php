<?php declare(strict_types=1);

namespace SledovaniTV\Repository\Movie;

use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SledovaniTV\Model\Movie\Movie;
use SledovaniTV\QueryObject\Movie\MovieQueryObject;

class MovieRepository extends EntityRepository
{

	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Movie::class);
	}

	public function getList()
	{
		$qb = new MovieQueryObject();

	}
}