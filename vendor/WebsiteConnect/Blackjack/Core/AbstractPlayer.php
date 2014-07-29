<?php

namespace WebsiteConnect\Blackjack\Core;

abstract class AbstractPlayer {

	protected $_score = null;
	protected $_cards = null;
	protected $_limit = null;
	protected $_threshold = null;

	public function __construct($limit, $threshold){

		$this->_limit = $limit;
		$this->_threshold = $threshold;

		$this->reset();

	}

	public abstract function move(\WebsiteConnect\Blackjack\Deck\Deck $deck, $scoreToBeat = null);

	public function getScore($asLongString = false){

		$score = null;

		if ($asLongString){
			if ($this->isBust())
				$score = 'Bust!';
			elseif ($this->isBlackjack())
				$score = 'Blackjack!';
		}

		if (is_null($score))
			$score = $this->_score;

		return $score;

	}

	public function getCards(){

		return $this->_cards;

	}

	public function getCardsAsString($separator = '<br>'){

		$result = '';
		$i = 0;

		foreach ($this->_cards as $card){
			if ($card->isVisible()){
				$result .= ($i > 0 ? $separator : '') . $card;
				$i++;
			}
		}

		return $result;

	}

	public function addCard(\WebsiteConnect\Blackjack\Core\AbstractCard $card, $visible = true){

		$card->setVisible($visible);
		$this->_cards[] = $card;
		$this->_score += $card->getValue();

	}

	public function reset(){

		$this->score = 0;
		$this->_cards = array();

	}

	public function isBust(){

		return $this->_score > $this->_limit;

	}

	public function isBlackjack(){

		return $this->_score === $this->_limit;

	}

	public function isAboveThreshold(){

		return $this->_score >= $this->_threshold;

	}

}