<?php declare(strict_types=1);

namespace App\Model\Database\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Id implements MappingAttribute
{

}