<?php

namespace Twitter\Core;

use \Twitter\Exception\ApiException as TwitterException;

class Client {

	public $consumerKey = '';
	public $consumerSecret = '';
	public $oauthToken = null;
	public $oauthSecret = null;

	private $_consumer = null;
	private $_token = null;
	private $_signatureMethod = null;

	const DEFAULT_RESULT_LIMIT = 200;
	const DEFAULT_BLOCK_SIZE = 100;
	const SCREEN_NAME_MAX = 15;
	const URL_API = 'https://api.twitter.com/1.1/';
	const URL_OAUTH = 'https://api.twitter.com/oauth/';

	public function __construct($consumerKey, $consumerSecret, $OAuthToken = null, $OAuthSecret = null){

		$this->consumerKey = $consumerKey;
		$this->consumerSecret = $consumerSecret;
		$this->oauthToken = $OAuthToken;
		$this->oauthSecret = $OAuthSecret;

		$this->_consumer = new \OAuth\Core\Consumer($this->consumerKey, $this->consumerSecret);
		$this->_signatureMethod = new \OAuth\SignatureMethod\HmacSha1();

		if (!is_null($OAuthToken) && !is_null($OAuthSecret))
			$this->_token = new \OAuth\Core\Token($this->oauthToken, $this->oauthSecret);

		@session_start();

	}

	public function login($callback){

		$token = $this->getRequestToken($callback);

		if ($token)
			$this->getAuthoriseUrl($token);
		else
			throw new TwitterException('getRequestToken');

	}

	public function logout(){

		unset($_SESSION['twitter_oauth_token']);
		unset($_SESSION['twitter_oauth_secret']);
		unset($_SESSION['twitter_oauth_verifier']);

	}

	public function getRequestToken($callback){

		$params = array(
			'oauth_callback' => $callback,
		);

		$result = $this->_api('request_token', $params, 'GET', false);

		if (!is_string($result))
			throw new TwitterException();

		$token = $this->_getString($result, 'oauth_token=', '&');
		$secret = $this->_getString($result, 'oauth_token_secret=', '&');
		//$confirmed = $this->_getString($result, 'oauth_callback_confirmed=');

		if ($token !== '')
			$this->oauthToken = $token;

		if ($secret !== '')
			$this->oauthSecret = $secret;

		if (!is_null($this->oauthToken) && !is_null($this->oauthSecret)){
			$this->_token = new \OAuth\Core\Token($this->oauthToken, $this->oauthSecret);
			$_SESSION['twitter_oauth_token'] = $token;
			$_SESSION['twitter_oauth_secret'] = $secret;
		}

		return $this->_token;

	}

	public function getAuthoriseUrl($token, $signIn = true, $redirect = true){

		$url = self::URL_OAUTH . ($signIn ? 'authenticate' : 'authorize') . '?oauth_token=' . $token->key;

		if ($redirect)
			header('Location: ' . $url);

		return $url;

	}

	public function handleCallback(){

		$verifier = null;
		$token = null;
		$secret = null;
		//$result = false;

		if (array_key_exists('oauth_verifier', $_GET))
			$verifier = $_GET['oauth_verifier'];

		if (array_key_exists('twitter_oauth_token', $_SESSION))
			$token = $_SESSION['twitter_oauth_token'];

		if (array_key_exists('twitter_oauth_secret', $_SESSION))
			$secret = $_SESSION['twitter_oauth_secret'];

		if (is_null($token) || is_null($secret) || is_null($verifier)){

			throw new TwitterException();

		} else {

			//$result = true;
			$_SESSION['twitter_oauth_verifier'] = $verifier;

		}

		//return $result;

	}

	public function getUserTimeLine($username, $max = self::DEFAULT_RESULT_LIMIT){

		$params = array(
			'count' => $max,
			'screen_name' => $username,
			'include_rts' => true,
			'include_entities' => true,
			'contributor_details' => true,
		);

		return $this->_api('statuses/user_timeline.json', $params);

	}

	private function _api($url, array $params = array(), $method = 'GET', $useApiUrl = true){

		$url = ($useApiUrl ? self::URL_API : self::URL_OAUTH) . $url;
		$pos = strpos($url, '://');
		$scheme = substr($url, 0, $pos);
		$url = $scheme . substr($url, $pos) . (strstr($url, '?') ? '&' : '?') . http_build_query($params);

		$request = \OAuth\Core\Request::from_consumer_and_token($this->_consumer, $this->_token, $method, $url);
		$request->sign_request($this->_signatureMethod, $this->_consumer, $this->_token);
		$url = $request->to_url();

		$options=array(
			$scheme => array(
				'method' => $method,
				'header'  => $request->to_header() . "\r\n",
			),
		);

		if (count($params) > 0)
			$options[$scheme]['content'] = http_build_query($params);

		$context = stream_context_create($options);
		$result = @file_get_contents($url, false, $context);

		if ($result !== false && $useApiUrl)
			$result = json_decode($result);

		return $result;

	}

	private function _getString($data, $search1, $search2 = null){

		$result = '';
		$pos1 = strpos($data, $search1);
		$pos2 = is_null($search2) ? null : strpos($data, $search2, $pos1 + strlen($search1));

		if ($pos1 !== false && $pos2 !== false)
			$result = substr($data, $pos1 + strlen($search1), $pos2 - $pos1 - strlen($search1));

		elseif ($pos1 !== false && is_null($pos2))
			$result = substr($data, $pos1 + strlen($search1));

		return $result;

	}

}