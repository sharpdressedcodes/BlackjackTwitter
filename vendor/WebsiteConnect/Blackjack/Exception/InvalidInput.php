<?php

namespace WebsiteConnect\Blackjack\Exception;

class InvalidInput extends \WebsiteConnect\Blackjack\Core\AbstractException {

	public function __construct(){

		parent::__construct('Invalid input.');

	}

}