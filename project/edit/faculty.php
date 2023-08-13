<?php 
require_once(__DIR__ . '/../helpers/Authorization.php');
checkAccessRedirect($RIGHTS['MODERATOR']);

if(isset($_POST["fac_abbrev"]) && !empty($_POST["fac_abbrev"]) &&
    isset($_POST["fac_name"]) && !empty($_POST["fac_name"]) &&
    isset($_POST["fac_id"]) && !empty($_POST["fac_id"])){
  $query_editFaculty = sprintf("UPDATE `faculties` SET `abbrev` = %s, `name` = %s WHERE `id` = %s",
                       GetSQLValueString($_POST['fac_abbrev'], "text"),
                       GetSQLValueString($_POST['fac_name'], "text"),
                       GetSQLValueString($_POST['fac_id'], "int"));
  mysqli_query($connection, $query_editFaculty) or die($connection->error);
  $nextURL = makeURL('faculties');
  redirectTo($nextURL);
}

$query_faculty = sprintf("SELECT faculties.id, faculties.abbrev, faculties.name FROM faculties WHERE faculties.id = %s", addslashes($_GET['faculty']));
$result_faculty = mysqli_query($connection, $query_faculty) or die($connection->error);
$faculty_row = mysqli_fetch_assoc($result_faculty);
freeResult($result_faculty);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Редактирование кафедры</title>
  <link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css">
</head>
<body>
  <h1>Редактирование кафедры</h1>
  <form action="<?php echo getCurrentURL(); ?>" method="POST" autocomplete="off">
    <p>Наименование: 
      <input name="fac_name" type="text" class="border-bottom" value="<?php echo $faculty_row['name'] ?>" size="50" maxlength="50">
    </p>
    <p>Аббревиатура: 
      <input name="fac_abbrev" type="text" class="border-bottom" value="<?php echo $faculty_row['abbrev'] ?>" size="10" maxlength="10">
      <input name="fac_id" type="hidden" value="<?php echo $faculty_row['id'] ?>">
    </p>
    <p>
      <input type="submit" name="Submit" class="blueButton" value="Изменить">
      <input type="reset" name="Reset" class="redButton" value="Отмена">
    </p>
  </form>
  <p><a href="<?php echo makeURL('faculties'); ?>">На список кафедр</a> </p>
<?php includeModule('footer'); ?>