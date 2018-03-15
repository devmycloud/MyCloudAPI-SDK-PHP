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
	private $photo_file = NULL;
	private $client_references = NULL;

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

	public function getSKU() {
		return $this->sku;
	}
	public function setSKU($sku) {
		$this->sku = $sku;
		return $this;
	}

	public function getShopSKU() {
		return $this->shop_sku;
	}
	public function setShopSKU($shop_sku) {
		$this->shop_sku = $shop_sku;
		return $this;
	}

	public function getName() {
		return $this->name;
	}
	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	public function getDescription() {
		return $this->description;
	}
	public function setDescription($description) {
		$this->description = $description;
		return $this;
	}

	public function getSupplierReference() {
		return $this->supplier_reference;
	}
	public function setSupplierReference($supplier_reference) {
		$this->supplier_reference = $supplier_reference;
		return $this;
	}

	public function getClientReferences() {
		return empty($this->client_references) ?
			array( '', '', '', '' ) : $this->client_references;
	}
	public function setClientReferences($references) {
		if ( $references === NULL ) {
			$this->client_references = NULL;
		} elseif ( empty($references) || count($references) == 0 ) {
			$this->client_references = array( '', '', '', '' );
		} else {
			$cnt = count($references);
			// UNDONE Needs to adapt to DB changes (more than 4 reference fields)
			if ( $cnt > 4 ) {
				$cnt = 4;
				$references = array_splice ( $references, 0, 4 );
			}
			if ( $cnt == 4 ) {
				$this->client_references = $references;
			} else {
				$this->client_references = array_merge(
					$references, array_fill( $cnt, (4 - $cnt), '' ) );
			}
		}
		return $this;
	}

	public function getClientReference($index) {
		if ( $this->client_references === NULL ) {
			$this->client_references = array( '', '', '', '' );
		}
		return $this->client_references[$index];
	}
	public function setClientReference($index, $value) {
		if ( $this->client_references === NULL ) {
			$this->client_references = array( '', '', '', '' );
		}
		$this->client_references[$index] = $value;
		return $this;
	}

	public function getPhotoUrl() {
		return $this->photo_url;
	}
	public function setPhoto( $filename, $filetype, $filepath )
	{
		$this->photo_file =
			array(
				'filename'   => $filename,
				'filetype'   => $filetype,
				'filepath'   => $filepath
			);
		return $this;
	}

    public static function all( $params = array(), $apiContext = null )
    {
		$products = NULL;

		// ArgumentValidator::validate($params, 'params');
        $payLoad = "";
        $allowedParams = array(
            'offset' => 0,
            'count' => 100,
            // 'start_time' => 1,
            // 'end_time' => 1,
            // 'sort_order' => 1,
            // 'sort_by' => 1,
            // 'total_required' => 1
        );

        $json_data = self::executeCall(
            "/v1/products" . "?" . http_build_query(array_intersect_key($params, $allowedParams)),
            "GET",
            $payLoad,
            array(),
            $apiContext
        );
		// print "Product::all() DATA: " . $json_data . PHP_EOL;

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
				$products = new MCError( 'API Returned invalid Product data' );
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
            "/v1/products/" . self::rfc3986Encode($product_id),
            "GET",
            $payLoad,
            array(),
            $apiContext
        );
		// print "Product::get(" . $product_id . ") DATA: " . $json_data . PHP_EOL;

		$result = json_decode( $json_data, true );

		if ( $result['success'] ) {
			if ( isset($result['data']) && is_array($result['data']) ) {
				$product = new Product();
				$product->fromArray( $result['data'] );
			} else {
				$product = new MCError( 'API Returned invalid Product data' );
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

    public function create( $apiContext = null )
    {
		$product = NULL;
        $payload = $this->toArray();

		if ( isset($this->client_references) ) {
			foreach ( $this->client_references as $idx => $ref ) {
				$payload['client_references[' . $idx . ']'] = $ref;
			}
		}

		if ( ! empty($this->photo_file) ) {
			// print "CREATE PRODUCT: ATTACH PHOTO: " . var_export($this->photo_file, true) . PHP_EOL;
			$payload['photo'] =
				new \CurlFile(
					$this->photo_file['filepath'],
					$this->photo_file['filetype'],
					$this->photo_file['filename']
				);
		}
		// print "CREATE PRODUCT: PAYLOAD: " . var_export($payload, true) . PHP_EOL;

        $json_data = self::executeCall(
            "/v1/products",
            "POST",
            $payload,
            array(),
            $apiContext
        );
		// print "CREATE PRODUCT: JSON RESULT: " . $json_data . PHP_EOL;

		$result = json_decode( $json_data, true );

		if ( $result['success'] ) {
			if ( isset($result['data']) && is_array($result['data']) ) {
				$product = new Product();
				$product->fromArray( $result['data'] );
			} else {
				$product = new MCError( 'API Returned invalid Product data' );
				MCLoggingManager::getInstance(__CLASS__)
					->error( "Product data not array: " . print_r($result['data']) );
			}
		} else {
			$product = new MCError( $result['message'] );
			MCLoggingManager::getInstance(__CLASS__)
				->error( "Failed creating Product: " . $result['message'] );
		}

        return $product;
    }

    public function update( $apiContext = null )
    {
		if ( empty($this->id) ) {
			return new MCError( "Product has no id. You must set the id of the product to update." );
		}

		$product = NULL;
        $payload = $this->toArray();

		if ( $this->client_references !== NULL && is_array($this->client_references) ) {
			foreach ( $this->client_references as $idx => $ref ) {
				$payload['client_references[' . $idx . ']'] = $ref;
			}
		}

		if ( ! empty($this->photo_file) ) {
			$payload['photo'] =
				new \CurlFile(
					$this->photo_file['filepath'],
					$this->photo_file['filetype'],
					$this->photo_file['filename']
				);
		}
		// print "UPDATE PRODUCT: PAYLOAD: " . var_export($payload, true) . PHP_EOL;

        $json_data = self::executeCall(
            "/v1/products/" . $this->id,
            "PATCH",
            $payload,
            array(),
            $apiContext
        );
		// print "UPDATE PRODUCT: JSON RESULT: " . $json_data . PHP_EOL;

		$result = json_decode( $json_data, true );

		if ( $result['success'] ) {
			if ( isset($result['data']) && is_array($result['data']) ) {
				$product = new Product();
				$product->fromArray( $result['data'] );
			} else {
				$product = new MCError( 'API Returned invalid Product data' );
				MCLoggingManager::getInstance(__CLASS__)
					->error( "Product data not array: " . print_r($result['data']) );
			}
		} else {
			$product = new MCError( $result['message'] );
			MCLoggingManager::getInstance(__CLASS__)
				->error( "Failed creating Product: " . $result['message'] );
		}

        return $product;
    }

	/**
	 * Delete this Product.
	 *
	 * The ID of this Product object must be set before calling this function.
	 *
	 */

    public function delete( $apiContext = null )
    {
		if ( empty($this->id) ) {
			return new MCError( "Product has no id. You must set the id of the product to delete." );
		}

		$product = NULL;
        $payload = array();

        $json_data = self::executeCall(
            "/v1/products/" . $this->id,
            "DELETE",
            $payload,
            array(),
            $apiContext
        );
		// print "DELETE PRODUCT: JSON RESULT: " . $json_data . PHP_EOL;

		$result = json_decode( $json_data, true );

		if ( $result['success'] ) {
			if ( isset($result['data']) && is_array($result['data']) ) {
				$product = new Product();
				$product->fromArray( $result['data'] );
			} else {
				$product = new MCError( 'API Returned invalid Product data' );
				MCLoggingManager::getInstance(__CLASS__)
					->error( "Product data not array: " . print_r($result['data']) );
			}
		} else {
			$product = new MCError( $result['message'] );
			MCLoggingManager::getInstance(__CLASS__)
				->error( "Failed deleting Product: " . $result['message'] );
		}

        return $product;
    }

	public function fromArray( $data )
	{
		$this->client_references = $data['attributes']['client_references'];
		unset( $data['attributes']['client_references'] );
		$this->assignAttributes( $data['attributes'] );
	}

}
