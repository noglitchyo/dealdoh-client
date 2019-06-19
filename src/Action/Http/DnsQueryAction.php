<?php declare(strict_types=1);

namespace NoGlitchYo\DealdohClient\Action\Http;

use NoGlitchYo\DealdohClient\Domain\HttpProxy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DnsQueryAction
{
    /**
     * @var HttpProxy
     */
    private $httpProxy;

    public function __construct(HttpProxy $httpProxy)
    {
        $this->httpProxy = $httpProxy;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        return $this->httpProxy->forward($request);
    }
}
