<?php

namespace WebsiteConnect\Blackjack\Exception;

class GoneBust extends \WebsiteConnect\Blackjack\Core\AbstractException {

	private $_score = 0;
	private $_max = 0;

	public function __construct($score, $max){

		$this->_score = $score;
		$this->_max = $max;

		$message = sprintf('You have gone bust! Your score was %s (max: %s)', $score, $max);

		parent::__construct($message);

	}

	public function getScore(){

		return $this->_score;

	}

	public function getMax(){

		return $this->_max;

	}
}