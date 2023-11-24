<?php
declare(strict_types=1);

namespace Ds7\Asignacion4\Infrastructure\Db;

use Ds7\Asignacion4\Core\Model\Viaje;
use Ds7\Asignacion4\Core\Model\Patron;
use Ds7\Asignacion4\Core\Db\PersistenceGatewayOperations;
use Ds7\Asignacion4\Core\Db\GenericPersistenceError;
use Ds7\Asignacion4\Core\Model\InvalidDomainObjectError;
use mysqli;

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

    /**
     * Obtener lista de Barcos
     * 
     * @see Ds7\Asignacion4\Core\Model\Barco
     */
    public function obtenerBarcos(): array {
        $resultados = $this->db
            ->query("SELECT b.*, s.* FROM barco b INNER JOIN socio s ON b.cedsocio = s.cedula")
            ->fetch_all(MYSQLI_ASSOC);

        return array_map(fn($record) => $this->mapper->convertToBarco($record), $resultados);
    }

    /**
     * Obtener lista de Patrones
     * 
     * @see Ds7\Asignacion4\Core\Model\Patron
     */
    public function obtenerPatrones(): array {
        $resultados = $this->db
            ->query("SELECT p.*,
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
                    FROM conductor_patron p")
            ->fetch_all(MYSQLI_ASSOC);

        $patrones = array_map(fn($record) => $this->mapper->convertToPatron($record), $resultados);

        return $patrones;
    }

    /**
     * Registrar un viaje
     * 
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

        $stmt->close();
    }

    /**
     * Obtener lista de viajes
     * 
     * @see Ds7\Asignacion4\Core\Model\Viaje
     */
    public function obtenerViajes(): array {
        $resultados = $this->db
            ->query("SELECT 
                        v.*, 
                        (SELECT JSON_OBJECT(
                            'codigo', p.codigo,
                            'nombre', p.nombre,
                            'telefono', p.telefono
                        ) FROM conductor_patron p WHERE v.codpatron = p.codigo ) AS patron,
                        (SELECT JSON_OBJECT(
                            'matricula', b.matricula,
                            'nombre', b.nombre_barco,
                            'numamarre', b.numamarre,
                            'cuota', b.cuota
                        ) FROM barco b WHERE v.matribarco = b.matricula) AS barco
                    FROM viaje v")
            ->fetch_all(MYSQLI_ASSOC);

        return array_map(fn($record) => $this->mapper->convertToViaje($record), $resultados);
    }

    /**
     * Busca viajes. Por ahora, por barco
     * 
     * @return array of viajes
     */
    public function buscarViajes(string $busqueda): array {
        $resultados = $this->db
            ->execute_query("SELECT 
                        v.*,
                        barco.barco_in_json as barco,
                        (SELECT JSON_OBJECT(
                            'codigo', p.codigo,
                            'nombre', p.nombre,
                            'telefono', p.telefono
                        ) FROM conductor_patron p WHERE v.codpatron = p.codigo) as patron
                    FROM viaje v
                        LEFT JOIN(
                            SELECT b.matricula,
                            (SELECT JSON_OBJECT(
                                'matricula', b.matricula,
                                'nombre', b.nombre_barco,
                                'numamarre', b.numamarre,
                                'cuota', b.cuota            
                            )) AS barco_in_json
                            FROM barco b WHERE MATCH (b.nombre_barco) AGAINST (? IN NATURAL LANGUAGE MODE)
                        ) barco ON v.matribarco = barco.matricula
                    WHERE barco.matricula IS NOT NULL", [$busqueda])
            ->fetch_all(MYSQLI_ASSOC);

        $viajes = array_map(fn($record) => $this->mapper->convertToViaje($record), $resultados);

        return $viajes;
    }

    /**
     * Registrar un patron
     * 
     * @param Patron $patron
     * @todo Traducir excepciones de base de datos a dominio
     */
    public function guardarPatron(Patron $patron): void {
        $sql = "INSERT INTO conductor_patron (codigo, nombre, telefono, direccion) VALUES(?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);

        $stmt->bind_param(
            "ssss",
            $patron->codigo,
            $patron->nombre,
            $patron->telefono,
            $patron->direccion
        );

        $stmt->execute();

        $stmt->close();
    }
}