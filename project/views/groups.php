<?php 
require_once(__DIR__ . '/../helpers/Authorization.php');

if (userHasRights($RIGHTS['MODERATOR']) &&
    isset($_POST["group_name"]) && !empty($_POST["group_name"])) {
  $query_insertGroup = sprintf("INSERT INTO groups (name) VALUES (%s)",
                       GetSQLValueString($_POST['group_name'], "text"));
  mysqli_query($connection, $query_insertGroup) or die($connection->error);
  $nextURL = makeURL('groups');
  redirectTo($nextURL);
}

$query_groups = "SELECT * FROM groups ORDER BY groups.name ASC";
$result_groups = mysqli_query($connection, $query_groups) or die($connection->error);
$group_row = mysqli_fetch_assoc($result_groups);
$groups_totalRows = mysqli_num_rows($result_groups);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Группы УТиИТ</title>
  <link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css">
</head>
<body>
  <h1>Группы студентов УТиИТ</h1>
  <p>&nbsp;</p>
  <hr>
  <p align="center">&nbsp;</p>
<?php if (rowExist($result_groups)) { do { ?>
  <div class="fullnote">
    <h2>
      <a href="<?php echo makeURL('students', 'view', 'group', $group_row['id']);?>"><?php echo $group_row['name']; ?></a> 
    </h2>
 <?php if(userHasRights($RIGHTS['MODERATOR'])) { ?>
    <p><a href="<?php echo makeURL('group', 'update', 'group', $group_row['id']); ?>">Изменить</a></p>
    <p><a href="<?php echo makeURL('group', 'delete', 'group', $group_row['id']); ?>">Удалить</a></p>
 <?php } ?>
  </div>
<?php } while ($group_row = mysqli_fetch_assoc($result_groups)); freeResult($result_groups); ?>
  <p>Группы с 1 по <?php echo $groups_totalRows ?> </p>
<?php } else { ?>
  <h3>Групп пока нет! </h3>
<?php } if(userHasRights($RIGHTS['MODERATOR'])) { ?>
  <p>&nbsp;</p>
  <form action="<?php echo getCurrentURL(); ?>" method="POST" autocomplete="off">
    <p>Группа: 
      <input name="group_name" type="text" class="border-bottom" size="50" maxlength="50">
    </p>
    <p>
      <input type="submit" name="Submit" class="blueButton" value="Добавить">
      <input type="reset" name="Reset" class="redButton" value="Отмена">
    </p>
  </form>
<?php } includeModule('footer'); ?>