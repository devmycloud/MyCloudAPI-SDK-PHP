<?php

namespace MyCloud\Api\Core;

/**
 * Class MCConfigManager
 *
 * Manages the configuration of the API.
 *
 * @package MyCloud\Api\Core
 */
class MCConfigManager
{

    /**
     * Configuration Options
     *
     * @var array
     */
    private $configs = array();

    /**
     * Singleton Object
     *
     * @var $this
     */
    private static $instance;

    /**
     * Private Constructor
     */
    private function __construct()
    {
        if ( defined('MCAPI_CONFIG_PATH') ) {
            $configFile = constant('MCAPI_CONFIG_PATH') . '/sdk_config.ini';
        } else {
			// FIXME
            $configFile = implode(
				DIRECTORY_SEPARATOR,
                array( dirname(__FILE__), "..", "config", "sdk_config.ini" )
			);
        }

        if ( file_exists($configFile) ) {
            $this->addConfigFromIni( $configFile );
        }
    }

    /**
     * Returns the singleton object
     *
     * @return $this
     */
    public static function getInstance()
    {
        if ( ! isset(self::$instance) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Add Configuration from configurations ini file
     *
     * @param string $fileName
     * @return $this
     */
    public function addConfigFromIni( $fileName )
    {
        if ( $configs = parse_ini_file($fileName) ) {
            $this->addConfigs( $configs );
        }
        return $this;
    }

    /**
     * If a configuration exists in both arrays, then the element from
	 * the first array will be used and the matching key's element from
	 * the second array will be ignored.
     *
     * @param array $configs
     * @return $this
     */
    public function addConfigs( $configs = array() )
    {
        $this->configs = $configs + $this->configs;
        return $this;
    }

    /**
     * Simple getter for configuration params
     * Returns array of values for matched key, or an empty array.
     *
     * @param string $key
     * @return array
     */
    public function get( $key )
    {
        if ( array_key_exists($key, $this->configs) ) {
            return $this->configs[$key];
        } else {
            return array();
        }
    }

    /**
     * returns the config file hashmap
     */
    public function getConfigHashmap()
    {
        return $this->configs;
    }

    /**
     * Disabling __clone call - we are a Singleton!
     */
    public function __clone()
    {
        trigger_error( 'Clone is not allowed.', E_USER_ERROR );
    }
}
