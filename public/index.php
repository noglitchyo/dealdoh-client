<?php

require __DIR__ . '/../config/bootstrap.php';

use NoGlitchYo\Dealdoh\DohProxy;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;
use Slim\Middleware\ErrorMiddleware;

$app = AppFactory::create();

$responseFactory = $app->getResponseFactory();
$errorMiddleware = new ErrorMiddleware($app->getCallableResolver(), $responseFactory, true, true, true);
$app->add($errorMiddleware);

$app->map(['GET', 'POST'], '/dns-query', function (ServerRequestInterface $serverRequest){

})->addMiddleware($container->get(DohProxy::class));

$app->run();

