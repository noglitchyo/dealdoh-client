<?php declare(strict_types=1);

namespace NoGlitchYo\DealdohClient\Domain;

use NoGlitchYo\Dealdoh\Factory\Dns\MessageFactoryInterface;
use NoGlitchYo\Dealdoh\Factory\DohHttpMessageFactoryInterface;
use NoGlitchYo\Dealdoh\Helper\Base64UrlCodecHelper;
use NoGlitchYo\Dealdoh\Service\DnsResolverInterface;
use NoGlitchYo\DealdohClient\Domain\Exception\HttpProxyException;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Throwable;

class HttpProxy
{
    /**
     * @var DnsResolverInterface
     */
    private $dnsResolver;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var MessageFactoryInterface
     */
    private $dnsMessageFactory;

    /**
     * @var DohHttpMessageFactoryInterface
     */
    private $dohHttpMessageFactory;

    public function __construct(
        DnsResolverInterface $dnsResolver,
        MessageFactoryInterface $dnsMessageFactory,
        DohHttpMessageFactoryInterface $dohHttpMessageFactory,
        LoggerInterface $logger = null
    ) {
        $this->dnsResolver = $dnsResolver;
        $this->logger = $logger ?? new NullLogger();
        $this->dnsMessageFactory = $dnsMessageFactory;
        $this->dohHttpMessageFactory = $dohHttpMessageFactory;
    }

    /**
     * @throws HttpProxyException
     */
    public function forward(ServerRequestInterface $serverRequest): ResponseInterface
    {
        try {
            switch ($serverRequest->getMethod()) {
                case 'GET':
                    $dnsQuery = $serverRequest->getQueryParams()['dns'] ?? null;
                    if (!$dnsQuery) {
                        return new Response(400, [], 'Query parameter `dns` is mandatory.');
                    }
                    $dnsWireMessage = Base64UrlCodecHelper::decode($dnsQuery);
                    break;
                case 'POST':
                    $dnsWireMessage = (string)$serverRequest->getBody();
                    break;
                default:
                    return new Response(405);
            }

            $dnsRequestMessage = $this->dnsMessageFactory->createMessageFromDnsWireMessage($dnsWireMessage);
        } catch (Throwable $t) {
            $this->logger->error(
                sprintf('Failed to create DNS message: %s', $t->getMessage()),
                [
                    'exception' => $t,
                    'httpRequest' => $serverRequest
                ]
            );
            throw new HttpProxyException('DNS message creation failed.', 0, $t);
        }

        try {
            $dnsResource = $this->dnsResolver->resolve($dnsRequestMessage);
        } catch (Throwable $t) {
            $this->logger->error(
                sprintf('Failed to resolve DNS query: %s', $t->getMessage()),
                [
                    'exception' => $t,
                    'dnsRequestMessage' => $dnsRequestMessage,
                ]
            );
            throw new HttpProxyException('Resolving DNS message failed.', 0, $t);
        }

        $this->logger->info(
            sprintf("Resolved DNS query with method %s", $serverRequest->getMethod()),
            [
                'dnsRequestMessage'  => $dnsResource->getRequest(),
                'dnsResponseMessage' => $dnsResource->getResponse(),
            ]
        );

        return $this->dohHttpMessageFactory->createResponseFromMessage($dnsResource->getResponse());
    }
}
