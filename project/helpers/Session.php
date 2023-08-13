<?php 
require_once(__DIR__ . '/../consts/URL.php');

function startSessionIfnot(): void {
    if(!isset($_SESSION)) session_start();
}

function clearSession(): void {
	$keys = ['username', 'userRights', 'prevURL', 'userExist'];
	clearKeysSession($keys);
}

function clearSessionRedirectHome(): void {
	global $URL_INDEX;

    clearSession();
    redirectHome($URL_INDEX);
}

function clearKeySession(string $key): void {
	if(isset($_SESSION[$key])) unset($_SESSION[$key]);
}

function clearKeysSession(array $keys): void {
	if(is_array($keys)) {
		foreach($keys as $key) {
			clearKeySession($key);
		}
	}
}

function getAndClearKeySession($key, $defined = '') {
	if(isset($_SESSION[$key])) {
	  $key = $_SESSION[$key];
	  clearKeySession($key);
	  return $key;
	}
	return $defined;
}

function sessionKeyExist($key) {
	if(!isset($_SESSION[$key])) return;
	return $_SESSION[$key];
}

function setKeySession($key, $value): void {
	$_SESSION[$key] = $value;
}

function setKeysSession(array $keys, array $values): void {
	if(is_array($keys) && is_array($values) && count($keys) == count($values)) {
		for($i = 0; $i < count($keys); $i++) {
			setKeySession($keys[$i], $values[$i]);
		}
	}
}
?>