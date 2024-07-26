<?php declare(strict_types = 1);

namespace SledovaniTV\Model;


use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

#[
	Entity,
	Table(name: 'movies')
]
class Movie
{
	#[Column(type: 'int')]
	private int $id;
}
