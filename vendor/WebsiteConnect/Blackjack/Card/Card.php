<?php

namespace WebsiteConnect\Blackjack\Card;

class Card extends \WebsiteConnect\Blackjack\Core\AbstractCard {

	public function __construct($suit, $value){

		parent::__construct($suit, (string)$value, $value);

	}

}