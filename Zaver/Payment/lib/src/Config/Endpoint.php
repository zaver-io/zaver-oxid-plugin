<?php
namespace Zaver\SDK\Config;

class Endpoint {
	/** API endpoint to production environment */
	const PRODUCTION = 'https://api.zaver.se';

	/** URL to script in production environment */
	const PRODUCTION_SCRIPT = 'https://iframe-checkout.zaver.se/loader/v1.js';

	/** API endpoint to test environment */
	const TEST = 'https://api.test.zaver.se';

	/** URL to script in test environment */
	const TEST_SCRIPT = 'https://iframe-checkout.test.zaver.se/loader/v1.js';
}