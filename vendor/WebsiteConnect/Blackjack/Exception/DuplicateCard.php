<?php

namespace WebsiteConnect\Blackjack\Exception;

class DuplicateCard extends \WebsiteConnect\Blackjack\Core\AbstractCardException {

	public function __construct($suit, $name){

		$message = sprintf('Card %s of %s has already been used.', $name, $suit);

		parent::__construct($suit, $name, $message);

	}

}