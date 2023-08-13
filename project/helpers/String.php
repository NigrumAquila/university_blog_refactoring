<?php

function strContains(string $haystack, $needle): bool {
	if ('' === $needle) return true;
	if(is_array($needle)) {
		foreach($needle as $item) {
			if(strpos($haystack, $item) !== false) return true;
		}
		return false;
	}
	return false !== strpos($haystack, $needle);
}

function strStartsWith(string $haystack, $needle): bool {
	if ('' === $needle) return true;
	if(is_array($needle)) {
		foreach($needle as $item) {
			if(0 === strpos($haystack, $item)) return true;
		}
		return false;
	}
	return 0 === strpos($haystack, $needle);
}

function strEndsWith(string $haystack, $needle ): bool {
	if ('' === $haystack && '' !== $needle) return false;
	if(is_array($needle)) {
		foreach($needle as $item) {
			$len = strlen($item);
			if(0 === substr_compare($haystack, $item, -$len, $len)) return true;
		}
		return false;
	}
	$len = strlen($needle);
	return 0 === substr_compare($haystack, $needle, -$len, $len);
}

?>