<?php

namespace s9e\TextFormatter\Build\PHP53;

$version = (isset($_SERVER['argv'][1])) ? $_SERVER['argv'][1] : PHP_VERSION;

if (version_compare($version, '5.3.99', '>'))
{
	echo 'No need to run ', __FILE__, ' on PHP ', $version, "\n";
	return;
}

function fqn($file)
{
	$table = array();
	if (!preg_match('#namespace ([^;]+)#', $file, $m))
	{
		die("Could not capture namespace from $filepath\n");
	}
	$namespace = $m[1];

	preg_match_all('#^use ([^;]+)#m', $file, $m);
	foreach ($m[1] as $fqn)
	{
		$table[preg_replace('#.*\\\\#', '', $fqn)] = $fqn;
	}

	return array($namespace, $table);
}

function convertCustom($filepath, &$file)
{
	// Some specific tweaks for PHP 5.3 that would be considered bad code in 5.4
	$replacements = array(
		'BBCodeMonkey.php' => array(
			array(
				'return TemplateHelper::replaceTokens(',
				'$_this=$this;return TemplateHelper::replaceTokens('
			),
			array(
				'function ($m) use ($tokens, $passthroughToken)',
				'function ($m) use ($_this, $tokens, $passthroughToken)'
			),
			array(
				'if ($this->isFilter($tokenId))',
				'if ($_this->isFilter($tokenId))'
			),
			array(
				'protected function isFilter($tokenId)',
				'public function isFilter($tokenId)'
			)
		),
		'BundleGenerator.php' => array(
			array(
				'protected function exportCallback($namespace, callable $callback, $argument)',
				'protected function exportCallback($namespace, $callback, $argument)'
			)
		),
		'BundleTest.php' => array(
			array(
				'DummyBundle::$beforeParse = function ($arg)',
				'$test=$this;DummyBundle::$beforeParse = function ($arg) use ($test)'
			),
			array(
				"\$this->assertSame('', \$arg);",
				"\$test->assertSame('', \$arg);"
			),
			array(
				'DummyBundle::$afterParse = function ($arg)',
				'$test=$this;DummyBundle::$afterParse = function ($arg) use ($test)'
			),
			array(
				"\$this->assertSame('<pt></pt>', \$arg);",
				"\$test->assertSame('<pt></pt>', \$arg);"
			),
			array(
				'DummyBundle::$beforeRender = function ($arg)',
				'$test=$this;DummyBundle::$beforeRender = function ($arg) use ($test)'
			),
			array(
				"\$this->assertSame('<pt></pt>', \$arg);",
				"\$test->assertSame('<pt></pt>', \$arg);",
			),
			array(
				'DummyBundle::$afterRender = function ($arg)',
				'$test=$this;DummyBundle::$afterRender = function ($arg) use ($test)'
			),
			array(
				"\$this->assertSame('...', \$arg);",
				"\$test->assertSame('...', \$arg);"
			),
			array(
				'DummyBundle::$beforeUnparse = function ($arg)',
				'$test=$this;DummyBundle::$beforeUnparse = function ($arg) use ($test)'
			),
			array(
				"\$this->assertSame('<pt>original</pt>', \$arg);",
				"\$test->assertSame('<pt>original</pt>', \$arg);"
			),
			array(
				'DummyBundle::$afterUnparse = function ($arg)',
				'$test=$this;DummyBundle::$afterUnparse = function ($arg) use ($test)'
			),
			array(
				"\$this->assertSame('original', \$arg);",
				"\$test->assertSame('original', \$arg);"
			),
		),
		'Configurator.php' => array(
			array(
				// https://bugs.php.net/52854
				'return $reflection->newInstanceArgs(array_slice($args, 1));',
				'return (isset($args[1])) ? $reflection->newInstanceArgs(array_slice($args, 1)) : $reflection->newInstance();'
			),
			array(
				'$options[\'finalizeParser\']($parser);',
				'call_user_func($options[\'finalizeParser\'], $parser);'
			),
			array(
				'$options[\'finalizeRenderer\']($renderer);',
				'call_user_func($options[\'finalizeRenderer\'], $renderer);'
			)
		),
		'ConfiguratorTest.php' => array(
			array(
				"\$cacheDir . '/Renderer_b508f06eee8492e13c62ffc5c3c69011a6a769ff.php'",
				"\$cacheDir . '/Renderer_33632ba0e17861e90687b5fe2b0552c51f21d192.php'"
			),
			array(
				"unlink(\$cacheDir . '/Renderer_b508f06eee8492e13c62ffc5c3c69011a6a769ff.php');",
				"unlink(\$cacheDir . '/Renderer_33632ba0e17861e90687b5fe2b0552c51f21d192.php');"
			)
		),
		'Custom.php' => array(
			array(
				'public function __construct(callable $callback)',
				'public function __construct($callback)'
			),
			array(
				'$this->callback = $callback;',
				str_replace(
					"\n\t\t\t",
					"\n",
					'if (!is_callable($callback))
					{
						trigger_error("Argument 1 passed to " . __METHOD__ . "() must be callable, " . gettype($callback) . " given", E_USER_ERROR);
					}

					$this->callback = $callback;'
				)
			)
		),
		'Censor/Helper.php' => array(
			array(
				'return preg_replace_callback(',
				'$_this=$this;return preg_replace_callback('
			),
			array(
				'$xml = preg_replace_callback(',
				'$_this=$this;$xml = preg_replace_callback('
			),
			array(
				'function ($m)',
				'function ($m) use ($_this)'
			),
			array(
				'			foreach ($this->replacements as list($regexp, $replacement))',
				'			foreach ($_this->replacements as list($regexp, $replacement))'
			),
			array(
				'return htmlspecialchars($this->defaultReplacement);',
				'return htmlspecialchars($_this->defaultReplacement);'
			),
			array(
				'return $this->defaultReplacement;',
				'return $_this->defaultReplacement;'
			),
			array(
				'return (preg_match($this->regexp, $m[1])) ? $this->buildTag($m[1]) : $m[1];',
				'return (preg_match($_this->regexp, $m[1])) ? $_this->buildTag($m[1]) : $m[1];'
			),
			array(
				'return $this->buildTag($m[0]);',
				'return $_this->buildTag($m[0]);'
			),
			array(
				'protected function buildTag($word)',
				'public function buildTag($word)'
			)
		),
		'Logger.php' => array(
			array(
				'$callback($msg, $context);',
				'call_user_func_array($callback, array(&$msg, &$context));'
			)
		),
		'PHP.php' => array(
			array(
				"protected \$dynamicParams=[' . implode(',', \$dynamicParams) . '];",
				"protected \$dynamicParams=array(' . implode(',', \$dynamicParams) . ');"
			),
			array(
				"protected \$params=[' . implode(',', \$staticParams) . '];",
				"protected \$params=array(' . implode(',', \$staticParams) . ');"
			),
			array(
				'return ["htmlOutput","dynamicParams","params"];',
				'return array("htmlOutput","dynamicParams","params");'
			),
			array(
				'$toks = [];',
				'$toks = array();'
			),
			array(
				// mb_substr() doesn't like null as third parameter on PHP 5.3
				"\$php .= 'null';",
				'$php .= 0x7fffffe;'
			)
		),
		'PHPTest.php' => array(
			array(
				"'class Renderer_b508f06eee8492e13c62ffc5c3c69011a6a769ff',",
				"'class Renderer_33632ba0e17861e90687b5fe2b0552c51f21d192',"
			),
			array(
				"'class Bar_renderer_b508f06eee8492e13c62ffc5c3c69011a6a769ff',",
				"'class Bar_renderer_33632ba0e17861e90687b5fe2b0552c51f21d192',"
			),
			array(
				"\$cacheDir . '/Renderer_b508f06eee8492e13c62ffc5c3c69011a6a769ff.php'",
				"\$cacheDir . '/Renderer_33632ba0e17861e90687b5fe2b0552c51f21d192.php'"
			),
			array(
				"unlink(\$cacheDir . '/Renderer_b508f06eee8492e13c62ffc5c3c69011a6a769ff.php');",
				"unlink(\$cacheDir . '/Renderer_33632ba0e17861e90687b5fe2b0552c51f21d192.php');"
			),
			array(
				'"mb_substr(\\$node->textContent,1,null,\'utf-8\')",',
				'"mb_substr(\\$node->textContent,1,134217726,\'utf-8\')",'
			)
		),
		'Regexp.php' => array(
			array(
				"\$variant->setDynamic(\n\t\t\t'JS',\n\t\t\tfunction ()\n\t\t\t{\n\t\t\t\treturn \$this",
				"\$_this=\$this;\$variant->setDynamic(\n\t\t\t'JS',\n\t\t\tfunction () use (\$_this)\n\t\t\t{\n\t\t\t\treturn \$_this"
			)
		),
		'RegexpBuilder.php' => array(
			array(
				'if (preg_match_all(\'#.#us\', $word, $matches) === false)',
				'if (preg_match_all(\'#.#us\', $word, $matches) === false || !preg_match(\'/^(?:[[:ascii:]]|[\\xC0-\\xDF][\\x80-\\xBF]|[\\xE0-\\xEF][\\x80-\\xBF]{2}|[\\xF0-\\xF7][\\x80-\\xBF]{3})*$/D\', $word))'
			)
		),
		'Variant.php' => array(
			array(
				'return ($isDynamic) ? $value() : $value;',
				'return ($isDynamic) ? call_user_func($value) : $value;'
			)
		),
		'XSLTTest.php' => array(
			array(
				"/**\n\t* @testdox setParameter() accepts values that contain both types of quotes\n\t*/\n\tpublic function testSetParameterBothQuotes()",
				"public function _ignore()"
			)
		)
	);

	foreach ($replacements as $path => $pairs)
	{
		$path = '/' . $path;
		if (substr($filepath, -strlen($path)) === $path)
		{
			foreach ($pairs as $pair)
			{
				if (!is_array($pair))
				{
					$file = $pair($file);
					continue;
				}

				list($search, $replace) = $pair;
				$file = str_replace($search, $replace, $file);
			}
		}
	}

	// Some class-specific modifications
	switch (basename($filepath))
	{
		case 'AttributeFilter.php':
			$block =
<<<'END'
	/**
	* Return whether this object is safe to be used in JavaScript
	*
	* @return bool
	*/
	public function isSafeInJS()
	{
		return $this->isSafe('InJS');
	}
END;
			$file = str_replace($block, '', $file);
			break;

		case 'Attribute.php':
			$block =
<<<'END'
	/**
	* Return whether this object is safe to be used in given context
	*
	* @param  string $context Either 'AsURL', 'InCSS' or 'InJS'
	* @return bool
	*/
	protected function isSafe($context)
	{
		// Test whether this attribute was marked as safe in given context
		return !empty($this->markedSafe[$context]);
	}
END;
			$file = str_replace($block, '', $file);
			break;
	}
}

function convertFile($filepath)
{
	$file    = file_get_contents($filepath);
	$oldFile = $file;

	convertUse($filepath, $file);
	convertCustom($filepath, $file);
	convertArraySyntax($file);

	if ($file !== $oldFile)
	{
		echo "Replacing $filepath\n";
		file_put_contents($filepath, $file);
	}
}

function convertUse($filepath, &$file)
{
	if (!strpos($file, "\tuse "))
	{
		return;
	}

	list($namespace, $table) = fqn($file);

	// Hardcode a couple of names
	if (strpos($filepath, 'Parser.php'))
	{
		$table['BuiltInFilters'] = 's9e\\TextFormatter\\Parser\\BuiltInFilters';
		$table['Tag'] = 's9e\\TextFormatter\\Parser\\Tag';
	}

	$file = preg_replace_callback(
		'#^\\tuse ([^;]+);#m',
		function ($m) use ($namespace, &$table)
		{
			$fqn  = (isset($table[$m[1]])) ? $table[$m[1]] : $namespace . '\\' . $m[1];
			$path = __DIR__ . '/../../src/s9e/TextFormatter' . str_replace('\\', DIRECTORY_SEPARATOR, substr($fqn, 17)) . '.php';

			$path = str_replace(
				'/../../src/s9e/TextFormatter/Tests/',
				'/../../tests/',
				$path
			);

			if (!file_exists($path))
			{
				die("Cannot find $fqn in $path\n");
			}

			$file = file_get_contents($path);

			list(, $traitTable) = fqn($file);
			$table += $traitTable;

			preg_match('#\\n{\\n(.*)\\n}$#s', $file, $m);

			return $m[1];
		},
		$file
	);

	if ($table)
	{
		$table = array_unique($table);
		sort($table);

		$file = preg_replace('#^use.*?;\\n\\n#ms', 'use ' . implode(";\nuse ", $table) . ";\n\n", $file);
	}
}

function convertDir($dir)
{
	foreach (glob($dir . '/*', GLOB_ONLYDIR) as $sub)
	{
		convertDir($sub);
	}

	foreach (glob($dir . '/*.php') as $filepath)
	{
		convertFile($filepath);
	}
}

function convertArraySyntax(&$file)
{
	$tokens = token_get_all($file);

	$i       = 0;
	$cnt     = count($tokens);
	$level   = 0;
	$replace = array();

	while (++$i < $cnt)
	{
		$token = $tokens[$i];

		if ($token === '[')
		{
			$j = $i;
			while ($tokens[--$j] === T_WHITESPACE);

			if ($tokens[$j] === ']')
			{
				++$level;
			}
			elseif (is_array($tokens[$j])
			    && ($tokens[$j][0] === T_STRING || $tokens[$j][0] === T_VARIABLE))
			{
				++$level;
			}
			else
			{
				$tokens[$i] = 'array(';
				$replace[]  = $level;
			}
		}
		elseif ($token === ']')
		{
			if ($level === end($replace))
			{
				$tokens[$i] = ')';
				array_pop($replace);
			}
			else
			{
				--$level;
			}
		}
	}

	$file = '';
	foreach ($tokens as $token)
	{
		$file .= (is_string($token)) ? $token : $token[1];
	}
}

convertDir(realpath(__DIR__ . '/../../src/s9e/TextFormatter'));
convertDir(realpath(__DIR__ . '/../../tests'));
