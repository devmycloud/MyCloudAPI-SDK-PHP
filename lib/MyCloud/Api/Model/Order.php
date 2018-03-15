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

	public $order_items = array();
	
	public $customer = NULL;
	
	public $delivery_mode = NULL;

	public $attachments = array();

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

	public function getBillingTitle() {
		return $this->billing_title;
	}
	public function setBillingTitle($billing_title) {
		$this->billing_title = $billing_title;
		return $this;
	}

	public function getCustomerReference() {
		return $this->customer_reference;
	}
	public function setCustomerReference($customer_reference) {
		$this->customer_reference = $customer_reference;
		return $this;
	}

	public function getOrderNumber() {
		return $this->order_number;
	}
	public function setOrderNumber($order_number) {
		$this->order_number = $order_number;
		return $this;
	}

	public function getCreateDate() {
		return $this->create_date;
	}
	public function setCreateDate($create_date) {
		$this->create_date = $create_date;
		return $this;
	}

	public function getDeliveryDate() {
		return $this->delivery_date;
	}
	public function setDeliveryDate($delivery_date) {
		$this->delivery_date = $delivery_date;
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

	public function getTotalPrice() {
		return $this->total_price;
	}
	public function setTotalPrice($total_price) {
		$this->total_price = $total_price;
		return $this;
	}

	public function getUrgent() {
		return $this->urgent;
	}
	public function setUrgent($urgent) {
		$this->urgent = $urgent;
		return $this;
	}

	public function getWeight() {
		return $this->mc_number;
	}
	public function setWeight($weight) {
		$this->weight = $weight;
		return $this;
	}

	public function getPaymentAmount() {
		return $this->payment_amount;
	}
	public function setPaymentAmount($payment_amount) {
		$this->payment_amount = $payment_amount;
		return $this;
	}

	public function getPaymentDate() {
		return $this->payment_date;
	}
	public function setPaymentDate($payment_date) {
		$this->payment_date = $payment_date;
		return $this;
	}

	public function getPaymentTime() {
		return $this->payment_time;
	}
	public function setPaymentTime($payment_time) {
		$this->payment_time = $payment_time;
		return $this;
	}

	public function getAttachments()
	{
		return $this->attachments;
	}

	public function getCustomerId() {
		return $this->customer_id;
	}

	public function getCustomer()
	{
		return $this->customer;
	}

	public function setCustomer( $customer )
	{
		$customer->setOrder( $this );
		$this->customer = $customer;
		return $this;
	}

	public function getDeliveryModeId() {
		return $this->delivery_mode_id;
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

	public function getOrderItems()
	{
		return $this->order_items;
	}

	public function addOrderItem( $order_item )
	{
		$order_item->setOrder( $this );
		$this->order_items[] = $order_item;
		return $this;
	}

	/*
	 * addProduct is a convenience function for adding an order item
	 * using an existing Product object. This method will create a
	 * new OrderItem object and add it to the Order. This should only
	 * used when creating a new Order.
	 *
	 * NOTE You can create the product using only the product's id,
	 *      but that product MUST already exist.
	 */
	public function addProduct( $product, $quantity, $price )
	{
		$order_item = new OrderItem();
		$order_item
			->setOrder( $this )
			->setProduct( $product )
			->setQuantity( $quantity )
			->setPrice( $price );
		$this->addOrderItem( $order_item );
		return $this;
	}

	/*
	 * addProductById is a convenience function for adding an order item
	 * using only a Product id. This method will create a new OrderItem
	 * object and add it to the Order. This should only used when creating
	 * a new Order.
	 *
	 * NOTE The Product with the given ID MUST already exist.
	 */
	public function addProductById( $product_id, $quantity, $price )
	{
		$product = new Product();
		$product->setId( $product_id );
		$order_item = new OrderItem();
		$order_item
			->setOrder( $this )
			->setProduct( $product )
			->setQuantity( $quantity )
			->setPrice( $price );
		$this->addOrderItem( $order_item );
		return $this;
	}

	/*
	 * addProductBySKU is a convenience function for adding an order item
	 * using only the MyCloud SKU. This method will create a new OrderItem
	 * object and add it to the Order. This should only used when creating
	 * a new Order.
	 *
	 * NOTE The Product with the given MyCloud SKU MUST already exist.
	 */
	public function addProductBySKU( $sku, $quantity, $price )
	{
		$product = new Product();
		$product->setSKU( $sku );
		$order_item = new OrderItem();
		$order_item
			->setOrder( $this )
			->setProduct( $product )
			->setQuantity( $quantity )
			->setPrice( $price );
		$this->addOrderItem( $order_item );
		return $this;
	}

	/*
	 * addProductByShopSKU is a convenience function for adding an order item
	 * using only the Shop's SKU. This method will create a new OrderItem
	 * object and add it to the Order. This should only used when creating
	 * a new Order.
	 *
	 * NOTE The Product with the given Shop SKU MUST already exist.
	 */
	public function addProductByShopSKU( $shop_sku, $quantity, $price )
	{
		$product = new Product();
		$product->setShopSKU( $shop_sku );
		$order_item = new OrderItem();
		$order_item
			->setOrder( $this )
			->setProduct( $product )
			->setQuantity( $quantity )
			->setPrice( $price );
		$this->addOrderItem( $order_item );
		return $this;
	}

	public function attachFile( $attachment, $filename, $filetype, $filepath )
	{
		// if ( ! isset($this->_params['attachments']) ) {
		// 	$this->attachments = array();
		// }
		$this->attachments[] =
			array(
				'attachment' => $attachment,
				'filename'   => $filename,
				'filetype'   => $filetype,
				'filepath'   => $filepath
			);
		return $this;
	}

	/**
	 * Get a list of all Orders attached to your shop.
	 *
	 */
    public static function all( $params = array(), $apiContext = null )
    {
		$orders = NULL;

		// ArgumentValidator::validate($params, 'params');
        $payLoad = array();
        $allowedParams = array(
			'deleted' => 1,
            'offset' => 0,
            'count' => 100,
            // 'start_time' => 1,
            // 'end_time' => 1,
            // 'sort_order' => 1,
            // 'sort_by' => 1,
            // 'total_required' => 1,
        );

        $json_data = self::executeCall(
            "/v1/orders" . "?" . http_build_query( array_intersect_key($params, $allowedParams) ),
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
				$orders = new MCError( 'API Returned invalid Order data' );
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

	/**
	 * Get an existing Order using it's ID.
	 *
	 */
    public static function get( $orderId, $apiContext = null )
    {
		$order = NULL;

        $payLoad = array();
        $json_data = self::executeCall(
            "/v1/orders/" . self::rfc3986Encode($orderId),
            "GET",
            $payLoad,
            array(),
            $apiContext
        );
		// print "Order::get(" . $orderId . ") DATA: " . $json_data . PHP_EOL;

		$result = json_decode( $json_data, true );

		if ( $result['success'] ) {
			if ( isset($result['data']) && is_array($result['data']) ) {
				$order = new Order();
				$order->fromArray( $result['data'] );
			} else {
				$order = new MCError( 'API Returned invalid Order data' );
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

	/**
	 * Create a new Order.
	 *
	 * The ID of this Order object will be ignored by this function.
	 *
	 * NOTE
	 * The create will store any fields that you have set on this Order
	 * object, and will not store any fields that you have not set. Any
	 * fields not set will get appropriate default values.
	 *
	 * NOTE
	 * A Customer can be attached to this Order with setCustomer().
	 * You only need to set the ID of the Customer object passed to setCustomer().
	 * The Customer must alreay exist in the datbase - a new one will NOT be created
	 * for you.
	 *
	 * NOTE
	 * A DeliveryMode can be assigned to this Order with setDeliveryMode().
	 * You only need to set the ID of the DeliveryMode object passed to setDeliveryMode().
	 * The DeliveryMode must alreay exist in the database - a new one will NOT be created
	 * for you.
	 *
	 * NOTE
	 * Only three images can be attached to an order at any one time.
	 *
	 */

    public function create( $apiContext = null )
    {
		$order = NULL;
        $payload = $this->toArray();

		$payload['customer_id'] = empty($this->customer) ? '0' : $this->customer->id;
		$payload['delivery_mode_id'] = empty($this->delivery_mode) ? '0' : $this->delivery_mode->id;

		$index  = 0;
		foreach ( $this->order_items as $order_item ) {
			if ( ! empty($order_item->product->id) ) {
				$payload['order_items[' . $index . '][product_id]'] = $order_item->product->id;
			}
			if ( ! empty($order_item->product->sku) ) {
				$payload['order_items[' . $index . '][product_sku]'] = $order_item->product->sku;
			}
			if ( ! empty($order_item->product->shop_sku) ) {
				$payload['order_items[' . $index . '][shop_sku]'] = $order_item->product->shop_sku;
			}
			$payload['order_items[' . $index . '][quantity]'] = $order_item->quantity;
			$payload['order_items[' . $index . '][price]'] = $order_item->price;
			$index++;
		}

		$index  = 0;
		foreach ( $this->attachments as $attach ) {
			$payload['attach_name[' . $index . ']'] = $attach['attachment'];
			$payload['attach_file[' . $attach['attachment'] . ']'] =
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
				$order = new MCError( 'API Returned invalid Order data' );
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

	/**
	 * Update this Order.
	 *
	 * The ID of this Order object must be set before calling this function.
	 *
	 * NOTE
	 * The update will update any fields that you have set on this Order
	 * object, and will not change any fields that you have not set. In
	 * other words, if you set the ID and status fields of this Order object,
	 * then update() will change the status of the Order matching this object's
	 * ID. The other fields in the database will remain unchanged.
	 *
	 * NOTE
	 * The Customer attached to this Order can be changed with setCustomer().
	 * You only need to set the ID of the Customer object passed to setCustomer().
	 * The Customer must alreay exist in the datbase - a new one will NOT be created
	 * for you.
	 *
	 * NOTE
	 * The DeliveryMode assigned to this Order can be changed with setDeliveryMode().
	 * You only need to set the ID of the DeliveryMode object passed to setDeliveryMode().
	 * The DeliveryMode must alreay exist in the database - a new one will NOT be created
	 * for you.
	 *
	 * NOTE
	 * Order image attachment updates check the name of existing attachments.
	 * If the name of the update attachment matches an existing attachment, then
	 * the existing attachment's image will be replaced by the update image. If
	 * no existing attachment name matches the update attachment name, then the
	 * update will be attached as a new attachment. Only three images can be
	 * attached to an order at any one time.
	 *
	 */

    public function update( $apiContext = null )
    {
		if ( empty($this->id) ) {
			return new MCError( "Order has no id. You must set the id of the order to update." );
		}

		$order = NULL;
        $payload = $this->toArray();

		$payload['customer_id'] = empty($this->customer) ? '0' : $this->customer->id;
		$payload['delivery_mode_id'] = empty($this->delivery_mode) ? '0' : $this->delivery_mode->id;

		$index  = 0;
		foreach ( $this->order_items as $order_item ) {
			if ( ! empty($order_item->id) ) {
				$payload['order_items[' . $index . '][id]'] = $order_item->id;
			}
			if ( ! empty($order_item->product) ) {
				$payload['order_items[' . $index . '][product_id]'] = $order_item->product->id;
			}
			$payload['order_items[' . $index . '][quantity]'] = $order_item->quantity;
			$payload['order_items[' . $index . '][price]'] = $order_item->price;
			$index++;
		}

		$index  = 0;
		foreach ( $this->attachments as $attach ) {
			$payload['attach_name[' . $index . ']'] = $attach['attachment'];
			$payload['attach_file[' . $attach['attachment'] . ']'] =
				new \CurlFile(
					$attach['filepath'],
					$attach['filetype'],
					$attach['filename']
				);
			$index++;
		}
		// print "UPDATE ORDER: PAYLOAD: " . var_export($payload, true) . PHP_EOL;

        $json_data = self::executeCall(
            "/v1/orders/" . $this->id,
            "PATCH",
            $payload,
            array(),
            $apiContext
        );
		// print "UPDATE ORDER: JSON RESULT: " . $json_data . PHP_EOL;

		$result = json_decode( $json_data, true );

		if ( $result['success'] ) {
			if ( isset($result['data']) && is_array($result['data']) ) {
				$order = new Order();
				$order->fromArray( $result['data'] );
			} else {
				$order = new MCError( 'API Returned invalid Order data' );
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

	/**
	 * Delete this Order.
	 *
	 * The ID of this Order object must be set before calling this function.
	 *
	 */

    public function delete( $apiContext = null )
    {
		if ( empty($this->id) ) {
			return new MCError( "Order has no id. You must set the id of the order to delete." );
		}

		$order = $this;
        $payload = array();

        $json_data = self::executeCall(
            "/v1/orders/" . $this->id,
            "DELETE",
            $payload,
            array(),
            $apiContext
        );
		// print "DELETE ORDER: JSON RESULT: " . $json_data . PHP_EOL;

		$result = json_decode( $json_data, true );

		if ( ! $result['success'] ) {
			$order = new MCError( $result['message'] );
			MCLoggingManager::getInstance(__CLASS__)
				->error( "Failed deleting Order: " . $result['message'] );
		}

        return $order;
    }

	public function fromArray( $data )
	{
		// FIXME The "if" statements below checking for is_array() and ! empty()
		//       should be logging the "not" cases, as they imply errors in the data.

		$this->attachments = array();
		if ( isset($data['attachments']) ) {
			if ( is_array($data['attachments']) && !empty($data['attachments']) ) {
				$this->attachments = $data['attachments'];
			}
			unset($data['attachments']);
		}

		if ( isset($data['customer']) ) {
			if ( is_array($data['customer']) && !empty($data['customer']) ) {
				$customer = new Customer();
				$customer->setOrder( $this );
				$customer->fromArray( $data['customer'] );
				$this->customer = $customer;
			}
			unset($data['customer']);
		}

		if ( isset($data['delivery_mode']) ) {
			if ( is_array($data['delivery_mode']) && !empty($data['delivery_mode']) ) {
				$delivery_mode = new DeliveryMode( $this );
				$delivery_mode->fromArray( $data['delivery_mode'] );
				$this->delivery_mode = $delivery_mode;
			}
			unset($data['delivery_mode']);
		}

		if ( isset($data['order_items']) ) {
			if ( is_array($data['order_items']) ) {
				foreach ( $data['order_items'] as $order_item_data ) {
					$order_item = new OrderItem();
					$order_item->setOrder($this);
					$order_item->fromArray( $order_item_data );
					$this->order_items[] = $order_item;
				}
			}
			unset($data['order_items']);
		}

		$this->assignAttributes( $data['attributes'] );
	}

}
