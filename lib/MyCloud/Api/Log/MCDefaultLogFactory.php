<?php

namespace MyCloud\Api\Log;

// use Psr\Log\LoggerInterface;

/**
 * Class MCDefaultLogFactory
 *
 * This factory is the default implementation of Log factory.
 *
 * @package MyCloud\Api\Log
 */
class MCDefaultLogFactory implements \MyCloud\Api\Log\MCLogFactory
{
    /**
     * Returns logger instance implementing \Psr\Log\LoggerInterface.
     *
     * @param string $className
     * @return Instance of a logger object implementing \Psr\Log\LoggerInterface
     */
    public function getLogger( $className )
    {
        return new MCLogger( $className );
    }
}
