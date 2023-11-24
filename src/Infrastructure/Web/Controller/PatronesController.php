<?php

namespace Ds7\Asignacion4\Infrastructure\Web\Controller;

use Ds7\Asignacion4\Application\ResponseEmitter;
use Ds7\Asignacion4\Application\TemplatesProcessor;
use Ds7\Asignacion4\Core\UseCase\ListarPatronesUseCase;
use Ds7\Asignacion4\Core\UseCase\RegistrarPatronUseCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class PatronesController extends AbstractController
{
    // private ListarPatronesUseCase $listarPatronesUseCase;
    // private RegistrarPatronUseCase $registrarPatronUseCase;

    public function __construct(ResponseInterface  $response,
                                TemplatesProcessor $templatesProcessor,
                                ResponseEmitter    $responseEmitter,
                                private ListarPatronesUseCase $listarPatronesUseCase,
                                private RegistrarPatronUseCase $registrarPatronUseCase) {
        parent::__construct($response, $templatesProcessor, $responseEmitter);
        // $this->listarPatronesUseCase = $listarPatronesUseCase;
    }

    public function __invoke(ServerRequestInterface $request): void {
        if ($request->getServerParams()['REQUEST_URI'] === '/listar-patrones') {
            $this->mostrarListaDePatrones();
        } else if ($request->getServerParams()['REQUEST_URI'] === '/crear-patron') {
            $this->crearPatron($request);
        }
    }

    public function mostrarListaDePatrones(): void {
        $this->view('lista-patrones.html', [
            'patrones' => $this->listarPatronesUseCase->listarPatrones()
        ]);
    }

    /**
     * Acción para crear un nuevo patrón.
     * 
     * @todo Validar entrada (tipos de datos, etc) y enviar error si aplica
     * @todo Traducir errors de dominio a errores de interfaz web
     */
    public function crearPatron(ServerRequestInterface $request) {
        $requestBody = $request->getParsedBody();
        $this->registrarPatronUseCase->registrarViaje($requestBody);
        $this->redirect("/listar-patrones");
    }
}
