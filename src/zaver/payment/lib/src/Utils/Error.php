<?php
namespace Zaver\SDK\Utils;
use Exception;
use Throwable;

class Error extends Exception {
	protected $errorCode = null;
	protected $docs = null;
	protected $errors = [];

	/**
	 * @param string|array $errorMessage
	 * @param string|null $errorCode
	 * @param string|null $docs
	 * @param Throwable|null $previous
	 */
	public function __construct($errorMessage, ?string $errorCode = null, ?string $docs = null, ?Throwable $previous = null) {
		if(is_array($errorMessage) && !empty($errorMessage)) {
			$error = $errorMessage[0];

			parent::__construct($error['errorMessage'] ?? '', 0, $previous);

			if(isset($error['errorCode'])) $this->errorCode = $error['errorCode'];
			if(isset($error['docs'])) $this->docs = $error['docs'];

			$this->errors = $errorMessage;
		}
		elseif(is_string($errorMessage)) {
			parent::__construct($errorMessage, 0, $previous);

			$this->errorCode = $errorCode;
			$this->docs = $docs;
			$this->errors = [
				[
					'errorCode' => $errorCode,
					'errorMessage' => $errorMessage,
					'docs' => $docs
				]
			];
		}
		else {
			throw new Error('Invalid error message');
		}
	}

	/**
	 * A link to relevant documentation.
	 */
	public function getDocs(): ?string {
		return $this->docs;
	}

	public function getErrorMessage(): string {
		return $this->getMessage();
	}
}