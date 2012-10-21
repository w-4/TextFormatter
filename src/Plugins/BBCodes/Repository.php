<?php

/**
* @package   s9e\TextFormatter
* @copyright Copyright (c) 2010-2012 The s9e Authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\TextFormatter\Plugins\BBCodes;

use DOMDocument;
use DOMXPath;
use InvalidArgumentException;
use RuntimeException;
use s9e\TextFormatter\Generator\Collections\NormalizeCollection;

class Repository
{
	/**
	* @var DOMDocument Repository document
	*/
	protected $dom;

	/**
	* Constructor
	*
	* @return void
	*/
	public function __construct($value)
	{
		if (!($value instanceof DOMDocument))
		{
			if (!file_exists($value))
			{
				throw new InvalidArgumentException('Not a DOMDocument or the path to a repository file');
			}

			$dom = new DOMDocument;
			$dom->preserveWhiteSpace = false;


			$useErrors = libxml_use_internal_errors(true);
			$success = $dom->load($value);
			libxml_use_internal_errors($useErrors);

			if (!$success)
			{
				throw new InvalidArgumentException('Invalid repository file');
			}

			$value = $dom;
		}

		$this->dom = $value;

		return $value;
	}

	/**
	* Get a BBCode and its associated tag from this repository
	*
	* @param  string $bbcodeName BBCode's name
	* @param  array  $vars       Template variables
	* @return array              Array with two keys: "bbcode" and"tag"
	*/
	public function get($bbcodeName, array $vars = array())
	{
		$bbcodeName = BBCode::normalizeName($bbcodeName);

		$xpath = new DOMXPath($this->dom);
		$node  = $xpath->query('//bbcode[@name="' . $bbcodeName . '"]')->item(0);

		if (!$node)
		{
			throw new RuntimeException("Could not find BBCode '" . $bbcodeName . "' in repository");
		}

		// Now we can parse the BBCode usage and prepare the template
		// Grab the content of the <usage> element then use BBCodeMonkey to parse it
		$usage  = $node->getElementsByTagName('usage')->item(0)->textContent;
		$config = BBCodeMonkey::parse($usage);

		if ($node->hasAttribute('tagName'))
		{
			$config['bbcode']->tagName = $node->getAttribute('tagName');
		}

		// Clone the <template> element
		$templateNode = $node->getElementsByTagName('template')->item(0)->cloneNode(true);

		// Replace all the <var> descendants if applicable
		foreach ($templateNode->getElementsByTagName('var') as $varNode)
		{
			$varName = $varNode->getAttribute('name');

			if (isset($vars[$varName]))
			{
				$varNode->parentNode->replaceChild(
					$this->dom->createTextNode($vars[$varName]),
					$varNode
				);
			}
		}

		// Now process the template
		$config['tag']->defaultTemplate = BBCodeMonkey::replaceTokens(
			$templateNode->textContent,
			$config['tokens'],
			$config['passthroughToken']
		);

		return array(
			'bbcode' => $config['bbcode'],
			'tag'    => $config['tag']
		);
	}
}