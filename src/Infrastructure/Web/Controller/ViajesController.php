<?php

namespace Ds7\Asignacion4\Infrastructure\Web\Controller;

use Ds7\Asignacion4\Core\Model\Viaje;
use Ds7\Asignacion4\Core\Model\Patron;
use Ds7\Asignacion4\Core\Model\Barco;
use Ds7\Asignacion4\Core\UseCase\RegistrarViajeUseCase;
use Ds7\Asignacion4\Application\ResponseEmitter;
use Ds7\Asignacion4\Application\TemplatesProcessor;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class ViajesController extends AbstractController
{
    private RegistrarViajeUseCase $registrarViajeUseCase;

    public function __construct(ResponseInterface  $response,
                                TemplatesProcessor $templatesProcessor,
                                ResponseEmitter    $responseEmitter,
                                RegistrarViajeUseCase $registrarViajeUseCase) {
        parent::__construct($response, $templatesProcessor, $responseEmitter);

        $this->registrarViajeUseCase = $registrarViajeUseCase;
    }

    public function __invoke(ServerRequestInterface $request): void {
        if ($request->getServerParams()['REQUEST_URI'] === '/listar-viajes') {
            $this->mostrarListaDeViajes();
        } else if ($request->getServerParams()['REQUEST_URI'] === '/nuevo-viaje') {
            $this->mostrarNuevoFormularioDeNuevoViaje();
        } else if ($request->getServerParams()['REQUEST_URI'] === '/crear-viaje') {
            $this->crearViaje($request);
        }
    }

    public function mostrarListaDeViajes(): void {
        $this->text("Lista de viajes");
    }

    public function mostrarNuevoFormularioDeNuevoViaje(): void {
        $this->view('nuevo-viaje.html');
    }

    public function crearViaje(ServerRequestInterface $request) {
        $requestBody = $request->getParsedBody();

        $viaje = new Viaje(
            numero: 1,
            destino: $requestBody['destino'],
            fecha: $requestBody['fecha'],
            hora: $requestBody['hora']
        );

        $viaje->barco = new Barco($requestBody['barco']);
        $viaje->patron = new Patron($requestBody['patron']);

        $this->registrarViajeUseCase->registrarViaje($viaje);

        $this->view('viaje-creado.html');
    }
}