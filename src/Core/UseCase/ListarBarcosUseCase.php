<?php
declare(strict_types=1);

namespace Ds7\Asignacion4\Core\UseCase;

use Ds7\Asignacion4\Core\Model\Barco;
use Ds7\Asignacion4\Core\Db\PersistenceGatewayOperations;

class ListarBarcosUseCase {
    private PersistenceGatewayOperations $persistence;

    public function __construct(PersistenceGatewayOperations $persistence) {
        $this->persistence = $persistence;
    }

    public function listarBarcos(): array {
        return $this->persistence->obtenerBarcos();
    }
}