<?php

namespace MyCloud\Api\Model;

use MyCloud\Api\Core\MyCloudModel;

/**
 * Class Product
 *
 * Represents a MyCloud DeliveryMode
 *
 * @package MyCloud\Api\Model
 */
class DeliveryMode extends MyCloudModel
{

    public static function all( $params = array(), $apiContext = null )
    {
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
            "/v1/deliverymodes" . "?" . http_build_query(array_intersect_key($params, $allowedParams)),
            "GET",
            $payLoad,
            array(),
            $apiContext
        );
		// print "DeliveryMode::all() DATA: " . $json_data . "\n";

		$result = json_decode( $json_data, true );

		if ( $result['success'] ) {
			if ( isset($result['data']) && is_array($result['data']) ) {
				$modes = array();
				foreach ( $result['data'] as $mode_data ) {
					$delivery_mode = new DeliveryMode();
					$delivery_mode->fromArray( $mode_data );
					$modes[] = $delivery_mode;
				}
			} else {
				$modes = new MCError( 'API Returned invalid data' );
				MCLoggingManager::getInstance(__CLASS__)
					->error( "DeliveryMode list not array: " . print_r($result['data']) );
			}
		} else {
			$modes = new MCError( $result['message'] );
			MCLoggingManager::getInstance(__CLASS__)
				->error( "Failed getting DeliveryMode list: " . $result['message'] );
		}

        return $modes;
    }

	public function fromArray( $data )
	{
		$this->assignAttributes( $data['attributes'] );
	}

}
