<?php
namespace Zaver\SDK\Utils;
use JsonSerializable;
use ArrayAccess;

abstract class DataObject implements JsonSerializable, ArrayAccess {
	protected $data = [];

	/**
	 * @return static
	 */
	static public function create(array $data = []): self {
		return new static($data);
	}

	public function __construct(array $data = []) {
		foreach($data as $key => $value) {
			if(is_null($value)) continue;

			$method = 'set' . ucfirst($key);

			if(method_exists($this, $method)) {
				$this->$method($value);
			}
			else {
				$this->data[$key] = $value;
			}
		}
	}

	public function __call($name, $arguments) {
		if(strncmp($name, 'get', 3) === 0) {
			$key = strtolower($name[3]) . substr($name, 4);

			return ($this->data[$key] ?? null);
		}
	}

	/**
	 * @ignore
	 */
	public function jsonSerialize() {
        return $this->data;
    }

	/**
	 * @ignore
	 */
	public function offsetExists($offset): bool {
		return isset($this->data[$offset]);
	}

	/**
	 * @ignore
	 */
	public function offsetGet($offset) {
		return $this->data[$offset] ?? null;
	}

	/**
	 * @ignore
	 */
	public function offsetSet($offset, $value): void {
		$method = 'set' . ucfirst($offset);

		if(method_exists($this, $method)) {
			$this->$method($value);
		}
		else {
			$this->data[$offset] = $value;
		}
	}

	/**
	 * @ignore
	 */
	public function offsetUnset($offset): void {
		unset($this->data[$offset]);
	}
}