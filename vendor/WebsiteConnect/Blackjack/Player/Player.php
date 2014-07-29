<?php

namespace WebsiteConnect\Blackjack\Player;

class Player extends \WebsiteConnect\Blackjack\Core\AbstractPlayer {

	public function move(\WebsiteConnect\Blackjack\Deck\Deck $deck, $scoreToBeat = null){

		if (!$this->isBust() && !$this->isBlackjack())
			$this->addCard($deck->getNewCard());

	}

}