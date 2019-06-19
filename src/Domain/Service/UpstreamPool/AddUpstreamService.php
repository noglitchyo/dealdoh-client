<?php declare(strict_types=1);


namespace NoGlitchYo\DealdohClient\Domain\Service\UpstreamPool;

use Exception;
use NoGlitchYo\Dealdoh\Entity\DnsUpstream;
use NoGlitchYo\Dealdoh\Entity\DnsUpstreamPool;
use NoGlitchYo\DealdohClient\Domain\Exception\UpstreamPoolConfigException;
use NoGlitchYo\DealdohClient\Domain\UpstreamPoolLoader;

class AddUpstreamService
{
    /**
     * @var string
     */
    private $upstreamPoolFilePath;

    public function __construct(string $upstreamPoolFilePath)
    {
        $this->upstreamPoolFilePath = $upstreamPoolFilePath;
    }

    public function add(string $upstreamUri, string $upstreamCode): DnsUpstreamPool
    {
        try {
            $dnsUpstreamPool = DnsUpstreamPool::fromJson(UpstreamPoolLoader::load($this->upstreamPoolFilePath));
        } catch (UpstreamPoolConfigException $e) {
            $dnsUpstreamPool = new DnsUpstreamPool();
        }

        $dnsUpstreamPool->addUpstream(new DnsUpstream($upstreamUri, $upstreamCode));

        $upstreamJsonEncoded = json_encode($dnsUpstreamPool, JSON_PRETTY_PRINT);

        if (false === file_put_contents($this->upstreamPoolFilePath, $upstreamJsonEncoded)) {
            throw new Exception(sprintf('Failed to dump upstream pool to %s', $this->upstreamPoolFilePath));
        }

        return $dnsUpstreamPool;
    }
}
