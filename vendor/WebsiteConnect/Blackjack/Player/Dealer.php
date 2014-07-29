<?php

namespace WebsiteConnect\Blackjack\Player;

class Dealer extends \WebsiteConnect\Blackjack\Core\AbstractPlayer {

	public function showCards(){

		foreach ($this->_cards as $card)
			$card->setVisible(true);

	}

}