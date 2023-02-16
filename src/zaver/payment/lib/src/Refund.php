<?php
namespace Zaver\SDK;
use Zaver\SDK\Utils\Base;
use Zaver\SDK\Utils\Error;
use Zaver\SDK\Utils\Helper;
use Zaver\SDK\Object\RefundCreationRequest;
use Zaver\SDK\Object\RefundResponse;
use Zaver\SDK\Object\RefundUpdateRequest;
use Exception;

class Refund extends Base {
	public function createRefund(RefundCreationRequest $request): RefundResponse {
		$response = $this->client->post('/refund/v1', $request);

		return new RefundResponse($response);
	}

	public function getRefund(string $refundId): RefundResponse {
		$response = $this->client->get(sprintf('/refund/v1/%s', $refundId));

		return new RefundResponse($response);
	}

	public function approveRefund(string $refundId, ?RefundUpdateRequest $request = null): RefundResponse {
		$response = $this->client->post(sprintf('/refund/v1/%s/approve', $refundId), $request);

		return new RefundResponse($response);
	}

	public function cancelRefund(string $refundId, ?RefundUpdateRequest $request = null): RefundResponse {
		$response = $this->client->post(sprintf('/refund/v1/%s/cancel', $refundId), $request);

		return new RefundResponse($response);
	}

	public function receiveCallback(?string $callbackKey = null, ?string $content = null): RefundResponse {
		if(is_null($callbackKey)) {
			$callbackKey = $this->getCallbackToken();
		}

		if(!is_null($callbackKey) && !hash_equals($callbackKey, Helper::getAuthorizationKey())) {
			throw new Error('Invalid callback key', 401);
		}

		try {
			if($_SERVER['REQUEST_METHOD'] !== 'POST') {
				throw new Error('Invalid HTTP method', 405);
			}
			
			if(is_null($content)) {
				$content = file_get_contents('php://input');
			}
			
			$data = json_decode($content, true, 10, JSON_THROW_ON_ERROR);
		}
		catch(Exception $e) {
			throw new Error('Failed to decode Zaver response', null, $e);
		}

		return new RefundResponse($data);
	}
}