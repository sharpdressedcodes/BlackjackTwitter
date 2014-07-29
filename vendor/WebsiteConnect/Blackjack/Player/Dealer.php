<?php

namespace WebsiteConnect\Blackjack\Player;

class Dealer extends \WebsiteConnect\Blackjack\Core\AbstractPlayer {

	public function move(\WebsiteConnect\Blackjack\Deck\Deck $deck, $scoreToBeat = null){

		while (!$this->isBust() && !$this->isBlackjack() && $this->getScore() < $scoreToBeat)
			$this->addCard($deck->getNewCard());

	}

	public function showCards(){

		foreach ($this->_cards as $card)
			$card->setVisible(true);

	}

}