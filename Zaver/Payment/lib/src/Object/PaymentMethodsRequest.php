<?php

namespace Zaver\SDK\Object;
use Zaver\SDK\Utils\DataObject;

/**
 * The ´PaymentMethodsRequest` contains the necessary information to capture a payment.
 * 
 * @method float          getAmount()                   The Payment amount in the format 100 or the format 100.00.
 * @method string         getCurrency()                 The ISO currency code of the Payment.
 * @method string         getMarket()                   The ISO 3166-1 country code of the current market.
 */
class PaymentMethodsRequest extends DataObject {

	/**
	 * Required. The ISO 3166-1 country code of the current market.
	 */
	public function setMarket(string $market): self {
		$this->data['market'] = strtoupper($market);

		return $this;
	}

	/**
	 * The Payment amount in the format 100 or the format 100.00.
	 */
	public function setAmount(float $amount): self {
		$this->data['amount'] = $amount;

		return $this;
	}

	/**
	 * The ISO currency code of the Payment.
	 */
	public function setCurrency(string $currency): self {
		$this->data['currency'] = $currency;

		return $this;
	}


	public function getData(): array {
		return $this->data;
	}
}