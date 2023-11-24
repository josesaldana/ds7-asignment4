<?php
declare(strict_types=1);

namespace Ds7\Asignacion4\Core\UseCase;

use Ds7\Asignacion4\Core\Model\Viaje;
use Ds7\Asignacion4\Core\Db\PersistenceGatewayOperations;

class BuscarViajesUseCase {
    private PersistenceGatewayOperations $persistence;

    public function __construct(PersistenceGatewayOperations $persistence) {
        $this->persistence = $persistence;
    }

    public function buscarViajes(string $busqueda): array {
        if (isset($busqueda) && !empty(trim($busqueda))) {
            return $this->persistence->buscarViajes($busqueda);
        } else {
            return [];
        }
    }
}