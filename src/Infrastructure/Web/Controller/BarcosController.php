<?php

namespace Ds7\Asignacion4\Infrastructure\Web\Controller;

use Ds7\Asignacion4\Application\ResponseEmitter;
use Ds7\Asignacion4\Application\TemplatesProcessor;
use Ds7\Asignacion4\Core\UseCase\ListarBarcosUseCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class BarcosController extends AbstractController
{
    private ListarBarcosUseCase $listarBarcosUseCase;

    public function __construct(ResponseInterface  $response,
                                TemplatesProcessor $templatesProcessor,
                                ResponseEmitter    $responseEmitter,
                                ListarBarcosUseCase $listarBarcosUseCase) {
        parent::__construct($response, $templatesProcessor, $responseEmitter);
        $this->listarBarcosUseCase = $listarBarcosUseCase;
    }

    public function __invoke(ServerRequestInterface $request): void {
        if ($request->getServerParams()['REQUEST_URI'] === '/listar-barcos') {
            $this->mostrarListaDeBarcos();
        }
    }

    public function mostrarListaDeBarcos(): void {
        $this->view('lista-barcos.html', [
            'barcos' => $this->listarBarcosUseCase->listarBarcos()
        ]);
    }
}

