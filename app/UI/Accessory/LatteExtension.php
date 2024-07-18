<?php declare(strict_types = 1);

namespace App\UI\Accessory;

use Latte\Extension;

final class LatteExtension extends Extension
{

	/**
	 * @return callable[]
	 */
	public function getFilters(): array
	{
		return [];
	}

	/**
	 * @return callable[]
	 */
	public function getFunctions(): array
	{
		return [];
	}

}
