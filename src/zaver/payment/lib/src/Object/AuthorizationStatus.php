<?php
namespace Zaver\SDK\Object;
use Zaver\SDK\Utils\DataObject;

/**
 * Authorization Status for `PaymentStatusResponse`
 */
class AuthorizationStatus extends DataObject {

	/**
	 * A unique token that can be used to initiate a activating a very quick and simple checkout flow.
	 * @return string The payer token
	 */
	public function getPayerToken(): string {
		return $this->data['payerToken'] ?? '';
	}
}