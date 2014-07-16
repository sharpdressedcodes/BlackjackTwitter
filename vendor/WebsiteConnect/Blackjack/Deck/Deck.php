<?php

namespace WebsiteConnect\Blackjack\Deck;

class Deck {

	const DECK_NUM_MIN = 2;
	const DECK_NUM_MAX = 10;
	const BLACKJACK = 21;
	const INPUT_MAX = 3;

	private $_cards = array();

	public function __construct(){

		$this->_fillDeck();

	}

	private function _fillDeck(){

		$suits = \WebsiteConnect\Blackjack\Card\SuitType::getTypes();
		$types = \WebsiteConnect\Blackjack\Card\SpecialCardType::getTypes();

		foreach ($suits as $suit => $value)
			$this->_fillSuit($value, $types);

	}

	private function _fillSuit($suit, $types){

		// Fill the numbers
		for ($i = self::DECK_NUM_MIN; $i <= self::DECK_NUM_MAX; $i++)
			$this->_cards[] = new \WebsiteConnect\Blackjack\Card\Card($suit, $i);

		// Fill the special cards
		foreach ($types as $type => $value)
			$this->_cards[] = new \WebsiteConnect\Blackjack\Card\SpecialCard($suit, strtolower($type), $value);

	}

	public function getCard($suit, $name, $throw = false){

		$result = null;

		foreach ($this->_cards as $card){

			if (strtolower($suit) === strtolower($card->getSuitCode()) &&
				strtolower($name) === strtolower($card->getName())){
				$result = $card;
				break;
			}

		}

		if (is_null($result) && $throw)
			throw new \WebsiteConnect\Blackjack\Exception\CardNotFound($suit, $name);

		return $result;

	}

	public function addCards(array $params){

		$result = 0;
		$cards = array();

		foreach ($params as $param){

			$card = null;

			if (!is_array($param)){

				$param = $this->tryParse($param);

				if (!$param)
					throw new \WebsiteConnect\Blackjack\Exception\InvalidInput();

			}

			$card = $this->getCard($param['suit'], $param['name']);

			// Card must exist in the deck.
			if (is_null($card))
				throw new \WebsiteConnect\Blackjack\Exception\CardNotFound($param['suit'], $param['name']);

			// Can't add same card twice.
			elseif (in_array($card, $cards))
				throw new \WebsiteConnect\Blackjack\Exception\DuplicateCard($param['suit'], $param['name']);

			$cards[] = $card;
			$result += $card->getValue();

		}

		// Treating this as an exception can be debatable.
		if ($result > self::BLACKJACK)
			throw new \WebsiteConnect\Blackjack\Exception\GoneBust($result, self::BLACKJACK);

		return $result;

	}

	public function tryParse($data){

		$result = false;
		$len = strlen($data);

		if ($len > self::INPUT_MAX)
			throw new \WebsiteConnect\Blackjack\Exception\LengthExceededLimit($data, self::INPUT_MAX);

		elseif ($len > 1){

			$suit = substr($data, strlen($data) - 1, 1);
			$name = str_replace($suit, '', $data);

			$result = array('suit' => $suit, 'name' => $name);

		}

		return $result;

	}

}