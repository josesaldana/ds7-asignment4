<?php
declare(strict_types=1);

namespace Ds7\Asignacion4\Core\UseCase;

use Ds7\Asignacion4\Core\Model\Viaje;

class RegistrarViajeUseCase {
    private PersistanceGatewayOperations $persistance;

    public function __construct(PersistanceGatewayOperations $persistance) {
        $this->persistance = $persistance;
    }

    public function registrarViaje(Viaje $viaje): array {

    }
}