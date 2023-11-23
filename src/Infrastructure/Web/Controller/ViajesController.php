<?php

namespace Ds7\Asignacion4\Infrastructure\Web\Controller;

use Ds7\Asignacion4\Core\Model\Viaje;
use Ds7\Asignacion4\Core\Model\Patron;
use Ds7\Asignacion4\Core\Model\Barco;
use Ds7\Asignacion4\Core\UseCase\RegistrarViajeUseCase;
use Ds7\Asignacion4\Core\UseCase\ListarViajesUseCase;
use Ds7\Asignacion4\Application\ResponseEmitter;
use Ds7\Asignacion4\Application\TemplatesProcessor;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class ViajesController extends AbstractController
{
    private RegistrarViajeUseCase $registrarViajeUseCase;
    private ListarViajesUseCase $listarViajesUseCase;

    public function __construct(ResponseInterface  $response,
                                TemplatesProcessor $templatesProcessor,
                                ResponseEmitter    $responseEmitter,
                                RegistrarViajeUseCase $registrarViajeUseCase,
                                ListarViajesUseCase $listarViajesUseCase) {
        parent::__construct($response, $templatesProcessor, $responseEmitter);

        $this->registrarViajeUseCase = $registrarViajeUseCase;
        $this->listarViajesUseCase = $listarViajesUseCase;
    }

    /**
     * Método principal que despacha todas las acciones soportadas por el controlador.
     */
    public function __invoke(ServerRequestInterface $request): void {
        if ($request->getServerParams()['REQUEST_URI'] === '/listar-viajes') {
            $this->mostrarListaDeViajes();
        } else if ($request->getServerParams()['REQUEST_URI'] === '/nuevo-viaje') {
            $this->mostrarNuevoFormularioDeNuevoViaje();
        } else if ($request->getServerParams()['REQUEST_URI'] === '/crear-viaje') {
            $this->crearViaje($request);
        }
    }

    /**
     * Muestra la lista de viajes registrados.
     */
    public function mostrarListaDeViajes(): void {
        $viajes = $this->listarViajesUseCase->listarViajes();
        $this->view('lista-viajes.html', ['viajes' => $viajes]);
    }

    /**
     * Muestra el formulario para registrar nuevo viaje.
     */
    public function mostrarNuevoFormularioDeNuevoViaje(): void {
        $this->view('nuevo-viaje.html');
    }

    /**
     * Acción para crear un nuevo viaje.
     * 
     * @todo Validar entrada (tipos de datos, etc) y enviar error si aplica
     * @todo Traducir errors de dominio a errores de interfaz web
     */
    public function crearViaje(ServerRequestInterface $request) {
        $requestBody = $request->getParsedBody();

        ['barco' => $matriculaDeBarco, 'patron' => $codigoDePatron] = $requestBody;

        $this->registrarViajeUseCase->registrarViaje(
            $requestBody,
            $matriculaDeBarco,
            $codigoDePatron
        );

        $this->view('viaje-creado.html');
    }
}