<?php
namespace Zaver\SDK\Object;
use Zaver\SDK\Utils\DataObject;
use DateTime;

/**
 * Contains the current status of the payment. Returned by way of callback, or by calling the `GET` endpoint.
 */
class PaymentStatusResponse extends DataObject {

	/**
	 * The ID of the payment.
	 */
	public function getPaymentId(): string {
		return $this->data['paymentId'] ?? '';
	}

	/**
	 * The token used to start the in-page checkout
	 */
	public function getToken(): string {
		return $this->data['token'] ?? '';
	}

	/**
	 * The URL used for redirecting the user to the external checkout
	 * @return String The redirect URL
	 */
	public function getPaymentLink(): string {
		return $this->data['paymentLink'] ?? '';
	}

	/**
	 * Expiry time of the checkout session in ISO 8601 format. Payment must be authorized by end consumer before this time.
	 */
	public function getValidUntil(): ?DateTime {
		return (isset($this->data['validUntil']) ? new DateTime($this->data['validUntil']) : null);
	}

	/**
	 * Expiry time of the authorized payment session in ISO 8601 format. If delayed capture is used, all captures must be finalized before this time.
	 */
	public function getCaptureBefore(): ?DateTime {
		return (isset($this->data['captureBefore']) ? new DateTime($this->data['captureBefore']) : null);
	}

	/**
	 * Reference set by merchant, e.g. order reference.
	 */
	public function getMerchantPaymentReference(): string {
		return $this->data['merchantPaymentReference'] ?? '';
	}

	/**
	 * List of line items from the Payment Request.
	 * @return LineItem[] List of line items
	 */
	public function getLineItems(): array {
		if(empty($this->data['lineItems'])) {
			return [];
		}

		return array_map(fn($item) => ($item instanceof LineItem ? $item : LineItem::create($item)), $this->data['lineItems']);
	}

	/**
	 * The payment amount in the format 100 or the format 100.00.
	 */
	public function getAmount(): float {
		return (float)($this->data['amount'] ?? 0);
	}

	/**
	 * The captured payment amount in the format 100 or the format 100.00.
	 */
	public function getCapturedAmount(): float {
		return (float)($this->data['capturedAmount'] ?? 0);
	}

	/**
	 * The refunded payment amount in the format 100 or the format 100.00.
	 */
	public function getRefundedAmount(): float {
		return (float)($this->data['refundedAmount'] ?? 0);
	}

	/**
	 * The status of the payment. One of `PaymentStatus`.
	 */
	public function getPaymentStatus(): string {
		return $this->data['paymentStatus'] ?? '';
	}

	/**
	 * An associative array of merchant-defined key-value pairs. These are set at payment creation.
	 */
	public function getMerchantMetadata(): array {
		return $this->data['merchantMetadata'] ?? [];
	}

	/**
	 * Contains customization options. All values are strings.
	 */
	public function getMerchantCustomizations(): array {
		return $this->data['merchantCustomizations'] ?? [];
	}

	/**
	 * The response authorization status
	 * @return AuthorizationStatus
	 * @link https://api-docs.zaver.se/v-1-2-0/checkout.html#authorization-status
	 */
	public function getAuthorizationStatus(): AuthorizationStatus {
		return AuthorizationStatus::create([
			'payerToken' => $this->data['authorizationStatus']['payerToken'] ?? ''
		]);
	}

	/**
	 * Depending on if the payment request was settled and on which method was used this might be provided.
	 * @return SpecificPaymentMethodData[]|null
	 */
	public function getSpecificPaymentMethodData(): ?array {
		if (!empty($this->data['specificPaymentMethodData']) && is_array($this->data['specificPaymentMethodData'])) {
			return array_map(function($item) {
				return SpecificPaymentMethodData::create($item);
			}, $this->data['specificPaymentMethodData']);
		}

		return null;
	}
}