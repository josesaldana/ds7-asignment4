<?php

namespace Ds7\Asignacion4\Infrastructure\Web\Controller;

use Ds7\Asignacion4\Application\ResponseEmitter;
use Ds7\Asignacion4\Application\TemplatesProcessor;
use Ds7\Asignacion4\Core\UseCase\ListarPatronesUseCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class PatronesController extends AbstractController
{
    private ListarPatronesUseCase $listarPatronesUseCase;

    public function __construct(ResponseInterface  $response,
                                TemplatesProcessor $templatesProcessor,
                                ResponseEmitter    $responseEmitter,
                                ListarPatronesUseCase $listarPatronesUseCase) {
        parent::__construct($response, $templatesProcessor, $responseEmitter);
        $this->listarPatronesUseCase = $listarPatronesUseCase;
    }

    public function __invoke(ServerRequestInterface $request): void {
        if ($request->getServerParams()['REQUEST_URI'] === '/listar-patrones') {
            $this->mostrarListaDePatrones();
        }
    }

    public function mostrarListaDePatrones(): void {
        $this->view('lista-patrones.html', [
            'patrones' => $this->listarPatronesUseCase->listarPatrones()
        ]);
    }
}

