<?php 
require_once(__DIR__ . '/helpers/Authorization.php');
checkAccessRedirect($RIGHTS['MODERATOR']);

if(isset($_POST['faculty_id']) && !empty($_POST['faculty_id'])) {
	$query_deleteMarks = sprintf("DELETE academic_performance FROM academic_performance, groups_subjects, lecturers WHERE academic_performance.group_subject_id = groups_subjects.id AND groups_subjects.lecturer_id = lecturers.id AND lecturers.faculty_id = %s", GetSQLValueString($_POST['faculty_id'], "int"));
	$query_deleteGroupSubject = sprintf("DELETE groups_subjects FROM groups_subjects, lecturers WHERE groups_subjects.lecturer_id = lecturers.id AND lecturers.faculty_id = %s", GetSQLValueString($_POST['faculty_id'], "int"));
	// die($query_deleteMarks);
	$query_deleteLecturers = sprintf("DELETE FROM lecturers WHERE faculty_id = %s", GetSQLValueString($_POST['faculty_id'], "int"));
	$query_deleteFaculty = sprintf("DELETE FROM faculties WHERE id = %s", GetSQLValueString($_POST['faculty_id'], "int"));
	mysqli_query($connection, "LOCK TABLES academic_performance WRITE, groups_subjects WRITE, lecturers WRITE, faculties WRITE") or die($connection->error);
	mysqli_query($connection, $query_deleteMarks) or die($connection->error);
	mysqli_query($connection, $query_deleteGroupSubject) or die($connection->error);
	mysqli_query($connection, $query_deleteLecturers) or die($connection->error);
	mysqli_query($connection, $query_deleteFaculty) or die($connection->error);
	mysqli_query($connection, "UNLOCK TABLES") or die($connection->error);
	$nextURL = makeURL('faculties');
	redirectTo($nextURL);
}

$query_faculty = sprintf("SELECT faculties.id as faculty_id, faculties.name as faculty_name, faculties.abbrev as faculty_abbrev, 
	lecturers.surname as lecturer_surname, lecturers.name as lecturer_name, lecturers.patronymic as lecturer_patronymic, 
	posts.post as lecturer_post FROM faculties JOIN lecturers ON lecturers.faculty_id = faculties.id JOIN posts 
	ON lecturers.post_id = posts.id WHERE faculties.id = %s ORDER BY lecturers.surname, lecturers.name, lecturers.patronymic", 
	addslashes($_GET['faculty']));
$result_faculty = mysqli_query($connection, $query_faculty) or die($connection->error);
$hasLecturers = rowExist($result_faculty) ? true : false;
if(!$hasLecturers){
	$query_faculty = sprintf("SELECT faculties.id as faculty_id, faculties.name as faculty_name, faculties.abbrev as faculty_abbrev
					FROM faculties WHERE faculties.id = %s", addslashes($_GET['faculty']));
	$result_faculty = mysqli_query($connection, $query_faculty) or die($connection->error);
}
$faculty_row = mysqli_fetch_assoc($result_faculty);

$query_checkGroupSubject = sprintf("SELECT groups_subjects.id FROM groups_subjects, lecturers WHERE lecturers.faculty_id = %s AND groups_subjects.lecturer_id = lecturers.id", GetSQLValueString($_GET['faculty'], "int"));
$result_checkGroupSubject = mysqli_query($connection, $query_checkGroupSubject) or die($connection->error);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Удаление кафедры <?php if($hasLecturers) echo 'с преподавателями' ?></title>
  <link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css">
</head>
<body>
	<h1>Удаление кафедры <?php if($hasLecturers) echo 'с преподавателями' ?></h1>
	<form action="<?php echo getCurrentURL(); ?>" method="POST">
		<h3>Наименование: <?php echo $faculty_row['faculty_name']; ?></h3>
		<h3>Аббревиатура: <?php echo $faculty_row['faculty_abbrev']; ?>
		<input type="hidden" name="faculty_id" value="<?php echo $faculty_row['faculty_id']; ?>">
		</h3>
<?php if($hasLecturers) { ?>
		<table width="100%"  border="1" cellspacing="2" cellpadding="1">
			<tr>
				<th scope="col">Номер</th>
				<th scope="col">Фамилия </th>
				<th scope="col">Имя</th>
				<th scope="col">Отчество</th>
				<th scope="col">Должность</th>
			</tr>
<?php $i=1; do { ?>
			<tr>
				<td class="center"><?php echo $i; ?></td>
				<td class="center"><?php echo $faculty_row['lecturer_surname']; ?></td>
				<td class="center"><?php echo $faculty_row['lecturer_name']; ?></td>
				<td class="center"><?php echo $faculty_row['lecturer_patronymic']; ?></td>
				<td class="center"><?php echo $faculty_row['lecturer_post']; ?></td>
			</tr>
<?php $i++; } while ($faculty_row = mysqli_fetch_assoc($result_faculty)); freeResult($result_faculty); ?> 
		</table>
		<h3 class="note">Предупреждение: при удалении кафедры будут удалены связанные с ней преподаватели<?php if(rowExist($result_checkGroupSubject)) echo ', ведомости и оценки' ?></h3>
<?php } ?>
		<p>
			<input type="submit" name="Submit" value="Удалить" class="redButton">
		</p>
	</form>
  <p><a href="<?php echo makeURL('faculties'); ?>">На список кафедр</a> </p>
<?php includeModule('footer'); ?>