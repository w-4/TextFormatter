<?php

/*
* @package   s9e\TextFormatter
* @copyright Copyright (c) 2010-2019 The s9e Authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\TextFormatter\Parser;
use s9e\TextFormatter\Parser;
use s9e\TextFormatter\Parser\Logger;
use s9e\TextFormatter\Parser\Tag;
class FilterProcessing
{
	public static function executeAttributePreprocessors(Tag $tag, array $tagConfig)
	{
		if (empty($tagConfig['attributePreprocessors']))
			return;
		foreach ($tagConfig['attributePreprocessors'] as list($attrName, $regexp, $map))
			if ($tag->hasAttribute($attrName))
				self::executeAttributePreprocessor($tag, $attrName, $regexp, $map);
	}
	public static function filterAttributes(Tag $tag, array $tagConfig, array $registeredVars, Logger $logger)
	{
		$attributes = [];
		foreach ($tagConfig['attributes'] as $attrName => $attrConfig)
		{
			$attrValue = \false;
			if ($tag->hasAttribute($attrName))
			{
				$vars = [
					'attrName'       => $attrName,
					'attrValue'      => $tag->getAttribute($attrName),
					'logger'         => $logger,
					'registeredVars' => $registeredVars
				];
				$attrValue = self::executeAttributeFilterChain($attrConfig['filterChain'], $vars);
			}
			if ($attrValue !== \false)
				$attributes[$attrName] = $attrValue;
			elseif (isset($attrConfig['defaultValue']))
				$attributes[$attrName] = $attrConfig['defaultValue'];
			elseif (!empty($attrConfig['required']))
				$tag->invalidate();
		}
		$tag->setAttributes($attributes);
	}
	public static function filterTag(Tag $tag, Parser $parser, array $tagsConfig, array $openTags)
	{
		$tagName   = $tag->getName();
		$tagConfig = $tagsConfig[$tagName];
		$logger = $parser->getLogger();
		$logger->setTag($tag);
		$text = $parser->getText();
		$vars = [
			'innerText'      => '',
			'logger'         => $logger,
			'openTags'       => $openTags,
			'outerText'      => \substr($text, $tag->getPos(), $tag->getLen()),
			'parser'         => $parser,
			'registeredVars' => $parser->registeredVars,
			'tag'            => $tag,
			'tagConfig'      => $tagConfig,
			'tagText'        => \substr($text, $tag->getPos(), $tag->getLen()),
			'text'           => $text
		];
		$endTag = $tag->getEndTag();
		if ($endTag)
		{
			$vars['innerText'] = \substr($text, $tag->getPos() + $tag->getLen(), $endTag->getPos() - $tag->getPos() - $tag->getLen());
			$vars['outerText'] = \substr($text, $tag->getPos(), $endTag->getPos() + $endTag->getLen() - $tag->getPos());
		}
		foreach ($tagConfig['filterChain'] as $filter)
		{
			if ($tag->isInvalid())
				break;
			self::executeFilter($filter, $vars);
		}
		$logger->unsetTag();
	}
	protected static function executeAttributeFilterChain(array $filterChain, array $vars)
	{
		$vars['logger']->setAttribute($vars['attrName']);
		foreach ($filterChain as $filter)
		{
			$vars['attrValue'] = self::executeFilter($filter, $vars);
			if ($vars['attrValue'] === \false)
				break;
		}
		$vars['logger']->unsetAttribute();
		return $vars['attrValue'];
	}
	protected static function executeAttributePreprocessor(Tag $tag, $attrName, $regexp, $map)
	{
		$attrValue = $tag->getAttribute($attrName);
		$captures  = self::getNamedCaptures($attrValue, $regexp, $map);
		foreach ($captures as $k => $v)
			if ($k === $attrName || !$tag->hasAttribute($k))
				$tag->setAttribute($k, $v);
	}
	protected static function executeFilter(array $filter, array $vars)
	{
		$vars += ['registeredVars' => []];
		$vars += $vars['registeredVars'];
		$args = [];
		if (isset($filter['params']))
			foreach ($filter['params'] as $k => $v)
				$args[] = (isset($vars[$k])) ? $vars[$k] : $v;
		return \call_user_func_array($filter['callback'], $args);
	}
	protected static function getNamedCaptures($str, $regexp, $map)
	{
		if (!\preg_match($regexp, $str, $m))
			return [];
		$values = [];
		foreach ($map as $i => $k)
			if (isset($m[$i]) && $m[$i] !== '')
				$values[$k] = $m[$i];
		return $values;
	}
}