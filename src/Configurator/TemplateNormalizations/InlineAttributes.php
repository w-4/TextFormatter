<?php

/**
* @package   s9e\TextFormatter
* @copyright Copyright (c) 2010-2015 The s9e Authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\TextFormatter\Configurator\TemplateNormalizations;

use DOMElement;
use DOMException;
use DOMText;
use DOMXPath;
use s9e\TextFormatter\Configurator\TemplateNormalization;

class InlineAttributes extends TemplateNormalization
{
	/**
	* Inline the attribute declarations of a template
	*
	* Will replace
	*     <a><xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>...</a>
	* with
	*     <a href="{@url}">...</a>
	*
	* @param  DOMElement $template <xsl:template/> node
	* @return void
	*/
	public function normalize(DOMElement $template)
	{
		$xpath = new DOMXPath($template->ownerDocument);
		$query = '//*[namespace-uri() != "' . self::XMLNS_XSL . '"]/xsl:attribute';

		foreach ($xpath->query($query) as $attribute)
		{
			$this->inlineAttribute($attribute);
		}
	}

	/**
	* Inline the content of an xsl:attribute element
	*
	* @param  DOMElement $attribute xsl:attribute element
	* @return void
	*/
	protected function inlineAttribute(DOMElement $attribute)
	{
		$value = '';
		foreach ($attribute->childNodes as $childNode)
		{
			if ($childNode instanceof DOMText)
			{
				$value .= preg_replace('([{}])', '$0$0', $childNode->textContent);
			}
			elseif ($childNode->namespaceURI === self::XMLNS_XSL
			     && $childNode->localName    === 'value-of')
			{
				$value .= '{' . $childNode->getAttribute('select') . '}';
			}
			elseif ($childNode->namespaceURI === self::XMLNS_XSL
			     && $childNode->localName    === 'text')
			{
				$value .= preg_replace('([{}])', '$0$0', $childNode->textContent);
			}
			else
			{
				// Can't inline this attribute
				return;
			}
		}

		$attribute->parentNode->setAttribute($attribute->getAttribute('name'), $value);
		$attribute->parentNode->removeChild($attribute);
	}
}