<?php 
require_once(__DIR__ . '/../helpers/Authorization.php');
if(userIsLoggedIn()) redirectBack();

if(isset($_POST['username']) && !empty($_POST['username'] && 
   isset($_POST['password']) && !empty($_POST['password']))) login($_POST['username'], $_POST['password']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Вход</title>
	<link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css">
	<script src="<?php echo getPathToModule('js'); ?>"></script>
</head>
<body>
	<h2 align="center">Вход на сайт "Обучение студентов УТиИТ"</h2>
	<p>Авторизация посетителя:</p>
<?php if(!sessionKeyExist('userExist') && isset($_SESSION['userExist'])) { ?>
	<h2 class="text-left_red">Неверный пароль, попробуйте ещё раз!</h2>
<?php } ?>	
	<form action="<?php echo getCurrentURL(); ?>" method="POST" autocomplete="off">
		<p>Имя:
			<input type="text" name="username" class="border-bottom" size="20" maxlength="20">
		</p>
		<div>
			<p>Пароль:
				<input name="password" type="password" class="border-bottom" size="20">
				<svg onclick="show_hide_password()" class="password_eye" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20">
				<image id="svg_password" width="20" height="20" xlink:href="/svg/eye_show.svg"/>
				</svg>
			</p>
		</div>
		<p>
			<input type="submit" name="Submit" class="blueButton" value="Войти">
			<input type="reset" name="Reset" class="redButton" value="Отмена">
		</p>
	</form>
<?php includeModule('footer'); ?>