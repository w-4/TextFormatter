<?php

namespace s9e\TextFormatter\Tests\Configurator\Items;

use s9e\TextFormatter\Tests\Test;
use s9e\TextFormatter\Configurator\Items\CallbackTemplate;

/**
* @covers s9e\TextFormatter\Configurator\Items\CallbackTemplate
*/
class CallbackTemplateTest extends Test
{
	/**
	* @testdox __construct() throws an InvalidArgumentException if its argument is not callable
	* @expectedException InvalidArgumentException
	* @expectedExceptionMessage Callback '*invalid*' is not callable
	*/
	public function testInvalidCallback()
	{
		new CallbackTemplate('*invalid*');
	}

	/**
	* @testdox toArray() returns the callback template as an array
	*/
	public function testToArray()
	{
		$ct = new CallbackTemplate('mt_rand');

		$this->assertEquals(
			array(
				'callback' => 'mt_rand',
				'params'   => array()
			),
			$ct->toArray()
		);
	}

	/**
	* @testdox CallbackTemplate::fromArray() creates an instance from an array
	*/
	public function testFromArray()
	{
		$ct = CallbackTemplate::fromArray(
			array(
				'callback' => 'mt_rand',
				'params'   => array()
			)
		);

		$this->assertEquals(
			$ct,
			new CallbackTemplate('mt_rand')
		);
	}

	/**
	* @testdox addParameterByValue() adds a parameter as a value with no name
	*/
	public function testAddParameterByValue()
	{
		$ct = new CallbackTemplate('strtolower');
		$ct->addParameterByValue('foobar');

		$this->assertEquals(
			array(
				'callback' => 'strtolower',
				'params'   => array('foobar')
			),
			$ct->toArray()
		);
	}

	/**
	* @testdox addParameterByName() adds a parameter as a name with no value
	*/
	public function testAddParameterByName()
	{
		$ct = new CallbackTemplate('strtolower');
		$ct->addParameterByName('foobar');

		$this->assertEquals(
			array(
				'callback' => 'strtolower',
				'params'   => array('foobar' => null)
			),
			$ct->toArray()
		);
	}

	/**
	* @testdox Callback ['foo','bar'] is normalized to 'foo::bar'
	*/
	public function testNormalizeStatic()
	{
		$ct = new CallbackTemplate(array(__NAMESPACE__ . '\\DummyStaticCallback', 'bar'));

		$this->assertEquals(
			array(
				'callback' => __NAMESPACE__ . '\\DummyStaticCallback::bar',
				'params'   => array()
			),
			$ct->toArray()
		);
	}
}

class DummyStaticCallback
{
	public static function bar()
	{
	}
}