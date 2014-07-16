<?php

namespace WebsiteConnect\Blackjack\Exception;

class CardNotFound extends \WebsiteConnect\Blackjack\Core\AbstractCardException {

	public function __construct($suit, $name){

		$message = sprintf('Card %s of %s not found.', $name, $suit);

		parent::__construct($suit, $name, $message);

	}

}