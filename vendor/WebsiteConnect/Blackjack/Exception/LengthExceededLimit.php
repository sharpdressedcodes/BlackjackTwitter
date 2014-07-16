<?php

namespace WebsiteConnect\Blackjack\Exception;

class LengthExceededLimit extends \WebsiteConnect\Blackjack\Core\AbstractException {

	public function __construct($culprit, $max){

		parent::__construct("$culprit is too long. (max: $max)");

	}

}