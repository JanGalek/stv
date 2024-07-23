<?php declare(strict_types=1);

namespace App\Model\Database\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Column implements MappingAttribute
{
	public function __construct(
		public readonly string|null $name = null,
		public readonly string|null $type = null,
		public readonly int|null $length = null,
		public readonly int|null $precision = null,
		public readonly int|null $scale = null,
		public readonly bool $unique = false,
		public readonly bool $nullable = false,
		public readonly bool $insertable = true,
		public readonly bool $updatable = true,
		public readonly string|null $enumType = null,
		public readonly array $options = [],
		public readonly string|null $columnDefinition = null,
		public readonly string|null $generated = null,
	) {
	}
}
