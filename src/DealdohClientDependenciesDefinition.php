<?php declare(strict_types=1);

namespace NoGlitchYo\DealdohClient;

use GuzzleHttp\Client as HttpClient;
use Http\Adapter\Guzzle6\Client;
use NoGlitchYo\Dealdoh\Client\DohClient;
use NoGlitchYo\Dealdoh\Client\GoogleDnsClient;
use NoGlitchYo\Dealdoh\Client\StdClient;
use NoGlitchYo\Dealdoh\DohProxy;
use NoGlitchYo\Dealdoh\Entity\DnsUpstreamPool;
use NoGlitchYo\Dealdoh\Entity\DnsUpstreamPoolInterface;
use NoGlitchYo\Dealdoh\Factory\Dns\MessageFactory;
use NoGlitchYo\Dealdoh\Factory\Dns\MessageFactoryInterface;
use NoGlitchYo\Dealdoh\Factory\DohHttpMessageFactory;
use NoGlitchYo\Dealdoh\Factory\DohHttpMessageFactoryInterface;
use NoGlitchYo\Dealdoh\Mapper\GoogleDns\MessageMapper;
use NoGlitchYo\Dealdoh\Service\DnsPoolResolver;
use NoGlitchYo\DealdohClient\Action\Command\AddUpstreamCommand;
use NoGlitchYo\DealdohClient\Action\Command\ListUpstreamCommand;
use NoGlitchYo\DealdohClient\Action\Command\ResolveCommand;
use NoGlitchYo\DealdohClient\Domain\Service\UpstreamPool\AddUpstreamService;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Socket\Raw\Factory;

/**
 * @codeCoverageIgnore
 */
class DealdohClientDependenciesDefinition
{
    public const UPSTREAM_POOL_FILE_PATH = 'upstream.pool.file_path';
    public const UPSTREAM_POOL_JSON = 'upstream.pool.json';

    public static function get(): array
    {
        return [
            MessageFactory::class => function (): MessageFactoryInterface {
                return new MessageFactory();
            },
            DohHttpMessageFactory::class => function (ContainerInterface $container): DohHttpMessageFactoryInterface {
                return new DohHttpMessageFactory($container->get(MessageFactory::class));
            },
            Client::class => function (): ClientInterface {
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
            DohProxy::class => function (ContainerInterface $container): DohProxy {
                return new DohProxy(
                    $container->get(DnsPoolResolver::class),
                    $container->get(MessageFactory::class),
                    $container->get(DohHttpMessageFactory::class)
                );
            },
            DnsUpstreamPool::class => function (ContainerInterface $container): DnsUpstreamPoolInterface {
                return DnsUpstreamPool::fromJson(
                    $container->get(DealdohClientDependenciesDefinition::UPSTREAM_POOL_JSON)
                );
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
            ResolveCommand::class => function (ContainerInterface $container): ResolveCommand {
                return new ResolveCommand(
                    $container->get(DnsPoolResolver::class),
                    $container->get(MessageFactory::class)
                );
            },
            AddUpstreamCommand::class => function (ContainerInterface $container): AddUpstreamCommand {
                return new AddUpstreamCommand(
                    $container->get(self::UPSTREAM_POOL_FILE_PATH),
                    $container->get(AddUpstreamService::class)
                );
            },
            ListUpstreamCommand::class => function (ContainerInterface $container): ListUpstreamCommand {
                return new ListUpstreamCommand($container->get(self::UPSTREAM_POOL_FILE_PATH));
            },
            AddUpstreamService::class => function (ContainerInterface $container): AddUpstreamService {
                return new AddUpstreamService($container->get(self::UPSTREAM_POOL_FILE_PATH));
            }
        ];
    }
}
