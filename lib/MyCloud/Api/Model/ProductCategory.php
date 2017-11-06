<?php

namespace MyCloud\Api\Model;

use MyCloud\Api\Core\MCError;
use MyCloud\Api\Core\MyCloudModel;
use MyCloud\Api\Log\MCLoggingManager;

/**
 * Class Product
 *
 * Represents a MyCloud ProductCategory that categorizes Products
 *
 * @package MyCloud\Api\Model
 */
class ProductCategory extends MyCloudModel
{
	private $photo_file = NULL;
	private $client_references = array( '', '', '', '' );

	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	public function getShopId() {
		return $this->shop_id;
	}
	public function setShopId($shop_id) {
		$this->shop_id = $shop_id;
		return $this;
	}

	public function getName() {
		return $this->name;
	}
	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	public function getCode() {
		return $this->code;
	}

    public static function all( $params = array(), $apiContext = null )
    {
		$product_categories = NULL;

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
            "/v1/productcategories" . "?" . http_build_query(array_intersect_key($params, $allowedParams)),
            "GET",
            $payLoad,
            array(),
            $apiContext
        );
		// print "ProductCategory::all() DATA: " . $json_data . PHP_EOL;

		$result = json_decode( $json_data, true );

		if ( $result['success'] ) {
			if ( isset($result['data']) && is_array($result['data']) ) {
				$product_categories = array();
				foreach ( $result['data'] as $category_data ) {
					$category = new ProductCategory();
					$category->fromArray( $category_data );
					$product_categories[] = $category;
				}
			} else {
				$product_categories = new MCError( 'API Returned invalid ProductCategory data' );
				MCLoggingManager::getInstance(__CLASS__)
					->error( "ProductCategory list not array: " . print_r($result['data']) );
			}
		} else {
			$product_categories = new MCError( $result['message'] );
			MCLoggingManager::getInstance(__CLASS__)
				->error( "Failed getting ProductCategory list: " . $result['message'] );
		}

        return $product_categories;
    }

    public static function get( $category_id, $apiContext = null )
    {
		$product_category = NULL;

        $payLoad = array();
        $json_data = self::executeCall(
            "/v1/productcategories/" . self::rfc3986Encode($category_id),
            "GET",
            $payLoad,
            array(),
            $apiContext
        );
		// print "ProductCategory::get(" . $category_id . ") DATA: " . $json_data . PHP_EOL;

		$result = json_decode( $json_data, true );

		if ( $result['success'] ) {
			if ( isset($result['data']) && is_array($result['data']) ) {
				$product_category = new ProductCategory();
				$product_category->fromArray( $result['data'] );
			} else {
				$product_category = new MCError( 'API Returned invalid ProductCategory data' );
				MCLoggingManager::getInstance(__CLASS__)
					->error( "Product data not array: " . print_r($result['data']) );
			}
		} else {
			$product_category = new MCError( $result['message'] );
			MCLoggingManager::getInstance(__CLASS__)
				->error( "Failed getting ProductCategory[" . $category_id . "]: " . $result['message'] );
		}

        return $product_category;
    }

    public function create( $apiContext = null )
    {
		$product_category = NULL;
        $payload = $this->toArray();
		// print "CREATE PRODUCTCATEGORY: PAYLOAD: " . var_export($payload, true) . PHP_EOL;

        $json_data = self::executeCall(
            "/v1/productcategories",
            "POST",
            $payload,
            array(),
            $apiContext
        );
		// print "CREATE PRODUCTCATEGORY: JSON RESULT: " . $json_data . PHP_EOL;

		$result = json_decode( $json_data, true );

		if ( $result['success'] ) {
			if ( isset($result['data']) && is_array($result['data']) ) {
				$product_category = new ProductCategory();
				$product_category->fromArray( $result['data'] );
			} else {
				$product_category = new MCError( 'API Returned invalid ProductCategory data' );
				MCLoggingManager::getInstance(__CLASS__)
					->error( "Product data not array: " . print_r($result['data']) );
			}
		} else {
			$product_category = new MCError( $result['message'] );
			MCLoggingManager::getInstance(__CLASS__)
				->error( "Failed creating ProductCategory: " . $result['message'] );
		}

        return $product_category;
    }

	public function fromArray( $data )
	{
		$this->assignAttributes( $data['attributes'] );
	}

}
