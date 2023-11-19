<?php
declare(strict_types=1);

/*
    COPYRIGHT DISCLAIMER:
    --------------------

    A lot of code in this project is modified from or directly inspired by
    the excellent example from Kevin Smith: https://github.com/kevinsmith/no-framework

    Here is the related article by Kevin Smith: https://kevinsmith.io/modern-php-without-a-framework/
 */


namespace Ds7\Asignacion4\Application;

use DI\ContainerBuilder;
use Ds7\Asignacion4\Config\ConfigAdapter;
use Ds7\Asignacion4\Infrastructure\Web\Controller\WelcomeController;
use Ds7\Asignacion4\Core\Db\PersistanceGatewayOperations;
use Ds7\Asignacion4\Infrastructure\Db\MySQLPersistanceGateway;
use Ds7\Asignacion4\Infrastructure\Db\PersistanceMapper;
use mysqli;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Relay\Relay;
use function DI\autowire;
use function DI\create;
use function FastRoute\simpleDispatcher;

class App
{

    public function setupTemplatesProcessor(string $templatesDir): TemplatesProcessor
    {
        return new TwigTemplatesProcessor($templatesDir);
    }

    public function setupResponseEmitter(): ResponseEmitter
    {
        return new SapiResponseEmitter();
    }

    public function setupContainer(TemplatesProcessor $templatesProcessor,
                                   ResponseEmitter    $responseEmitter): ContainerInterface
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->useAutowiring(true);
        $containerBuilder->useAnnotations(false);
        $containerBuilder->addDefinitions([

            // PSR-7 request and response
            ResponseInterface::class => create(Response::class),
            ServerRequestInterface::class => function () {
                return ServerRequestFactory::fromGlobals();
            },

            // template processor, response emitter, etc.
            TemplatesProcessor::class => $templatesProcessor,
            ResponseEmitter::class => $responseEmitter,

            PersistanceGatewayOperations::class => create(MySQLPersistanceGateway::class),
            PersistanceMapper::class => create(PersistanceMapper::class)
        ]);
        return $containerBuilder->build();
    }

    public function setupRouting(): Dispatcher
    {
        return simpleDispatcher(function (RouteCollector $r) {
            $r->get('/', WelcomeController::class);
        });
    }

    public function setupMiddleware(Dispatcher $routes, ContainerInterface $container): RequestHandlerInterface
    {
        $middlewareQueue[] = new FastRoute($routes);
        $middlewareQueue[] = new RequestHandler($container);
        return new Relay($middlewareQueue);
    }

    public function setupPersistence(string $host, string $username, string $password): \mysqli
    {
        return new \mysqli($host, $username, $password, 'bdnautico');
    }

    public function run(RequestHandlerInterface $requestHandler, ContainerInterface $container): void
    {

        $request = $container->get(ServerRequestInterface::class);
        $requestHandler->handle($request);
    }
}