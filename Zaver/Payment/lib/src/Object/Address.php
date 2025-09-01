<?php
namespace Zaver\SDK\Object;
use Zaver\SDK\Utils\DataObject;

/**
 * An object containing address information.
 * 
 * @method string getName			The name of the person or company.
 * @method string getAddressLine1	First line of address, generally street name, e.g. "Sveavägen 59". For market DE streetNameand houseNumber must be used instead.
 * @method string getAddressLine2	Second line of address, if needed. City and postal code should go into their respective fields.
 * @method string getAddressLine3	Third line of address, if needed.
 * @method string getCareOf			The care of (C/O) part of the address, where needed.
 * @method string getStreetName		The name of the street, without the house number. Required for market DE.
 * @method string getHouseNumber	The number part after the street name. Required for market DE.
 * @method string getCity			The city part of the address.
 * @method string getRegion			The region part of the address, where needed.
 * @method string getPostalCode		The postal code (zip) of the address
 * @method string getCountry		The country for this address in ISO 3166-1 alpha-2 (two-letter codes) format. E.g. SE
 */
class Address extends DataObject {

	/**
	 * The name of the person or company.
	 */
	public function setName(string $name): self {
		$this->data['name'] = $name;

		return $this;
	}

	/**
	 * First line of address, generally street name, e.g. "Sveavägen 59". For market DE streetNameand houseNumber must be used instead.
	 */
	public function setAddressLine1(string $line): self {
		$this->data['addressLine1'] = $line;

		return $this;
	}

	/**
	 * Second line of address, if needed. City and postal code should go into their respective fields.
	 */
	public function setAddressLine2(string $line): self {
		$this->data['addressLine2'] = $line;

		return $this;
	}

	/**
	 * Third line of address, if needed.
	 */
	public function setAddressLine3(string $line): self {
		$this->data['addressLine3'] = $line;

		return $this;
	}

	/**
	 * The care of (C/O) part of the address, where needed.
	 */
	public function setCareOf(string $careOf): self {
		$this->data['careOf'] = $careOf;

		return $this;
	}

	/**
	 * The name of the street, without the house number. Required for market DE.
	 */
	public function setStreetName(string $streetName): self {
		$this->data['streetName'] = $streetName;

		return $this;
	}

	/**
	 * The number part after the street name. Required for market DE.
	 */
	public function setHouseNumber(string $houseNumber): self {
		$this->data['houseNumber'] = $houseNumber;

		return $this;
	}

	/**
	 * The city part of the address.
	 */
	public function setCity(string $city): self {
		$this->data['city'] = $city;

		return $this;
	}

	/**
	 * The region part of the address, where needed.
	 */
	public function setRegion(string $region): self {
		$this->data['region'] = $region;

		return $this;
	}

	/**
	 * The postal code (zip) of the address
	 */
	public function setPostalCode(string $postalCode): self {
		$this->data['postalCode'] = $postalCode;

		return $this;
	}

	/**
	 * The country for this address in ISO 3166-1 alpha-2 (two-letter codes) format. E.g. SE
	 */
	public function setCountry(string $country): self {
		$this->data['country'] = $country;

		return $this;
	}
}