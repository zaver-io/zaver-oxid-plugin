<?php
namespace Zaver\SDK\Object;
use Zaver\SDK\Utils\DataObject;

/**
 * A representative (an employee) of the merchant.
 * 
 * @method string getUsername() The username of the representative in Zaver for Business. This is always an e-mail address. E.g. `sara@example.com`.
 */
class MerchantRepresentative extends DataObject {
	/**
	 * The username of the representative in Zaver for Business. This is always an e-mail address. E.g. `sara@example.com`
	 */
	public function setUsername(string $username): self {
		$this->data['username'] = $username;

		return $this;
	}

	public function __toString(): string {
		return $this->data['username'] ?? '';
	}
}