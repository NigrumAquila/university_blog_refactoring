<?php 
require_once(__DIR__ . '/../helpers/Authorization.php');
checkAccessRedirect($RIGHTS['MODERATOR']);

if(isset($_POST["name"]) && !empty($_POST["name"]) &&
    isset($_POST["password"]) && !empty($_POST["password"]) &&
    isset($_POST["rights"]) && !empty($_POST["rights"]) &&
    isset($_POST["user_id"]) && !empty($_POST["user_id"])) {
  $query_editUser = sprintf("UPDATE users SET name = %s, password = %s, rights = %s WHERE users.id = %s",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['rights'], "text"),
                       GetSQLValueString($_POST['user_id'], "int"));
  mysqli_query($connection, $query_editUser) or die($connection->error);
  $nextURL = makeURL('users');
  redirectTo($nextURL);
}

$query_rights = "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA='students-date' 
    AND TABLE_NAME='users'
    AND COLUMN_NAME='rights'";
$result_rights = mysqli_query($connection, $query_rights) or die($connection->error);
$rights_set = substr(mysqli_fetch_row($result_rights)[0],3);
preg_match_all('/\w/', $rights_set, $matches);
freeResult($result_rights);

$query_user = sprintf("SELECT * FROM users WHERE id = %s", $_GET['user']);
$result_user = mysqli_query($connection, $query_user) or die($connection->error);
$user_row = mysqli_fetch_assoc($result_user);
freeResult($result_user); 
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Редактирование пользователя</title>
  <link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css">
</head>
<body>
  <h1>Редактирование пользователя</h1>
  <p>Страничка администратора, предназначенная для редактирования данных о пользователе. </p>
  <form action="<?php echo getCurrentURL(); ?>" method="POST" autocomplete="off">
    <p>Имя:
      <input name="name" type="text" class="border-bottom" size="20" value="<?php echo $user_row['name'] ?>">
    </p>
    <p>Пароль:
      <input name="password" type="text" class="border-bottom" size="20" value="<?php echo $user_row['password'] ?>">
    </p>
    <p>Права:
      <select name="rights" class="border-bottom">
<?php foreach($matches[0] as $rights) { ?>
        <option value="<?php echo $rights ?>" <?php if($rights == $user_row['rights']) echo "selected"; ?>><?php echo $rights ?> </option>
<?php } ?>
      </select>
      <input type="hidden" name="user_id" value="<?php echo $user_row['id']; ?>">
    </p>
    <p>
      <input type="submit" name="Submit" class="blueButton" value="Добавить">
      <input type="reset" name="Reset" class="redButton" value="Отмена">
    </p>
  </form>
  <p>
    <a href="<?php echo makeURL('users'); ?>">На список пользователей</a>
<?php includeModule('footer'); ?>