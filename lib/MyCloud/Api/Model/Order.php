<?php

namespace MyCloud\Api\Model;

use \DateTime;
use MyCloud\Api\Core\MCError;
use MyCloud\Api\Core\MyCloudModel;
use MyCloud\Api\Log\MCLoggingManager;

/**
 * Class Order
 *
 * Represents a MyCloud Order belonging to a Shop.
 *
 * @package MyCloud\Api\Model
 */
class Order extends MyCloudModel
{
	// Constants defining the possible states that an Order
	// can have. Getting an UNKNOWN state, or a state that
	// is not in the list of constants here, indicates that
	// something is wrong, most likely your API needs to
	// be updated.
	//
	const API_STATUS_RESERVED    = 'RESERVED';
	const API_STATUS_WAITPAYMENT = 'WAITPAY';
	const API_STATUS_RECVPAYMENT = 'RECVPAY';
	const API_STATUS_APPROVED    = 'APPROVED';
	const API_STATUS_PICKING     = 'PICKING';
	const API_STATUS_PROCESSING  = 'PROCESSING';
	const API_STATUS_SHIPPED     = 'SHIPPED';
	const API_STATUS_DELIVERED   = 'DELIVERED';
	const API_STATUS_UNKNOWN     = 'UNKNOWN';

	private $shop = NULL;

	private $order_items = array();
	
	private $customer = NULL;
	
	private $delivery_mode = NULL;

	private $attachments = array();

	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	public function getStatus() {
		return $this->status;
	}
	public function setStatus($status) {
		$this->status = $status;
		return $this;
	}

	public function getMcNumber() {
		return $this->mc_number;
	}
	public function setMcNumber($mc_number) {
		$this->mc_number = $mc_number;
		return $this;
	}

	public function getName() {
		return $this->mc_number;
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

	public function getPhoneNumber() {
		return $this->phone;
	}
	public function setPhoneNumber($phone_number) {
		$this->phone_number = $phone_number;
		return $this;
	}

	public function getEmail() {
		return $this->email;
	}
	public function setEmail($email) {
		$this->email = $email;
		return $this;
	}

	public function getWeight() {
		return $this->mc_number;
	}
	public function setWeight($weight) {
		$this->weight = $weight;
		return $this;
	}

	public function getShop()
	{
		if ( $this->shop == NULL ) {
			// FIXME
		}
		return $this->shop;
	}

	public function getOrderItems()
	{
		return $this->order_items;
	}

	public function getAttachments()
	{
		return $this->attachments;
	}

	public function getCustomer()
	{
		return $this->customer;
	}

	public function setCustomer( $customer )
	{
		$this->customer = $customer;
		return $this;
	}

	public function getDeliveryMode()
	{
		return $this->delivery_mode;
	}

	public function setDeliveryMode( $delivery_mode )
	{
		$this->delivery_mode = $delivery_mode;
		return $this;
	}

	public function addOrderItem( $order_item )
	{
		$this->order_items[] = $order_item;
		return $this;
	}

	/*
	 * addProduct is a convenience function for adding an order item
	 * using an existing Product object. This method will create a
	 * new OrderItem object and add it to the Order. This is typically
	 * used when creating a new Order.
	 */
	public function addProduct( $product, $quantity, $price )
	{
		$order_item = new OrderItem( $this, $product, $quantity, $price );
		$this->addOrderItem( $order_item );
		return $this;
	}

	public function attachFile( $attachment, $filename, $filetype, $filepath )
	{
		$this->attachments[] =
			array(
				'attachment' => $attachment,
				'filename'   => $filename,
				'filetype'   => $filetype,
				'filepath'   => $filepath
			);
		return $this;
	}

    public static function all( $params = array(), $apiContext = null )
    {
		$orders = NULL;

		// ArgumentValidator::validate($params, 'params');
        $payLoad = array();
        $allowedParams = array(
            'page_size' => 1,
            'page' => 1,
            // 'start_time' => 1,
            // 'end_time' => 1,
            // 'sort_order' => 1,
            // 'sort_by' => 1,
            // 'total_required' => 1,
        );

        $json_data = self::executeCall(
            "/v1/orders" . "?" . http_build_query(array_intersect_key($params, $allowedParams)),
            "GET",
            $payLoad,
            array(),
            $apiContext
        );

		$result = json_decode( $json_data, true );

		if ( $result['success'] ) {
			if ( isset($result['data']) && is_array($result['data']) ) {
				$orders = array();
				foreach ( $result['data'] as $order_data ) {
					$order = new Order();
					$order->fromArray( $order_data );
					$orders[] = $order;
				}
			} else {
				$orders = new MCError( 'API Returned invalid data' );
				MCLoggingManager::getInstance(__CLASS__)
					->error( "Order list not array: " . print_r($result['data']) );
			}
		} else {
			$orders = new MCError( $result['message'] );
			MCLoggingManager::getInstance(__CLASS__)
				->error( "Failed getting Order list: " . $result->message );
		}

        return $orders;
    }

    public static function get( $orderId, $apiContext = null )
    {
		$order = NULL;

        $payLoad = array();
        $json_data = self::executeCall(
            "/v1/orders/" . $orderId,
            "GET",
            $payLoad,
            array(),
            $apiContext
        );
		// print "Order::get(" . $orderId . ") DATA: " . $json_data . "\n";

		$result = json_decode( $json_data, true );

		if ( $result['success'] ) {
			if ( isset($result['data']) && is_array($result['data']) ) {
				$order = new Order();
				$order->fromArray( $result['data'] );
			} else {
				$order = new MCError( 'API Returned invalid data' );
				MCLoggingManager::getInstance(__CLASS__)
					->error( "Order data not array: " . print_r($result['data']) );
			}
		} else {
			$order = new MCError( $result['message'] );
			MCLoggingManager::getInstance(__CLASS__)
				->error( "Failed getting Order list: " . $result['message'] );
		}

        return $order;
    }

    public function create( $apiContext = null )
    {
		$order = NULL;
        $payload = $this->toArray();

		$payload['customer_id'] = empty($this->customer) ? '0' : $this->customer->id;
		$payload['delivery_mode_id'] = empty($this->delivery_mode) ? '0' : $this->delivery_mode->id;

		$index  = 0;
		foreach ( $this->order_items as $order_item ) {
			$payload['order_items[' . $index . '][product_id]'] = $order_item->product->id;
			$payload['order_items[' . $index . '][quantity]'] = $order_item->quantity;
			$payload['order_items[' . $index . '][price]'] = $order_item->price;
			$index++;
		}

		$index  = 0;
		foreach ( $this->attachments as $attach ) {
			$payload['attach_name[' . $index . ']'] = $attach['attachment'];
			$payload['attach_file[' . $index . ']'] =
				new \CurlFile(
					$attach['filepath'],
					$attach['filetype'],
					$attach['filename']
				);
			$index++;
		}
		// print "CREATE ORDER: PAYLOAD: " . var_export($payload, true) . PHP_EOL;

        $json_data = self::executeCall(
            "/v1/orders",
            "POST",
            $payload,
            array(),
            $apiContext
        );
		// print "CREATE ORDER: JSON RESULT: " . $json_data . PHP_EOL;

		$result = json_decode( $json_data, true );

		if ( $result['success'] ) {
			if ( isset($result['data']) && is_array($result['data']) ) {
				$order = new Order();
				$order->fromArray( $result['data'] );
			} else {
				$order = new MCError( 'API Returned invalid data' );
				MCLoggingManager::getInstance(__CLASS__)
					->error( "Order data not array: " . print_r($result['data']) );
			}
		} else {
			$order = new MCError( $result['message'] );
			MCLoggingManager::getInstance(__CLASS__)
				->error( "Failed creating Order: " . $result['message'] );
		}

        return $order;
    }

	public function fromArray( $data )
	{
		$this->assignAttributes( $data['attributes'] );

		if ( isset($data['customer']) && is_array($data['customer']) && !empty($data['customer']) ) {
			$customer = new Customer( $this );
			$customer->fromArray( $data['customer'] );
			$this->customer = $customer;
		}

		if ( isset($data['delivery_mode']) && is_array($data['delivery_mode']) && !empty($data['delivery_mode']) ) {
			$delivery_mode = new DeliveryMode( $this );
			$delivery_mode->fromArray( $data['delivery_mode'] );
			$this->delivery_mode = $delivery_mode;
		}

		if ( isset($data['order_items']) && is_array($data['order_items']) ) {
			foreach ( $data['order_items'] as $order_item_data ) {
				$order_item = new OrderItem( $this );
				$order_item->fromArray( $order_item_data );
				$this->order_items[] = $order_item;
			}
		}
	}

}
