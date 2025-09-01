<?php
namespace Zaver\SDK\Utils;

class Helper {
	static public function getAuthorizationKey(): ?string {
		if(isset($_SERVER['HTTP_AUTHORIZATION'])) {
			$auth = explode(' ', $_SERVER['HTTP_AUTHORIZATION']);

			return end($auth);
		}
		elseif(isset($_SERVER['HTTP_X_CALLBACK_AUTHORIZATION'])) {
			$auth = explode(' ', $_SERVER['HTTP_X_CALLBACK_AUTHORIZATION']);

			return end($auth);
		}
		elseif(function_exists('getallheaders')) {
			foreach(getallheaders() as $key => $value) {
				if(strtolower($key) === 'authorization') {
					$auth = explode(' ', $value);

					return end($auth);
				}
			}
		}

		return null;
	}
}