<?php 
require_once(__DIR__ . '/../helpers/Authorization.php'); 
checkAccessRedirect($RIGHTS['MODERATOR']);

if(isset($_POST['group_id']) && !empty($_POST['group_id'])) {
  $query_deleteGroup = sprintf("DELETE FROM groups WHERE id = %s", GetSQLValueString($_POST['group_id'], "int"));
  $query_deleteGroupSubject = sprintf("DELETE FROM groups_subjects WHERE groups_subjects.group_id = %s", GetSQLValueString($_POST['group_id'], "int"));
  $query_checkStudents = sprintf("SELECT id FROM students WHERE group_id = %s",
                       GetSQLValueString($_POST['group_id'], "int"));
  mysqli_query($connection, "LOCK TABLES students WRITE, groups WRITE, groups_subjects WRITE") or die($connection->error);
  $result_checkStudents = mysqli_query($connection, $query_checkStudents) or die($connection->error);
  if(!rowExist($result_checkStudents)) {
    mysqli_query($connection, $query_deleteGroupSubject) or die($connection->error);
    mysqli_query($connection, $query_deleteGroup) or die($connection->error);
  }
  mysqli_query($connection, "UNLOCK TABLES");
  $nextURL = rowExist($result_checkStudents) ? makeURL('know_group', '', 'group', $_GET['group']) : makeURL('groups');
  freeResult($result_checkStudents);
  redirectTo($nextURL);
}

$query_group = sprintf("SELECT * FROM groups WHERE id = %s", addslashes($_GET['group']));
$result_group = mysqli_query($connection, $query_group) or die($connection->error);
$group_row = mysqli_fetch_assoc($result_group);


$query_academicPerformances = sprintf("SELECT lecturers.surname as lecturer_surname, lecturers.name as lecturer_name, lecturers.patronymic as lecturer_patronymic, subjects.name as subject, groups_subjects.exam_test FROM groups_subjects, subjects, lecturers WHERE groups_subjects.group_id = %s AND groups_subjects.subject_id = subjects.id AND groups_subjects.lecturer_id = lecturers.id", addslashes($_GET['group']));
$result_academicPerformances = mysqli_query($connection, $query_academicPerformances) or die($connection->error);
$academicPerformance_row = mysqli_fetch_assoc($result_academicPerformances);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Удаление группы</title>
  <link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css">
</head>
<body>
	<h1>Удаление группы</h1>
	<form action="<?php echo getCurrentURL(); ?>" method="post">
		<h3>Наименование: <?php echo $group_row['name']; ?></h3>
			<input type="hidden" name="group_id" value="<?php echo $group_row['id']; ?>">
		</h3>
<?php if(rowExist($result_academicPerformances)) { ?>
    <h3 class="note">Предупреждение: при удалении группы будут удалены связанные с ней ведомости</h3>
<?php } ?>
		<p>
			<input type="submit" name="Submit" value="Удалить" class="redButton">
		</p>
	</form>
<?php if(rowExist($result_academicPerformances)) { ?>
  <table width="100%"  border="1" cellspacing="2" cellpadding="1">
    <tr>
      <th scope="col">Номер</th>
      <th scope="col">Преподаватель </th>
      <th scope="col">Предмет</th>
      <th scope="col">Форма сдачи</th>
    </tr>
<?php $i=1; do { ?>
    <tr>
      <td class="center"><?php echo $i; ?></td>
      <td class="center"><?php echo $academicPerformance_row['lecturer_surname'] . ' ' . $academicPerformance_row['lecturer_name'] . ' ' . $academicPerformance_row['lecturer_patronymic']; ?></td>
      <td class="center"><?php echo $academicPerformance_row['subject']; ?></td>
      <td class="center"><?php echo $academicPerformance_row['exam_test']; ?></td>
    </tr>
<?php $i++; } while ($academicPerformance_row = mysqli_fetch_assoc($result_academicPerformances)); freeResult($result_academicPerformances); ?> 
  </table>
<?php } ?>
  <p><a href="<?php echo makeURL('groups'); ?>">На список групп</a> </p>
<?php includeModule('footer'); ?>