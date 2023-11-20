<?php

namespace Ds7\Asignacion4\Infrastructure\Db;

use Ds7\Asignacion4\Core\Model\Barco;
use Ds7\Asignacion4\Core\Model\Socio;
use Ds7\Asignacion4\Core\Model\Patron;
use Ds7\Asignacion4\Core\Model\Viaje;
use Ds7\Asignacion4\Core\Model\InvalidDomainObjectError;

/**
 * This mapper will convert model entities to and from corresponding
 * persistent entities. Mapping might not be one-to-one. Use cases
 * will only ever operate with model entities.
 */
class PersistenceMapper
{
    public function convertToBarco(array $input): Barco {
        $barco = new Barco(
            matricula: $input['matricula'],
            nombre: $input['nombre_barco'],
            numamarre: $input['numamarre'],
            cuota: $input['cuota']
        );

        $barco->propietario = new Socio(
            cedula: $input['cedula'],
            nombreCompleto: $input['nombre_completo'],
            telefono: $input['telefono'],
            correo: $input['correo']
        );

        return $barco;
    }

    public function convertToPatron(array $input): Patron {
        $patron = new Patron(
            codigo: $input['codigo'],
            nombre: $input['nombre'],
            telefono: $input['telefono'],
            direccion: $input['direccion'],
        );

        $dataUltimoViaje = json_decode($input['ultimo_viaje']);
        $dataBarcoUltimoViaje = json_decode($dataUltimoViaje->barco);

        $barcoDeUltimoViaje = new Barco(
            matricula: $dataBarcoUltimoViaje->matricula,
            nombre: $dataBarcoUltimoViaje->nombre,
            numamarre: $dataBarcoUltimoViaje->numamarre,
            cuota: $dataBarcoUltimoViaje->cuota
        );

        $ultimoViaje = new Viaje(
            numero: $dataUltimoViaje->numero,
            destino: $dataUltimoViaje->destino,
            fecha: $dataUltimoViaje->fecha,
            hora: $dataUltimoViaje->hora
        );

        $ultimoViaje->patron = $patron;
        $ultimoViaje->barco = $barcoDeUltimoViaje;

        array_push($patron->viajes, $ultimoViaje);

        return $patron;
    }
}