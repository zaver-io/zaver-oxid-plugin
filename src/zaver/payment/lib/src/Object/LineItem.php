<?php
namespace Zaver\SDK\Object;
use Zaver\SDK\Utils\DataObject;

/**
 * Line item for `PaymentCreationRequest`
 * 
 * @method string getId()                A unique id for the line item. Set by Zaver.
 * @method string getName()              Name of the item being sold.
 * @method int    getQuantity()          Total number of units being paid.
 * @method float  getUnitPrice()         Price per unit being paid, including tax.
 * @method float  getTotalAmount()       Total amount of line item including tax. Must satisfy `totalAmount = unitPrice x quantity`.
 * @method float  getTaxRatePercent()    Tax percentage for a line item - e.g. `25.0`.
 * @method float  getTaxAmount()         Total amount of tax (e.g. VAT) included in the line item.
 * @method string getDescription()       Longer description of the line item.
 * @method string getItemType()          One of: `PHYSICAL`, `DIGITAL`, `SERVICE`, `SHIPPING`, `FEE`, `DISCOUNT`
 * @method string getMerchantReference() Your reference for a line item, e.g. a SKU.
 * @method array  getMerchantMetadata()  An associative array of merchant-defined key-value pairs.
 * @method string getQuantityUnit()      The unit in which quantity is measured - e.g. pcs, kgs.
 */
class LineItem extends DataObject {

	/**
	 * Required. Name of the item being sold.
	 */
	public function setName(string $name): self {
		$this->data['name'] = $name;

		return $this;
	}

	/**
	 * Required. Total number of units being paid.
	 */
	public function setQuantity(int $quantity): self {
		$this->data['quantity'] = $quantity;

		return $this;
	}

	/**
	 * Required. Price per unit being paid, including tax.
	 */
	public function setUnitPrice(float $unitPrice): self {
		$this->data['unitPrice'] = $unitPrice;

		return $this;
	}

	/**
	 * Required. Total amount of line item including tax. Must satisfy `totalAmount = unitPrice x quantity`.
	 */
	public function setTotalAmount(float $totalAmount): self {
		$this->data['totalAmount'] = $totalAmount;

		return $this;
	}

	/**
	 * Required. Tax percentage for a line item - e.g. `25.0`.
	 */
	public function setTaxRatePercent(float $taxRatePercent): self {
		$this->data['taxRatePercent'] = $taxRatePercent;

		return $this;
	}

	/**
	 * Total amount of tax (e.g. VAT) included in the line item.
	 */
	public function setTaxAmount(float $taxAmount): self {
		$this->data['taxAmount'] = $taxAmount;

		return $this;
	}

	/**
	 * Longer description of the line item.
	 */
	public function setDescription(string $description): self {
		$this->data['description'] = $description;

		return $this;
	}

	/**
	 * One of: `PHYSICAL`, `DIGITAL`, `SERVICE`, `SHIPPING`, `FEE`, `DISCOUNT`
	 */
	public function setItemType(string $itemType): self {
		$this->data['itemType'] = $itemType;

		return $this;
	}

	/**
	 * Your reference for a line item, e.g. a SKU.
	 */
	public function setMerchantReference(string $merchantReference): self {
		$this->data['merchantReference'] = $merchantReference;

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

	/**
	 * The unit in which quantity is measured - e.g. pcs, kgs.
	 */
	public function setQuantityUnit(string $quantityUnit): self {
		$this->data['quantityUnit'] = $quantityUnit;

		return $this;
	}
}