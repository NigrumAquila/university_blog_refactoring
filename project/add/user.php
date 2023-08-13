<?php 
require_once(__DIR__ . '/../helpers/Authorization.php');
checkAccessRedirect($RIGHTS['ADMIN']);

if(isset($_POST["name"]) && !empty($_POST["name"]) &&
    isset($_POST["password"]) && !empty($_POST["password"]) &&
    isset($_POST["rights"]) && !empty($_POST["rights"])) {
  if(checkUserExist($_POST["name"]))  redirectTo(getCurrentURL());
  else { 
    $query_insertUser = sprintf("INSERT INTO users (name, password, rights) VALUES (%s, %s, %s)",
            GetSQLValueString($_POST['name'], "text"),
            GetSQLValueString($_POST['password'], "text"),
            GetSQLValueString($_POST['rights'], "text"));
    mysqli_query($connection, $query_insertUser) or die($connection->error);
    $nextURL = makeURL('users');
    redirectTo($nextURL);
  }
}

$query_rights = "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA='students-date' 
    AND TABLE_NAME='users'
    AND COLUMN_NAME='rights'";
$rights = mysqli_query($connection, $query_rights) or die($connection->error);
$set_rights = substr(mysqli_fetch_row($rights)[0],3);
preg_match_all('/\w/', $set_rights, $matches);
freeResult($rights);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Добавление пользователя</title>
  <link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css">
  <script src="<?php echo getPathToModule('js'); ?>"></script>
</head>
<body>
  <h1>Добавление пользователя</h1>
  <p>Страничка администратора, предназначенная для добавления нового пользователя. </p>
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
          <image id="svg_password" width="20" height="20" xlink:href="/svg/eye_show.svg"/>
        </svg>
      </p>
    </div>
    <p>Права:
      <select name="rights" class="border-bottom">
<?php foreach($matches[0] as $rights) { ?>
        <option value="<?php echo $rights ?>"><?php echo $rights ?></option>
<?php } ?>
      </select>
    </p>
    <p>
      <input type="submit" name="Submit" class="blueButton" value="Добавить">
      <input type="reset" name="Reset" class="redButton" value="Отмена">
    </p>
  </form>
  <p>
<?php includeModule('footer'); ?>