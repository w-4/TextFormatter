<?php

/*
* @package   s9e\TextFormatter
* @copyright Copyright (c) 2010-2015 The s9e Authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\TextFormatter;

use InvalidArgumentException;
use RuntimeException;
use s9e\TextFormatter\Configurator\BundleGenerator;
use s9e\TextFormatter\Configurator\Collections\AttributeFilterCollection;
use s9e\TextFormatter\Configurator\Collections\PluginCollection;
use s9e\TextFormatter\Configurator\Collections\Ruleset;
use s9e\TextFormatter\Configurator\Collections\TagCollection;
use s9e\TextFormatter\Configurator\ConfigProvider;
use s9e\TextFormatter\Configurator\Helpers\ConfigHelper;
use s9e\TextFormatter\Configurator\Helpers\RulesHelper;
use s9e\TextFormatter\Configurator\JavaScript;
use s9e\TextFormatter\Configurator\Rendering;
use s9e\TextFormatter\Configurator\RulesGenerator;
use s9e\TextFormatter\Configurator\TemplateChecker;
use s9e\TextFormatter\Configurator\TemplateNormalizer;
use s9e\TextFormatter\Configurator\UrlConfig;

class Configurator implements ConfigProvider
{
	public $attributeFilters;

	public $bundleGenerator;

	public $javascript;

	public $plugins;

	public $registeredVars;

	public $rendering;

	public $rootRules;

	public $rulesGenerator;

	public $tags;

	public $templateChecker;

	public $templateNormalizer;

	public function __construct()
	{
		$this->attributeFilters   = new AttributeFilterCollection;
		$this->bundleGenerator    = new BundleGenerator($this);
		$this->plugins            = new PluginCollection($this);
		$this->registeredVars     = array('urlConfig' => new UrlConfig);
		$this->rendering          = new Rendering($this);
		$this->rootRules          = new Ruleset;
		$this->rulesGenerator     = new RulesGenerator;
		$this->tags               = new TagCollection;
		$this->templateChecker    = new TemplateChecker;
		$this->templateNormalizer = new TemplateNormalizer;
	}

	public function __get($k)
	{
		if (\preg_match('#^[A-Z][A-Za-z_0-9]+$#D', $k))
			return (isset($this->plugins[$k]))
			     ? $this->plugins[$k]
			     : $this->plugins->load($k);

		if (isset($this->registeredVars[$k]))
			return $this->registeredVars[$k];

		throw new RuntimeException("Undefined property '" . __CLASS__ . '::$' . $k . "'");
	}

	public function __isset($k)
	{
		if (\preg_match('#^[A-Z][A-Za-z_0-9]+$#D', $k))
			return isset($this->plugins[$k]);

		return isset($this->registeredVars[$k]);
	}

	public function __set($k, $v)
	{
		if (\preg_match('#^[A-Z][A-Za-z_0-9]+$#D', $k))
			$this->plugins[$k] = $v;
		else
			$this->registeredVars[$k] = $v;
	}

	public function __unset($k)
	{
		if (\preg_match('#^[A-Z][A-Za-z_0-9]+$#D', $k))
			unset($this->plugins[$k]);
		else
			unset($this->registeredVars[$k]);
	}

	public function enableJavaScript()
	{
		if (!isset($this->javascript))
			$this->javascript = new JavaScript($this);
	}

	public function finalize(array $options = array())
	{
		$return = array();

		$options += array(
			'addHTML5Rules'  => \true,
			'optimizeConfig' => \true,
			'returnJS'       => isset($this->javascript),
			'returnParser'   => \true,
			'returnRenderer' => \true
		);

		if ($options['addHTML5Rules'])
			$this->addHTML5Rules($options);

		if ($options['returnRenderer'])
		{
			$renderer = $this->getRenderer();

			if (isset($options['finalizeRenderer']))
				\call_user_func($options['finalizeRenderer'], $renderer);

			$return['renderer'] = $renderer;
		}

		if ($options['returnJS'] || $options['returnParser'])
		{
			$config = $this->asConfig();

			if ($options['returnJS'])
			{
				$jsConfig = $config;
				ConfigHelper::filterVariants($jsConfig, 'JS');

				$return['js'] = $this->javascript->getParser($jsConfig);
			}

			if ($options['returnParser'])
			{
				ConfigHelper::filterVariants($config);

				if ($options['optimizeConfig'])
					ConfigHelper::optimizeArray($config);

				$parser = new Parser($config);

				if (isset($options['finalizeParser']))
					\call_user_func($options['finalizeParser'], $parser);

				$return['parser'] = $parser;
			}
		}

		return $return;
	}

	public function getParser()
	{
		$config = $this->asConfig();

		ConfigHelper::filterVariants($config);

		return new Parser($config);
	}

	public function getRenderer()
	{
		return $this->rendering->getRenderer();
	}

	public function loadBundle($bundleName)
	{
		if (!\preg_match('#^[A-Z][A-Za-z0-9]+$#D', $bundleName))
			throw new InvalidArgumentException("Invalid bundle name '" . $bundleName . "'");

		$className = __CLASS__ . '\\Bundles\\' . $bundleName;

		$bundle = new $className;
		$bundle->configure($this);
	}

	public function saveBundle($className, $filepath, array $options = array())
	{
		$file = "<?php\n\n" . $this->bundleGenerator->generate($className, $options);

		return (\file_put_contents($filepath, $file) !== \false);
	}

	public function addHTML5Rules(array $options = array())
	{
		$options += array('rootRules' => $this->rootRules);

		$this->plugins->finalize();

		foreach ($this->tags as $tag)
			$this->templateNormalizer->normalizeTag($tag);

		$rules = $this->rulesGenerator->getRules($this->tags, $options);

		$this->rootRules->merge($rules['root'], \false);

		foreach ($rules['tags'] as $tagName => $tagRules)
			$this->tags[$tagName]->rules->merge($tagRules, \false);
	}

	public function asConfig()
	{
		$this->plugins->finalize();

		$properties = \get_object_vars($this);
		unset($properties['attributeFilters']);
		unset($properties['bundleGenerator']);
		unset($properties['javascript']);
		unset($properties['rendering']);
		unset($properties['rulesGenerator']);
		unset($properties['templateChecker']);
		unset($properties['templateNormalizer']);
		unset($properties['stylesheet']);

		$config    = ConfigHelper::toArray($properties);
		$bitfields = RulesHelper::getBitfields($this->tags, $this->rootRules);

		$config['rootContext'] = $bitfields['root'];
		$config['rootContext']['flags'] = $config['rootRules']['flags'];

		$config += array(
			'plugins'        => array(),
			'registeredVars' => array(),
			'tags'           => array()
		);

		$config['tags'] = \array_intersect_key($config['tags'], $bitfields['tags']);

		foreach ($bitfields['tags'] as $tagName => $tagBitfields)
			$config['tags'][$tagName] += $tagBitfields;

		unset($config['rootRules']);

		return $config;
	}
}