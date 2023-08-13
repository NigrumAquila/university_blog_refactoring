<?php 
require_once(__DIR__ . '/helpers/Authorization.php');
if(userIsLoggedIn()) redirectBack();

if(isset($_POST["name"]) && !empty($_POST["name"]) &&
    isset($_POST["password"]) && !empty($_POST["password"])) {
  if(checkUserExist($_POST["name"]))  redirectTo(getCurrentURL());
  else { 
    $query_insertUser = sprintf("INSERT INTO users (name, password) VALUES (%s, %s)",
            GetSQLValueString($_POST['name'], "text"),
            GetSQLValueString($_POST['password'], "text"));
    mysqli_query($connection, $query_insertUser) or die($connection->error);
    login($_POST["name"], $_POST["password"]);
  }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Регистрация пользователя</title>
  <link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css">
  <script src="<?php echo getPathToModule('js'); ?>"></script>
</head>
<body>
  <h2>Регистрация на сайте "Обучение студентов УТиИТ"</h1>
  <p>Регистрация пользователя:</p>
<?php if(sessionKeyExist('userExist')) { ?>
  <h2 class=text-left_red>Такой пользователь уже существует!</h2>
<?php } ?>
  <form action="<?php echo getCurrentURL(); ?>" method="POST" autocomplete="off">
    <p>Имя:
      <input name="name" type="text" class="border-bottom" size="20">
    </p>
    <div>
      <p>Пароль:
        <input name="password" type="password" class="border-bottom" size="20">
        <svg onclick="show_hide_password()" class="password_eye" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20">
          <image id="svg_password" width="20" height="20" xlink:href="svg/eye_show.svg"/>
        </svg>
      </p>
    </div>
    <p>
      <input type="submit" name="Submit" class="blueButton" value="Регистрация">
      <input type="reset" name="Reset" class="redButton" value="Отмена">
    </p>
  </form>
  <p>
<?php includeModule('footer'); ?>