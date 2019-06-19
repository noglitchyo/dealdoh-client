<?php declare(strict_types=1);

use NoGlitchYo\DealdohClient\DealdohClientDependenciesDefinition;
use NoGlitchYo\DealdohClient\Domain\UpstreamPoolLoader;
use Psr\Container\ContainerInterface;

return [
    DealdohClientDependenciesDefinition::UPSTREAM_POOL_FILE_PATH => __DIR__ . '/../config/upstreampool.json',
    DealdohClientDependenciesDefinition::UPSTREAM_POOL_JSON => function (ContainerInterface $container) {
        try {
            $upstreams = UpstreamPoolLoader::load($container->get(DealdohClientDependenciesDefinition::UPSTREAM_POOL_FILE_PATH));
        } catch (Throwable $t) {
            $upstreams = '[]';
        }
        return $upstreams;
    }
];

