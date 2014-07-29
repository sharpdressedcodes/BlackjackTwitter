<?php

require_once('vendor/Autoloader.php');

use \WebsiteConnect\Blackjack\Deck\Deck as BlackjackDeck;

@session_start();

$playerResult = '';
$dealerResult = '';
$playerCardString = '';
$dealerCardString = '';
$move = getFromArray('move', $_POST);
$deck = null;
$player = null;
$dealer = null;

if (empty($move)){

	$deck = new BlackjackDeck(true);
	$player = new \WebsiteConnect\Blackjack\Player\Player(BlackjackDeck::BLACKJACK, BlackjackDeck::THRESHOLD);
	$dealer = new \WebsiteConnect\Blackjack\Player\Dealer(BlackjackDeck::BLACKJACK, BlackjackDeck::THRESHOLD);

    // give 2 cards to player
    // give 2 cards to dealer (1 card hidden)

	$player->addCard($deck->getNewCard());
	$dealer->addCard($deck->getNewCard(), false);

	$player->addCard($deck->getNewCard());
	$dealer->addCard($deck->getNewCard());

} else {

	$deck = getFromArray('blackjack-deck', $_SESSION, false);
	$player = getFromArray('blackjack-player', $_SESSION, false);
	$dealer = getFromArray('blackjack-dealer', $_SESSION, false);

	if (empty($deck))
		die('Error retrieving deck from session.');
	elseif (empty($player))
		die('Error retrieving player from session.');
	elseif (empty($dealer))
		die('Error retrieving dealer from session.');

	if (strtolower($move) === 'hit'){

		$player->move($deck);

	} else {

		if (!$player->isAboveThreshold()){

			$playerResult = 'You cannot stop before ' . BlackjackDeck::THRESHOLD;

		} else {

			$dealer->showCards();
			$scoreToBeat = $player->getScore();
			$dealer->move($deck, $scoreToBeat);
			$dealerResult = $dealer->getScore(true);

		}

	}

}

$_SESSION['blackjack-deck'] = $deck;
$_SESSION['blackjack-player'] = $player;
$_SESSION['blackjack-dealer'] = $dealer;

$playerCardString = $player->getCardsAsString();
$dealerCardString = $dealer->getCardsAsString();
if (empty($playerResult))
	$playerResult = $player->getScore(true);

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
			<td><b>Player:</b></td>
			<td><b>Dealer:</b></td>
		</tr>
		<tr>
			<td style="vertical-align: top;"><?php echo $playerCardString; ?></td>
			<td style="vertical-align: top;"><?php echo $dealerCardString; ?></td>
		</tr>
		<tr>
			<td><?php echo $playerResult; ?></td>
			<td><?php echo $dealerResult; ?></td>
		</tr>
		<tr>
			<td>
				<form method="post" action="game.php" style="float:left;">
					<input type="submit" name="move" value="Hit">
				</form>

				<form method="post" action="game.php" style="float:left;">
					<input type="submit" name="move" value="Stay">
				</form>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>
				<br>
				<form method="get" action="game.php">
					<input type="submit" name="new" value="New Game">
				</form>
			</td>
			<td>&nbsp;</td>
		</tr>
	</table>
</div>

</body>
</html>