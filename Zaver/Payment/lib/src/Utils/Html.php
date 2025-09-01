<?php
namespace Zaver\SDK\Utils;

class Html {
	static public function getTag(string $tag, bool $selfClosing = false, array $attributes = []): string {
		return sprintf($selfClosing ? '<%s %s />' : '<%1$s %2$s></%1$s>', self::sanitize($tag), self::formatAttributes($attributes));
	}

	static private function formatAttributes(array $attributes): string {
		$formatted = [];

		foreach($attributes as $name => $value) {
			if(!is_scalar($value)) continue;
			
			$formatted[] = sprintf('%s="%s"', self::sanitize($name), addslashes($value));
		}

		return implode(' ', $formatted);
	}

	static private function sanitize(string $name): string {

		// Convert camelCase to dashed-case
		$name = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $name));

		return preg_replace('/[^a-z0-9-]+/', '', $name);
	}
}