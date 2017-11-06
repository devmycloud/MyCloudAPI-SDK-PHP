<?php

namespace MyCloud\Api\Core;

use MyCloud\Api\Exception\MCConfigurationException;
use MyCloud\Api\Exception\MCConnectionException;
use MyCloud\Api\Log\MCLoggingManager;

/**
 * A wrapper class based on the curl extension.
 * Requires the PHP curl module to be enabled.
 * See for full requirements the PHP manual: http://php.net/curl
 */
class MCHttpConnection
{

    /**
     * @var token
     */
    private $token;

    /**
     * @var ApiContext
     */
    private $apiContext;

    /**
     * @var HttpConfig
     */
    private $httpConfig;

    /**
     * HTTP status codes for which a retry must be attempted
     * retry is currently attempted for Request timeout, Bad Gateway,
     * Service Unavailable and Gateway timeout errors.
     */
    private static $retryCodes = array( '408', '502', '503', '504' );

    /**
     * LoggingManager
     *
     * @var MCLoggingManager
     */
    private $logger;

    /**
     * Default Constructor
     *
     * @param ApiContext       $apiContext
     * @param MCHttpConfig     $httpConfig
     * @param array            $config
     * @throws MCConfigurationException
     */
    public function __construct( $apiContext, $httpConfig, $token )
    {
        if ( ! function_exists("curl_init") ) {
            throw new MCConfigurationException( "Curl module is not available on this system" );
        }
		$this->token = $token;
		$this->apiContext = $apiContext;
        $this->httpConfig = $httpConfig;
        $this->logger = MCLoggingManager::getInstance(__CLASS__);
    }

    /**
     * Gets all Http Headers
     *
     * @return array
     */
    private function getHttpHeaders()
    {
        $ret = array();
        foreach ( $this->httpConfig->getHeaders() as $k => $v ) {
            $ret[] = "$k: $v";
        }
        return $ret;
    }

    /**
     * Executes an HTTP request
     *
     * @param string $payLoad query string OR POST content as a string
     * @return mixed
     * @throws MCConnectionException
     */
    public function execute( $path, $method, $payLoad, $headers )
    {
		$this->handle( $path, $payLoad );

        // Initialize the logger
        $this->logger->info($this->httpConfig->getMethod() . ' ' . $this->httpConfig->getUrl());

        // Initialize Curl Options
        $ch = curl_init( $this->httpConfig->getUrl() );
        $options = $this->httpConfig->getCurlOptions();
        if ( empty($options[CURLOPT_HTTPHEADER]) ) {
            unset( $options[CURLOPT_HTTPHEADER] );
        }
	
        curl_setopt_array( $ch, $options );
        curl_setopt( $ch, CURLOPT_URL, $this->httpConfig->getUrl() );
        curl_setopt( $ch, CURLOPT_HEADER, true );
        curl_setopt( $ch, CURLINFO_HEADER_OUT, true );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $this->getHttpHeaders() );

        // Determine Curl Options based on Method
        switch ( $this->httpConfig->getMethod() ) {
            case 'POST':
                curl_setopt( $ch, CURLOPT_POST, true );
                curl_setopt( $ch, CURLOPT_POSTFIELDS, $payLoad );
                break;

            // case 'PUT': Currently unused
            case 'PATCH':
            case 'DELETE':
                curl_setopt( $ch, CURLOPT_POSTFIELDS, $payLoad );
                break;
        }

        // Default Option if Method not of given types in switch case
        if ( $this->httpConfig->getMethod() != null ) {
            curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $this->httpConfig->getMethod() );
        }

        // Logging Each Headers for debugging purposes
        foreach ( $this->getHttpHeaders() as $header ) {
            // TODO: Strip out credentials and other secure info when logging.
            $this->logger->debug( $header );
        }

        // Execute Curl Request
        $result = curl_exec( $ch );

        // Retrieve Response Status
        $httpStatus = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

        // Retry if Certificate Exception
        if ( curl_errno($ch) == 60 ) {
            $this->logger->info( "Invalid or no certificate authority found - Retrying using bundled CA certs file" );
            curl_setopt( $ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem' );
            $result = curl_exec( $ch );
            // Retrieve Response Status
            $httpStatus = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
        }

        // Retry if Failing
        $retries = 0;
        if ( in_array( $httpStatus, self::$retryCodes) &&
				$this->httpConfig->getHttpRetryCount() != null )
		{
            $this->logger->info( "Got $httpStatus response from server. Retrying" );
            do {
                $result = curl_exec( $ch );
                // Retrieve Response Status
                $httpStatus = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
            } while ( in_array($httpStatus, self::$retryCodes) &&
				(++$retries < $this->httpConfig->getHttpRetryCount()) );
        }

        // Throw Exception if Retries and Certificates doenst work
        if ( curl_errno($ch) ) {
            $ex = new MCConnectionException(
                $this->httpConfig->getUrl(),
                curl_error($ch),
                curl_errno($ch)
            );
            curl_close( $ch );
            throw $ex;
        }

        // Get Request and Response Headers
        $requestHeaders = curl_getinfo( $ch, CURLINFO_HEADER_OUT );

        // Using alternative solution to CURLINFO_HEADER_SIZE as it throws invalid number when called using PROXY.
        $responseHeaderSize = strlen($result) - curl_getinfo( $ch, CURLINFO_SIZE_DOWNLOAD );
        $responseHeaders = substr( $result, 0, $responseHeaderSize );
        $result = substr( $result, $responseHeaderSize );

        $this->logger->debug( "Request Headers \t: " . str_replace("\r\n", ", ", $requestHeaders) );
		$this->logger->debug( str_repeat('-', 128) . PHP_EOL );
        $this->logger->info(  "Response Status \t: " . $httpStatus );
        $this->logger->debug( "Response Headers\t: " . str_replace("\r\n", ", ", $responseHeaders) );

        // Close the curl request
        curl_close( $ch );

        // Throw Exceptions based on HttpStatus Code
        if ( $httpStatus < 200 || $httpStatus >= 300 ) {
			$msg = "Got Http response code " . $httpStatus . " when " .
				$this->httpConfig->getMethod() . "-ing to " .
				$this->httpConfig->getUrl() . ".";
			if ( in_array($httpStatus, self::$retryCodes) ) {
				$msg = $msg . " Retried " . $retries . " times.";
			}
            $ex = new MCConnectionException( $this->httpConfig->getUrl(), $msg, $httpStatus );
            $ex->setData( $result );
            $this->logger->error( $msg . " " . $result );
            $this->logger->debug( PHP_EOL . PHP_EOL . str_repeat('=', 128) . PHP_EOL );
            throw $ex;
        }

        $this->logger->debug( ( $result && $result != '' ?
			"Response Data \t: " . $result :
			"No Response Body" ) . PHP_EOL . PHP_EOL );
        $this->logger->debug( str_repeat('=', 128) . PHP_EOL );

        // Return result object
        return $result;
    }

    /**
     * @param string  $path     Request path
     * @param array   $payload  Request data
     */
    public function handle( $path, $payLoad )
    {
        // $credential = $this->apiContext->getCredential();
        $config = $this->apiContext->getConfig();

        $this->httpConfig->setUrl(
            rtrim( trim( $this->getEndpoint($config) ), '/' ) .
            	( empty($path) ? '' : $path )
        );

		if ( ! empty($this->token) ) {
			$this->httpConfig->addHeader( "Authorization", 'Bearer ' . $this->token );
		}

		// REVIEW TGE What is the 100 Continue issue?!
        // Overwrite Expect Header to disable 100 Continue Issue
        $this->httpConfig->addHeader( "Expect", null );

        if ( ! array_key_exists( "User-Agent", $this->httpConfig->getHeaders() ) ) {
            $this->httpConfig->addHeader( "User-Agent",
				$this->getUserAgent(MCConstants::SDK_NAME, MCConstants::SDK_VERSION));
        }

        if ( $this->httpConfig->getMethod() == 'POST' ||
				$this->httpConfig->getMethod() == 'PUT') {
            $this->httpConfig->addHeader( 'MyCloud-Request-Id', $this->apiContext->getRequestId() );
        }

        // Add any additional Headers that they may have provided
        $headers = $this->apiContext->getRequestHeaders();
        foreach ( $headers as $key => $value ) {
            $this->httpConfig->addHeader( $key, $value );
        }
    }

    /**
     * End Point
     *
     * @param array $config
     *
     * @return string
     * @throws \MyCloud\Api\Exception\MCConfigurationException
     */
    private function getEndpoint($config)
    {
		$endpoint = MCConstants::REST_TEST_ENDPOINT;

        if ( isset($config['service.EndPoint']) ) {
			$this->logger->debug( "getEndpoint() Using OVERRIDE mode: 'service.EndPoint'" );
            $endpoint = $config['service.EndPoint'];
        } elseif ( isset($config['mode']) ) {
            switch ( strtoupper($config['mode']) ) {
                case 'TEST':
					$this->logger->debug( "getEndpoint() Using TEST mode: 'service.test.EndPoint'" );
					if ( isset($config['service.test.EndPoint']) ) {
                    	$endpoint = $config['service.test.EndPoint'];
					} else {
	                    throw new MCConfigurationException(
							"Config 'mode' set to 'test', but 'service.test.EndPoint' is not set.");
					}
                    break;
                case 'LIVE':
					$this->logger->debug( "getEndpoint() Using LIVE mode: 'service.live.EndPoint'" );
					if ( isset($config['service.live.EndPoint']) ) {
                    	$endpoint = $config['service.live.EndPoint'];
					} else {
	                    throw new MCConfigurationException(
							"Config 'mode' set to 'live', but 'service.live.EndPoint' is not set.");
					}
                    break;
                default:
                    throw new MCConfigurationException(
						"Config 'mode' is set to '" . $config['mode'] .
							"', but must be set to 'test' or 'live'");
                    break;
            }
        }

		return $endpoint;
    }

    public function getUserAgent( $sdkName, $sdkVersion )
    {
        $featureList = array(
            'platform-ver=' . PHP_VERSION,
            'bit=' . $this->getPHPBit(),
            'os=' . str_replace(' ', '_', php_uname('s') . ' ' . php_uname('r')),
            'machine=' . php_uname('m')
        );
        if ( defined('OPENSSL_VERSION_TEXT') ) {
            $opensslVersion = explode(' ', OPENSSL_VERSION_TEXT);
            $featureList[] = 'crypto-lib-ver=' . $opensslVersion[1];
        }
        if ( function_exists('curl_version') ) {
            $curlVersion = curl_version();
            $featureList[] = 'curl=' . $curlVersion['version'];
        }
        return sprintf( "MyCloudSDK/%s %s (%s)", $sdkName, $sdkVersion, implode('; ', $featureList) );
    }

    /**
     * Gets PHP Bit version
     *
     * @return int|string
     */
    private function getPHPBit()
    {
        switch (PHP_INT_SIZE) {
            case 4:
                return '32';
            case 8:
                return '64';
            default:
                return PHP_INT_SIZE;
        }
    }
	
}
