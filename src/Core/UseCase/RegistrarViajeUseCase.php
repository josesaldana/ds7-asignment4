<?php
declare(strict_types=1);

namespace Ds7\Asignacion4\Core\UseCase;

use Ds7\Asignacion4\Core\Model\Viaje;
use Ds7\Asignacion4\Core\Db\PersistenceGatewayOperations;

class RegistrarViajeUseCase {
    private PersistenceGatewayOperations $persistance;

    public function __construct(PersistenceGatewayOperations $persistance) {
        $this->persistence = $persistance;
    }
    
    public function registrarViaje(Viaje $viaje): void {
        $this->persistence->guardarViaje($viaje);
    }
}