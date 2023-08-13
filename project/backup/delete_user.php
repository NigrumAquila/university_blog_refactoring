<?php 
require_once(__DIR__ . '/helpers/Authorization.php');
checkAccessRedirect($RIGHTS['ADMIN']);

if(isset($_POST['user_id']) && !empty($_POST['user_id'])) {
  $query_deleteUser = sprintf("DELETE FROM users WHERE users.id = %s",
                       GetSQLValueString($_POST['user_id'], "int"));
  mysqli_query($connection, $query_deleteUser) or die($connection->error);
  $nextURL = makeURL('users');
  redirectTo($nextURL);
}

$query_user = sprintf("SELECT * FROM users WHERE users.id = %s", $_GET['user']);
$result_user = mysqli_query($connection, $query_user) or die($connection->error);
$user_row = mysqli_fetch_assoc($result_user);
freeResult($result_user);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Удаление пользователя</title>
  <link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css">
</head>
<body>
  <h1>Удаление пользователя</h1>
  <p>Страничка администратора, предназначенная для удаления пользователя. </p>
  <form action="<?php echo getCurrentURL(); ?>" method="POST">
    <h3>Имя: <?php echo $user_row['name'] ?></h3>
    <h3>Права: <?php echo $user_row['rights'] ?></h3>
    <p>
      <input type="hidden" name="user_id" value="<?php echo $user_row['id']; ?>">
      <input type="submit" name="Submit" value="Удалить" class="redButton">
    </p>
  </form>
  <p><a href="<?php echo makeURL('users'); ?>">На список пользователей</a></p>
<?php includeModule('footer'); ?>