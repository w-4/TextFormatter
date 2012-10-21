#!/usr/bin/php
<?php

namespace s9e\TextFormatter\Tests;

class Test {}

$filepath = __DIR__ . '/../tests/Generator/Helpers/HTML5/TemplateForensicsTest.php';
include $filepath;

$test = new Generator\Helpers\HTML5\TemplateForensicsTest;

$php = '';
foreach ($test->getData() as $case)
{
	$php .= "\n\t/**\n\t* @testdox " . $case[0] . "\n\t*/\n\tpublic function test" . sprintf('%08X', crc32(serialize($case))) . "()\n\t{\n\t\t\$this->runCase(";
	
	foreach ($case as $k => $str)
	{
		if ($k)
		{
			$php .= ',';
		}

		$php .= "\n\t\t\t" . var_export($str, true);
	}

	$php .= "\n\t\t);\n\t}\n";
}

$file = file_get_contents($filepath);

$startComment = '// Start of content generated by ../../../../scripts/patchTemplateForensicsTest.php';
$endComment = "\t// End of content generated by ../../../../scripts/patchTemplateForensicsTest.php";

$file = substr($file, 0, strpos($file, $startComment) + strlen($startComment))
      . $php
      . substr($file, strpos($file, $endComment));

file_put_contents($filepath, $file);

die("Done.\n");