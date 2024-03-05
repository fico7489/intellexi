<?php

$finder = (new PhpCsFixer\Finder())
    ->in('app')
    ->in('tests')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
    ])
    ->setFinder($finder)
;
