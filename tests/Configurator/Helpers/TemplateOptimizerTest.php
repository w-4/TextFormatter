<?php

namespace s9e\TextFormatter\Tests\Configurator\Helpers;

use s9e\TextFormatter\Tests\Test;
use s9e\TextFormatter\Configurator\Helpers\TemplateOptimizer;

/**
* @covers s9e\TextFormatter\Configurator\Helpers\TemplateOptimizer
*/
class TemplateOptimizerTest extends Test
{
	// Start of content generated by ../../../scripts/patchTemplateOptimizerTest.php
	/**
	* @testdox Comments are removed
	*/
	public function test39C3D587()
	{
		$this->runCase(
			'Comments are removed',
			'<!-- This is a comment -->hi',
			'hi'
		);
	}

	/**
	* @testdox Superfluous whitespace between elements is removed
	*/
	public function testE770FE93()
	{
		$this->runCase(
			'Superfluous whitespace between elements is removed',
			'<div>
					<b>
						<xsl:apply-templates/>
					</b>
				</div>',
			'<div><b><xsl:apply-templates/></b></div>'
		);
	}

	/**
	* @testdox Single space characters are preserved
	*/
	public function test50A8C325()
	{
		$this->runCase(
			'Single space characters are preserved',
			'<b>foo:</b> <i><xsl:apply-templates/></i>',
			'<b>foo:</b> <i><xsl:apply-templates/></i>'
		);
	}

	/**
	* @testdox Superfluous whitespace inside tags is removed
	*/
	public function testAC8ED1D2()
	{
		$this->runCase(
			'Superfluous whitespace inside tags is removed',
			'<div id = "foo" ><xsl:apply-templates /></div >',
			'<div id="foo"><xsl:apply-templates/></div>'
		);
	}

	/**
	* @testdox Superfluous whitespace around XSL attributes is removed
	*/
	public function test4FD06380()
	{
		$this->runCase(
			'Superfluous whitespace around XSL attributes is removed',
			'<div><xsl:value-of select=" @foo "/></div>',
			'<div><xsl:value-of select="@foo"/></div>'
		);
	}

	/**
	* @testdox Superfluous whitespace in simple attribute expressions is removed
	*/
	public function test28322044()
	{
		$this->runCase(
			'Superfluous whitespace in simple attribute expressions is removed',
			'<div><xsl:value-of select="@ foo"/></div>',
			'<div><xsl:value-of select="@foo"/></div>'
		);
	}

	/**
	* @testdox <xsl:element/> is inlined where possible
	*/
	public function testBBC4349B()
	{
		$this->runCase(
			'<xsl:element/> is inlined where possible',
			'<xsl:element name="div"><xsl:apply-templates/></xsl:element>',
			'<div><xsl:apply-templates/></div>'
		);
	}

	/**
	* @testdox <xsl:attribute/> is inlined where possible
	*/
	public function testA48B1064()
	{
		$this->runCase(
			'<xsl:attribute/> is inlined where possible',
			'<div><xsl:attribute name="class"><xsl:value-of select="@foo"/></xsl:attribute><xsl:apply-templates/></div>',
			'<div class="{@foo}"><xsl:apply-templates/></div>'
		);
	}

	/**
	* @testdox Conditional <xsl:attribute/> is replaced with <xsl:copy-of/> where possible
	*/
	public function test933268A2()
	{
		$this->runCase(
			'Conditional <xsl:attribute/> is replaced with <xsl:copy-of/> where possible',
			'<a><xsl:if test="@title"><xsl:attribute name="title"><xsl:value-of select="@title"/></xsl:attribute></xsl:if><xsl:apply-templates/></a>',
			'<a><xsl:copy-of select="@title"/><xsl:apply-templates/></a>'
		);
	}

	/**
	* @testdox Conditional <xsl:attribute/> is not replaced with <xsl:copy-of/> if names do not match
	*/
	public function testADB20165()
	{
		$this->runCase(
			'Conditional <xsl:attribute/> is not replaced with <xsl:copy-of/> if names do not match',
			'<a><xsl:if test="@foo"><xsl:attribute name="title"><xsl:value-of select="@foo"/></xsl:attribute></xsl:if><xsl:apply-templates/></a>',
			'<a><xsl:if test="@foo"><xsl:attribute name="title"><xsl:value-of select="@foo"/></xsl:attribute></xsl:if><xsl:apply-templates/></a>'
		);
	}

	/**
	* @testdox <xsl:text/> is inlined
	*/
	public function testFD8BE5D1()
	{
		$this->runCase(
			'<xsl:text/> is inlined',
			'<b><xsl:text>Hello world</xsl:text></b>',
			'<b>Hello world</b>'
		);
	}
	// End of content generated by ../../../scripts/patchTemplateOptimizerTest.php

	public function runCase($title, $input, $expected)
	{
		$this->assertSame(
			$expected,
			TemplateOptimizer::optimize($input)
		);
	}

	public function getData()
	{
		return array(
			array(
				'Comments are removed',
				'<!-- This is a comment -->hi',
				'hi'
			),
			array(
				'Superfluous whitespace between elements is removed',
				'<div>
					<b>
						<xsl:apply-templates/>
					</b>
				</div>',
				'<div><b><xsl:apply-templates/></b></div>'
			),
			array(
				'Single space characters are preserved',
				'<b>foo:</b> <i><xsl:apply-templates/></i>',
				'<b>foo:</b> <i><xsl:apply-templates/></i>'
			),
			array(
				'Superfluous whitespace inside tags is removed',
				'<div id = "foo" ><xsl:apply-templates /></div >',
				'<div id="foo"><xsl:apply-templates/></div>'
			),
			array(
				'Superfluous whitespace around XSL attributes is removed',
				'<div><xsl:value-of select=" @foo "/></div>',
				'<div><xsl:value-of select="@foo"/></div>'
			),
			array(
				'Superfluous whitespace in simple attribute expressions is removed',
				'<div><xsl:value-of select="@ foo"/></div>',
				'<div><xsl:value-of select="@foo"/></div>'
			),
			array(
				'<xsl:element/> is inlined where possible',
				'<xsl:element name="div"><xsl:apply-templates/></xsl:element>',
				'<div><xsl:apply-templates/></div>'
			),
			array(
				'<xsl:attribute/> is inlined where possible',
				'<div><xsl:attribute name="class"><xsl:value-of select="@foo"/></xsl:attribute><xsl:apply-templates/></div>',
				'<div class="{@foo}"><xsl:apply-templates/></div>'
			),
			array(
				'Conditional <xsl:attribute/> is replaced with <xsl:copy-of/> where possible',
				'<a><xsl:if test="@title"><xsl:attribute name="title"><xsl:value-of select="@title"/></xsl:attribute></xsl:if><xsl:apply-templates/></a>',
				'<a><xsl:copy-of select="@title"/><xsl:apply-templates/></a>'
			),
			array(
				'Conditional <xsl:attribute/> is not replaced with <xsl:copy-of/> if names do not match',
				'<a><xsl:if test="@foo"><xsl:attribute name="title"><xsl:value-of select="@foo"/></xsl:attribute></xsl:if><xsl:apply-templates/></a>',
				'<a><xsl:if test="@foo"><xsl:attribute name="title"><xsl:value-of select="@foo"/></xsl:attribute></xsl:if><xsl:apply-templates/></a>'
			),
			array(
				'<xsl:text/> is inlined',
				'<b><xsl:text>Hello world</xsl:text></b>',
				'<b>Hello world</b>'
			)
		);
	}
}