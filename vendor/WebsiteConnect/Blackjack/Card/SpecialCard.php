<?php

namespace WebsiteConnect\Blackjack\Card;

class SpecialCard extends \WebsiteConnect\Blackjack\Core\AbstractCard {

	private $_fullName = null;

	public function __construct($suit, $name, $value){

		parent::__construct($suit, substr($name, 0, 1), $value);

		$this->_fullName = $name;

	}

	public function getFullName(){

		return $this->_fullName;

	}

	public function __toString(){

		return sprintf('%s of %s', $this->_fullName, $this->getSuit());

	}

}