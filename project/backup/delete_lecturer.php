<?php 
require_once(__DIR__ . '/helpers/Authorization.php');
checkAccessRedirect($RIGHTS['MODERATOR']);
if(!sessionKeyExist('prevURL')) setKeySession('prevURL', getPrevURL());

if(isset($_POST['lecturer_id']) && !empty($_POST['lecturer_id'])) {
  $query_deleteMarks = sprintf("DELETE academic_performance FROM academic_performance JOIN groups_subjects WHERE academic_performance.group_subject_id = groups_subjects.id AND groups_subjects.lecturer_id = %s", GetSQLValueString($_POST['lecturer_id'], "int"));
  $query_deleteGroupSubject = sprintf("DELETE FROM groups_subjects WHERE groups_subjects.lecturer_id = %s", GetSQLValueString($_POST['lecturer_id'], "int"));
  $query_deleteLecturer = sprintf("DELETE FROM lecturers WHERE id = %s", GetSQLValueString($_POST['lecturer_id'], "int"));
  mysqli_query($connection, "LOCK TABLES academic_performance WRITE, groups_subjects WRITE, lecturers WRITE") or die($connection->error);
  mysqli_query($connection, $query_deleteMarks) or die($connection->error);
  mysqli_query($connection, $query_deleteGroupSubject) or die($connection->error);
  mysqli_query($connection, $query_deleteLecturer) or die($connection->error);
  mysqli_query($connection, "UNLOCK TABLES") or die($connection->error);
  $nextURL = prevURLcontains("lecturers_search", true) ? makeURL("lecturers_search") : makeURL("lecturers", '', 'faculty', $_POST['faculty_id']);
  redirectTo($nextURL);
}

$query_lecturer= sprintf("SELECT lecturers.id, lecturers.surname, lecturers.name, lecturers.patronymic, lecturers.faculty_id, 
      posts.post, faculties.name as faculty_name FROM lecturers, posts, faculties WHERE lecturers.id = %s 
      AND posts.id = lecturers.post_id AND faculties.id = lecturers.faculty_id", addslashes($_GET['lecturer']));
$result_lecturer = mysqli_query($connection, $query_lecturer) or die($connection->error);
$lecturer_row = mysqli_fetch_assoc($result_lecturer);
freeResult($result_lecturer);

$query_academicPerformances = sprintf("SELECT groups.name as group_name, subjects.name as subject, groups_subjects.exam_test FROM groups_subjects, groups, subjects WHERE groups_subjects.lecturer_id = %s AND groups_subjects.subject_id = subjects.id AND groups_subjects.group_id = groups.id", $_GET['lecturer']);
$result_academicPerformances = mysqli_query($connection, $query_academicPerformances) or die($connection->error);
$academicPerformance_row = mysqli_fetch_assoc($result_academicPerformances);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Удаление преподавателя</title>
  <link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css">
</head>
<body>
  <h1>Удаление преподавателя</h1>
  <form action="<?php echo getCurrentURL(); ?>" method="post">
    <h3>Фамилия: <?php echo $lecturer_row['surname']; ?></h3>
    <h3>Имя: <?php echo $lecturer_row['name']; ?></h3>
    <h3>Отчество: <?php echo $lecturer_row['patronymic']; ?></h3>
    <h3>Должность: <?php echo $lecturer_row['post']; ?></h3>
    <h3>Кафедра: <?php echo $lecturer_row['faculty_name']; ?></h3>
<?php if(rowExist($result_academicPerformances)) { ?>
    <h3 class="note">Предупреждение: при удалении преподавателя будут удалены связанные с ним ведомости и оценки</h3>
<?php } ?>
    <input name="lecturer_id" type="hidden" value="<?php echo $lecturer_row['id']; ?>">
    <input name="faculty_id" type="hidden" value="<?php echo $lecturer_row['faculty_id']; ?>">
    <p>
      <input type="submit" name="Submit" value="Удалить" class="redButton">
    </p>
  </form>
<?php if(rowExist($result_academicPerformances)) { ?>
  <table width="100%"  border="1" cellspacing="2" cellpadding="1">
    <tr>
      <th scope="col">Номер</th>
      <th scope="col">Группа </th>
      <th scope="col">Предмет</th>
      <th scope="col">Форма сдачи</th>
    </tr>
<?php $i=1; do { ?>
    <tr>
      <td class="center"><?php echo $i; ?></td>
      <td class="center"><?php echo $academicPerformance_row['group_name']; ?></td>
      <td class="center"><?php echo $academicPerformance_row['subject']; ?></td>
      <td class="center"><?php echo $academicPerformance_row['exam_test']; ?></td>
    </tr>
<?php $i++; } while ($academicPerformance_row = mysqli_fetch_assoc($result_academicPerformances)); freeResult($result_academicPerformances); ?> 
  </table>
<?php } ?>
  <p>
<?php if(prevURLcontains('lecturers_search', true)) { ?>
    <a href="<?php echo makeURL('lecturers_search'); ?>">На список преподавателей кафедры</a>
<?php } else { ?>
    <a href="<?php echo makeURL('lecturers', '', 'faculty', $lecturer_row['faculty_id']); ?>">
    На список преподавателей кафедры <?php echo $lecturer_row['faculty_name']; ?></a>
<?php } ?>
  </p>
<?php includeModule('footer'); ?>