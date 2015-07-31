<?php

/**
* @package   s9e\TextFormatter
* @copyright Copyright (c) 2010-2015 The s9e Authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\TextFormatter;

use DOMDocument;
use InvalidArgumentException;

abstract class Renderer
{
	/**
	* @var string Regexp that matches meta elements to be removed
	*/
	public $metaElementsRegexp = '(<[eis]>[^<]*</[eis]>)';

	/**
	* @var array Associative array of [paramName => paramValue]
	*/
	protected $params = [];

	/**
	* Create a return a new DOMDocument loaded with given XML
	*
	* @param  string      $xml Source XML
	* @return DOMDocument
	*/
	protected function loadXML($xml)
	{
		$this->checkUnsupported($xml);

		// Activate small nodes allocation and relax LibXML's hardcoded limits if applicable. Limits
		// on tags can be set during configuration
		$flags = (LIBXML_VERSION >= 20700) ? LIBXML_COMPACT | LIBXML_PARSEHUGE : 0;

		$dom = new DOMDocument;
		$dom->loadXML($xml, $flags);

		return $dom;
	}

	/**
	* Render an intermediate representation
	*
	* @param  string $xml Intermediate representation
	* @return string      Rendered result
	*/
	public function render($xml)
	{
		if (substr($xml, 0, 3) === '<t>')
		{
			return $this->renderPlainText($xml);
		}
		else
		{
			return $this->renderRichText(preg_replace($this->metaElementsRegexp, '', $xml));
		}
	}

	/**
	* Render an intermediate representation of plain text
	*
	* @param  string $xml Intermediate representation
	* @return string      Rendered result
	*/
	protected function renderPlainText($xml)
	{
		// Remove the <t> and </t> tags
		$html = substr($xml, 3, -4);

		// Replace all <br/> with <br>
		$html = str_replace('<br/>', '<br>', $html);

		// Decode encoded characters from the Supplementary Multilingual Plane
		$html = $this->decodeSMP($html);

		return $html;
	}

	/**
	* Render an intermediate representation of rich text
	*
	* @param  string $xml Intermediate representation
	* @return string      Rendered result
	*/
	abstract protected function renderRichText($xml);

	/**
	* Get the value of a parameter
	*
	* @param  string $paramName
	* @return string
	*/
	public function getParameter($paramName)
	{
		return (isset($this->params[$paramName])) ? $this->params[$paramName] : '';
	}

	/**
	* Get the values of all parameters
	*
	* @return array Associative array of parameter names and values
	*/
	public function getParameters()
	{
		return $this->params;
	}

	/**
	* Set the value of a parameter from the stylesheet
	*
	* @param  string $paramName  Parameter name
	* @param  mixed  $paramValue Parameter's value
	* @return void
	*/
	public function setParameter($paramName, $paramValue)
	{
		$this->params[$paramName] = (string) $paramValue;
	}

	/**
	* Set the values of several parameters from the stylesheet
	*
	* @param  string $params Associative array of [parameter name => parameter value]
	* @return void
	*/
	public function setParameters(array $params)
	{
		foreach ($params as $paramName => $paramValue)
		{
			$this->setParameter($paramName, $paramValue);
		}
	}

	/**
	* Test for the presence of unsupported XML and throw an exception if found
	*
	* @param  string $xml XML
	* @return void
	*/
	protected function checkUnsupported($xml)
	{
		if (strpos($xml, '<!') !== false)
		{
			throw new InvalidArgumentException('DTDs, CDATA nodes and comments are not allowed');
		}
		if (strpos($xml, '<?') !== false)
		{
			throw new InvalidArgumentException('Processing instructions are not allowed');
		}
	}

	/**
	* Decode encoded characters from the Supplementary Multilingual Plane
	*
	* @param  string $str Encoded string
	* @return string      Decoded string
	*/
	protected function decodeSMP($str)
	{
		if (strpos($str, '&#') === false)
		{
			return $str;
		}

		return preg_replace_callback('(&#(?:x[0-9A-Fa-f]+|[0-9]+);)', __CLASS__ . '::decodeEntity', $str);
	}

	/**
	* Decode a matched SGML entity
	*
	* @param  string[] $m Captures from PCRE
	* @return string      Decoded entity
	*/
	protected static function decodeEntity(array $m)
	{
		return htmlspecialchars(html_entity_decode($m[0], ENT_NOQUOTES, 'UTF-8'), ENT_QUOTES);
	}
}