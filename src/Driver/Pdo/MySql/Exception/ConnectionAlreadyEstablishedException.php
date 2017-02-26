<?php

namespace Calam\Dbal\Driver\Pdo\MySql\Exception;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
final class ConnectionAlreadyEstablishedException extends DriverException
{
    public function __construct()
    {
        parent::__construct('Connection already established.');
    }
}
