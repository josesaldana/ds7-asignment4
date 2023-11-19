<?php

namespace Ds7\Asignacion4\Infrastructure\Web\Controller;

use Ds7\Asignacion4\Application\ResponseEmitter;
use Ds7\Asignacion4\Application\TemplatesProcessor;
use Psr\Http\Message\ResponseInterface;


class WelcomeController extends AbstractController
{
    public function __construct(ResponseInterface  $response,
                                TemplatesProcessor $templatesProcessor,
                                ResponseEmitter    $responseEmitter)
    {
        $this->response = $response;
        $this->responseEmitter = $responseEmitter;
        $this->templatesProcessor = $templatesProcessor;
    }

    public function __invoke(): void
    {
        $this->view('welcome.html', []);
    }
}