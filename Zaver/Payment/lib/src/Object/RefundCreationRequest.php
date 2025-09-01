<?php
namespace Zaver\SDK\Object;
use Zaver\SDK\Utils\DataObject;

/**
 * The Payment Creation Request contains the necessary information to create a payment.
 * 
 * @method string                 getDescription()                A description of the refund.
 * @method string                 getPaymentId()                  The id of the payment being refunded.
 * @method string                 getInvoiceReference()           The invoice reference of the payment being refunded.
 * @method float                  getRefundAmount()               The amount to be refunded. Decimal amount are specified with dot (.) as separator, e.g. 175400.50.
 * @method float                  getRefundTaxAmount()            The total amount of tax for the refund. Could be required for Fixed amount refunds. See Create a Fixed amount refund for details on when this is required.
 * @method float                  getRefundTaxPercent()           The tax percent of the refund. Could be required for Fixed amount refunds. See Create a Fixed amount refund for details on when this is required.
 * @method MerchantRepresentative getInitializingRepresentative() Merchant representative that is creating the Refund.
 * @method string                 getMerchantReference()          A reference that the merchant can set to track the Refund in their system.
 * @method array                  getMerchantMetadata()           Metadata on the refund in the form of key/value pairs.
 * @method MerchantUrls           getMerchantUrls()               URLs relevant to the refund.
 * @method RefundLineItem[]       getLineItems()                  List of line items being refunded.
 */
class RefundCreationRequest extends DataObject {

	/**
	 * A description of the refund.
	 */
	public function setDescription(string $description): self {
		$this->data['description'] = $description;

		return $this;
	}

	/**
	 * Required. The id of the payment being refunded.
	 */
	public function setPaymentId(string $paymentId): self {
		$this->data['paymentId'] = $paymentId;

		return $this;
	}

	/**
	 * Required. The invoice reference of the payment being refunded.
	 */
	public function setInvoiceReference(string $invoiceReference): self {
		$this->data['invoiceReference'] = $invoiceReference;

		return $this;
	}

	/**
	 * Required. The amount to be refunded. Decimal amount are specified with dot (.) as separator, e.g. 175400.50.
	 * The amount needs to be less than or equal to the amount of the payment request being refunded.
	 */
	public function setRefundAmount(float $refundAmount): self {
		$this->data['refundAmount'] = $refundAmount;

		return $this;
	}

	/**
	 * The total amount of tax for the refund. Could be required for Fixed amount refunds. See Create a Fixed amount refund for details on when this is required.
	 */
	public function setRefundTaxAmount(float $refundTaxAmount): self {
		$this->data['refundTaxAmount'] = $refundTaxAmount;

		return $this;
	}

	/**
	 * 	The tax percent of the refund. Could be required for Fixed amount refunds. See Create a Fixed amount refund for details on when this is required.
	 */
	public function setRefundTaxPercent(float $refundTaxRatePercent): self {
		$this->data['refundTaxRatePercent'] = $refundTaxRatePercent;

		return $this;
	}

	/**
	 * Merchant representative that is creating the Refund.
	 */
	public function setInitializingRepresentative(MerchantRepresentative $initializingRepresentative): self {
		$this->data['initializingRepresentative'] = $initializingRepresentative;

		return $this;
	}

	/**
	 * A reference that the merchant can set to track the Refund in their system. Must be unique: no two Refunds may use the same reference.
	 */
	public function setMerchantReference(string $merchantReference): self {
		$this->data['merchantReference'] = $merchantReference;

		return $this;
	}

	/**
	 * Metadata on the refund in the form of key/value pairs.
	 */
	public function setMerchantMetadata(array $metadata): self {
		$this->data['merchantMetadata'] = $metadata;

		return $this;
	}

	/**
	 * URLs relevant to the refund.
	 */
	public function setMerchantUrls(MerchantUrls $merchantUrls): self {
		$this->data['merchantUrls'] = $merchantUrls;

		return $this;
	}

	/**
	 * Add to the list of line items to be refunded.
	 */
	public function addLineItem(RefundLineItem $lineItem): self {
		if(!isset($this->data['lineItems'])) {
			$this->data['lineItems'] = [];
		}

		$this->data['lineItems'][] = $lineItem;

		return $this;
	}
}