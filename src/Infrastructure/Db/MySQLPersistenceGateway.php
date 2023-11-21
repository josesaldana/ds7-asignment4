<?php
declare(strict_types=1);

namespace Ds7\Asignacion4\Infrastructure\Db;

use mysqli;
use Ds7\Asignacion4\Core\Db\PersistenceGatewayOperations;
use Ds7\Asignacion4\Core\Db\GenericPersistenceError;
use Ds7\Asignacion4\Core\Model\Viaje;
use Ds7\Asignacion4\Core\Model\InvalidDomainObjectError;

class MySQLPersistenceGateway implements PersistenceGatewayOperations
{
    private mysqli $db;
    private PersistenceMapper $mapper;

    /**
     * @param mysqli $db
     * @param PersistenceMapper $mapper
     */
    public function __construct(mysqli $db, PersistenceMapper $mapper)
    {
        $this->db = $db;
        $this->mapper = $mapper;
    }

    public function obtenerBarcos(): array {
        $resultados = $this->db->query("SELECT b.*, s.* FROM barco b INNER JOIN socio s ON b.cedsocio = s.cedula;");

        return array_map(function($record) {
            return $this->mapper->convertToBarco($record);
        }, $resultados->fetch_all(MYSQLI_ASSOC));
    }

    public function obtenerPatrones(): array {
        $resultados = $this->db->query("
            SELECT p.*,
                (SELECT JSON_OBJECT(
                    'numero', v.numero,
                    'fecha', v.fecha,
                    'hora', v.hora,
                    'destino', destino,
                    'barco', (SELECT JSON_OBJECT(
                        'matricula', b.matricula,
                        'socio', b.cedsocio,
                        'nombre', b.nombre_barco,
                        'numamarre', b.numamarre,
                        'cuota', b.cuota
                    ) FROM barco b WHERE v.matribarco = b.matricula)
                )
                FROM viaje v 
                WHERE p.codigo = v.codpatron
                ORDER BY fecha DESC LIMIT 1
            ) AS ultimo_viaje
            FROM conductor_patron p
        ");

        $patrones = array_map(function($record) {
            return $this->mapper->convertToPatron($record);
        }, $resultados->fetch_all(MYSQLI_ASSOC));

        return $patrones;
    }

    /**
     * @param Viaje $viaje
     */
    public function guardarViaje(Viaje $viaje): void {
        $sql = "INSERT INTO viaje (codpatron, matribarco, destino, fecha, hora) VALUES(?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);

        $stmt->bind_param(
            "sisss",
            $viaje->patron->codigo,
            $viaje->barco->matricula,
            $viaje->destino,
            $viaje->fecha,
            $viaje->hora
        );

        $stmt->execute();
    }
}