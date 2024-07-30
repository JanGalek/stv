<?php declare(strict_types=1);

namespace SledovaniTV\Repository\Movie;

use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SledovaniTV\Model\Movie\Genre;

class GenreRepository extends EntityRepository
{

	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Genre::class);
	}
}