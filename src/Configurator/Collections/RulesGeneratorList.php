<?php

/*
* @package   s9e\TextFormatter
* @copyright Copyright (c) 2010-2015 The s9e Authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\TextFormatter\Configurator\Collections;

use InvalidArgumentException;
use s9e\TextFormatter\Configurator\RulesGenerators\Interfaces\BooleanRulesGenerator;
use s9e\TextFormatter\Configurator\RulesGenerators\Interfaces\TargetedRulesGenerator;

class RulesGeneratorList extends NormalizedList
{
	public function normalizeValue($generator)
	{
		if (\is_string($generator))
		{
			$className = 's9e\\TextFormatter\\Configurator\\RulesGenerators\\' . $generator;

			if (\class_exists($className))
				$generator = new $className;
		}

		if (!($generator instanceof BooleanRulesGenerator)
		 && !($generator instanceof TargetedRulesGenerator))
			throw new InvalidArgumentException('Invalid rules generator ' . \var_export($generator, \true));

		return $generator;
	}
}