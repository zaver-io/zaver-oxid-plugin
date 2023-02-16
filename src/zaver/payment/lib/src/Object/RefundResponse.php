<?php
namespace Zaver\SDK\Object;
use Zaver\SDK\Utils\DataObject;
use DateTime;

/**
 * Contains the current status of the payment. Returned by way of callback, or by calling the `GET` endpoint.
 */
class RefundResponse extends DataObject {

	/**
	 * The ID of the Refund. This is used when retrieving, approving, and cancelling the Refund.
	 */
	public function getRefundId(): string {
		return $this->data['refundId'] ?? '';
	}

	/**
	 * A description of the refund.
	 */
	public function getDescription(): string {
		return $this->data['description'] ?? '';
	}

	/**
	 * The ID of the payment being refunded.
	 */
	public function getPaymentId(): string {
		return $this->data['paymentId'] ?? '';
	}

	/**
	 * The invoice reference of the payment being refunded.
	 */
	public function getInvoiceReference(): string {
		return $this->data['invoiceReference'] ?? '';
	}

	/**
	 * The amount to be refunded. The amount needs to be less than or equal to the amount of the payment request being refunded.
	 */
	public function getRefundAmount(): float {
		return (float)$this->data['refundAmount'] ?? 0;
	}

	/**
	 * The ISO currency code of the Refund. Currently, only SEK is supported. This matches the currency of the payment that is refunded.
	 */
	public function getCurrency(): string {
		return $this->data['currency'] ?? '';
	}

	/**
	 * The total amount of tax for the refund.
	 */
	public function getRefundTaxAmount(): float {
		return (float)$this->data['refundTaxAmount'] ?? 0;
	}

	/**
	 * The tax percent of the refund.
	 */
	public function getRefundTaxRatePercent(): float {
		return (float)$this->data['refundTaxRatePercent'] ?? 0;
	}

	/**
	 * The status of the Refund.
	 */
	public function getStatus(): string {
		return $this->data['status'] ?? '';
	}

	/**
	 * An optional reference that the merchant can set to track the Refund in their system.
	 * Must be unique: No two refunds may use the same reference.
	 */
	public function getMerchantReference(): string {
		return $this->data['merchantReference'] ?? '';
	}

	/**
	 * Metadata on the Refund in the form of key/value pairs.
	 */
	public function getMerchantMetadata(): array {
		return (array)$this->data['merchantMetadata'] ?? [];
	}

	/**
	 * Merchant representative that created the Refund.
	 */
	public function getInitializingRepresentative(): ?MerchantRepresentative {
		return (!empty($this->data['initializingRepresentative']) ? new MerchantRepresentative($this->data['initializingRepresentative']) : null);
	}

	/**
	 * Merchant representative that approved the Refund.
	 */
	public function getApprovingRepresentative(): ?MerchantRepresentative {
		return (!empty($this->data['approvingRepresentative']) ? new MerchantRepresentative($this->data['approvingRepresentative']) : null);
	}

	/**
	 * Merchant representative that cancelled the Refund.
	 */
	public function getCancellingRepresentative(): ?MerchantRepresentative {
		return (!empty($this->data['cancellingRepresentative']) ? new MerchantRepresentative($this->data['cancellingRepresentative']) : null);
	}

	/**
	 * When the the refund was last updated.
	 */
	public function getLastEvent(): ?DateTime {
		return (isset($this->data['lastEvent']) ? new DateTime($this->data['lastEvent']) : null);
	}

	/**
	 * URLs relevant to the refund.
	 */
	public function getMerchantUrls(): ?MerchantUrls {
		return (empty($this->data['merchantUrls']) ? null : MerchantUrls::create($this->data['merchantUrls']));
	}

	/**
	 * List of line items being refunded
	 * @return RefundLineItem[] List of line items
	 */
	public function getLineItems(): array {
		if(empty($this->data['lineItems'])) {
			return [];
		}

		return array_map(fn($item) => ($item instanceof RefundLineItem ? $item : RefundLineItem::create($item)), $this->data['lineItems']);
	}
}