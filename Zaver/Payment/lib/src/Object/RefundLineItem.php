<?php
namespace Zaver\SDK\Object;
use Zaver\SDK\Utils\DataObject;

/**
 * The Payment Creation Request contains the necessary information to create a payment.
 * 
 * @method string getLineItemId()           The id of the line item being refunded.
 * @method float  getRefundTotalAmount()    Total refunded amount of the line item, including tax (e.g. VAT).
 * @method float  getRefundTaxAmount()      Total amount of tax (e.g. VAT) of the refunded line item.
 * @method float  getRefundTaxRatePercent() Tax percentage for the refunded line item, in percent (e.g. 25)
 * @method int    getRefundQuantity()       The number of units refunded.
 * @method float  getRefundUnitPrice()      The refunded amount per unit, including tax (e.g. VAT).
 * @method string getRefundDescription()    A brief description of the refunded line item.
 */
class RefundLineItem extends DataObject {
	
	/**
	 * Required. The id of the line item being refunded.
	 */
	public function setLineItemId(string $lineItemId): self {
		$this->data['lineItemId'] = $lineItemId;

		return $this;
	}

	/**
	 * Required. Total refunded amount of the line item, including tax (e.g. VAT).
	 */
	public function setRefundTotalAmount(float $refundTotalAmount): self {
		$this->data['refundTotalAmount'] = $refundTotalAmount;

		return $this;
	}

	/**
	 * Required. Total amount of tax (e.g. VAT) of the refunded line item.
	 */
	public function setRefundTaxAmount(float $refundTaxAmount): self {
		$this->data['refundTaxAmount'] = $refundTaxAmount;

		return $this;
	}

	/**
	 * Required. Tax percentage for the refunded line item, in percent (e.g. 25)
	 */
	public function setRefundTaxRatePercent(float $refundTaxRatePercent): self {
		$this->data['refundTaxRatePercent'] = $refundTaxRatePercent;

		return $this;
	}

	/**
	 * Required. The number of units refunded.
	 */
	public function setRefundQuantity(int $refundQuantity): self {
		$this->data['refundQuantity'] = $refundQuantity;

		return $this;
	}

	/**
	 * Required. The refunded amount per unit, including tax (e.g. VAT).
	 */
	public function setRefundUnitPrice(float $refundUnitPrice): self {
		$this->data['refundUnitPrice'] = $refundUnitPrice;

		return $this;
	}

	/**
	 * A brief description of the refunded line item.
	 */
	public function setRefundDescription(string $refundDescription): self {
		$this->data['refundDescription'] = $refundDescription;

		return $this;
	}
}