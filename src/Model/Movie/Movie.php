<?php declare(strict_types = 1);

namespace SledovaniTV\Model\Movie;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity]
class Movie
{
	#[Id, GeneratedValue, Column(type: 'integer')]
	private int $id;

	private string $name;

	#[ManyToOne(targetEntity: Author::class, inversedBy: 'movies')]
	private Author $author;

	#[ManyToMany(targetEntity: Genre::class, mappedBy: 'movies')]
	private Collection $genres;

	#[Column(type: 'string')]
	private string $description;

	public function __construct()
	{
		$this->genres = new ArrayCollection();
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function getAuthor(): Author
	{
		return $this->author;
	}

	public function setAuthor(Author $author): void
	{
		$this->author = $author;
	}

	public function getGenres(): Collection
	{
		return $this->genres;
	}

	public function setGenres(Collection $genres): void
	{
		$this->genres = $genres;
	}

	public function getDescription(): string
	{
		return $this->description;
	}

	public function setDescription(string $description): void
	{
		$this->description = $description;
	}
}
