<?php 
require_once(__DIR__ . '/helpers/Authorization.php'); 
checkAccessRedirect($RIGHTS['MODERATOR']);

if(isset($_POST['subject_id']) && !empty($_POST['subject_id'])) {
	$query_deleteMarks = sprintf("DELETE academic_performance FROM academic_performance JOIN groups_subjects WHERE academic_performance.group_subject_id = groups_subjects.id AND groups_subjects.subject_id = %s", GetSQLValueString($_POST['subject_id'], "int"));
	$query_deleteGroupSubject = sprintf("DELETE FROM groups_subjects WHERE groups_subjects.subject_id = %s", GetSQLValueString($_POST['subject_id'], "int"));
	$query_deleteSubject = sprintf("DELETE FROM subjects WHERE id = %s", GetSQLValueString($_POST['subject_id'], "int"));
	mysqli_query($connection, "LOCK TABLES academic_performance WRITE, groups_subjects WRITE, subjects WRITE") or die($connection->error);
	mysqli_query($connection, $query_deleteMarks) or die($connection->error);
	mysqli_query($connection, $query_deleteGroupSubject) or die($connection->error);
	mysqli_query($connection, $query_deleteSubject) or die($connection->error);
	mysqli_query($connection, "UNLOCK TABLES") or die($connection->error);
	$nextURL = makeURL('subjects');
	redirectTo($nextURL);
}

$query_subject = sprintf("SELECT * FROM subjects WHERE id = %s", addslashes($_GET['subject']));
$result_subject = mysqli_query($connection, $query_subject) or die($connection->error);
$subject_row = mysqli_fetch_assoc($result_subject);
freeResult($result_subject);

$query_academicPerformances = sprintf("SELECT groups.name as group_name, lecturers.surname as lecturer_surname, lecturers.name as lecturer_name, lecturers.patronymic as lecturer_patronymic, groups_subjects.exam_test FROM groups_subjects, lecturers, groups WHERE groups_subjects.subject_id = %s AND groups_subjects.group_id = groups.id AND groups_subjects.lecturer_id = lecturers.id", addslashes($_GET['subject']));
$result_academicPerformances = mysqli_query($connection, $query_academicPerformances) or die($connection->error);
$academicPerformance_row = mysqli_fetch_assoc($result_academicPerformances);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Удаление предмета</title>
  <link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css">
</head>
<body>
	<h1>Удаление предмета</h1>
	<form action="<?php echo getCurrentURL(); ?>" method="post">
		<h3>Название: <?php echo $subject_row['name']; ?></h3>
			<input type="hidden" name="subject_id" value="<?php echo $subject_row['id']; ?>">
		<h3>Часы: <?php echo $subject_row['hours']; ?></h3>
<?php if($result_academicPerformances) { ?>
		<h3 class="note">Предупреждение: при удалении предмета будут удалены связанные с ним ведомости и оценки</h3>
<?php } ?>
		<p>
			<input type="submit" name="Submit" value="Удалить" class="redButton">
		</p>
	</form>
	<?php if(rowExist($result_academicPerformances)) { ?>
	<table width="100%"  border="1" cellspacing="2" cellpadding="1">
		<tr>
		<th scope="col">Номер</th>
		<th scope="col">Группа</th>
		<th scope="col">Преподаватель </th>
		<th scope="col">Форма сдачи</th>
		</tr>
<?php $i=1; do { ?>
		<tr>
		<td class="center"><?php echo $i; ?></td>
		<td class="center"><?php echo $academicPerformance_row['group_name']; ?></td>
		<td class="center"><?php echo $academicPerformance_row['lecturer_surname'] . ' ' . $academicPerformance_row['lecturer_name'] . ' ' . $academicPerformance_row['lecturer_patronymic']; ?></td>
		<td class="center"><?php echo $academicPerformance_row['exam_test']; ?></td>
		</tr>
<?php $i++; } while ($academicPerformance_row = mysqli_fetch_assoc($result_academicPerformances)); freeResult($result_academicPerformances); ?> 
	</table>
<?php } ?>
  <p><a href="<?php echo makeURL('subjects'); ?>">На список предметов</a> </p>
<?php includeModule('footer'); ?>