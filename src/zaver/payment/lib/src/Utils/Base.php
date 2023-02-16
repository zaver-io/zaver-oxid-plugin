<?php
namespace Zaver\SDK\Utils;
use Zaver\SDK\Config\Endpoint;

abstract class Base {
	
	/** @var Client $client */
	protected $client = null;
	protected $test = false;
	protected $callbackToken = null;

	public function __construct(string $apiKey, bool $test = false, ?string $callbackToken = null) {
		$this->client = new Client(($test ? Endpoint::TEST : Endpoint::PRODUCTION), $apiKey);
		$this->test = $test;
		$this->callbackToken = $callbackToken;
	}

	public function isTest(): bool {
		return $this->test;
	}

	public function getCallbackToken() {
		return $this->callbackToken;
	}
}