<?php 
require_once(__DIR__ . '/../helpers/Authorization.php');
checkAccessRedirect($RIGHTS['MODERATOR']);

if(isset($_POST['group_id']) && !empty($_POST['group_id'])) {
  $query_deleteMarks = sprintf("DELETE academic_performance FROM academic_performance JOIN groups_subjects WHERE academic_performance.group_subject_id = groups_subjects.id AND groups_subjects.group_id = %s", GetSQLValueString($_POST['group_id'], "int"));
  $query_deleteGroupSubject = sprintf("DELETE FROM groups_subjects WHERE groups_subjects.group_id = %s", GetSQLValueString($_POST['group_id'], "int"));
  $query_deleteStudents = sprintf("DELETE FROM students WHERE group_id = %s", GetSQLValueString($_POST['group_id'], "int"));
  $query_deleteGroup = sprintf("DELETE FROM groups WHERE id = %s", GetSQLValueString($_POST['group_id'], "int"));
  mysqli_query($connection, "LOCK TABLES academic_performance WRITE, groups_subjects WRITE, students WRITE, groups WRITE") or die($connection->error);
  mysqli_query($connection, $query_deleteMarks) or die($connection->error);
  mysqli_query($connection, $query_deleteGroupSubject) or die($connection->error);
  mysqli_query($connection, $query_deleteStudents) or die($connection->error);
  mysqli_query($connection, $query_deleteGroup) or die($connection->error);
  mysqli_query($connection, "UNLOCK TABLES") or die($connection->error);
  $nextURL = makeURL('groups');
  redirectTo($nextURL);
}

$query_group = sprintf("SELECT groups.id as group_id, groups.name as group_name, students.number as student_number, students.surname as student_surname, 
      students.name as student_name, students.patronymic as student_patronymic, students.gender as student_gender, 
      students.birthday as student_birthday FROM groups JOIN students ON students.group_id = groups.id WHERE groups.id = %s
      ORDER BY students.surname, students.name, students.patronymic", addslashes($_GET['group']));
$result_group = mysqli_query($connection, $query_group) or die($connection->error);
$group_row = mysqli_fetch_assoc($result_group);

$query_checkGroupSubject = sprintf("SELECT id FROM groups_subjects WHERE group_id = %s", GetSQLValueString($_GET['group'], "int"));
$result_checkGroupSubject = mysqli_query($connection, $query_checkGroupSubject) or die($connection->error);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Удаление группы со студентами</title>
  <link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css">
</head>
<body>
	<h1>Удаление группы со студентами</h1>
	<form action="<?php echo getCurrentURL(); ?>" method="post">
		<h3>Наименование группы: <?php echo $group_row['group_name']; ?></h3>
    <table width="100%"  border="1" cellspacing="2" cellpadding="1">
      <tr>
        <th width="1%" scope="col">Номер</th>
        <th width="10%" scope="col">Зачетная книжка</th>
        <th width="22%" scope="col">Фамилия </th>
        <th width="15%" scope="col">Имя</th>
        <th width="24%" scope="col">Отчество</th>
        <th width="5%" scope="col">Пол</th>
        <th width="15%" scope="col">День рождения</th>
      </tr>
<?php $i=1; do { ?>
      <tr>
        <td class="center"><?php echo $i; ?></td>
        <td class="center"><?php echo $group_row['student_number']; ?></td>
        <td class="center"><?php echo $group_row['student_surname']; ?></td>
        <td class="center"><?php echo $group_row['student_name']; ?></td>
        <td class="center"><?php echo $group_row['student_patronymic']; ?></td>
        <td class="center"><?php echo $group_row['student_gender']; ?></td>
        <td class="center"><?php echo $group_row['student_birthday']; ?></td>
      </tr>
<?php $i++; } while ($group_row = mysqli_fetch_assoc($result_group)); freeResult($result_group); ?> 
    </table>
<?php if(rowExist($result_checkGroupSubject)) { ?>
    <h3 class="note">Предупреждение: при удалении группы будут удалены связанные с ней студенты, ведомости и оценки</h3>
<?php } ?>
    <p>
      <input type="hidden" name="group_id" value="<?php echo $_GET['group']; ?>">
			<input type="submit" name="Submit" value="Удалить" class="redButton">
    </p>
	</form>
  <p><a href="<?php echo makeURL('groups'); ?>">На список групп</a> </p>
<?php includeModule('footer'); ?>