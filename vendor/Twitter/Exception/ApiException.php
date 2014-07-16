<?php

namespace Twitter\Exception;

class ApiException extends \Twitter\Core\AbstractException{

	public function __construct($method = null){

		if (is_null($method)){
			$trace = debug_backtrace()[1];
			$method = $trace['function'];
		}

		parent::__construct("Error with Twitter API call: $method");

	}

}