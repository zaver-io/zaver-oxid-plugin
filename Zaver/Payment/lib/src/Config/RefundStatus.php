<?php
namespace Zaver\SDK\Config;

class RefundStatus {
	/** This status is set when a refund is created. The refund needs to be approved by the merchant before it is being processed. */
	const PENDING_MERCHANT_APPROVAL = 'PENDING_MERCHANT_APPROVAL';

	/** The refund has been approved by the merchant and it is being processed by Zaver. */
	const PENDING_EXECUTION = 'PENDING_EXECUTION';

	/** The refund has been executed and funds have been refunded. */
	const EXECUTED = 'EXECUTED';

	/** The refund has been cancelled. */
	const CANCELLED = 'CANCELLED';
}