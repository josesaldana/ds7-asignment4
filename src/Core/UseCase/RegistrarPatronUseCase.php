<?php
declare(strict_types=1);

namespace Ds7\Asignacion4\Core\UseCase;

use Ds7\Asignacion4\Core\Model\Patron;
use Ds7\Asignacion4\Core\Db\PersistenceGatewayOperations;

class RegistrarPatronUseCase {
    private PersistenceGatewayOperations $persistance;

    public function __construct(PersistenceGatewayOperations $persistance) {
        $this->persistence = $persistance;
    }
    
    /**
     * Registra un nuevo patrón.
     * 
     * @throws Exception
     * @todo Agregar validaciones de negocio (patrón y barco existente, sobreasignación de patrón, etc)
     * @todo Cambiar Exception a domain exception
     */
    public function registrarViaje(array $datosDePatron): void {
        $patron = new Patron(
            codigo: intval($datosDePatron['codigo']),
            nombre: $datosDePatron['nombre'],
            telefono: $datosDePatron['telefono'],
            direccion: $datosDePatron['direccion']
        );

        $this->persistence->guardarPatron($patron);
    }
}