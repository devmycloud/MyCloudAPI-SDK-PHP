<?php

namespace MyCloud\Api\Model;

use MyCloud\Api\Core\MyCloudModel;

/**
 * Class Shop
 *
 * Represents a MyCloud Shop belonging to a User.
 *
 * @package MyCloud\Api\Model
 */
class Shop extends MyCloudModel
{

	public function getId() {
		return $this->id;
	}

	public function isAavailable() {
		return $this->available;
	}

	public function getCode() {
		return $this->code;
	}
	public function setCode($code) {
		$this->code = $code;
		return $this;
	}

	public function getContactName() {
		return $this->contact_name;
	}
	public function setContactName($contact_name) {
		$this->contact_name = $contact_name;
		return $this;
	}

	public function getAddress() {
		return $this->address;
	}
	public function setAddress($address) {
		$this->address = $address;
		return $this;
	}

	public function getPostcode() {
		return $this->postcode;
	}
	public function setPostcode($postcode) {
		$this->postcode = $postcode;
		return $this;
	}

	public function getEmail() {
		return $this->email;
	}
	public function setEmail($email) {
		$this->email = $email;
		return $this;
	}

	public function getLogo() {
		return $this->logo;
	}
	public function setLogo($logo) {
		$this->logo = $logo;
		return $this;
	}

	public function getName() {
		return $this->name;
	}
	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	public function getPhoneNumber() {
		return $this->phone_number;
	}
	public function setPhoneNumber($phone_number) {
		$this->phone_number = $phone_number;
		return $this;
	}

	/**
	 * Get all shops attached to your account.
	 * Note that currently MyCloud supports only a single Shop
	 * per client account, so this method will return a single
	 * shop in the resulting array.
	 *
	 */

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
            "/v1/shops" . "?" . http_build_query(array_intersect_key($params, $allowedParams)),
            "GET",
            $payLoad,
            array(),
            $apiContext
        );
		print "Shop::all() DATA: " . $json_data . PHP_EOL;

		$result = json_decode( $json_data, true );

		if ( $result['success'] ) {
			if ( isset($result['data']) && is_array($result['data']) ) {
				$shops = array();
				foreach ( $result['data'] as $shop_data ) {
					$shop = new Customer();
					$shop->fromArray( $shop_data );
					$shops[] = $shop;
				}
			} else {
				$shops = new MCError( 'API Returned invalid Shop data' );
				MCLoggingManager::getInstance(__CLASS__)
					->error( "Shop list not array: " . print_r($result['data']) );
			}
		} else {
			$shops = new MCError( $result['message'] );
			MCLoggingManager::getInstance(__CLASS__)
				->error( "Failed getting Shop list: " . $result['message'] );
		}

        return $shops;
    }

	/**
	 * Get a Shop by it's ID.
	 * Note that if you request a Shop that is not attached
	 * to your account, you will get a "not located" error.
	 *
	 */

    public static function get( $shop_id, $apiContext = null )
    {
		$shop = NULL;

        $payLoad = array();
        $json_data = self::executeCall(
            "/v1/shops/" . self::rfc3986Encode($shop_id),
            "GET",
            $payLoad,
            array(),
            $apiContext
        );
		print "Shop::get() DATA: " . $json_data . PHP_EOL;

		$result = json_decode( $json_data, true );

		if ( $result['success'] ) {
			if ( isset($result['data']) && is_array($result['data']) ) {
				$shop = new Customer();
				$shop->fromArray( $result['data'] );
			} else {
				$shop = new MCError( 'API Returned invalid Shop data' );
				MCLoggingManager::getInstance(__CLASS__)
					->error( "Shop data not array: " . print_r($result['data']) );
			}
		} else {
			$shop = new MCError( $result['message'] );
			MCLoggingManager::getInstance(__CLASS__)
				->error( "Failed getting Shop: " . $result['message'] );
		}

        return $shop;
    }

}
