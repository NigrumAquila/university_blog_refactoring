<?php 
require_once(__DIR__ . '/helpers/Authorization.php');

$query_groups = "SELECT groups.name, groups_subjects.exam_test, count(groups_subjects.id) as count_subjects, 
    if(groups_subjects.exam_test = 'экзамен', 5*count(*), count(*)) AS max_mark
    FROM groups, groups_subjects
    WHERE groups_subjects.group_id = groups.id
    GROUP BY groups.name, groups_subjects.exam_test 
    ORDER BY groups.name, groups_subjects.exam_test ASC";
$result_groups = mysqli_query($connection, $query_groups) or die($connection->error);
$group_row = mysqli_fetch_assoc($result_groups);

$query_actualResultsStudents = "SELECT groups.name as group_name, students.number, groups_subjects.exam_test,
    concat_ws('.', concat_ws(' ', students.surname, LEFT(students.name, 1)), concat(LEFT(students.patronymic, 1), '.')) AS student, 
    count(academic_performance.mark) AS count_marks, min(academic_performance.mark) AS min_mark, sum(academic_performance.mark) AS sum_marks
    FROM groups_subjects INNER JOIN  academic_performance ON groups_subjects.id = academic_performance.group_subject_id
    INNER JOIN groups ON groups_subjects.group_id = groups.id INNER JOIN students ON academic_performance.student_id = students.id
    GROUP BY groups_subjects.group_id, academic_performance.student_id, groups_subjects.exam_test";
$result_actualResultsStudents = mysqli_query($connection, $query_actualResultsStudents) or die($connection->error);
$actualResultsStudent_row = mysqli_fetch_assoc($result_actualResultsStudents);

$query_studentsSessionResults = "SELECT groups.name as group_name, students.number,
    concat_ws('.', concat_ws(' ', students.surname, LEFT(students.name, 1)), concat(LEFT(students.patronymic, 1), '.')) AS student, 
    if(count(if((groups_subjects.exam_test = 'зачет' AND academic_performance.mark = 0) OR (groups_subjects.exam_test = 'экзамен' AND academic_performance.mark < 3), true, NULL)) = 0, 'да', 'нет') as passed,
    CASE 
    WHEN sum(if(groups_subjects.exam_test = 'экзамен', 5, if(groups_subjects.exam_test = 'зачет', 1, 0))) = sum(academic_performance.mark) THEN '200'
    WHEN sum(if(groups_subjects.exam_test = 'экзамен', 5, if(groups_subjects.exam_test = 'зачет', 1, 0))) - 1 = sum(academic_performance.mark) THEN '150'
    WHEN count(if((groups_subjects.exam_test = 'экзамен' AND academic_performance.mark < 4) OR (groups_subjects.exam_test = 'зачет' AND academic_performance.mark = 0), true , NULL)) = 0 THEN '100'
    ELSE '0' END as grants
    FROM academic_performance INNER JOIN groups_subjects ON academic_performance.group_subject_id = groups_subjects.id INNER JOIN students ON academic_performance.student_id = students.id INNER JOIN groups ON groups_subjects.id = groups.id GROUP BY academic_performance.student_id, groups_subjects.group_id";
$result_studentsSessionResults = mysqli_query($connection, $query_studentsSessionResults) or die($connection->error);
$studentSessionResults_row = mysqli_fetch_assoc($result_studentsSessionResults);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta http-equiv="Content-Type" content="text/html;  charset='utf-8'">
  <title>Результаты сессии</title>
  <link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css">
</head>
<body>
  <h1>Результаты сессии</h1>
  <h3>Планируемые показатели по группам</h3>
  <hr>
<?php if(rowExist($result_groups)) { ?>
  <table width="100%"  border="1" cellspacing="2" cellpadding="1">
    <tr>
      <th scope="col">Номер</th>
      <th scope="col">Группа</th>
      <th scope="col">Форма сдачи</th>
      <th scope="col">Количество предметов</th>
      <th scope="col">Максимальный балл</th>
    </tr>
<?php $i = 1; do { ?>
    <tr>
      <td class="center"><?php echo $i; ?></td>
      <td class="center"><?php echo $group_row['name']; ?></td>
      <td class="center"><?php echo $group_row['exam_test']; ?></td>
      <td class="center"><?php echo $group_row['count_subjects']; ?></td>
      <td class="center"><?php echo $group_row['max_mark']; ?></td>
    </tr>
<?php $i++; } while ($group_row = mysqli_fetch_assoc($result_groups)); freeResult($result_groups); ?>
  </table>
<?php } else { ?>
  <h3>Групп пока нет!</h3>
<?php } ?>
  <hr>
  <h3>Фактические результаты сдачи сессии  студентами</h3>
  <hr>
<?php if(rowExist($result_actualResultsStudents)) { ?>
  <table width="100%"  border="1" cellspacing="2" cellpadding="1">
    <tr>
      <th width="10%" scope="col">Номер </th>
      <th width="10%" scope="col">Группа </th>
      <th width="10%" scope="col">Номер зачетной книжки </th>
      <th width="10%" scope="col">Студент</th>
      <th width="20%" scope="col">Форма сдачи </th> 
      <th width="15%" scope="col">Количество оценок </th>
      <th width="15%" scope="col">Минимальная оценка</th>
      <th width="15%" scope="col">Суммарный балл</th>
    </tr>
<?php $i = 1; do { ?>
    <tr>
      <td class="center"><?php echo $i; ?></td>
      <td class="center"><?php echo $actualResultsStudent_row['group_name']; ?></td>
      <td class="center"><?php echo $actualResultsStudent_row['number']; ?></td>
      <td class="center"><?php echo $actualResultsStudent_row['student']; ?></td>
      <td class="center"><?php echo $actualResultsStudent_row['exam_test']; ?></td>
      <td class="center"><?php echo $actualResultsStudent_row['count_marks']; ?></td>
      <td class="center"><?php echo $actualResultsStudent_row['min_mark']; ?></td>
      <td class="center"><?php echo $actualResultsStudent_row['sum_marks']; ?></td>
    </tr>
<?php $i++; } while ($actualResultsStudent_row = mysqli_fetch_assoc($result_actualResultsStudents)); freeResult($result_actualResultsStudents); ?>
  </table>
  <hr>
  <h3>Подведение итогов сессии</h3>
  <hr>
  <table width="100%"  border="1" cellspacing="2" cellpadding="1">
    <tr>
      <th scope="col">Номер </th>
      <th scope="col">Группа </th>
      <th scope="col">Номер зачетной книжки</th>
      <th scope="col">Студент</th>
      <th scope="col">Сдал</th>
      <th scope="col">Стипендия (%)</th>
    </tr>
<?php $i = 1; do { ?>
    <tr>
      <td class="center"><?php echo $i;?></td>
      <td class="center"><?php echo $studentSessionResults_row['group_name'];?></td>
      <td class="center"><?php echo $studentSessionResults_row['number']; ?></td>
      <td class="center"><?php echo $studentSessionResults_row['student']; ?></td>
      <td class="center"><?php echo $studentSessionResults_row['passed']; ?></td>
      <td class="center"><?php echo $studentSessionResults_row['grants']; ?></td>
    </tr>
<?php $i++; } while($studentSessionResults_row = mysqli_fetch_assoc($result_studentsSessionResults)); freeResult($result_studentsSessionResults); ?>
  </table>
<?php } else { ?>
  <h3>Групп пока нет!</h3>
<?php } includeModule('footer'); ?>