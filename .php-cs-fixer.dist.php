<?php

/**
 * The file is part of the "webifycms/domain", WebifyCMS extension package.
 *
 * @see https://webifycms.com/extension/domain
 *
 * @copyright Copyright (c) 2023 WebifyCMS
 * @license https://webifycms.com/extension/domain/license
 * @author Mohammed Shifreen <mshifreen@gmail.com>
 */
declare(strict_types=1);

// should require the composer autoloader on first
require __DIR__ . '/vendor/autoload.php';

use PhpCsFixer\Finder;
use Webify\Tools\Fixer\Fixer;

$finder = Finder::create()
	->in(__DIR__)
	->exclude(
		[
			'vendor',
		]
	)
	->ignoreDotFiles(false)
	->name('*.php')
;
$header = <<<'HEADER'
	The file is part of the "webifycms/domain", WebifyCMS extension package.

	@see https://webifycms.com/extension/domain

	@copyright Copyright (c) 2023 WebifyCMS
	@license https://webifycms.com/extension/domain/license
	@author Mohammed Shifreen <mshifreen@gmail.com>
	HEADER;
$rules = [
	'header_comment'                      => [
		'header'       => $header,
		'location'     => 'after_open',
		'comment_type' => 'PHPDoc',
		'separate'     => 'top',
	],
	'no_superfluous_phpdoc_tags'          => [
		'remove_inheritdoc' => false,
	],
	'php_unit_test_class_requires_covers' => false,
];

return new Fixer($finder, $rules)
	->getConfig()
	->setUsingCache(false)
;
