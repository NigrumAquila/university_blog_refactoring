<?php 
require_once(__DIR__ . '/helpers/Authorization.php');
checkAccessRedirect($RIGHTS['MODERATOR']);

if(isset($_POST["subject_name"]) && !empty($_POST["subject_name"]) &&
    isset($_POST["hours"]) && !empty($_POST["hours"])){
  $query_editSubject = sprintf("UPDATE subjects SET name = %s, hours = %d WHERE id = %s",
                       GetSQLValueString($_POST['subject_name'], "text"),
                       GetSQLValueString($_POST['hours'], "int"),
                       GetSQLValueString($_POST['subject_id'], "int"));
  mysqli_query($connection, $query_editSubject) or die($connection->error);
  $nextURL = makeURL('subjects');
  redirectTo($nextURL);
}

$query_subject = sprintf("SELECT * FROM subjects WHERE id = %s", addslashes($_GET['subject']));
$result_subject = mysqli_query($connection, $query_subject) or die($connection->error);
$subject_row = mysqli_fetch_assoc($result_subject);
freeResult($result_subject); 
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Редактирование предмета</title>
  <link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css">
</head>
<body>
  <h1>Редактирование предмета</h1>
  <form action="<?php echo getCurrentURL(); ?>" method="POST" autocomplete="off">
    <p>Предмет: 
      <input name="subject_name" type="text" class="border-bottom" value="<?php echo $subject_row['name'] ?>" size="50" maxlength="50">
    </p>
    <p>Часы: 
      <input name="hours" type="text" class="border-bottom" value="<?php echo $subject_row['hours'] ?>" size="50" maxlength="50">
    </p>
    <input name="subject_id" type="hidden" value="<?php echo $subject_row['id'] ?>">
    <p>
      <input type="submit" name="Submit" class="blueButton" value="Изменить">
      <input type="reset" name="Reset" class="redButton" value="Отмена">
    </p>
  </form>
  <p><a href="<?php echo makeURL('subjects'); ?>">На список предметов</a> </p>
<?php includeModule('footer'); ?>