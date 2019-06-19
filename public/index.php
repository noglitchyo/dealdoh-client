<?php

require __DIR__ . '/../config/bootstrap.php';

use NoGlitchYo\DealdohClient\Action\Http\DnsQueryAction;
use Slim\Factory\AppFactory;
use Slim\Middleware\ErrorMiddleware;

$app = AppFactory::create();

$responseFactory = $app->getResponseFactory();
$errorMiddleware = new ErrorMiddleware($app->getCallableResolver(), $responseFactory, true, true, true);
$app->add($errorMiddleware);

$app->map(['GET', 'POST'], '/dns-query', $container->get(DnsQueryAction::class));

$app->run();

