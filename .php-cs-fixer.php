<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config())
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setRules([
        // PSR Standards
        '@PSR12' => true,
        '@PSR12:risky' => true,
        '@DoctrineAnnotation' => true,

        // Import & Namespace
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,
        'global_namespace_import' => [
            'import_classes' => true,
            'import_functions' => true,
            'import_constants' => true,
        ],

        // Arrays
        'array_syntax' => ['syntax' => 'short'],
        'trim_array_spaces' => true,
        'whitespace_after_comma_in_array' => true,

        // Strings
        'single_quote' => true,
        'string_implicit_backslashes' => true,
        'string_length_to_empty' => true,

        // Formatting
        'binary_operator_spaces' => [
            'default' => 'single_space',
        ],
        'concat_space' => ['spacing' => 'one'],
        'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],

        // Functions & Methods
        'type_declaration_spaces' => true,
        'nullable_type_declaration_for_default_null_value' => true,

        // Classes & Methods
        'class_attributes_separation' => [
            'elements' => ['method' => 'one', 'property' => 'one'],
        ],
        'protected_to_private' => false, // falls du Protected brauchst
        'self_static_accessor' => true,

        // Whitespace
        'no_extra_blank_lines' => [
            'tokens' => [
                'extra',
                'use',
                'throw',
                'return',
                'continue',
                'curly_brace_block',
            ],
        ],

        // Comments
        'single_line_comment_spacing' => true,

        // Modernization / Risky
        'strict_param' => true,
        'no_alias_functions' => true,
        'native_function_invocation' => [
            'include' => ['@all'],
        ],
        'simplified_null_return' => true,
    ]);
