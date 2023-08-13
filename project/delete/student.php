<?php 
require_once(__DIR__ . '/../helpers/Authorization.php');
checkAccessRedirect($RIGHTS['MODERATOR']);
if(!sessionKeyExist('prevURL')) setKeySession('prevURL', getPrevURL());

if(isset($_POST['student_id']) && !empty($_POST['student_id']) &&
  (isset($_POST['group_id']) && !empty($_POST['group_id']))) {
  $query_deleteMarks = sprintf("DELETE FROM academic_performance WHERE student_id = %s",
                        GetSQLValueString($_POST['student_id'], "int"));
  $query_deleteStudent = sprintf("DELETE FROM students WHERE id = %s",
                        GetSQLValueString($_POST['student_id'], "int"));
  mysqli_query($connection, "LOCK TABLES academic_performance WRITE, students WRITE") or die($connection->error);
  mysqli_query($connection, $query_deleteMarks) or die($connection->error);
  mysqli_query($connection, $query_deleteStudent) or die($connection->error);
  mysqli_query($connection, "UNLOCK TABLES") or die($connection->error);
  $nextURL = prevURLcontains('students_search', true) ? makeURL('students_search') : makeURL('students', '', 'group', $_POST['group_id']);
  redirectTo($nextURL);
}

$query_student= sprintf("SELECT students.id, students.group_id, students.number, students.surname, students.name, 
students.patronymic, students.gender, students.birthday, groups.name as group_name
FROM students, groups WHERE students.id = %s AND students.group_id = groups.id", addslashes($_GET['student']));
$result_student = mysqli_query($connection, $query_student) or die($connection->error);
$student_row = mysqli_fetch_assoc($result_student);
freeResult($result_student);

$query_checkGroupSubject = sprintf("SELECT id FROM academic_performance WHERE student_id = %s", GetSQLValueString($_GET['student'], "int"));
$result_checkGroupSubject = mysqli_query($connection, $query_checkGroupSubject) or die($connection->error);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Удаление студента</title>
  <link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css">
</head>
<body>
  <h1>Удаление студента</h1>
  <form action="<?php echo getCurrentURL(); ?>" method="POST">
    <h3>Группа: <?php echo $student_row['group_name']; ?></h3>
    <h3>Номер зачетной книжки: <?php echo $student_row['number']; ?></h3>
    <h3>Фамилия: <?php echo $student_row['surname']; ?></h3>
    <h3>Имя: <?php echo $student_row['name']; ?></h3>
    <h3>Отчество: <?php echo $student_row['patronymic']; ?></h3>
    <h3>Пол: <?php echo $student_row['gender']; ?></h3>
    <h3>День рождения: <?php echo $student_row['birthday']; ?></h3>
    <p>
      <input name="student_id" type="hidden" value="<?php echo $student_row['id']; ?>">
      <input name="group_id" type="hidden" value="<?php echo $student_row['group_id']; ?>">
    </p>
<?php if(rowExist($result_checkGroupSubject)) { ?>
    <h3 class="note">Предупреждение: при удалении студента будут удалены связанные с ним оценки</h3>
<?php } ?>
    <p>
      <input type="submit" name="Submit" value="Удалить" class="redButton">
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