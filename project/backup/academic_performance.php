<?php 
require_once(__DIR__ . '/helpers/Authorization.php');

if(userHasRights($RIGHTS["MODERATOR"])) {
  if(isset($_POST['deleteAcademicPerformance'])){
    $query_deleteMarks = sprintf("DELETE FROM academic_performance WHERE group_subject_id = %s", GetSQLValueString($_GET['group_subject'], "int"));
    $query_deleteAcademicPerformance = sprintf("DELETE FROM groups_subjects WHERE groups_subjects.id = %s", GetSQLValueString($_GET['group_subject'], "int"));
    mysqli_query($connection, "LOCK TABLES academic_performance WRITE, groups_subjects WRITE") or die($connection->error);
    mysqli_query($connection, $query_deleteMarks) or die($connection->error);
    mysqli_query($connection, $query_deleteAcademicPerformance) or die($connection->error);
    mysqli_query($connection, "UNLOCK TABLES") or die($connection->error);
    $nextURL = makeURL('groups_subjects');
    redirectTo($nextURL);
  }

  if(isset($_POST['deleteMark']) &&
    isset($_POST['mark_id']) && !empty($_POST['mark_id'])){
    $query_deleteMark = sprintf("DELETE FROM academic_performance WHERE id = %s",
                        GetSQLValueString($_POST['mark_id'], "int"));
    mysqli_query($connection, $query_deleteMark) or die($connection->error);
    $nextURL = makeURL('academic_performance', '', 'group_subject', $_POST['group_subject_id']);
    redirectTo($nextURL);
  }

  if(isset($_POST['insertMark']) &&
      isset($_POST['student_id']) && !empty($_POST['student_id']) &&
      isset($_POST['mark']) && $_POST['mark'] != '' &&
      isset($_POST['date_exam']) && !empty($_POST['date_exam']) &&
      isset($_POST['group_subject_id']) && !empty($_POST['group_subject_id'])){
    $query_insertMark = sprintf("INSERT INTO academic_performance (group_subject_id, student_id, mark, date_exam) VALUES (%s, %s, %s, %s)",
                        GetSQLValueString($_POST['group_subject_id'], "int"),
                        GetSQLValueString($_POST['student_id'], "int"),
                        GetSQLValueString($_POST['mark'], "int"),
                        GetSQLValueString($_POST['date_exam'], "date"));
    mysqli_query($connection, $query_insertMark) or die($connection->error);
    $nextURL = makeURL('academic_performance', '', 'group_subject', $_POST['group_subject_id']);
    redirectTo($nextURL);
  }

  if(isset($_POST['updateMark']) &&
    isset($_POST["mark_id"]) && !empty($_POST["mark_id"]) && 
    isset($_POST["mark"]) && ($_POST["mark"]) !== '' && 
    isset($_POST["date_exam"]) && !empty($_POST["date_exam"])){
    $query_editMark = sprintf("UPDATE academic_performance SET mark = %s, date_exam = %s WHERE id = %s",
                                GetSQLValueString($_POST['mark'], "int"),
                                GetSQLValueString($_POST['date_exam'], "date"),
                                GetSQLValueString($_POST['mark_id'], "int"));
    mysqli_query($connection, $query_editMark) or die($connection->error);
    $nextURL = makeURL('academic_performance');
    redirectWithParams($nextURL, 'group_subject', $_GET['group_subject']);
  }

  $query_students = sprintf("SELECT students.id, students.surname, students.name, students.patronymic FROM students, groups_subjects 
        WHERE students.group_id = groups_subjects.group_id AND groups_subjects.id = %s", GetSQLValueString($_GET['group_subject'], "int"));
  $result_students = mysqli_query($connection, $query_students) or die($connection->error);
  $student_row = mysqli_fetch_assoc($result_students);

  $query_marks = sprintf("SELECT marks.id, marks.mark FROM marks, groups_subjects 
      WHERE marks.id <= if(groups_subjects.exam_test = 'зачет', 1, if(groups_subjects.exam_test = 'экзамен', 5, NULL)) 
      AND marks.id >= if(groups_subjects.exam_test = 'зачет', 0, if(groups_subjects.exam_test = 'экзамен', 2, NULL)) 
      AND groups_subjects.id = %s ORDER BY id DESC", addslashes($_GET['group_subject']));
  $result_marks = mysqli_query($connection, $query_marks) or die($connection->error);
  $mark_row = mysqli_fetch_assoc($result_marks);
}

$query_academicPerformance = sprintf("SELECT academic_performance.id as mark_id, marks.mark, academic_performance.date_exam, groups.name as group_name, 
      subjects.name as subject, lecturers.surname as lecturer_surname, lecturers.name as lecturer_name, 
      lecturers.patronymic as lecturer_patronymic, groups_subjects.exam_test, students.number as student_number, 
      students.surname as student_surname, students.name as student_name, students.patronymic as student_patronymic
      FROM groups_subjects JOIN academic_performance ON academic_performance.group_subject_id = groups_subjects.id JOIN groups ON 
      groups.id = groups_subjects.group_id  JOIN marks ON marks.id = academic_performance.mark JOIN subjects ON 
      subjects.id = groups_subjects.subject_id JOIN lecturers ON lecturers.id = groups_subjects.lecturer_id
      JOIN students ON students.id = academic_performance.student_id WHERE groups_subjects.id = %s
      ORDER BY student_surname, student_name, student_patronymic ASC", 
      addslashes($_GET['group_subject']));
$result_academicPerformance = mysqli_query($connection, $query_academicPerformance) or die($connection->error);
$recordHasMarks = rowExist($result_academicPerformance) ? true : false;
if(!$recordHasMarks) {
  $query_academicPerformance = sprintf("SELECT groups.name as group_name, subjects.name as subject, lecturers.surname as lecturer_surname, 
  lecturers.name as lecturer_name, lecturers.patronymic as lecturer_patronymic, groups_subjects.exam_test 
  FROM groups_subjects, academic_performance, groups, subjects, lecturers 
  WHERE groups_subjects.group_id = groups.id AND groups_subjects.subject_id = subjects.id AND groups_subjects.lecturer_id = lecturers.id AND groups_subjects.id = %s", addslashes($_GET['group_subject']));  
  $result_academicPerformance = mysqli_query($connection, $query_academicPerformance) or die($connection->error);
}
$academicPerformance_row = mysqli_fetch_assoc($result_academicPerformance);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Ведомость</title>
	<link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css" charset="utf-8">
</head>
<body>
	<h2>Ведомость</h2>
  <h3>&nbsp;</h3>
  <h3>Группа: <?php echo $academicPerformance_row['group_name']; ?></h3>
  <h3>Предмет: <?php echo $academicPerformance_row['subject']; ?></h3>
  <h3>Преподаватель: <?php echo $academicPerformance_row['lecturer_surname'] . " " . $academicPerformance_row['lecturer_name'] . " " . $academicPerformance_row['lecturer_patronymic']; ?></h3>
  <h3>Форма сдачи: <?php echo $academicPerformance_row['exam_test']; ?></h3>
  <form action="<?php echo getCurrentURL(); ?> " method="POST" autocomplete="off">
    <input type="hidden" name="deleteAcademicPerformance">
    <input type="submit" value="Удалить ведомость<?php if($recordHasMarks) echo " с оценками" ?>" class="redButton">
  </form>
	<hr>
<?php if ($recordHasMarks && rowExist($result_students)) { ?>
  <table width="100%"  border="1" cellspacing="2" cellpadding="1">
    <tr>
      <th width="10%" scope="col">Номер</th>
      <th width="14%" scope="col">Номер зачетной книжки</th>
      <th width="14%" scope="col">Фамилия</th>
      <th width="14%" scope="col">Имя</th>
      <th width="14%" scope="col">Отчество</th>
      <th width="16%" scope="col">Оценка</th>
      <th width="13%" scope="col">Дата</th>
      <th width="13%" scope="col">Удалить</th>
    </tr>
<?php $i=1; do { ?> 
    <tr>
      <td class="center"><?php echo $i; ?></td>
      <td class="center"><?php echo $academicPerformance_row['student_number']; ?></td>
      <td class="paddingLeft"><?php echo $academicPerformance_row['student_surname']; ?></td>
      <td class="paddingLeft"><?php echo $academicPerformance_row['student_name']; ?></td>
      <td class="paddingLeft"><?php echo $academicPerformance_row['student_patronymic']; ?></td>
<?php if(userHasRights($RIGHTS['MODERATOR'])) { ?>     
      <form action="<?php echo getCurrentURL(); ?>" method="POST" autocomplete="off">
      <td>
        <select name="mark" class="border-bottom" style="margin: 5px 0 0 5px; width: 60%; text-align:center">
<?php do { ?>
          <option value="<?php echo $mark_row['id']?>"
          <?php echo $academicPerformance_row['mark'] == $mark_row['mark'] ?  "selected" : "" ;  ?>
          ><?php echo $mark_row['mark']?>
          </option>
<?php } while ($mark_row = mysqli_fetch_assoc($result_marks)); mysqli_data_seek($result_marks, 0); $mark_row = mysqli_fetch_assoc($result_marks); ?>
        </select>
        <input type="submit" style="float:right" class="blueButton"  name="Submit" value="!">
      </td>
      <td>
        <input name="date_exam" type="date" class="border-bottom" style="margin: 5px 0 0 5px; width: 50%; text-align:center" value="<?php echo $academicPerformance_row['date_exam']; ?>">
        <input name="mark_id" type="hidden" value="<?php echo $academicPerformance_row['mark_id']; ?>">
        <input type="hidden" name="updateMark" >
        <input type="submit" name="Submit" style="float:right" value="!" class="blueButton">
      </td>
      </form>
<?php } else { ?>
      <td class="center"><?php echo $academicPerformance_row['mark']; ?></td>
      <td class="center"><?php echo $academicPerformance_row['date_exam']; ?></td>
<?php } ?>
      <td>
        <form action="<?php echo getCurrentURL(); ?>" method="POST" autocomplete="off">
          <input type="hidden" name="deleteMark">
          <input type="hidden" name="group_subject_id" value="<?php echo $_GET['group_subject']; ?>">
          <input type="hidden" name="mark_id" value="<?php echo $academicPerformance_row['mark_id'] ?>">
          <input type="submit" name="Submit" class="redButton" value="!" style="margin: 0 5px 0 5px;">
        </form>
      </td>
    </tr>
<?php $i++; } while ($academicPerformance_row = mysqli_fetch_assoc($result_academicPerformance)); freeResult($result_academicPerformance); ?>            
  </table>
<?php } elseif(rowExist($result_students)) { ?>
  <h3>Оценок пока нет</h3>
<?php } else { ?>
  <h3>Студентов в группе пока нет</h3>
<?php } ?>
<p>&nbsp;</p>
<?php if(userHasRights($RIGHTS['MODERATOR']) && rowExist($result_students)) { ?>
  <div class="fullnote">
  <h3>Добавить запись:</h3>
  <form action="<?php echo getCurrentURL(); ?>" method="POST" autocomplete="off"> 
    <table width="900"  border="0" cellspacing="2" cellpadding="2">
      <tr class="tr-nohover">
        <th width="150" scope="col">Студент: </th>
        <td>
          <select name="student_id" class="border-bottom">
  <?php do { ?>
            <option value="<?php echo $student_row['id']; ?>"><?php echo $student_row['surname'] . ' ' . substr($student_row['name'], 0, 2) . '. ' . 
            substr($student_row['patronymic'], 0, 2) . '.'; ?></option>
  <?php } while ($student_row = mysqli_fetch_assoc($result_students)); freeResult($result_students); ?>
          </select>
        </td>
      </tr>
      <tr class="tr-nohover">
        <th width="150" scope="col">Оценка: </th>
        <td>
          <select name="mark" class="border-bottom">
<?php do { ?>
            <option value="<?php echo $mark_row['id']; ?>"><?php echo $mark_row['mark']; ?></option>
<?php } while ($mark_row = mysqli_fetch_assoc($result_marks)); ?>
          </select>
        </td>
      </tr>
      <tr class="tr-nohover">
        <th width="150" scope="col">Дата: </th>
        <td>
          <input type="date" name="date_exam" class="border-bottom" value="<?=date("Y-m-d");?>">
        </td>
      </tr>
      <input type="hidden" name="group_subject_id" value="<?php echo $_GET['group_subject']; ?>">
      <input type="hidden" name="insertMark">
    </table>
    <p></p>
    <input type="submit" name="Submit" class="blueButton" value="Добавить">
    <input type="reset" name="Reset" class="redButton" value="Отмена">
  </form>
 </div>
<?php } freeResult($result_marks); ?>
  <p><a href="<?php echo makeURL('groups_subjects'); ?>">На программу обучения</a></p>
<?php includeModule('footer'); ?>