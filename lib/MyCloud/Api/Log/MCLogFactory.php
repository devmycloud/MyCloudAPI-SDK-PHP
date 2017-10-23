<?php

namespace MyCloud\Api\Log;

use Psr\Log\LoggerInterface;

interface MCLogFactory
{
    /**
     * Returns logger instance implementing Psr\Log\LoggerInterface.
     *
     * @param string $className
     * @return LoggerInterface instance of logger object implementing LoggerInterface
     */
    public function getLogger( $className );
}
