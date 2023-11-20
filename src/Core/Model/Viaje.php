<?php

declare(strict_types=1);

namespace Ds7\Asignacion4\Core\Model;

class Viaje {
    public Patron $patron;
    public Barco $barco;

    public function __construct(
        public int $numero,
        public string $destino,
        public string $fecha,
        public string $hora,
    ) { }
}