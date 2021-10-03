<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('vendor')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR2' => true,
        'array_syntax' => ['syntax' => 'short'],
        'function_declaration' => ['closure_function_spacing' => 'none'],
        'single_import_per_statement' => false,
    ])
    ->setFinder($finder)
;
