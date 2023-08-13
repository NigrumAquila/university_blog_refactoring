<?php 
require_once(__DIR__ . '/../helpers/Authorization.php');
checkAccessRedirect($RIGHTS['MODERATOR']);

if(isset($_POST["group_name"]) && !empty($_POST["group_name"])) {
  $query_editGroup = sprintf("UPDATE `groups` SET `name`=%s WHERE `id`=%s",
                       GetSQLValueString($_POST['group_name'], "text"),
                       GetSQLValueString($_POST['group_id'], "int"));
  mysqli_query($connection, $query_editGroup) or die($connection->error);
  $nextURL = makeURL('groups');
  redirectTo($nextURL);
}

$query_group = sprintf("SELECT id, name FROM groups WHERE id = %s", addslashes($_GET['group']));
$result_group = mysqli_query($connection, $query_group) or die($connection->error);
$group_row = mysqli_fetch_assoc($result_group);
freeResult($result_group); 
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Редактирование группы</title>
  <link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css">
</head>
<body>
  <h1>Редактирование группы</h1>
  <form action="<?php echo getCurrentURL(); ?>" method="POST" autocomplete="off">
    <p>Наименование: 
      <input name="group_name" type="text" class="border-bottom" value="<?php echo $group_row['name'] ?>" size="50" maxlength="50">
    </p>
    <input name="group_id" type="hidden" value="<?php echo $group_row['id'] ?>">
    <p>
      <input type="submit" name="Submit" class="blueButton" value="Изменить">
      <input type="reset" name="Reset" class="redButton" value="Отмена">
    </p>
  </form>
  <p><a href="<?php echo makeURL('groups'); ?>">На список групп</a> </p>
<?php includeModule('footer'); ?>