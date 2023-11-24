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
use Ds7\Asignacion4\Infrastructure\Web\Controller\ViajesController;
use Ds7\Asignacion4\Infrastructure\Web\Controller\BarcosController;
use Ds7\Asignacion4\Infrastructure\Web\Controller\PatronesController;
use Ds7\Asignacion4\Core\Db\PersistenceGatewayOperations;
use Ds7\Asignacion4\Infrastructure\Db\MySQLPersistenceGateway;
use Ds7\Asignacion4\Infrastructure\Db\PersistenceMapper;
use Ds7\Asignacion4\Core\UseCase\ListarBarcosUseCase;
use Ds7\Asignacion4\Core\UseCase\ListarPatronesUseCase;
use Ds7\Asignacion4\Core\UseCase\ListarViajesUseCase;
use Ds7\Asignacion4\Core\UseCase\RegistrarViajeUseCase;
use mysqli;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use Middlewares\FastRoute;
use Middlewares\GzipEncoder;
use Middlewares\Expires;
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
                                   ResponseEmitter    $responseEmitter,
                                   mysqli $db): ContainerInterface
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

            mysqli::class => $db,

            PersistenceMapper::class => create(PersistenceMapper::class),
            PersistenceGatewayOperations::class => autowire(MySQLPersistenceGateway::class),

            ListarBarcosUseCase::class => autowire(ListarBarcosUseCase::class),
            ListarPatronesUseCase::class => autowire(ListarPatronesUseCase::class),
            ListarViajesUseCase::class => autowire(ListarViajesUseCase::class),
            RegistrarViajeUseCase::class => autowire(RegistrarViajeUseCase::class)
        ]);
        return $containerBuilder->build();
    }

    public function setupRouting($isDebugEnabled = false): Dispatcher
    {
        return simpleDispatcher(function (RouteCollector $r) {
            $r->get('/', WelcomeController::class);
            $r->get('/viajes', ViajesController::class);
            $r->get('/listar-viajes', ViajesController::class);
            $r->get('/nuevo-viaje', ViajesController::class);
            $r->post('/crear-viaje', ViajesController::class);
            $r->get('/listar-barcos', BarcosController::class);
            $r->get('/listar-patrones', PatronesController::class);
            $r->post('/crear-patron', PatronesController::class);
        }, [
            'cacheFile' => __DIR__ . '/route.cache', 
            'cacheDisabled' => $isDebugEnabled,
        ]);
    }

    public function setupMiddleware(Dispatcher $routes, ContainerInterface $container): RequestHandlerInterface
    {
        $middlewareQueue[] = new FastRoute($routes);
        $middlewareQueue[] = new GzipEncoder();
        $middlewareQueue[] = new Expires();
        $middlewareQueue[] = new RequestHandler($container);
        return new Relay($middlewareQueue);
    }

    public function setupPersistence(string $host, string $username, string $password, $database): \mysqli
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $db = new mysqli($host, $username, $password, $database);
        $this->insertData($db);
        return $db;
    }

    public function run(RequestHandlerInterface $requestHandler, ContainerInterface $container): void
    {
        $request = $container->get(ServerRequestInterface::class);
        $requestHandler->handle($request);
    }

    private function insertData(mysqli $db) {
        if ($db->query("SELECT * FROM socio")->num_rows == 0) {
            $db->query("INSERT INTO socio (cedula, nombre_completo, telefono, correo) VALUES('7-000-000', 'Socio 1', '6000-0000', 'socio1@email.com');");
            $db->query("INSERT INTO socio (cedula, nombre_completo, telefono, correo) VALUES('8-000-000', 'Socio 2', '9999-9998', 'socio2@email.com');");
            $db->query("INSERT INTO socio (cedula, nombre_completo, telefono, correo) VALUES('9-000-000', 'Socio 3', '9999-9999', 'socio3@email.com');");
        }

        if ($db->query("SELECT * FROM barco")->num_rows == 0) {
            $db->query("INSERT INTO barco (matricula, cedsocio, nombre_barco, numamarre, cuota) VALUES(12345, '7-000-000', 'Sea Explorer', 8, 400.00);");
            $db->query("INSERT INTO barco (matricula, cedsocio, nombre_barco, numamarre, cuota) VALUES(67890, '8-000-000', 'Crucero 7 Mares', 9, 800.00);");
            $db->query("INSERT INTO barco (matricula, cedsocio, nombre_barco, numamarre, cuota) VALUES(14789, '9-000-000', 'Nave Socio 3', 10, 300.00);");
        }
    }
}