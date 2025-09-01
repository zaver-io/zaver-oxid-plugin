<?php
namespace Zaver\SDK\Config;

class ItemType {

	/** A physical product that needs to be shipped/retrieved. */
	const PHYSICAL = 'PHYSICAL';

	/** A virtual product that won't be shipped/retrieved in any way. */
	const DIGITAL = 'DIGITAL';

	/** A service to be done by a human. */
	const SERVICE = 'SERVICE';

	/** A shipping fee. */
	const SHIPPING = 'SHIPPING';

	/** A fee that isn't shipping. */
	const FEE = 'FEE';

	/** A discount or voucher. */
	const DISCOUNT = 'DISCOUNT';
}