<?php declare(strict_types = 1);

namespace SledovaniTV\Model;

use App\Model\Database\Attribute\Column;
use App\Model\Database\Attribute\Entity;
use App\Model\Database\Attribute\Table;

#[
	Entity,
	Table(name: 'movies')
]
class Movie
{
	#[Column(type: 'int')]
	private int $id;
}
