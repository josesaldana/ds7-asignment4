<?php
declare(strict_types=1);

namespace Ds7\Asignacion4\Core\Model;

class Socio {
    public function __construct(
        public string $cedula,
        public string $nombreCompleto,
        public string $telefono,
        public string $correo
    ) { }
}