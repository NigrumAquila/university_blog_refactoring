<?php 
require_once(__DIR__ . '/../helpers/Authorization.php');
clearKeySession('prevURL');

if(userHasRights($RIGHTS['MODERATOR'])) {
  if(isset($_POST["group_id"]) && !empty($_POST["group_id"]) &&
    isset($_POST["number"]) && !empty($_POST["number"]) &&
    isset($_POST["surname"]) && !empty($_POST["surname"]) &&
    isset($_POST["name"]) && !empty($_POST["name"]) &&
    isset($_POST["patronymic"]) && !empty($_POST["patronymic"]) &&
    isset($_POST["gender"]) && !empty($_POST["gender"]) &&
    isset($_POST["birthday"]) && !empty($_POST["birthday"])){
    $query_addStudent = sprintf("INSERT INTO students (group_id, number , surname, name, patronymic, gender, birthday) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                        GetSQLValueString($_POST['group_id'], "int"),
                        GetSQLValueString($_POST['number'], "int"),
                        GetSQLValueString($_POST['surname'], "text"),
                        GetSQLValueString($_POST['name'], "text"),
                        GetSQLValueString($_POST['patronymic'], "text"),
                        GetSQLValueString($_POST['gender'], "text"),
                        GetSQLValueString($_POST['birthday'], "date"));
    mysqli_query($connection, $query_addStudent) or die($connection->error);
    $nextURL = makeURL('students_search');
    redirectTo($nextURL);
  }

  $query_groups = "SELECT * from groups";
  $result_groups = mysqli_query($connection, $query_groups) or die($connection->error);
  $group_row = mysqli_fetch_assoc($result_groups);
}

$keywords = ['group' => '', 'number' => '', 'surname' => '', 'name' => '', 'patronymic' => '', 'gender' => '', 'birthday' => ''];

if(isset($_GET['group'])) $keywords['group'] = $_GET['group'];
if(isset($_GET['number'])) $keywords['number'] = $_GET['number'];
if(isset($_GET['surname'])) $keywords['surname'] = $_GET['surname'];
if(isset($_GET['name'])) $keywords['name'] = $_GET['name'];
if(isset($_GET['patronymic'])) $keywords['patronymic'] = $_GET['patronymic'];
if(isset($_GET['gender'])) $keywords['gender'] = $_GET['gender'];
if(isset($_GET['birthday'])) $keywords['birthday'] = $_GET['birthday'];

$query_students = "SELECT students.id, groups.name as group_name, students.number, students.surname, students.name, students.patronymic, students.gender, students.birthday 
FROM students, groups WHERE students.group_id = groups.id %s %s %s %s %s %s %s
ORDER BY groups.name, name ASC";

$search_query_students_group = $keywords['group'] ? sprintf("AND groups.name LIKE '%%%s%%' ",  $keywords['group']) : "";
$search_query_students_number = $keywords['number'] ? sprintf("AND students.number LIKE '%%%s%%' ",  $keywords['number']) : "";
$search_query_students_surname = $keywords['surname'] ? sprintf("AND students.surname LIKE '%%%s%%' ",  $keywords['surname']) : "";
$search_query_students_name = $keywords['name'] ? sprintf("AND students.name LIKE '%%%s%%' ",  $keywords['name']) : "";
$search_query_students_patronymic = $keywords['patronymic'] ? sprintf("AND students.patronymic LIKE '%%%s%%' ",  $keywords['patronymic']) : "";
$search_query_students_gender = $keywords['gender'] ? sprintf("AND students.gender LIKE '%%%s%%' ",  $keywords['gender']) : "";
$search_query_students_birthday = $keywords['birthday'] ? sprintf("AND students.birthday LIKE '%%%s%%' ",  $keywords['birthday']) : "";

$query_students = sprintf($query_students, $search_query_students_group, 
$search_query_students_number, $search_query_students_surname, 
$search_query_students_name, $search_query_students_patronymic,
$search_query_students_gender, $search_query_students_birthday);

$result_students = mysqli_query($connection, $query_students) or die($connection->error);
$student_row = mysqli_fetch_assoc($result_students);
$students_totalRows = mysqli_num_rows($result_students);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Студенты УТиИТ</title>
	<link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css">
</head>
<body>
	<h1>Полный список студентов УТиИТ</h1>
  <h3>Поиск:</h3>
  <table border="0" cellspacing="2" cellpadding="1">
    <tr class="tr-nohover">
      <th width="153px" scope="col">Группа</th>
      <th width="153px" scope="col">Зачетная книжка</th>
      <th width="153px" scope="col">Фамилия </th>
      <th width="153px" scope="col">Имя</th>
      <th width="153px" scope="col">Отчество</th>
      <th width="153px" scope="col">Пол</th>
      <th width="153px" scope="col">День рождения</th>
    </tr>
  </table>    
  <form action="<?php echo getCurrentURL(); ?>" method="get" enctype="text/plain" autocomplete="off">
    <input name="group" type="text" class="border-bottom" value="<?php echo $keywords['group']; ?>" size="13">    
    <input name="number" type="text" class="border-bottom" value="<?php echo $keywords['number']; ?>"size="13">
    <input name="surname" type="text" class="border-bottom" value="<?php echo $keywords['surname']; ?>"size="13">
    <input name="name" type="text" class="border-bottom" value="<?php echo $keywords['name']; ?>"size="13" maxlength="30">
    <input name="patronymic" type="text" class="border-bottom" value="<?php echo $keywords['patronymic']; ?>"size="13"> 
    <input name="gender" type="text" class="border-bottom" value="<?php echo $keywords['gender']; ?>"size="13"> 
    <input name="birthday" type="text" class="border-bottom" value="<?php echo $keywords['birthday']; ?>"size="13"> 
    <input type="submit" name="Submit" value="Найти" class="blueButton">
  </form>
  <form action="<?php echo getCurrentURL(); ?>" method="get" enctype="text/plain">
    <input name="group" type="hidden" value="">
    <input name="number" type="hidden" value="">
    <input name="surname" type="hidden" value="">
    <input name="name" type="hidden" value="">
    <input name="patronymic" type="hidden" value="">
    <input name="gender" type="hidden" value="">
    <input name="birthday" type="hidden" value="">
    <input type="submit" name="Submit" value="Все" class="blueButton" style="margin-top: 10px;">
  </form>
	<p>&nbsp;</p>
	<hr>
<?php if (rowExist($result_students)) { ?>
  <table width="100%"  border="1" cellspacing="2" cellpadding="1">
    <tr>
      <th width="1%" scope="col">Номер</th>
      <th width="8%" scope="col">Группа</th>
      <th width="10%" scope="col">Зачетная книжка</th>
      <th width="22%" scope="col">Фамилия </th>
      <th width="15%" scope="col">Имя</th>
      <th width="24%" scope="col">Отчество</th>
      <th width="5%" scope="col">Пол</th>
      <th width="15%" scope="col">День рождения</th>
<?php if(userHasRights($RIGHTS['MODERATOR'])) { ?>
      <th width="5%" scope="col">Администрирование</th>
<?php } ?>
    </tr>
<?php $i=1; do { ?>
    <tr>
      <td class="center"><?php echo $i; ?></td>
      <td class="center"><?php echo $student_row['group_name']; ?></td>
      <td class="center"><?php echo $student_row['number']; ?></td>
      <td class="paddingLeft"><?php echo $student_row['surname']; ?></td>
      <td class="paddingLeft"><?php echo $student_row['name']; ?></td>
      <td class="paddingLeft"><?php echo $student_row['patronymic']; ?></td>
      <td class="center"><?php echo $student_row['gender']; ?></td>
      <td class="center"><?php echo $student_row['birthday']; ?></td>
<?php if(userHasRights($RIGHTS['MODERATOR'])) { ?>
      <td>
        <a href="<?php echo makeURL('student', 'update', 'student', $student_row['id']); ?>">Изменить</a>| 
        <a href="<?php echo makeURL('student', 'delete', 'student', $student_row['id']); ?>">Удалить</a>
      </td>
<?php } ?>
    </tr>
<?php $i++; } while ($student_row = mysqli_fetch_assoc($result_students)); freeResult($result_students); ?> 
  </table>
<?php } else { ?> 
  <h3>Студентов пока нет!</h3>
<?php } ?>
<p>&nbsp;</p>
<?php if(userHasRights($RIGHTS['MODERATOR'])) { ?>
  <div class="fullnote">
    <h3>Добавить студента:</h3>
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
          <th width="150" scope="col">Номер зачетной книжки: </th>
          <td><input name="number" type="text" class="border-bottom" size="20" maxlength="10"></td>
        </tr>
        <tr class="tr-nohover">
          <th width="150" scope="col" >Фамилия: </th>
          <td><input name="surname" type="text" class="border-bottom" size="20" maxlength="15"></td>
        </tr>
        <tr class="tr-nohover">   
          <th width="150" scope="col">Имя</th>
          <td><input name="name" type="text" class="border-bottom" size="20" maxlength="10"></td>
        </tr>
        <tr class="tr-nohover">
          <th width="150" scope="col">Отчество</th>
          <td><input name="patronymic" type="text" class="border-bottom" size="20" maxlength="15"></td>
        </tr>
        <tr class="tr-nohover">
          <th width="150" scope="col">Пол</th> 
          <td>  
            <input checked="checked" name="gender" type="radio" value="м" > 
            <label for="gender">мужской</label>
            <input name="gender" type="radio" value="ж">
            <label for="gender">женский</label> 
          </td>
        </tr>
        <tr class="tr-nohover">
          <th width="150" scope="col">День рождения</th>
          <td><input name="birthday" type="date" class="border-bottom" value="<?=date("Y-m-d");?>"></td>
        </tr>
      </table>
      <p></p>
      <input type="submit" name="Submit" class="blueButton" value="Добавить">
      <input type="reset" name="Reset" class="redButton" value="Отмена">
    </form>
 </div>
<?php } ?>
  <p><a href="<?php echo makeURL('groups'); ?>">На список групп</a> </p>
  <?php includeModule('footer'); ?>