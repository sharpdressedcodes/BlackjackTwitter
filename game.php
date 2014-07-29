<?php

require_once('vendor/Autoloader.php');

use \WebsiteConnect\Blackjack\Deck\Deck as BlackjackDeck;
use \WebsiteConnect\Blackjack\Core\AbstractException as BlackjackException;

@session_start();

$blackjackResult = '';
$playerResult = '';
$dealerResult = '';
$playerCardString = '';
$dealerCardString = '';
$move = getFromArray('move', $_POST);
$deck = new BlackjackDeck(true);
$player = null;
$dealer = null;

if (empty($move)){

	$player = new \WebsiteConnect\Blackjack\Player\Player();
	$dealer = new \WebsiteConnect\Blackjack\Player\Dealer();

    // give 2 cards to player
    // give 2 cards to dealer (1 card flipped)

	$player->addCard($deck->getNewCard());
	$dealer->addCard($deck->getNewCard(), false);
	$player->addCard($deck->getNewCard());
	$dealer->addCard($deck->getNewCard());

} else {

	$player = getFromArray('blackjack-player', $_SESSION);
	$dealer = getFromArray('blackjack-dealer', $_SESSION);

	if (empty($player)){
		die('Error retrieving player from session.');
	} elseif (empty($dealer)){
		die('Error retrieving dealer from session.');
	}

	if (strtolower($move) === 'hit'){

		try {
			$player->addCard($deck->getNewCard());
		} catch (BlackjackException $e){
			// can either get blackjack here or go over
			// then its the dealers turn
		}

	} else {

		$dealer->showCards();

		// dealer has to keep going until stop or bust.

		try {
			$dealer->addCard($deck->getNewCard());
		} catch (BlackjackException $e){



		}

		$dealerResult = $dealer->getScore();

	}


}

$_SESSION['blackjack-player'] = $player;//serialize($player);
$_SESSION['blackjack-dealer'] = $dealer;//serialize($dealer);

$playerCardString = $player->getCardsAsString();
$dealerCardString = $dealer->getCardsAsString();
$playerResult = $player->getScore();
//$dealerResult = $dealer->getScore();
//$playerCardString = implode('<br>', $playerCards);
//$dealerCardString = implode('<br>', $dealerCards);
//$playerResult = $deck->addCards($playerCards);
//$dealerResult = $deck->addCards($dealerCards);


//if ($card1 !== '' && $card2 !== ''){
//
//    $deck = new BlackjackDeck();
//    //$deck->shuffle();
//
//
//    try {
//
//        // Pass the data to the Deck class. It is responsible for
//        // parsing the input strings and turning them into cards.
//        $blackjackResult = $deck->addCards(array($card1, $card2));
//
//        if ($blackjackResult === BlackjackDeck::BLACKJACK)
//            $blackjackResult = 'Blackjack! ' . $blackjackResult;
//
//    } catch (BlackjackException $e){
//
//        $blackjackResult = "Error: " . $e->getMessage();
//
//    }
//
//}

/*
 * Helper methods.
 */

function getFromArray($key, $array, $sanitise = true){

    $result = '';

    if (array_key_exists($key, $array)){

        $result = $array[$key];

        if ($sanitise && is_string($result))
            $result = sanitiseString($result);

    }

    return $result;

}

/*
 * Quick and dirty input filter.
 */
function sanitiseString($data){

	$data = html_entity_decode($data, ENT_QUOTES, 'UTF-8');
	$data = htmlspecialchars_decode($data, ENT_QUOTES);
	$data = strip_tags(trim($data));

    return $data;

}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Blackjack Game</title>
    <meta name="description" content="Basic Blackjack Game">
    <meta name="author" content="Greg Kappatos">
</head>
<body>

<div>
    <h2>Blackjack</h2>
	<table>
		<tr>
			<td>Player:</td>
			<td>Dealer:</td>
		</tr>
		<tr>
			<td><?php echo $playerCardString; ?></td>
			<td><?php echo $dealerCardString; ?></td>
		</tr>
		<tr>
			<td><?php echo $playerResult; ?></td>
			<td><?php echo $dealerResult; ?></td>
		</tr>
		<tr>
			<td colspan="2">
				<form method="post" action="game.php">
					<input type="submit" name="move" value="Hit">
				</form>

				<form method="post" action="game.php">
					<input type="submit" name="move" value="Stay">
				</form>
			</td>
		</tr>
	</table>
</div>

</body>
</html>