<?php

namespace WebsiteConnect\Blackjack\Core;

abstract class AbstractCardException extends AbstractException {

	protected $_suit = null;
	protected $_name = null;

	public function __construct($suit, $name, $message){

		$this->_suit = $suit;
		$this->_name = $name;

		parent::__construct($message);

	}

	public function getSuit(){

		return $this->_suit;

	}

	public function getName(){

		return $this->_name;

	}

}