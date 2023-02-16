<?php

namespace Zaver\SDK;

use Zaver\SDK\Config\Endpoint;
use Zaver\SDK\Object\PaymentCreationRequest;
use Zaver\SDK\Object\PaymentUpdateRequest;
use Zaver\SDK\Object\PaymentStatusResponse;
use Zaver\SDK\Object\PaymentCaptureRequest;
use Zaver\SDK\Object\PaymentCaptureResponse;
use Zaver\SDK\Object\PaymentMethodsRequest;
use Zaver\SDK\Object\PaymentMethodsResponse;
use Zaver\SDK\Utils\Base;
use Zaver\SDK\Utils\Error;
use Zaver\SDK\Utils\Html;
use Zaver\SDK\Utils\Helper;
use Exception;

class Checkout extends Base {

	/**
	 * Create a payment using a `PaymentCreationRequest` as the message body. In return, you get a `PaymentStatusResponse`.
	 */
	public function createPayment(PaymentCreationRequest $request): PaymentStatusResponse {
		$response = $this->client->post('/payments/checkout/v1', $request);

		return new PaymentStatusResponse($response);
	}

	/**
	 * Capture a payment using a previously created `paymentId` and a `PaymentCaptureRequest` as the message body. In return, you get a `PaymentCaptureResponse`.
	 */
	public function capturePayment(string $paymentId, PaymentCaptureRequest $request): PaymentCaptureResponse {
		$response = $this->client->post("/payments/checkout/v1/$paymentId/capture", $request);

		return new PaymentCaptureResponse($response);
	}

	public function getPaymentStatus(string $paymentId): PaymentStatusResponse {
		$response = $this->client->get("/payments/checkout/v1/$paymentId");

		return new PaymentStatusResponse($response);
	}

	public function getPaymentMethods(PaymentMethodsRequest $request): PaymentMethodsResponse {
		$data = $request->getData();
		$query = !empty($data) ? '?' . http_build_query($data) : '';

		$response = $this->client->get("/payments/checkout/paymentmethods/v1$query");

		return new PaymentMethodsResponse($response);
	}

	public function updatePayment(string $paymentId, PaymentUpdateRequest $request): PaymentStatusResponse {
		$response = $this->client->patch("/payments/checkout/v1/$paymentId", $request);

		return new PaymentStatusResponse($response);
	}

	public function receiveCallback(?string $callbackKey = null, ?string $content = null): PaymentStatusResponse {
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

		return new PaymentStatusResponse($data);
	}

	/**
	 * @param PaymentStatusResponse|string $token
	 */
	public function getHtmlSnippet($token, array $attributes = []): string {
		if($token instanceof PaymentStatusResponse) {
			$token = $token->getToken();
		}
		elseif(!is_string($token)) {
			throw new Error('Expected token string');
		}

		return Html::getTag('script', false, array_merge([
			'src' => ($this->isTest() ? Endpoint::TEST_SCRIPT : Endpoint::PRODUCTION_SCRIPT),
			'id' => 'zco-loader',
			'zco-token' => $token
		], $attributes));
	}
}