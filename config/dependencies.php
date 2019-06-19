<?php declare(strict_types=1);

use GuzzleHttp\Client as HttpClient;
use Http\Adapter\Guzzle6\Client;
use NoGlitchYo\Dealdoh\Client\DohClient;
use NoGlitchYo\Dealdoh\Client\GoogleDnsClient;
use NoGlitchYo\Dealdoh\Client\StdClient;
use NoGlitchYo\Dealdoh\Entity\DnsUpstreamPool;
use NoGlitchYo\Dealdoh\Entity\DnsUpstreamPoolInterface;
use NoGlitchYo\Dealdoh\Factory\Dns\MessageFactory;
use NoGlitchYo\Dealdoh\Factory\Dns\MessageFactoryInterface;
use NoGlitchYo\Dealdoh\Factory\DohHttpMessageFactory;
use NoGlitchYo\Dealdoh\Factory\DohHttpMessageFactoryInterface;
use NoGlitchYo\Dealdoh\Mapper\GoogleDns\MessageMapper;
use NoGlitchYo\Dealdoh\Service\DnsPoolResolver;
use NoGlitchYo\DealdohClient\Domain\HttpProxy;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Socket\Raw\Factory;

return [
    MessageFactory::class => function (ContainerInterface $container): MessageFactoryInterface {
        return new MessageFactory();
    },
    DohHttpMessageFactory::class => function (ContainerInterface $container): DohHttpMessageFactoryInterface {
        return new DohHttpMessageFactory($container->get(MessageFactory::class));
    },
    Client::class => function (ContainerInterface $container): ClientInterface {
        return new Client(new HttpClient(['verify' => false]));
    },
    GoogleDnsClient::class => function (ContainerInterface $container): GoogleDnsClient {
        return new GoogleDnsClient($container->get(Client::class), new MessageMapper());
    },
    DohClient::class => function (ContainerInterface $container): DohClient {
        return new DohClient($container->get(Client::class), $container->get(MessageFactory::class));
    },
    StdClient::class => function (ContainerInterface $container): StdClient {
        return new StdClient(new Factory(), $container->get(MessageFactory::class));
    },
    HttpProxy::class => function (ContainerInterface $container): HttpProxy {
        return new HttpProxy(
            $container->get(DnsPoolResolver::class),
            $container->get(MessageFactory::class),
            $container->get(DohHttpMessageFactory::class)
        );
    },
    DnsUpstreamPool::class => function (ContainerInterface $container): DnsUpstreamPoolInterface {
        return new DnsUpstreamPool($container->get(DealdohClientDependenciesDefinition::UPSTREAM_POOL_FILE_PATH));
    },
    DnsPoolResolver::class => function (ContainerInterface $container): DnsPoolResolver {
        return new DnsPoolResolver(
            $container->get(DnsUpstreamPool::class),
            [
                $container->get(GoogleDnsClient::class),
                $container->get(StdClient::class),
                $container->get(DohClient::class),
            ]
        );
    },
    DnsQueryAction::class => function (ContainerInterface $container): DnsQueryAction {
        return new DnsQueryAction($container->get(HttpProxy::class));
    },
];
