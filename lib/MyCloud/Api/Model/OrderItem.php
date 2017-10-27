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
class OrderItem extends MyCloudModel
{
	public $order = NULL;
	public $product = NULL;

    /**
     * Default Constructor
     *
     */
    public function __construct( $order, $product=NULL, $quantity=0, $price=0 )
    {
		$this->order = $order;
		$this->price = $price;
		$this->product = $product;
		$this->quantity = $quantity;
    }

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

	public function getProduct()
	{
		return $this->product;
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
