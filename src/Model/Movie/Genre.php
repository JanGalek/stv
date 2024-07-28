<?php declare(strict_types=1);

namespace SledovaniTV\Model\Movie;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToMany;

#[Entity]
class Genre
{
	#[Id, GeneratedValue, Column(type: 'integer')]
	private int $id;

	#[Column(type: 'string')]
	private string $name;

	#[ManyToMany(targetEntity: Movie::class, mappedBy: 'genres')]
	private Collection $movies;

	public function __construct()
	{
		$this->movies = new ArrayCollection();
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function setId(int $id): void
	{
		$this->id = $id;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function getMovies(): Collection
	{
		return $this->movies;
	}

	public function setMovies(Collection $movies): void
	{
		$this->movies = $movies;
	}

}