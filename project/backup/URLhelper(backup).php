<?php
require_once(__DIR__ . '/../consts/URL.php');
require_once(__DIR__ . '/String.php');

function makeURL(string $page, string $form = 'view', $GETparams = '', $GETvalues = ''): string {
    global $constsViewURL, $constsDeleteFormURL, $constsInsertFormURL, $constsUpdateFormURL;
    $URL = '';
    switch($form) {
        case 'delete': $URL = $constsDeleteFormURL[$page]; break;
        case 'update': $URL = $constsUpdateFormURL[$page]; break;
        case 'insert': $URL = $constsInsertFormURL[$page]; break;
        case 'view': $URL = $constsViewURL[$page]; break;
        default: $URL = $constsViewURL[$page];
    }
    return addURLParams($URL, $GETparams, $GETvalues);
}

function addURLParams(string $URL, $GETparams = '', $GETvalues = ''): string {
    if(!empty($GETparams) AND !empty($GETvalues)) {
        if(is_array($GETparams) AND is_array($GETvalues) AND count($GETparams) == count($GETvalues)) {
            $URL = $URL . '?' . $GETparams[0] . '=' . $GETvalues[0];
            if(count($GETparams) > 1) {
                for($i = 1; $i < count($GETparams); $i++) {
                    $URL = $URL . "&" . $GETparams[$i] . '=' . $GETvalues[$i];
                }
                return $URL;
            }
            return $URL;
        }
        return $URL . '?' . $GETparams . '=' . $GETvalues;
    }
    return $URL;
}

function normalizeURL(string $URL): string {
    if(strlen($URL) == 1 && $URL == '/') return $URL;
    if(strStartsWith($URL, ['http://', 'https://'])) $URL = substr($URL, strpos(substr($URL, 7), '/') + 1 + 7);
    elseif(strStartsWith($URL, '/')) $URL = substr($URL, 1);
    $URLwithoutArgs = substr($URL, 0, strpos($URL, '?'));
    if(strEndsWith($URLwithoutArgs, '.php')) $URL = substr($URLwithoutArgs, 0, -4) . substr($URL, strlen($URLwithoutArgs));
    if(strEndsWith($URL, '.php')) $URL = substr($URL, 0, -4);
    return $URL;
}

function getCurrentURL(): string {
    return normalizeURL($_SERVER['REQUEST_URI']);
}

function getPrevURL(): string {
    return normalizeURL($_SERVER['HTTP_REFERER']);
}

function prevURLcontains(string $entry, $checkSession = false): bool {
    if($checkSession == true && isset($_SESSION['prevURL'])) $prevURL = normalizeURL($_SESSION['prevURL']);
    elseif(isset($_SERVER['HTTP_REFERER'])) $prevURL = normalizeURL($_SERVER['HTTP_REFERER']);
    return strContains($prevURL, $entry);
}

function URLcontains(string $URL, string $entry): bool { 
    return strContains(normalizeURL($URL), $entry);
}

?>