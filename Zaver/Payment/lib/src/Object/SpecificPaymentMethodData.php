<?php

namespace Zaver\SDK\Object;

use Zaver\SDK\Utils\DataObject;
use Zaver\SDK\Utils\Error;

/**
 * If your API-key is configured to allow specificPaymentMethods, an array of this object will be returned.
 * 
 * @method string getCheckoutToken()	The token used to start the in-page checkout, directly in the specific payment method flow.
 * @method string getPaymentLink()		A link to the Zaver checkout, leading directly to the specific payment method flow.
 * @method string getPaymentMethod()	Specific payment method for this paymentLink/checkoutToken.
 */
class SpecificPaymentMethodData extends DataObject {
	/**
	 * The token used to start the in-page checkout, directly in the specific payment method flow.
	 */
	public function setCheckoutToken(string $checkoutToken): self {
		$this->data['checkoutToken'] = $checkoutToken;

		return $this;
	}
	
	/**
	 * A link to the Zaver checkout, leading directly to the specific payment method flow.
	 */
	public function setPaymentLink(string $paymentLink): self {
		$this->data['paymentLink'] = $paymentLink;

		return $this;
	}

	/**
	 * Specific payment method for this paymentLink/checkoutToken.
	 */
	public function setPaymentMethod(string $paymentMethod): self {
		if(!in_array($paymentMethod, MerchantCustomizationOptions::PAYMENT_METHODS)) {
			throw new Error('Invalid payment method.', 400);
		}

		$this->data['paymentMethod'] = $paymentMethod;

		return $this;
	}
}