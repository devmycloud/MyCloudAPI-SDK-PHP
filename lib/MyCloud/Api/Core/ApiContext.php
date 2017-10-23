<?php

namespace MyCloud\Api\Core;

/**
 * Class ApiContext
 *
 * Call level parameters such as request id, credentials etc
 *
 * @package MyCloud\Api\Core
 */
class ApiContext
{

    /**
     * Unique request id to be used for this call.
	 *
     * The user can either generate one or let the SDK generate one.
	 * The primary purpose of this is to prevent man-in-the-middle replays,
	 * but it can also identify each individual request made to the API server
	 * for audit or debugging purposes.
     *
     * @var null|string $requestId
     */
    private $requestId;

    /**
     * This is a placeholder for holding credential for the request
     * If the value is not set, it would get the value from cache
     *
     * @var string $token
     */
    private $token;

    /**
     * Construct
     *
     * @param string|null       $token
     * @param string|null       $requestId
     */
    public function __construct( $token = null, $requestId = null )
    {
        $this->token = $token;
        $this->requestId = $requestId;
    }

    /**
     * Get JWT Token
     *
     * @return string - the JWT token
     */
    public function getToken()
    {
        if ( $this->token == null ) {
			$auth = new MCAuthenticator( $this );
			$this->token = $auth->getToken();
        }
        return $this->token;
    }

    public function getRequestHeaders()
    {
        $config = MCConfigManager::getInstance()->get('http.headers');
        $headers = array();
        foreach ( $config as $header => $value ) {
            $headerName = ltrim( $header, 'http.headers' );
            $headers[$headerName] = $value;
        }
        return $headers;
    }

    public function addRequestHeader( $name, $value )
    {
        // Determine if the name already has a 'http.headers' prefix. If not, add one.
        if ( ! (substr( $name, 0, strlen('http.headers') ) === 'http.headers')) {
            $name = 'http.headers.' . $name;
        }
        MCConfigManager::getInstance()->addConfigs( array($name => $value) );
    }

    /**
     * Get Request ID
     *
     * @return string
     */
    public function getRequestId()
    {
        if ( $this->requestId == null ) {
            $this->requestId = $this->generateRequestId();
        }
        return $this->requestId;
    }

    /**
     * Resets the requestId that can be used to set the MyCloud-request-id
     * header used for idempotency. In cases where you need to make multiple create calls
     * using the same ApiContext object, you need to reset request Id.
     *
     * @return string
     */
    public function resetRequestId()
    {
        $this->requestId = $this->generateRequestId();
        return $this->getRequestId();
    }

    /**
     * Sets Config
     *
     * @param array $config SDK configuration parameters
     */
    public function setConfig(array $config)
    {
        MCConfigManager::getInstance()->addConfigs( $config );
    }

    /**
     * Gets Configurations
     *
     * @return array
     */
    public function getConfig()
    {
        return MCConfigManager::getInstance()->getConfigHashmap();
    }

    /**
     * Gets a specific configuration from key
     *
     * @param $key
     * @return mixed
     */
    public function get( $key )
    {
        return MCConfigManager::getInstance()->get( $key );
    }

    /**
     * Generates a unique per request id that
     * can be used to set the MyCloud-Request-Id header.
     *
     * @return string
     */
    private function generateRequestId()
    {
        static $pid = -1;
        static $addr = -1;

        if ( $pid == -1 ) {
            $pid = getmypid();
        }

        if ( $addr == -1 ) {
            if ( array_key_exists('SERVER_ADDR', $_SERVER) ) {
                $addr = ip2long( $_SERVER['SERVER_ADDR'] );
            } else {
                $addr = php_uname( 'n' );
            }
        }

        return implode( '-', array( $addr, $pid, $_SERVER['REQUEST_TIME'], mt_rand(0, 0xffff) ) );
    }
}
