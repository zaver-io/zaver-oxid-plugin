<?php
namespace Zaver\SDK\Object;
use Zaver\SDK\Utils\DataObject;

/**
 * Contains information about the captured payment.
 */
class PaymentCaptureResponse extends DataObject {

	/**
	 * The payment amount in the format 100 or the format 100.00.
	 */
	public function getId(): string {
		return (string)($this->data['id'] ?? '');
	}

	/**
	 * The payment amount in the format 100 or the format 100.00.
	 */
	public function getAmount(): float {
		return (float)($this->data['amount'] ?? 0);
	}

	/**
	 * The status of the payment. Possible statuses are `CREATED`, `SETTLED`, `CANCELLED` and `ERROR`.
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
}