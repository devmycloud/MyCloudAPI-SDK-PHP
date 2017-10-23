<?php

namespace MyCloud\Api\Core;


abstract class TokenCache
{
    public static $CACHE_PATH = './tokens.cache';

    /**
     * A pull method which would read the persisted data based on apiKey.
     * If apiKey is not provided, an array with all the tokens would be passed.
     *
     * @param array|null $config
     * @param string $clientId
     * @return mixed|null
     */
    public static function pull( $config = null, $apikey = null )
    {
        // Return if not enabled
        if ( ! self::isEnabled($config) ) {
            return null;
        }

        $tokens = null;
        $cachePath = self::cachePath( $config );
        if ( file_exists($cachePath) ) {
            // Read from the file
            $cachedContent = file_get_contents($cachePath);
            if ( $cachedContent ) {
                $tokens = json_decode( $cachedContent, true );
                if ( ! empty($apikey) && is_array($tokens) && array_key_exists($apikey, $tokens) ) {
                    // If api_key is found, send back only that token
                    return $tokens[$apikey];
                } elseif ( ! empty($apikey) ) {
                    // If api_key is provided, but not found in persisted data, return null
                    return null;
                }
            }
        }
        return $tokens;
    }

    /**
     * Persists the data into a cache file provided in $CACHE_PATH
     *
     * @param array|null $config
     * @param      $apikey
     * @param      $token
     * @param      $tokenCreateTime
     * @param      $tokenExpiresAt
     * @throws \Exception
     */
    public static function push( $config = null, $apikey, $token, $tokenCreateTime, $tokenExpiresAt )
    {
        // Return if not enabled
        if ( ! self::isEnabled($config) ) {
            return;
        }

        $cachePath = self::cachePath( $config );
        if ( ! is_dir(dirname($cachePath)) ) {
            if ( mkdir(dirname($cachePath), 0755, true) == false ) {
                throw new \Exception("Failed to create token cache directory at $cachePath");
            }
        }

        // Reads all the existing persisted data
        $tokens = self::pull();
        $tokens = $tokens ? $tokens : array();
        if ( is_array($tokens) ) {
            $tokens[$apikey] = array(
                'token' => $token,
                'tokenCreateTime' => $tokenCreateTime,
                'tokenExpiresAt' => $tokenExpiresAt
            );
        }
        if ( ! file_put_contents( $cachePath, json_encode($tokens)) ) {
            throw new \Exception( "Failed to write token cache" );
        };
    }

    /**
     * Determines from the Configuration if caching is currently enabled/disabled
     *
     * @param $config
     * @return bool
     */
    public static function isEnabled( $config )
    {
        $value = self::getConfigValue( 'auth.tokenCache.enabled', $config );
        return empty($value) ? false : ( (trim($value) == true || trim($value) == 'true') );
    }
    
    /**
     * Returns the cache file path
     *
     * @param $config
     * @return string
     */
    public static function cachePath( $config )
    {
        $cachePath = self::getConfigValue( 'auth.tokenCache.path', $config );
        return empty($cachePath) ? self::$CACHE_PATH : $cachePath;
    }

    /**
     * Returns the Value of the key if found in given config, or from MC Config Manager
     * Returns null if not found
     *
     * @param $key
     * @param $config
     * @return null|string
     */
    private static function getConfigValue( $key, $config )
    {
        $config = ($config && is_array($config)) ?
			$config : MCConfigManager::getInstance()->getConfigHashmap();
        return array_key_exists($key, $config) ? trim($config[$key]) : null;
    }
}
