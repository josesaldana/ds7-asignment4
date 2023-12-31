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

        if (isset($input['ultimo_viaje'])) {
            $dataUltimoViaje = json_decode($input['ultimo_viaje']);
            $ultimoViaje = new Viaje(
                numero: $dataUltimoViaje->numero,
                destino: $dataUltimoViaje->destino,
                fecha: $dataUltimoViaje->fecha,
                hora: $dataUltimoViaje->hora
            );

            $dataBarcoUltimoViaje = json_decode($dataUltimoViaje->barco);
            $barcoDeUltimoViaje = new Barco(
                matricula: $dataBarcoUltimoViaje->matricula,
                nombre: $dataBarcoUltimoViaje->nombre,
                numamarre: $dataBarcoUltimoViaje->numamarre,
                cuota: $dataBarcoUltimoViaje->cuota
            );

            $ultimoViaje->patron = $patron;
            $ultimoViaje->barco = $barcoDeUltimoViaje;

            array_push($patron->viajes, $ultimoViaje);
        }

        return $patron;
    }

    public function convertToViaje(array $input): Viaje {
        $viaje = new Viaje(
            numero: $input['numero'],
            destino: $input['destino'],
            fecha: $input['fecha'],
            hora: $input['hora']
        );

        $patronData = json_decode($input['patron']);
        $viaje->patron = new Patron(
            codigo: $patronData->codigo,
            nombre: $patronData->nombre,
            telefono: $patronData->telefono
        );

        $barcoData = json_decode($input['barco']);
        $viaje->barco = new Barco(
            matricula: $barcoData->matricula,
            nombre: $barcoData->nombre,
            numamarre: $barcoData->numamarre,
            cuota: $barcoData->cuota
        );

        return $viaje;
    }
}