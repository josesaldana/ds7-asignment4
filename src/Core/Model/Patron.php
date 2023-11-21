<?php
declare(strict_types=1);

namespace Ds7\Asignacion4\Core\Model;

class Patron {
    public array $viajes = [];

    public function __construct(
        public int $codigo,
        public ?string $nombre = NULL,
        public ?string $telefono = NULL,
        public ?string $direccion = NULL
    ) { }

    public function obtenerUltimoViaje() {
        return count($this->viajes) > 0 ? $this->viajes[0] : null;
    }
}