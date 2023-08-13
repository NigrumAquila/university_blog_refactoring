<?php 
require_once(__DIR__ . '/helpers/Authorization.php');
checkAccessRedirect($RIGHTS['MODERATOR']);
if(!sessionKeyExist('prevURL')) setKeySession('prevURL', getPrevURL());

if (isset($_POST["group_id"]) && !empty($_POST["group_id"]) &&
    isset($_POST["number"]) && !empty($_POST["number"]) &&
    isset($_POST["surname"]) && !empty($_POST["surname"]) &&
    isset($_POST["name"]) && !empty($_POST["name"]) &&
    isset($_POST["patronymic"]) && !empty($_POST["patronymic"]) &&
    isset($_POST["gender"]) && !empty($_POST["gender"]) &&
    isset($_POST["birthday"]) && !empty($_POST["birthday"]) &&
    isset($_POST["stud_id"]) && !empty($_POST["stud_id"]) ){
  $query_editStudent = sprintf("UPDATE `students` SET `group_id` = %s, `number` = %s, `surname` = %s, `name` = %s, `patronymic` = %s, `gender` = %s, `birthday` = %s WHERE `id` = %s",
                       GetSQLValueString($_POST['group_id'], "int"),
                       GetSQLValueString($_POST['number'], "text"),
                       GetSQLValueString($_POST['surname'], "text"),
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['patronymic'], "text"),
                       GetSQLValueString($_POST['gender'], "text"),
                       GetSQLValueString($_POST['birthday'], "date"),
                       GetSQLValueString($_POST['stud_id'], "int"));
  mysqli_query($connection, $query_editStudent) or die($connection->error);
  $nextURL = prevURLcontains('students_search', true) ? makeURL('students_search') : makeURL('students', '', 'group', $_POST['group_id']);
  redirectTo($nextURL);
}

$query_student = sprintf("SELECT students.id, students.group_id, students.number, students.surname, students.name, students.patronymic, students.gender, students.birthday, groups.name as group_name 
FROM students, groups WHERE students.id = %s AND students.group_id = groups.id", addslashes($_GET['student']));
$result_student = mysqli_query($connection, $query_student) or die($connection->error);
$student_row = mysqli_fetch_assoc($result_student);
freeResult($result_student);

$query_groups = "SELECT * from groups";
$result_groups = mysqli_query($connection, $query_groups) or die($connection->error);
$group_row = mysqli_fetch_assoc($result_groups);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Редактирование студента</title>
  <link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css">
</head>
<body>
  <h1>Редактирование студента</h1>
  <form action="<?php echo getCurrentURL(); ?>" method="POST"  autocomplete="off">
    <p>Группа: 
      <select name="group_id" class="border-bottom">
<?php do { ?>
        <option value="<?php echo $group_row['id']; ?>" 
          <?php echo ($student_row['group_id'] == $group_row['id']) ? "selected" : ""; ?>>
          <?php echo $group_row['name']; ?>
        </option>
<?php } while ($group_row = mysqli_fetch_assoc($result_groups)); freeResult($result_groups); ?>
      </select>
    </p>
    <p>Номер зачетной книжки: 
      <input name="number" type="text" class="border-bottom" value="<?php echo $student_row['number']; ?>" size="15" maxlength="15">
    </p>
      <p>Фамилия: 
      <input name="surname" type="text" class="border-bottom" value="<?php echo $student_row['surname']; ?>" size="15" maxlength="15">
    </p>
      <p>Имя: 
      <input name="name" type="text" class="border-bottom" value="<?php echo $student_row['name']; ?>" size="15" maxlength="15">
    </p>
      <p>Отчество: 
      <input name="patronymic" type="text" class="border-bottom" value="<?php echo $student_row['patronymic']; ?>" size="20" maxlength="20">
    </p>
    <p>Пол: 
      <input name="gender" type="radio" value="м" size="20" maxlength="20" <?php echo ($student_row['gender'] == 'м') ?  "checked" : "" ;  ?>>
      <label for="gender">мужской</label>
      <input name="gender" type="radio" value="ж" size="20" maxlength="20" <?php echo ($student_row['gender'] == 'ж') ?  "checked" : "" ;  ?>>
      <label for="gender">женский</label>
    </p>
    <p>День рождения: 
      <input name="birthday" type="date" class="border-bottom" value="<?php echo $student_row['birthday']; ?>" size="20" maxlength="20">
    </p>
    <p>
      <input name="stud_id" type="hidden" value="<?php echo $student_row['id']; ?>">
    </p>
    <p>
      <input type="submit" name="Submit" value="Ввод" class="blueButton">
      <input type="reset" name="Reset" value="Отмена" class="redButton"> 
    </p>
  </form>
  <p>
<?php if(prevURLcontains('students_search', true)) { ?>
    <a href="<?php echo makeURL('students_search'); ?>">На список студентов группы</a>
<?php } else { ?>
    <a href="<?php echo makeURL('students', '', 'group', $student_row['group_id']); ?>">
    На список студентов группы <?php echo $student_row['group_name']; ?></a>
<?php } ?>
  </p>
<?php includeModule('footer'); ?>