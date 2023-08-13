<?php
require_once(__DIR__ . '/../consts/URL.php');
require_once(__DIR__ . '/URL.php');

function redirectTo(string $URL): void {
    header("Location: " . $URL);
}

function redirectWithParams(string $URL, $GETparams = '', $GETvalues = ''): void {
    redirectTo(addURLParams($URL, $GETparams, $GETvalues));
}

function redirectHome(): void {
    global $URL_INDEX;
    
    redirectTo($URL_INDEX);
}

function redirectBack(): void {
    if(isset($_SERVER['HTTP_REFERER'])) {
        redirectTo(normalizeURL($_SERVER['HTTP_REFERER']));
        return;
    }
    redirectHome();
}
?>