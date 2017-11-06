<?php

namespace MyCloud\Api\Model;

use MyCloud\Api\Core\MyCloudModel;

/**
 * Class OrderItem
 *
 * Represents a MyCloud OrderItem included in an Order
 *
 * @package MyCloud\Api\Model
 */

// UNDONE - update
// We should probably provide an update() method to allow the client to
// update an order item's price and/or quantity without the overhead of
// an Order update.
// UNDONE - delete
// Need to provide a way to delete an order item from an order.

class OrderItem extends MyCloudModel
{
	public $order = NULL;
	public $product = NULL;

    /**
     * Default Constructor
     *
     */
	//    public function __construct( $order, $product=NULL, $quantity=0, $price=0 )
	//    {
	//		$this->order = $order;
	//		$this->price = $price;
	//		$this->product = $product;
	//		$this->quantity = $quantity;
	//    }

	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	public function getPrice() {
		return $this->price;
	}
	public function setPrice($price) {
		$this->price = $price;
		return $this;
	}

	public function getQuantity() {
		return $this->quantity;
	}
	public function setQuantity($quantity) {
		$this->quantity = $quantity;
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

	public function getProduct()
	{
		return $this->product;
	}
	public function setProduct( $product )
	{
		$this->product = $product;
		return $this;
	}

	/**
	 * Get a list of all OrderItems attached to an Order.
	 *
	 */
    public static function forOrder( $order, $apiContext = null )
    {
		return self::forOrderId( $order->id, $apiContext );
	}

	/**
	 * Get a list of all OrderItems attached to an Order specified by it's id.
	 *
	 */
    public static function forOrderId( $order_id, $apiContext = null )
    {
		$order_items = array();

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
            "/v1/orders/" . $order_id . "/orderitems" ,
            "GET",
            $payLoad,
            array(),
            $apiContext
        );

		$result = json_decode( $json_data, true );

		if ( $result['success'] ) {
			if ( isset($result['data']) && is_array($result['data']) ) {
				foreach ( $result['data'] as $order_item_data ) {
					$order_item = new OrderItem();
					$order_item->fromArray( $order_item_data );
					$order_items[] = $order_item;
				}
			} else {
				$orders = new MCError( 'API Returned invalid OrderItem data' );
				MCLoggingManager::getInstance(__CLASS__)
					->error( "OrderItem list not array: " . print_r($result['data']) );
			}
		} else {
			$orders = new MCError( $result['message'] );
			MCLoggingManager::getInstance(__CLASS__)
				->error( "Failed getting OrderItem list: " . $result->message );
		}

        return $order_items;
    }

    public static function get( $order_item_id, $apiContext = null )
    {
		$order_item = NULL;

        $payLoad = array();
        $json_data = self::executeCall(
            "/v1/orderitems/" . self::rfc3986Encode($order_item_id),
            "GET",
            $payLoad,
            array(),
            $apiContext
        );
		// print "OrderItem::get(" . $order_item_id . ") DATA: " . $json_data . PHP_EOL;

		$result = json_decode( $json_data, true );

		if ( $result['success'] ) {
			if ( isset($result['data']) && is_array($result['data']) ) {
				$order_item = new OrderItem();
				$order_item->fromArray( $result['data'] );
			} else {
				$order_item = new MCError( 'API Returned invalid OrderItem data' );
				MCLoggingManager::getInstance(__CLASS__)
					->error( "OrderItem data not array: " . print_r($result['data']) );
			}
		} else {
			$order_item = new MCError( $result['message'] );
			MCLoggingManager::getInstance(__CLASS__)
				->error( "Failed getting OrderItem[" . $order_item . "]: " . $result['message'] );
		}

        return $order_item;
    }

	/**
	 * Update this OrderItem.
	 *
	 * The ID of this Order object must be set before calling this function.
	 *
	 */

    public function update( $apiContext = null )
    {
		if ( empty($this->id) ) {
			return new MCError( "OrderItem has no id. You must set the id of the order item to update." );
		}

		$order_item = NULL;
        $payload = $this->toArray();

		if ( isset($this->price) ) {
			$payload['price'] = $this->price;
		}
		if ( isset($this->quantity) ) {
			$payload['quantity'] = $this->quantity;
		}

        $json_data = self::executeCall(
            "/v1/orderitems/" . $this->id,
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
	 * Delete this OrderItem from it's Order.
	 *
	 * The ID of this OrderItem object must be set before calling this function.
	 *
	 */

    public function delete( $apiContext = null )
    {
		if ( empty($this->id) ) {
			return new MCError( "OrderItem has no id. You must set the id of the order item to delete." );
		}

		$order_item = $this;
        $payload = array();

        $json_data = self::executeCall(
            "/v1/orderitems/" . $this->id,
            "DELETE",
            $payload,
            array(),
            $apiContext
        );
		// print "DELETE ORDERITEM: JSON RESULT: " . $json_data . PHP_EOL;

		$result = json_decode( $json_data, true );

		if ( ! $result['success'] ) {
			$order_item = new MCError( $result['message'] );
			MCLoggingManager::getInstance(__CLASS__)
				->error( "Failed deleting OrderItem: " . $result['message'] );
		}

        return $order_item;
    }

	public function fromArray( $data )
	{
		$this->assignAttributes( $data['attributes'] );
		if ( isset($data['product']) && is_array($data['product']) ) {
			$this->product = new Product();
			$this->product->fromArray( $data['product'] );
		}
	}

}
