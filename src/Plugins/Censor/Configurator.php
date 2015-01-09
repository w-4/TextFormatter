<?php

/*
* @package   s9e\TextFormatter
* @copyright Copyright (c) 2010-2015 The s9e Authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\TextFormatter\Plugins\Censor;

use ArrayAccess;
use Countable;
use Iterator;
use s9e\TextFormatter\Configurator\Collections\NormalizedCollection;
use s9e\TextFormatter\Configurator\Helpers\ConfigHelper;
use s9e\TextFormatter\Configurator\Helpers\RegexpBuilder;
use s9e\TextFormatter\Configurator\Items\Variant;
use s9e\TextFormatter\Configurator\JavaScript\RegExp;
use s9e\TextFormatter\Configurator\JavaScript\RegexpConvertor;
use s9e\TextFormatter\Configurator\Traits\CollectionProxy;
use s9e\TextFormatter\Plugins\ConfiguratorBase;

class Configurator extends ConfiguratorBase implements ArrayAccess, Countable, Iterator
{
	public function __call($methodName, $args)
	{
		return \call_user_func_array(array($this->collection, $methodName), $args);
	}

	public function offsetExists($offset)
	{
		return isset($this->collection[$offset]);
	}

	public function offsetGet($offset)
	{
		return $this->collection[$offset];
	}

	public function offsetSet($offset, $value)
	{
		$this->collection[$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		unset($this->collection[$offset]);
	}

	public function count()
	{
		return \count($this->collection);
	}

	public function current()
	{
		return $this->collection->current();
	}

	public function key()
	{
		return $this->collection->key();
	}

	public function next()
	{
		return $this->collection->next();
	}

	public function rewind()
	{
		$this->collection->rewind();
	}

	public function valid()
	{
		return $this->collection->valid();
	}

	protected $allowed = array();

	protected $attrName = 'with';

	protected $collection;

	protected $defaultReplacement = '****';

	protected $regexpOptions = array(
		'caseInsensitive' => \true,
		'specialChars'    => array(
			'*' => '[\\pL\\pN]*',
			'?' => '.',
			' ' => '\\s*'
		)
	);

	protected $tagName = 'CENSOR';

	protected function setUp()
	{
		$this->collection = new NormalizedCollection;
		$this->collection->onDuplicate('replace');

		if (isset($this->configurator->tags[$this->tagName]))
			return;

		$tag = $this->configurator->tags->add($this->tagName);

		$tag->attributes->add($this->attrName)->required = \false;

		$tag->rules->ignoreTags();

		$tag->template =
			'<xsl:choose>
				<xsl:when test="@' . $this->attrName . '">
					<xsl:value-of select="@' . \htmlspecialchars($this->attrName) . '"/>
				</xsl:when>
				<xsl:otherwise>' . \htmlspecialchars($this->defaultReplacement) . '</xsl:otherwise>
			</xsl:choose>';
	}

	public function allow($word)
	{
		$this->allowed[$word] = \true;
	}

	public function getHelper()
	{
		$config = $this->asConfig();

		if ($config === \false)
			$config = array(
				'attrName' => $this->attrName,
				'regexp'   => '/(?!)/',
				'tagName'  => $this->tagName
			);
		else
			ConfigHelper::filterVariants($config);

		return new Helper($config);
	}

	public function asConfig()
	{
		$words = \array_diff_key(\iterator_to_array($this->collection), $this->allowed);

		if (empty($words))
			return \false;

		$config = array(
			'attrName' => $this->attrName,
			'regexp'   => $this->getWordsRegexp(\array_keys($words)),
			'tagName'  => $this->tagName
		);

		$replacementWords = array();
		foreach ($words as $word => $replacement)
			if (isset($replacement) && $replacement !== $this->defaultReplacement)
				$replacementWords[$replacement][] = $word;

		foreach ($replacementWords as $replacement => $words)
		{
			$regexp = '/^' . RegexpBuilder::fromList($words, $this->regexpOptions) . '$/Diu';

			$variant = new Variant($regexp);

			$regexp = \str_replace('[\\pL\\pN]', '[^\\s!-\\/:-?]', $regexp);
			$variant->set('JS', RegexpConvertor::toJS($regexp));

			$config['replacements'][] = array($variant, $replacement);
		}

		if ($this->allowed)
			$config['allowed'] = $this->getWordsRegexp(\array_keys($this->allowed));

		return $config;
	}

	protected function getWordsRegexp(array $words)
	{
		$regexp = RegexpBuilder::fromList($words, $this->regexpOptions);

		$regexp = \preg_replace('/(?<!\\\\)((?>\\\\\\\\)*)\\(\\?:/', '$1(?>', $regexp);

		$variant = new Variant('/(?<![\\pL\\pN])' . $regexp . '(?![\\pL\\pN])/iu');

		$regexp = \str_replace('[\\pL\\pN]', '[^\\s!-\\/:-?]', $regexp);
		$variant->set('JS', new RegExp('(?:^|\\W)' . $regexp . '(?!\\w)', 'gi'));

		return $variant;
	}
}