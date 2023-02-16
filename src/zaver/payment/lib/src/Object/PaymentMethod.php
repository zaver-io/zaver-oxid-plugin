<?php
namespace Zaver\SDK\Object;
use Zaver\SDK\Utils\DataObject;

/**
 * PaymentMethod as found in `PaymentMethodsResponse`
 * 
 * @method string         getPaymentMethod()    The Payment Method identifier.
 * @method string         getTitle()            The title of the payment method for display purposes.
 * @method string         getDescription()      A short text that describes the payment method.
 * @method string         getIconSvgSrc()       A URL to an SVG icon for the payment method.
 * @method array          getLocalizations()    Array of localizations for title, descriptions and icon corresponding to the selected market.
 */
class PaymentMethod extends DataObject {

	/**
	 * Alias for `getPaymentMethod`
	 */
	public function getIdentifier(): string {
		return $this->getPaymentMethod();
	}
}