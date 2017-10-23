<?php

namespace MyCloud\Api\Log;

use MyCloud\Api\Core\MCConfigManager;
// use Psr\Log\LoggerInterface;

/**
 * MyCloud default Logging Manager.
 *
 * Provides the connective tissue to the factory provided logger.
 *
 */

class MCLoggingManager
{
    /**
     * @var array of logging manager instances with class name as key
     */
    private static $instances = array();

    /**
     * The logger to be used for all messages
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Logger Name
     *
     * @var string
     */
    private $loggerName;

    /**
     * Whether or not to log DEBUG level messages.
     *
     * @var string
     */
    private $showDebug;

    /**
     * Returns the singleton logger object
     *
     * @param string $loggerName
     * @return $this
     */
    public static function getInstance( $loggerName = __CLASS__ )
    {
        if ( array_key_exists($loggerName, MCLoggingManager::$instances) ) {
            return MCLoggingManager::$instances[$loggerName];
        }
        $instance = new self( $loggerName );
        MCLoggingManager::$instances[$loggerName] = $instance;
        return $instance;
    }

    /**
     * Default Constructor
     *
     * @param string $loggerName Generally represents the class name.
     */
    private function __construct( $loggerName )
    {
        $config = MCConfigManager::getInstance()->getConfigHashmap();

        // Check to see if a custom factory is defined, and that it provides
		// an implementation of MCLogFactory. If it is not defined, or it does
		// not implement MCLogFactory, then use the default factory.
		//
        $factory = array_key_exists('log.AdapterFactory', $config) &&
			in_array('MyCloud\Api\Log\MCLogFactory',
				class_implements($config['log.AdapterFactory'])) ?
			$config['log.AdapterFactory'] : '\MyCloud\Api\Log\MCDefaultLogFactory';

        $factoryInstance = new $factory();
        $this->logger = $factoryInstance->getLogger( $loggerName );
        $this->loggerName = $loggerName;

		// REVIEW Do we really want to disable DEBUG level log messages in live mode?
        // Disable debug in live mode.
		$this->showDebug =
			array_key_exists('mode', $config) ?
				(strtoupper($config['mode']) == 'TEST') : TRUE;
    }

    /**
     * Log Emergency
     *
     * @param string $message
     */
    public function emergency($message)
    {
        $this->logger->emergency($message);
    }

    /**
     * Log Alert
     *
     * @param string $message
     */
    public function alert($message)
    {
        $this->logger->alert($message);
    }

    /**
     * Log Critical
     *
     * @param string $message
     */
    public function critical($message)
    {
        $this->logger->critical($message);
    }

    /**
     * Log Error
     *
     * @param string $message
     */
    public function error($message)
    {
        $this->logger->error($message);
    }

    /**
     * Log Warning
     *
     * @param string $message
     */
    public function warning($message)
    {
        $this->logger->warning($message);
    }

    /**
     * Log Notice
     *
     * @param string $message
     */
    public function notice($message)
    {
        $this->logger->notice($message);
    }

    /**
     * Log Info
     *
     * @param string $message
     */
    public function info($message)
    {
        $this->logger->info($message);
    }

    /**
     * Log Debug
     *
     * @param string $message
     */
    public function debug($message)
    {
        if ( $this->showDebug ) {
            $this->logger->debug($message);
        }
    }
}
