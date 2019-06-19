<?php

use NoGlitchYo\DealdohClient\DealdohClientDependenciesDefinition;

require __DIR__ . '/../vendor/autoload.php';

$builder = new DI\ContainerBuilder();

// add configuration definitions
$builder->addDefinitions(require __DIR__ . '/configuration.php');

// add dependencies definitions
$builder->addDefinitions(DealdohClientDependenciesDefinition::get());

$container = $builder->build();
