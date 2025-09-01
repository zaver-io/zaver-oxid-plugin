<?php
namespace Zaver\SDK\Config;

class PaymentStatus {
	/** The payment has been created in the Zaver payment system, and is awaiting settlement. */
	const CREATED = 'CREATED';

	/** The payment is settled, i.e. payment has been completed. */
	const SETTLED = 'SETTLED';

	/** The payer has completed the checkout flow, and the payment is awaiting capture. */
	const PENDING = 'PENDING_MERCHANT_CAPTURE';

	/** The payment has been cancelled. */
	const CANCELLED = 'CANCELLED';

	/** An error occurred. The response will contain an array of errors with more information. */
	const ERROR = 'ERROR';
}