<?php

namespace MyCloud\Api\Model;

use MyCloud\Api\Core\MCError;
use MyCloud\Api\Core\MyCloudModel;
use MyCloud\Api\Log\MCLoggingManager;

/**
 * Class Product
 *
 * Represents a MyCloud Product that is kept in inventory
 *
 * @package MyCloud\Api\Model
 */
class Product extends MyCloudModel
{

    public static function all( $params = array(), $apiContext = null )
    {
		$products = NULL;

		// ArgumentValidator::validate($params, 'params');
        $payLoad = "";
        $allowedParams = array(
            'page_size' => 1,
            'page' => 1,
            'start_time' => 1,
            'end_time' => 1,
            'sort_order' => 1,
            'sort_by' => 1,
            'total_required' => 1
        );

        $json_data = self::executeCall(
            "/v1/products" . "?" . http_build_query(array_intersect_key($params, $allowedParams)),
            "GET",
            $payLoad,
            array(),
            $apiContext
        );
		// print "Product::all() DATA: " . $json_data . "\n";

		$result = json_decode( $json_data, true );

		if ( $result['success'] ) {
			if ( isset($result['data']) && is_array($result['data']) ) {
				$products = array();
				foreach ( $result['data'] as $product_data ) {
					$product = new Product();
					$product->fromArray( $product_data );
					$products[] = $product;
				}
			} else {
				$products = new MCError( 'API Returned invalid data' );
				MCLoggingManager::getInstance(__CLASS__)
					->error( "Product list not array: " . print_r($result['data']) );
			}
		} else {
			$products = new MCError( $result['message'] );
			MCLoggingManager::getInstance(__CLASS__)
				->error( "Failed getting Product list: " . $result['message'] );
		}

        return $products;
    }

    public static function get( $product_id, $apiContext = null )
    {
		$product = NULL;

        $payLoad = array();
        $json_data = self::executeCall(
            "/v1/products/" . $product_id,
            "GET",
            $payLoad,
            array(),
            $apiContext
        );
		// print "Product::get(" . $product_id . ") DATA: " . $json_data . "\n";

		$result = json_decode( $json_data, true );

		if ( $result['success'] ) {
			if ( isset($result['data']) && is_array($result['data']) ) {
				$product = new Product();
				$product->fromArray( $result['data'] );
			} else {
				$product = new MCError( 'API Returned invalid data' );
				MCLoggingManager::getInstance(__CLASS__)
					->error( "Product data not array: " . print_r($result['data']) );
			}
		} else {
			$product = new MCError( $result['message'] );
			MCLoggingManager::getInstance(__CLASS__)
				->error( "Failed getting Product[" . $product_id . "]: " . $result['message'] );
		}

        return $product;
    }

	public function fromArray( $data )
	{
		$this->assignAttributes( $data['attributes'] );
	}

}
