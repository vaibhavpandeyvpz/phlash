<?php

$header = <<<HEADER
This file is part of vaibhavpandeyvpz/phlash package.

(c) Vaibhav Pandey <contact@vaibhavpandey.com>

This source file is subject to the MIT license that is bundled with this source code in the file LICENSE.
HEADER;

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$config = new Config();
return $config->setFinder(
    Finder::create()
        ->in(__DIR__ . '/src')
        ->in(__DIR__ . '/tests'))
    ->setRules(array(
        '@PSR2' => true,
        'header_comment' => array('header' => $header),
        'array_syntax' => array('syntax' => 'long')))
    ->setUsingCache(true);
