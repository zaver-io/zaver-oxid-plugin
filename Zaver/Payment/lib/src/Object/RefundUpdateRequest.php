<?php
namespace Zaver\SDK\Object;
use Zaver\SDK\Utils\DataObject;

/**
 * The Payment Creation Request contains the necessary information to create a payment.
 * 
 * @method MerchantRepresentative|null getActingRepresentative() Merchant representative that is performing the action on the Refund. A user with the entered email needs to exist under the Merchant in Zaver for Business.
 */
class RefundUpdateRequest extends DataObject {

	/**
	 * Merchant representative that is performing the action on the Refund. A user with the entered email needs to exist under the Merchant in Zaver for Business.
	 */
	public function setActingRepresentative(MerchantRepresentative $actingRepresentative): self {
		$this->data['actingRepresentative'] = $actingRepresentative;

		return $this;
	}

	public function __toString(): string {
		return $this->data['actingRepresentative'] ?? '';
	}
}