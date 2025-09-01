<?php
namespace Zaver\SDK\Config;

class ErrorCode {
	/**
	 * A parameter was set to an invalid value. Will return a message with the invalid parameter, and the received value.
	 */
	const VALIDATION_ERROR = 'VALIDATION_ERROR';

	/**
	 * The request could not be parsed.
	 */
	const PARSE_ERROR = 'PARSE_ERROR';

	/**
	 * An attempt was made to change a payment in an unchangable state.
	 */
	const PAYMENT_LOCKED = 'PAYMENT_LOCKED';

	/**
	 * The refund was not in the required state for the given action. E.g. if the refund is cancelled when it has the status `PENDING_EXECUTION`.
	 */
	const REFUND_ILLEGAL_STATE = 'REFUND_ILLEGAL_STATE';

	/**
	 * The username specified in the `initializingRepresentative` field of the `RefundCreationRequest` object
	 * or the `actingRepresentative` field of the `RefundUpdateRequest` object does not match any user in Zaver
	 * for business. Ensure that a user in Zaver for business exists with the username specified.
	 */
	const USER_NOT_FOUND = 'USER_NOT_FOUND';

	/** 
	 * The username specified in the `initializingRepresentative` field of the `RefundCreationRequest` object
	 * or the `actingRepresentative` field of the `RefundUpdateRequest` object matches multiple users in Zaver
	 * for business. Ensure that only one user in Zaver for business matches the specified username.
	 */
	const MULTIPLE_USERS_FOUND = 'MULTIPLE_USERS_FOUND';

	/** 
	 * The supplied paymentId does not match any Zaver payment's id.
	 */
	const PAYMENT_NOT_FOUND = 'PAYMENT_NOT_FOUND';

	/**
	 * The supplied lineItemId does not match any line item connected to the payment being refunded.
	 */
	const LINE_ITEM_NOT_FOUND = 'LINE_ITEM_NOT_FOUND';

	/**
	 * Multiple occurances of a single unique lineItemId. Each lineItemId may not be refunded more than once in a single refund.
	 */
	const DUPLICATE_LINE_ITEM_ID = 'DUPLICATE_LINE_ITEM_ID';

	/** 
	 * The payment being refunded does not have the status `SETTLED`, and can therefore not be refunded.
	 */
	const PAYMENT_ILLEGAL_STATE = 'PAYMENT_ILLEGAL_STATE';

	/** 
	 * The requested refund amount exceeds the refundable amount of the payment.
	 */
	const AMOUNT_EXCEEDS_REFUNDABLE_AMOUNT = 'AMOUNT_EXCEEDS_REFUNDABLE_AMOUNT';

	/** 
	 * The requested refund amount is not allowed.
	 */
	const AMOUNT_INVALID = 'AMOUNT_INVALID';

	/**
	 * The bank account connected to Swish has insufficient funds to complete the refund. Please add funds to the bank account
	 * and try again by creating a new refund.
	 */
	const INSUFFICIENT_FUNDS_SWISH = 'INSUFFICIENT_FUNDS_SWISH';

	/**
	 * The payment can not be refunded using Zaver. Please use a different alternative to refund the payment.
	 */
	const REFUND_NOT_POSSIBLE = 'REFUND_NOT_POSSIBLE';
}