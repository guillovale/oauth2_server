<?php
namespace App\Domain\AwsApp;
use Exception;
use GuzzleHttp\Client;

class Aws {
	const TWITTER_API_BASE_URI = 'https://api.twitter.com';
	const GET_TWITS = '/1.1/statuses/user_timeline.json';

	private $key;
	private $secret;
	private $accessToken;
	private $client;

	public function __construct(String $key, String $secret) {
		$this->key = $key;
		$this->secret = $secret;

		$this->client = new Client(['base_uri' => self::TWITTER_API_BASE_URI]);

		$this->requestAccessToken();
		var_dump($this->accessToken);

	}

	private function requestAccessToken() {
		$encodedString = base64_encode(
			$this->key . ':' . $this->secret
		);
		$headers = [
			'Authorization' => 'Basic ' . $encodedString,
			'Content-Type' => 'application/x-www-form-
			urlencoded;charset=UTF-8'
		];
		$options = [
			'headers' => $headers,
			'body' => 'grant_type=client_credentials'
		];
		$response = $this->client->post(self:: OAUTH_ENDPOINT, $options);
		$body = json_decode($response->getBody(), true);
		$this->accessToken = $body['access_token'];
	}

	private function getAccessTokenHeaders(): array {
		if (empty($this->accessToken)) {
			$this->requestAccessToken();
		}
		return ['Authorization' => 'Bearer ' . $this->accessToken];
	}

	public function obtenerUsuarios(string nombre, int $cont): array {

		$options = [
			'headers' => $this->getAccessTokenHeaders(),
			'query' => [
				'count' => $cont,
				'screen_name' => $nombre
				]
		];

		try {
			$response = $this->client->get(self::GET_TWITS, $options);
		} catch (ClientException $e) {
			if ($e->getCode() == 401) {
				$this->requestAccessToken();
				$response = $this->client->get(self::GET_TWITS, $options);
			} else {
					throw $e;
			}
		}
		$responseTwits = json_decode($response->getBody(), true);
		$twits = [];
		foreach ($responseTwits as $twit) {
			$twits[] = [
				'created_at' => $twit['created_at'],
				'text' => $twit['text'],
			'user' => $twit['user']['name']
			];
		}

	return $twits;	

	}
}

