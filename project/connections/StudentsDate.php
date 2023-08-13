<?php
$db_hostname = "localhost";
$db_name = "students-date";
$db_username = "root";
$db_password = "";
$connection = mysqli_connect($db_hostname, $db_username, $db_password, $db_name) or 
trigger_error($connection->error,E_USER_ERROR); 
mysqli_query($connection, "SET NAMES utf8;") or die(debug_print_backtrace());
mysqli_query($connection, "SET CHARACTER SET utf8;") or die(debug_print_backtrace());
mysqli_query($connection, "SET SESSION collation_connection = 'utf8_general_ci';") or die(debug_print_backtrace()); 
?>
