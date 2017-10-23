<?php

namespace MyCloud\Api\Core;

/**
 * Class MCError
 *
 * All MyCloud API REST calls wil either return an API Model object,
 * or an instance of MCError. Thus, your code should check the result
 * of any REST call against "instanceof MCError" to see if an error
 * has occurred processing your request.
 *
 * @package MyCloud\Api\Core
 */
class MCError
{
	private $message = NULL;

	public function getMessage()
	{
		return $this->message;
	}

    /**
     * Default Constructor
     *
     */
    public function __construct( $message )
    {
		$this->message = $message;
    }

}
