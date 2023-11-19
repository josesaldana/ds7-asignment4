<?php

namespace Ds7\Asginacion4\Infrastructure\Db;

use Ds7\Asignacion4\Core\Model\InvalidDomainObjectError;
use mysqli;

class MySQLPersistanceGateway implements PersistenceGatewayOperations
{

    private mysqli $mysqli;
    private PersistenceMapper $mapper;

    /**
     * @param mysqli $store
     * @param PersistenceMapper $mapper
     */
    public function __construct(mysqli $mysqli, PersistenceMapper $mapper)
    {
        $this->mysqli = $mysqli;
        $this->mapper = $mapper;
    }
}