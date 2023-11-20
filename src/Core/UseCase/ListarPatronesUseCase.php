<?php
declare(strict_types=1);

namespace Ds7\Asignacion4\Core\UseCase;

use Ds7\Asignacion4\Core\Model\Patron;
use Ds7\Asignacion4\Core\Db\PersistenceGatewayOperations;

class ListarPatronesUseCase {
    private PersistenceGatewayOperations $persistence;

    public function __construct(PersistenceGatewayOperations $persistence) {
        $this->persistence = $persistence;
    }

    public function listarPatrones(): array {
        return $this->persistence->obtenerPatrones();
    }
}