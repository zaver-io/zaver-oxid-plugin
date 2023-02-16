<?php
namespace Zaver\SDK\Object;

use DateTime;
use Zaver\SDK\Utils\DataObject;

/**
 * The payer specific data that is used for verifying the payer on a `PaymentCreationRequest`
 * 
 * @method string getEmail				The email of the payer in question.
 * @method string getPhoneNumber		The phone number of the payer in question. Supplied with the country code followed by digits. Swedish example: country code +46, phone number 701740605 = +46701740605
 * @method string getGivenName			The given name(often the first name) of the payer in question.
 * @method string getFamilyName			The family name(often the last name) of the payer in question.
 * @method string getHonoraryPrefix		The honorary prefix of the payer in question. Example: Herr / Frau
 */
class PayerData extends DataObject {

	/**
	 * The email of the payer in question.
	 */
	public function setEmail(string $email): self {
		$this->data['email'] = $email;

		return $this;
	}

	/**
	 * The phone number of the payer in question. Supplied with the country code followed by digits. Swedish example: country code +46, phone number 701740605 = +46701740605
	 */
    public function setPhoneNumber(string $phoneNumber): self {
		$this->data['phoneNumber'] = $phoneNumber;

		return $this;
	}

	/**
	 * The given name(often the first name) of the payer in question.
	 */
    public function setGivenName(string $givenName): self {
		$this->data['givenName'] = $givenName;

		return $this;
	}

	/**
	 * The family name(often the last name) of the payer in question.
	 */
    public function setFamilyName(string $familyName): self {
		$this->data['familyName'] = $familyName;

		return $this;
	}

	/**
	 * The honorary prefix of the payer in question. Example: Herr / Frau
	 */
	public function setHonoraryPrefix(string $prefix): self {
		$this->data['honoraryPrefix'] = $prefix;

		return $this;
	}

	/**
	 * Date of birth of the payer in question. Given in ISO 8601 format. Example: 1994-07-14
	 */
    public function setDateOfBirth(DateTime $dateOfBirth): self {
		$this->data['dateOfBirth'] = $dateOfBirth->format('Y-m-d');

		return $this;
	}

	/**
	 * The billing address of the payer in question.
	 */
    public function setBillingAddress(Address $address): self {
		$this->data['billingAddress'] = $address;

		return $this;
	}
    
	/**
	 * The shipping address of the payer in question.
	 */
    public function setShippingAddress(Address $address): self {
		$this->data['shippingAddress'] = $address;

		return $this;
	}

	/**
	 * Date of birth of the payer in question.
	 */
    public function getDateOfBirth(): ?DateTime {
		$dob = $this->data['dateOfBirth'] ?? '';

		if (preg_match('/^\d{4}-\d{2}-\d{2}/', $dob) !== false) {
			return DateTime::createFromFormat('Y-m-d', $dob);
		}

		return null;
	}

	/**
	 * The billing address of the payer in question.
	 */
    public function getBillingAddress(): Address {
		return Address::create($this->data['billingAddress'] ?? []);
	}
    
	/**
	 * The shipping address of the payer in question.
	 */
    public function getShippingAddress(): Address {
		return Address::create($this->data['shippingAddress'] ?? []);
	}
}