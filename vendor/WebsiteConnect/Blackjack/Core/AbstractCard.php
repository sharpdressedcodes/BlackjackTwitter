<?php

namespace WebsiteConnect\Blackjack\Core;

class AbstractCard {

	protected $_suit = null;
	protected $_name = null;
	protected $_value = null;

	protected $_visible = true;

	protected function __construct($suit, $name, $value){

		$this->_suit = $suit;
		$this->_name = $name;
		$this->_value = $value;

	}

	public function getSuit(){

		return $this->_suit;

	}

	public function getName(){

		return $this->_name;

	}

	public function getValue(){

		return $this->_value;

	}

	public function getSuitCode(){

		return substr($this->_suit, 0, 1);

	}

	public function isVisible(){

		return $this->_visible;

	}

	public function setVisible($newValue){

		$this->_visible = $newValue;

	}


	public function __toString(){

		return sprintf('%s of %s', $this->_name, $this->_suit);

	}

}