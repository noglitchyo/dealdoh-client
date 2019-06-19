<?php declare(strict_types=1);

namespace NoGlitchYo\DealdohClient\Domain;

use NoGlitchYo\DealdohClient\Domain\Exception\UpstreamPoolConfigException;

class UpstreamPoolLoader
{
    public static function load(string $path)
    {
        $content = @file_get_contents($path);

        if ($content === false) {
            throw new UpstreamPoolConfigException('Unable to load upstream pool configuration file.');
        }

        return $content;
    }
}
