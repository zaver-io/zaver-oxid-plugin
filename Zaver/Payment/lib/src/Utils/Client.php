<?php
namespace Zaver\SDK\Utils;
use Exception;
use GuzzleHttp;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;

class Client {
	private $client = null;

	/**
	 * @param string $endpoint Endpoint for all requests
	 * @param string $apiKey Bearer token
	 */
	public function __construct(string $endpoint, string $apiKey) {
		$this->client = new GuzzleHttp\Client([
			'base_uri' => $endpoint,
			'timeout' => 3,
			'headers' => [
				'Accept' => 'application/json',
				'Content-Type' => 'application/json',
				'Authorization' => sprintf('Bearer %s', $apiKey)
			]
		]);
	}

	/**
	 * @param string $uri URI of the request
	 */
	public function get(string $uri): array {
		return $this->request('GET', $uri);
	}

	/**
	 * @param string $uri URI of the request
	 * @param array|JsonSerializable $body Body of the request
	 */
	public function post(string $uri, $body): array {
		return $this->request('POST', $uri, $body);
	}

	/**
	 * @param string $uri URI of the request
	 * @param array|JsonSerializable $body Body of the request
	 */
	public function patch(string $uri, $body): array {
		return $this->request('PATCH', $uri, $body);
	}

	/**
	 * @param string $uri URI of the request
	 */
	public function delete(string $uri): array {
		return $this->request('DELETE', $uri);
	}

	/**
	 * @param string $method HTTP method
	 * @param string $uri URI where the request will be sent
	 * @param array|JsonSerializable $body Body of the request
	 */
	private function request(string $method, string $uri, $body = null): array {
		try {
			$options = [];

			if(!is_null($body)) {
				$options['json'] = $body;
			}

			return self::unwrap($this->client->request($method, $uri, $options));
		}
		catch(Error $e) {
			throw $e;
		}
		catch(ClientException $e) {
			$response = self::unwrap($e->getResponse());

			if(empty($response['errors']) || !is_array($response['errors'])) {
				throw new Error('An error occured while communicating with the Zaver API', null, $e);
			}

			throw new Error($response['errors'], null, null, $e);
		}
		catch(Exception $e) {
			throw new Error('An error occured while communicating with the Zaver API', null, $e);
		}
	}

	static private function unwrap(ResponseInterface $response): array {
		try {
			return json_decode($response->getBody(), true, 10, JSON_THROW_ON_ERROR);
		}
		catch(Exception $e) {
			throw new Error('Failed to decode Zaver response', null, $e);
		}
	}
}