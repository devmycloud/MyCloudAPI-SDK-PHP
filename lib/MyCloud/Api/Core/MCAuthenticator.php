<?php

namespace MyCloud\Api\Core;

use MyCloud\Api\Exception\MCConnectionException;
use MyCloud\Api\Log\MCLoggingManager;
use SimpleJWT\JWT;
use SimpleJWT\Keys\KeySet;
use SimpleJWT\InvalidTokenException;

/**
 * Class MCAuthenticator
 *
 * This class will handle the authentication model for making
 * requests to the server. In this API, we use JWT.
 *
 * @package MyCloud\Api\Core
 */
class MCAuthenticator
{

    /**
     * @var ApiContext
     */
    private $apiContext;

    /**
     * LoggingManager
     *
     * @var MCLoggingManager
     */
    private $logger;

    /**
     * KeySet secret
     *
     * @var keySetSecret
     */
    private $keySetSecret;

    /**
     * Default Constructor
     *
     * @param ApiContext       $apiContext
     * @param array            $config
     * @throws MCConfigurationException
     */
    public function __construct( $apiContext )
    {
		$this->apiContext = $apiContext;
        $this->logger = MCLoggingManager::getInstance(__CLASS__);

		$config = $this->apiContext->getConfig();
		if ( array_key_exists('auth.KeySet.Secret', $config) ) {
			$this->keySetSecret = $config['auth.KeySet.Secret'];
		} else {
			$this->keySetSecret = 'XYZ';
			$this->logger->critical( "keyset.Secret is not defined in sdk_config.init!" );
			$this->logger->critical( "The JWT Authentication layer cannot work without this keyset!" );
		}
    }

	public function getToken()
	{
		$api_key = '';
		$secret_key = '';
		$config = $this->apiContext->getConfig();
		$this->logger->debug( str_repeat('-', 128) );
		$this->logger->debug( "getToken() Getting Authentication Token" );

		$mode = 'test';
		if ( isset($config['mode']) ) {
			if ( strtolower($config['mode']) == 'live' ) {
				$mode = 'live';
			}
		}

		$this->logger->debug( "getToken() Using " . $mode . " mode." );

		$apiKeyName = 'acct.' . $mode . '.apiKey';
		$secretKeyName = 'acct.' . $mode . '.secretKey';
        if ( isset($config[$apiKeyName]) ) {
            $api_key = $config[$apiKeyName];
		}
        if ( isset($config[$secretKeyName]) ) {
            $secret_key = $config[$secretKeyName];
		}

		if ( empty($api_key) ) {
            $ex = new MCConnectionException(
                'apiKey',
                "Missing 'apiKey'. Double check your api keys in your configuration.",
                401
            );
            $ex->setData( "" );
            throw $ex;
		}

		if ( empty($secret_key) ) {
            $ex = new MCConnectionException(
                'secretKey',
                "Missing 'secretKey'. Double check your api keys in your configuration.",
                401
            );
            $ex->setData( "" );
            throw $ex;
		}

		$token = NULL;
		$set = KeySet::createFromSecret( $this->keySetSecret );
		$token_cache = TokenCache::pull( $this->apiContext->getConfig(), $api_key );
		if ( ! empty($token_cache) ) {
			$token = $token_cache['token'];
			$this->logger->debug( "TokenCache matched token for '" . $api_key . "'" );
		}

		if ( ! empty($token) ) {
			try {
			    $jwt = JWT::decode( $token, $set, 'HS256' );
				$iat = $jwt->getClaim('iat');
				$exp = $jwt->getClaim('exp');
				$iatTime = \DateTime::createFromFormat( 'U', $iat );
				$expTime = \DateTime::createFromFormat( 'U', $exp );
				$dateUTC = new \DateTime(null, new \DateTimeZone("UTC"));
				$this->logger->debug( "Current UTC Time: " . $dateUTC->format('Y-m-d H:i:s') );
				$this->logger->debug( "Cache Token Issued At: " . $iatTime->format('Y-m-d H:i:s') );
				$this->logger->debug( "Cache Token Expires At: " . $expTime->format('Y-m-d H:i:s') );
				$now = $dateUTC->getTimestamp();
				if ( $now >= ($exp - 60) ) {
					// UNDONE Delete from cache, but need new TokenCache method to do so.
					$this->logger->debug( "Cache Token has expired already." );
					$token = NULL;
				}
			} catch ( InvalidTokenException $ex ) {
				// NOTE
				// It appears that the JWT library is smart enough to throw an exception
				// for us when the token has already expired:
				//    InvalidTokenException: Too late due to exp claim
				//
				// UNDONE Delete from cache, but need new TokenCache method to do so.
				$this->logger->debug( "Cache Token invalid: " . $ex->getMessage() );
				$token = NULL;
			}
		}

		if ( empty($token) ) {
			$this->logger->debug( "Requesting new token from server" );
			$path = '/v1/gettoken';
			$method = 'POST';
	        $config = $this->apiContext->getConfig();
			$httpConfig = new MCHttpConfig( $path, $method, $config );

			$http = new MCHttpConnection( $this->apiContext, $httpConfig, NULL );
			$parameters = array( 'apikey' => $api_key, 'secretkey' => $secret_key );

			// FIXME Need to try exception here?
			$json_data = $http->execute( $path, $method, $parameters, NULL );

			$this->logger->debug( "Returned token data: " . $json_data );
			$token_data = json_decode( $json_data, true );
			$token = $token_data['token'];
			$this->logger->debug( "Returned token: " . $token );

			$tokenIssued = 0;
			$tokenExpires = 0;
			try {
			    $jwt = JWT::decode( $token, $set, 'HS256' );
				$iat = $jwt->getClaim('iat');
				$tokenIssued = $iat;
				$exp = $jwt->getClaim('exp');
				$tokenExpires = $exp;
				$iatTime = \DateTime::createFromFormat( 'U', $iat );
				$expTime = \DateTime::createFromFormat( 'U', $exp );
				$dateUTC = new \DateTime( null, new \DateTimeZone("UTC") );
			} catch ( InvalidTokenException $ex ) {
				$token = NULL;
				$this->logger->debug( "Requested token invalid: " . $ex->getMessage() );
			}

			if ( ! empty($token) ) {
				TokenCache::push( $config, $api_key, $token, $tokenIssued, $tokenExpires );
			}
		}

		$this->logger->debug( "Returning token: " . (empty($token) ? 'EMPTY TOKEN' : $token) );
		$this->logger->debug( str_repeat('-', 128) );

		return $token;
	}

}
