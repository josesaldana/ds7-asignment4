<?php

namespace Ds7\Asignacion4\Infrastructure\Web\Controller;

use Ds7\Asignacion4\Application\ResponseEmitter;
use Ds7\Asignacion4\Application\TemplatesProcessor;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class ViajesController extends AbstractController
{
    public function __construct(ResponseInterface  $response,
                                TemplatesProcessor $templatesProcessor,
                                ResponseEmitter    $responseEmitter) {
        parent::__construct($response, $templatesProcessor, $responseEmitter);
    }

    public function __invoke(ServerRequestInterface $request): void {
        if ($request->getServerParams()['REQUEST_URI'] === '/nuevo-viaje') {
            $this->mostrarNuevoFormularioDeNuevoViaje();
        }
    }

    public function mostrarNuevoFormularioDeNuevoViaje(): void {
        $this->view('nuevo-viaje.html');
    }
}