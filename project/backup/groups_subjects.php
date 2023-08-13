<?php 
require_once(__DIR__ . '/helpers/Authorization.php');

if(userHasRights($RIGHTS['MODERATOR'])) {
  if(isset($_POST["group_id"]) && !empty($_POST["group_id"]) &&
    isset($_POST["subject_id"]) && !empty($_POST["subject_id"]) &&
    isset($_POST["lecturer_id"]) && !empty($_POST["lecturer_id"]) &&
    isset($_POST["exam_test"]) && !empty($_POST["exam_test"])){
    $query_addGroupsSubjects = sprintf("INSERT INTO groups_subjects (group_id, subject_id , lecturer_id, exam_test) VALUES (%s, %s, %s, %s)",
                        GetSQLValueString($_POST['group_id'], "int"),
                        GetSQLValueString($_POST['subject_id'], "int"),
                        GetSQLValueString($_POST['lecturer_id'], "int"),
                        GetSQLValueString($_POST['exam_test'], "text"));
    mysqli_query($connection, $query_addGroupsSubjects) or die($connection->error);
    $nextURL = makeURL('groups_subjects');
    redirectTo($nextURL);
  }

  $query_groups = "SELECT * from groups";
  $result_groups = mysqli_query($connection, $query_groups) or die($connection->error);
  $group_row = mysqli_fetch_assoc($result_groups);
  
  $query_subjects = "SELECT * from subjects";
  $result_subjects = mysqli_query($connection, $query_subjects) or die($connection->error);
  $subject_row = mysqli_fetch_assoc($result_subjects);
  
  $query_lecturers = "SELECT lecturers.id, lecturers.surname, lecturers.name, lecturers.patronymic, 
        posts.post from lecturers, posts WHERE lecturers.post_id = posts.id";
  $result_lecturers = mysqli_query($connection, $query_lecturers) or die($connection->error);
  $lecturer_row = mysqli_fetch_assoc($result_lecturers);
}

$keywords = ['group' => '', 'subject' => '', 'surname' => '', 'name' => '', 'patronymic' => '', 'exam_test' => ''];

if(isset($_GET['group'])) $keywords['group'] = $_GET['group'];
if(isset($_GET['subject'])) $keywords['subject'] = $_GET['subject'];
if(isset($_GET['surname'])) $keywords['surname'] = $_GET['surname'];
if(isset($_GET['name'])) $keywords['name'] = $_GET['name'];
if(isset($_GET['patronymic'])) $keywords['patronymic'] = $_GET['patronymic'];
if(isset($_GET['exam_test'])) $keywords['exam_test'] = $_GET['exam_test'];

$query_groups_subjects = "SELECT groups.name as group_name, subjects.name as subject, subjects.hours as subject_hours, lecturers.surname, 
    lecturers.name, lecturers.patronymic, groups_subjects.exam_test, groups_subjects.id as group_subject_id
    FROM groups, subjects, lecturers, groups_subjects
    WHERE groups_subjects.group_id = groups.id 
    AND groups_subjects.subject_id = subjects.id
    AND groups_subjects.lecturer_id = lecturers.id 
    %s %s %s %s %s %s
    ORDER BY surname ASC";

$search_query_groups_subjects_group = $keywords['group'] ? sprintf("AND groups.name LIKE '%%%s%%' ",  $keywords['group']) : "";
$search_query_groups_subjects_subject = $keywords['subject'] ? sprintf("AND subjects.name LIKE '%%%s%%' ",  $keywords['subject']) : "";
$search_query_groups_subjects_surname = $keywords['surname'] ? sprintf("AND lecturers.surname LIKE '%%%s%%' ",  $keywords['surname']) : "";
$search_query_groups_subjects_name = $keywords['name'] ? sprintf("AND lecturers.name LIKE '%%%s%%' ",  $keywords['name']) : "";
$search_query_groups_subjects_patronymic = $keywords['patronymic'] ? sprintf("AND lecturers.patronymic LIKE '%%%s%%' ",  $keywords['patronymic']) : "";
$search_query_groups_subjects_exam_test = $keywords['exam_test'] ? sprintf("AND groups_subjects.exam_test LIKE '%%%s%%' ",  $keywords['exam_test']) : "";

$query_groups_subjects = sprintf($query_groups_subjects, $search_query_groups_subjects_group, 
$search_query_groups_subjects_subject, $search_query_groups_subjects_surname, 
$search_query_groups_subjects_name, $search_query_groups_subjects_patronymic,
$search_query_groups_subjects_exam_test);

$result_groups_subjects = mysqli_query($connection, $query_groups_subjects) or die($connection->error);
$group_subject_row = mysqli_fetch_assoc($result_groups_subjects);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Программа обучения</title>
  <link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css" charset="utf-8">
</head>
<body>
  <h1>Список групп и предметов обучения</h1>
  <h3>Поиск:</h3>
   <table border="0" cellspacing="2" cellpadding="1">
      <tr class="tr-nohover">
        <th width="153px" scope="col">Группа</th>
        <th width="153px" scope="col">Предмет</th>
        <th width="153px" scope="col">Фамилия </th>
        <th width="153px" scope="col">Имя</th>
        <th width="153px" scope="col">Отчество</th>
        <th width="153px" scope="col">Форма сдачи</th>
      </tr>
    </table>    
  <form action="<?php echo getCurrentURL(); ?>" method="get" enctype="text/plain" autocomplete="off">
    <input name="group" type="text" class="border-bottom" value="<?php echo $keywords['group']; ?>" size="13">    
    <input name="subject" type="text" class="border-bottom" value="<?php echo $keywords['subject']; ?>"size="13">
    <input name="surname" type="text" class="border-bottom" value="<?php echo $keywords['surname']; ?>"size="13">
    <input name="name" type="text" class="border-bottom" value="<?php echo $keywords['name']; ?>"size="13">
    <input name="patronymic" type="text" class="border-bottom" value="<?php echo $keywords['patronymic']; ?>"size="13"> 
    <input name="exam_test" type="text" class="border-bottom" value="<?php echo $keywords['exam_test']; ?>"size="13"> 
    <input type="submit" name="Submit" value="Найти" class="blueButton">
  </form>
  <form action="<?php echo getCurrentURL(); ?>" method="get" enctype="text/plain">
    <input name="group" type="hidden" value="">
    <input name="subject" type="hidden" value="">
    <input name="surname" type="hidden" value="">
    <input name="name" type="hidden" value="">
    <input name="patronymic" type="hidden" value="">
    <input name="exam_test" type="hidden" value="">
    <input type="submit" name="Submit" value="Все" class="blueButton" style="margin-top: 10px;">
  </form>
  <p>&nbsp;</p>
  <hr>
<?php if (rowExist($result_groups_subjects)) { ?>
  <table width="100%"  border="1" cellspacing="2" cellpadding="1">
    <tr>
      <th scope="col">Номер</th>
      <th scope="col">Группа</th>
      <th scope="col">Предмет</th>
      <th scope="col">Фамилия </th>
      <th scope="col">Имя</th>
      <th scope="col">Отчество</th>
      <th scope="col">Форма сдачи</th>
      <th scope="col">Ведомости</th>
    </tr>
<?php $i=1; do { ?>
    <tr>
      <td class="center"><?php echo $i; ?></td>
      <td class="center"><?php echo $group_subject_row['group_name']; ?></td>
      <td class="paddingLeft"><?php echo $group_subject_row['subject'] . ' (' . $group_subject_row['subject_hours'] . ')'; ?></td>
      <td class="paddingLeft"><?php echo $group_subject_row['surname']; ?></td>
      <td class="paddingLeft"><?php echo $group_subject_row['name']; ?></td>
      <td class="paddingLeft"><?php echo $group_subject_row['patronymic']; ?></td>
      <td class="paddingLeft"><?php echo $group_subject_row['exam_test']; ?></td>
      <td><a href="<?php echo makeURL('academic_performance', '', 'group_subject', $group_subject_row['group_subject_id']); ?>">Ведомость</a></td>
    </tr>
<?php $i++; } while($group_subject_row = mysqli_fetch_assoc($result_groups_subjects)); freeResult($result_groups_subjects); ?>        
  </table>
<?php } else { ?> 
  <h3>Преподавателей на данной кафедре пока нет!</h3>
<?php } ?>
<p>&nbsp;</p>
<?php if(userHasRights($RIGHTS['MODERATOR'])) { ?>
  <div class="fullnote">
  <h3>Добавить ведомость:</h3>
  <form action="<?php echo getCurrentURL(); ?>" method="POST" autocomplete="off"> 
    <table width="900"  border="0" cellspacing="2" cellpadding="2">
      <tr class="tr-nohover">
        <th width="150" scope="col">Группа: </th>
        <td>
          <select name="group_id" class="border-bottom">
  <?php do { ?>
            <option value="<?php echo $group_row['id']; ?>"><?php echo $group_row['name']; ?></option>
  <?php } while ($group_row = mysqli_fetch_assoc($result_groups)); freeResult($result_groups); ?>
          </select>
        </td>
      </tr>
      <tr class="tr-nohover">
        <th width="150" scope="col">Предмет: </th>
        <td>
          <select name="subject_id" class="border-bottom">
<?php do { ?>
            <option value="<?php echo $subject_row['id']; ?>"><?php echo $subject_row['name'] . ' (' . $subject_row['hours'] . ')'; ?></option>
<?php } while ($subject_row = mysqli_fetch_assoc($result_subjects)); freeResult($result_subjects); ?>
          </select>
        </td>
      </tr>
      <tr class="tr-nohover">
        <th width="150" scope="col">Преподаватель: </th>
        <td>
          <select name="lecturer_id" class="border-bottom">
<?php do { ?>
            <option value="<?php echo $lecturer_row['id']; ?>"><?php echo $lecturer_row['post'] . ": " . $lecturer_row['surname'] . ' ' .
            substr($lecturer_row['name'], 0, 2) . '.' . substr($lecturer_row['patronymic'], 0, 2) . '.'; ?></option>
<?php } while ($lecturer_row = mysqli_fetch_assoc($result_lecturers)); freeResult($result_lecturers); ?>
          </select>
        </td>
      </tr>
      <tr class="tr-nohover">
        <th width="150" scope="col">Форма сдачи: </th>
        <td>
          <select name="exam_test" class="border-bottom">
            <option value="экзамен">экзамен</option>
            <option value="зачет">зачет</option>
          </select>
        </td>
      </tr>
    </table>
    <p></p>
    <input type="submit" name="Submit" class="blueButton" value="Добавить">
    <input type="reset" name="Reset" class="redButton" value="Отмена">
  </form>
 </div>
<?php } ?>
  <p><a href="<?php echo makeURL('session_results'); ?>">На результаты сессии</a> </p>
<?php includeModule('footer'); ?>