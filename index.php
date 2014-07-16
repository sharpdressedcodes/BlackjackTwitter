<?php

require_once('vendor/Autoloader.php');

use \WebsiteConnect\Blackjack\Deck\Deck as BlackjackDeck;
use \WebsiteConnect\Blackjack\Core\AbstractException as BlackjackException;
use \Twitter\Core\Client as TwitterClient;
use \Twitter\Core\AbstractException as TwitterException;

$config = array();
$configFile = __DIR__ . '/config.php';
if (file_exists($configFile))
	include_once($configFile);


if (empty($config))
	die('You must open config-sample.php and follow the instructions at the top.');

$tweetLimit = getFromArray('twitterTweetLimit', $config);
$consumerKey = getFromArray('twitterConsumerKey', $config);
$consumerSecret = getFromArray('twitterConsumerSecret', $config);

$thisUrl = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
$callbackUrl = $thisUrl . '?from=twitter'; // in the twitter app, this is the callback url
$twitter = new TwitterClient($consumerKey, $consumerSecret);
$blackjackResult = '';
$twitterResult = '';
$from = getFromArray('from', $_GET);
$denied = getFromArray('denied', $_GET);
$card1 = getFromArray('card1', $_POST);
$card2 = getFromArray('card2', $_POST);
$screenName = getFromArray('screenname', $_POST);
$storedScreenName = getFromArray('twitter_screenname', $_SESSION, false);



// Test for Twitter callback (user authorised).

if ($from === 'twitter' && empty($denied)){

	$parsedTweets = '';
	$followers = $friends = $tweetCount = 0;

	try {

		// Finish the login process.
		$twitter->handleCallback();

		// Grab the data.
		$tweets = $twitter->getUserTimeLine($storedScreenName, $tweetLimit);

	} catch (TwitterException $e){

		$twitterResult = $e->getMessage();

	}

	// Any tweets returned?
	if (!empty($tweets)){

		$user = $tweets[0]->user;
		$followers = $user->followers_count;
		$friends = $user->friends_count;
		$tweetCount = $user->statuses_count;

		$parsedTweets = array();
		foreach ($tweets as $tweet)
			$parsedTweets[] = $tweet->text;
		$parsedTweets = implode('<br>', $parsedTweets);

	}

	$twitterResult = sprintf(
		'Tweets: %s, Followers: %s, Friends: %s<br><br>%s',
		$tweetCount,
		$followers,
		$friends,
		$parsedTweets
	);

	// Show the screen name in the input field.
	$screenName = $storedScreenName;
	unset($_SESSION['twitter_screenname']);




// Test for Twitter.

} elseif ($screenName !== ''){

	// Ensure screen name does not exceed max.
	if (strlen($screenName) > TwitterClient::SCREEN_NAME_MAX){

		$twitterResult = 'Screen name cannot be longer than '. TwitterClient::SCREEN_NAME_MAX . ' characters.';

	} else {

		// Go for gold.
		try {

			$twitter->login($callbackUrl);

			// Store screen name, so we can show inside input field again.
			$_SESSION['twitter_screenname'] = $screenName;

		} catch (TwitterException $e){

			$twitterResult = $e->getMessage();

		}

	}




// Test for Blackjack

} elseif ($card1 !== '' && $card2 !== ''){

	$deck = new BlackjackDeck();

	try {

		// Pass the data to the Deck class. It is responsible for
		// parsing the input strings and turning them into cards.
		$blackjackResult = $deck->addCards(array($card1, $card2));

		if ($blackjackResult === BlackjackDeck::BLACKJACK)
			$blackjackResult = 'Blackjack! ' . $blackjackResult;

	} catch (BlackjackException $e){

		$blackjackResult = "Error: " . $e->getMessage();

	}

}

/*
 * Helper methods.
 */

function getFromArray($key, $array, $sanitise = true){

	$result = '';

	if (array_key_exists($key, $array)){

		$result = $array[$key];

		if ($sanitise)
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
	<title>Blackjack and Twitter Example</title>
	<meta name="description" content="Basic Blackjack game and Twitter information">
	<meta name="author" content="Greg Kappatos">
</head>
<body>

<div>
	<h2>Blackjack</h2>
	<form method="post" action="index.php">
		<table>
			<tr>
				<td><label for="card1">Card 1:</label></td>
				<td><input type="text" name="card1" id="card1" value="<?php echo $card1; ?>" maxlength="<?php echo BlackjackDeck::INPUT_MAX; ?>" required="true" placeholder="Enter card..."></td>
			</tr>
			<tr>
				<td><label for="card2">Card 2:</label></td>
				<td><input type="text" name="card2" id="card2" value="<?php echo $card2; ?>" maxlength="<?php echo BlackjackDeck::INPUT_MAX; ?>" required="true" placeholder="Enter card..."></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" value="Add Cards"></td>
			</tr>
		</table>
	</form>
	<div><?php echo $blackjackResult; ?></div>
</div>

<div style="margin-top: 20px;">
	<h2>Twitter</h2>
	<form method="post" action="index.php">
		<table>
			<tr>
				<td><label for="screenname">Screen Name: @</label></td>
				<td><input type="text" name="screenname" id="screenname" value="<?php echo $screenName; ?>" maxlength="<?php echo TwitterClient::SCREEN_NAME_MAX; ?>" required="true" placeholder="Enter screen name..."></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" value="Get Info"></td>
			</tr>
		</table>
	</form>
	<div><?php echo $twitterResult; ?></div>
</div>

</body>
</html>