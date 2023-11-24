<?php
declare(strict_types=1);

namespace Ds7\Asignacion4\Core\Db;

use Ds7\Asignacion4\Core\Model\Viaje;
use Ds7\Asignacion4\Core\Model\Patron;

interface PersistenceGatewayOperations {

    /**
     * Carga a lista de barcos registrados.
     */
    function obtenerBarcos(): array;

    /**
     * Carga la lista de patronos registrados
     */
    function obtenerPatrones(): array;

    /**
     * Guarda un viaje
     * @throws GenericPersistenceError
     */
    function guardarViaje(Viaje $viaje): void;

    /**
     * Obtener lista de viajes
     */
    function obtenerViajes(): array;

    /**
     * Guarda un patrón
     * @throws GenericPersistenceError
     */
    function guardarPatron(Patron $patron): void;
}