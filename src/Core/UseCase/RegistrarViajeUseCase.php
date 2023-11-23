<?php
declare(strict_types=1);

namespace Ds7\Asignacion4\Core\UseCase;

use Ds7\Asignacion4\Core\Model\Viaje;
use Ds7\Asignacion4\Core\Model\Barco;
use Ds7\Asignacion4\Core\Model\Patron;
use Ds7\Asignacion4\Core\Db\PersistenceGatewayOperations;

class RegistrarViajeUseCase {
    private PersistenceGatewayOperations $persistance;

    public function __construct(PersistenceGatewayOperations $persistance) {
        $this->persistence = $persistance;
    }
    
    /**
     * Registra un nuevo viaje.
     * 
     * @throws Exception
     * @todo Agregar validaciones de negocio (patrón y barco existente, sobreasignación de patrón, etc)
     * @todo Cambiar Exception a domain exception
     */
    public function registrarViaje(
            array $dataDeViaje, 
            string $matriculaDeBarco,
            string $codigoDePatron): void {
        $viaje = new Viaje(
            destino: $dataDeViaje['destino'],
            fecha: $dataDeViaje['fecha'],
            hora: $dataDeViaje['hora']
        );

        $viaje->barco = new Barco($matriculaDeBarco);
        $viaje->patron = new Patron(intval($codigoDePatron));

        $this->persistence->guardarViaje($viaje);
    }
}