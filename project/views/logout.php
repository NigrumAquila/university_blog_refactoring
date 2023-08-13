<?php
require_once(__DIR__ . '/../helpers/Authorization.php');
if(!userIsLoggedIn()) redirectBack(); 

if(isset($_GET['doLogout']) && $_GET['doLogout'] == "true") clearSessionRedirectHome()
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Выход</title>
	<link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css">
</head>
<body>
	<h2 align="center">Завершение сеанса работы посетителя с сайтом "Обучение студентов УТиИТ"</h2>
	<p><a href="<?php echo makeURL('logout', '', 'doLogout', 'true'); ?>">Выйти</a></p>
<?php includeModule('footer'); ?>