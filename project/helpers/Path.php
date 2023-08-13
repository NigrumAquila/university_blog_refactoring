<?php
require_once(__DIR__ . '/../consts/paths.php');

function getPathToModule(string $module): string {
    global $PATHS;

    return $PATHS[$module];
}

function includeModule(string $module): void {
    require_once(__DIR__ . getPathToModule($module));
}

?>