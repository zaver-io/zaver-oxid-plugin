<?php
namespace Zaver\SDK\Object;
use Zaver\SDK\Utils\DataObject;

/**
 * The Payment Capture Request contains the necessary information to capture a payment.
 * 
 * @method float          getAmount()                   The Payment amount in the format 100 or the format 100.00.
 * @method string         getCurrency()                 The ISO currency code of the Payment. Currently, only "SEK" is supported.
 * @method array          getMerchantMetadata()         List of lineItems on the payment request to capture.
 * @method array          getLineItems() 		        An associative array of merchant-defined key-value pairs.
 */
class PaymentCaptureRequest extends DataObject {

	/**
	 * Required. The Payment amount in the format 100 or the format 100.00.
	 */
	public function setAmount(float $amount): self {
		$this->data['amount'] = $amount;

		return $this;
	}

	/**
	 * Required. The ISO currency code of the Payment. Currently, only "SEK" is supported.
	 */
	public function setCurrency(string $currency): self {
		$this->data['currency'] = $currency;

		return $this;
	}

	/**
	 * Add a LineItem to capture.
	 */
	public function addLineItem(LineItem $lineItem): self {
		if(!isset($this->data['lineItems'])) {
			$this->data['lineItems'] = [];
		}

		$this->data['lineItems'][] = $lineItem;

		return $this;
	}

	/**
	 * An associative array of merchant-defined key-value pairs. These are returned with the Payment Status Response.
	 * A Maximum of 20 pairs is allowed, each key and value with a maximum length of 200 characters.
	 */
	public function setMerchantMetadata(array $merchantMetadata): self {
		$this->data['merchantMetadata'] = $merchantMetadata;

		return $this;
	}
}