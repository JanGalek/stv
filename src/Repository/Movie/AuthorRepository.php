<?php declare(strict_types=1);

namespace SledovaniTV\Repository\Movie;

use SledovaniTV\Model\Movie\Author;
use SledovaniTV\Repository\AbstractRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Author|null find(mixed $id, LockMode|int|null $lockMode = null, int|null $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method Author[] findAll()
 * @method Author[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null)
 */
class AuthorRepository extends AbstractRepository
{

	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Author::class);
	}

	public function save(Author $entity): void
	{
		$this->getEntityManager()->persist($entity);
		$this->getEntityManager()->flush();
	}

}
