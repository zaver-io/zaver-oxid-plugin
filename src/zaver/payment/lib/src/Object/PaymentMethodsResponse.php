<?php
namespace Zaver\SDK\Object;
use Zaver\SDK\Utils\DataObject;

/**
 * The ´PaymentMethodsResponse` contains the available payment methods for the merchant.
 */
class PaymentMethodsResponse extends DataObject {
	public function getPaymentMethods(): array {
		return array_map(function($m) {
			return PaymentMethod::create($m);
		}, $this->data['paymentMethods']);
	}
}