<?php
declare(strict_types=1);

namespace Ds7\Asignacion4\Core\Model;

class Barco {
    public Socio $propietario;

    public function __construct(
        public string $matricula,
        public string $nombre,
        public int $numamarre,
        public float $cuota
    ) { }
}