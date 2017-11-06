<?php

namespace MyCloud\Api\Model;

use MyCloud\Api\Core\MCError;
use MyCloud\Api\Core\MyCloudModel;
use MyCloud\Api\Log\MCLoggingManager;

/**
 * Class Customer
 *
 * Represents a MyCloud Customer
 *
 * @package MyCloud\Api\Model
 */
class Customer extends MyCloudModel
{
	public $order = NULL;

    /**
     * Default Constructor
     *
     */
	//    public function __construct( $order=NULL )
	//    {
	//		$this->order = $order;
	//    }

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

	public function getCode() {
		return $this->code;
	}
	public function setCode($code) {
		$this->code = $code;
		return $this;
	}

	public function getName() {
		return $this->name;
	}
	public function setName($name) {
		$this->name = $name;
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

	public function getSocialId() {
		return $this->social_id;
	}
	public function setSocialId($social_id) {
		$this->social_id = $social_id;
		return $this;
	}

	public function getEmail() {
		return $this->email;
	}
	public function setEmail($email) {
		$this->email = $email;
		return $this;
	}

	public function getPhoneNumber() {
		return $this->phone_number;
	}
	public function setPhoneNumber($phone_number) {
		$this->phone_number = $phone_number;
		return $this;
	}

	public function getNote() {
		return $this->note;
	}
	public function setNote($note) {
		$this->note = $note;
		return $this;
	}

	public function getOrder()
	{
		return $this->order;
	}
	public function setOrder( $order )
	{
		$this->order = $order;
		return $this;
	}

	/**
	 * Get all Customers attached to your shop.
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
            "/v1/customers" . "?" . http_build_query(array_intersect_key($params, $allowedParams)),
            "GET",
            $payLoad,
            array(),
            $apiContext
        );
		// print "Customer::all() DATA: " . $json_data . PHP_EOL;

		$result = json_decode( $json_data, true );

		if ( $result['success'] ) {
			if ( isset($result['data']) && is_array($result['data']) ) {
				$customers = array();
				foreach ( $result['data'] as $customer_data ) {
					$customer = new Customer();
					$customer->fromArray( $customer_data );
					$customers[] = $customer;
				}
			} else {
				$customers = new MCError( 'API Returned invalid Customer data' );
				MCLoggingManager::getInstance(__CLASS__)
					->error( "Customer list not array: " . print_r($result['data']) );
			}
		} else {
			$customers = new MCError( $result['message'] );
			MCLoggingManager::getInstance(__CLASS__)
				->error( "Failed getting Customer list: " . $result['message'] );
		}

        return $customers;
    }

	/**
	 * Get a Customer by ID.
	 *
	 */

    public static function get( $customer_id, $apiContext = null )
    {
		$customer = NULL;

        $payLoad = array();
        $json_data = self::executeCall(
            "/v1/customers/" . self::rfc3986Encode($customer_id),
            "GET",
            $payLoad,
            array(),
            $apiContext
        );
		// print "Customer::get(" . $customer_id . ") DATA: " . $json_data . PHP_EOL;

		$result = json_decode( $json_data, true );

		if ( $result['success'] ) {
			if ( isset($result['data']) && is_array($result['data']) ) {
				$customer = new Customer();
				$customer->fromArray( $result['data'] );
			} else {
				$customer = new MCError( 'API Returned invalid Customer data' );
				MCLoggingManager::getInstance(__CLASS__)
					->error( "Customer data not array: " . print_r($result['data']) );
			}
		} else {
			$customer = new MCError( $result['message'] );
			MCLoggingManager::getInstance(__CLASS__)
				->error( "Failed getting Customer[" . $customer_id . "]: " . $result['message'] );
		}

        return $customer;
    }

	/**
	 * Create a new Customer attached to your shop.
	 *
	 */

    public function create( $apiContext = null )
    {
		$customer = NULL;
        $payload = $this->toArray();
		// print "CREATE CUSTOMER: PAYLOAD: " . var_export($payload, true) . PHP_EOL;

        $json_data = self::executeCall(
            "/v1/customers",
            "POST",
            $payload,
            array(),
            $apiContext
        );
		// print "CREATE CUSTOMER: JSON RESULT: " . $json_data . PHP_EOL;

		$result = json_decode( $json_data, true );

		if ( $result['success'] ) {
			if ( isset($result['data']) && is_array($result['data']) ) {
				$customer = new Customer();
				$customer->fromArray( $result['data'] );
			} else {
				$customer = new MCError( 'API Returned invalid Customer data' );
				MCLoggingManager::getInstance(__CLASS__)
					->error( "Customer data not array: " . print_r($result['data']) );
			}
		} else {
			$customer = new MCError( $result['message'] );
			MCLoggingManager::getInstance(__CLASS__)
				->error( "Failed creating Customer: " . $result['message'] );
		}

        return $customer;
    }

	/**
	 * Update this Customer.
	 *
	 * The ID of this Customer object must be set before calling this function.
	 *
	 * NOTE The update will update any fields that you have set on this Customer
	 *      object, and will not change any fields that you have not set. In
	 *      other words, if you set the ID and Name fields of this Customer object,
	 *      then update() will change the name of the Customer matching this object's
	 *      ID. The other fields in the database will remain unchanged.
	 *
	 */

    public function update( $apiContext = null )
    {
		if ( empty($this->id) ) {
			return new MCError( "Customer has no id. You must set the id of the customer to update." );
		}

		$customer = NULL;
        $payload = $this->toArray();

		// print "UPDATE CUSTOMER: PAYLOAD: " . var_export($payload, true) . PHP_EOL;

        $json_data = self::executeCall(
            "/v1/customers/" . $this->id,
            "PATCH",
            $payload,
            array(),
            $apiContext
        );
		// print "UPDATE CUSTOMER: JSON RESULT: " . $json_data . PHP_EOL;

		$result = json_decode( $json_data, true );

		if ( $result['success'] ) {
			if ( isset($result['data']) && is_array($result['data']) ) {
				$customer = new Customer();
				$customer->fromArray( $result['data'] );
			} else {
				$customer = new MCError( 'API Returned invalid Customer data' );
				MCLoggingManager::getInstance(__CLASS__)
					->error( "Customer data not array: " . print_r($result['data']) );
			}
		} else {
			$customer = new MCError( $result['message'] );
			MCLoggingManager::getInstance(__CLASS__)
				->error( "Failed creating Customer: " . $result['message'] );
		}

        return $customer;
    }

	public function fromArray( $data )
	{
		$this->assignAttributes( $data['attributes'] );
	}

}
