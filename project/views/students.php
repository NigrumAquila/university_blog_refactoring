<?php 
require_once(__DIR__ . '/../helpers/Authorization.php');
clearKeySession('prevURL');

if (userHasRights($RIGHTS['MODERATOR']) &&
    isset($_POST["group_id"]) && !empty($_POST["group_id"]) &&
    isset($_POST["number"]) && !empty($_POST["number"]) &&
    isset($_POST["surname"]) && !empty($_POST["surname"]) &&
    isset($_POST["name"]) && !empty($_POST["name"]) &&
    isset($_POST["patronymic"]) && !empty($_POST["patronymic"]) &&
    isset($_POST["gender"]) && !empty($_POST["gender"]) &&
    isset($_POST["birthday"]) && !empty($_POST["birthday"])){
  $query_insertStudent = sprintf("INSERT INTO students (`group_id`, `number`, `surname`,
                      `name`, `patronymic`, `gender`, `birthday`)
                       VALUES (%s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['group_id'], "int"),
                       GetSQLValueString($_POST['number'], "int"),
                       GetSQLValueString($_POST['surname'], "text"),
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['patronymic'], "text"),
                       GetSQLValueString($_POST['gender'], "text"),
                       GetSQLValueString($_POST['birthday'], "date"));
  mysqli_query($connection, $query_insertStudent) or die($connection->error);
  $nextURL = makeURL("students");
  redirectWithParams($nextURL, "group", $_GET['group']);
}

$query_students = sprintf("SELECT students.id, students.group_id, students.number, students.surname, students.name,
      students.patronymic, students.gender, students.birthday, groups.name as group_name 
      FROM students INNER JOIN groups ON students.group_id = groups.id WHERE students.group_id = %s
      ORDER BY number ASC", addslashes($_GET['group']));
$result_students = mysqli_query($connection, $query_students) or die($connection->error);
$student_row = mysqli_fetch_assoc($result_students);

if(!rowExist($result_students)) {
  $query_group = sprintf("SELECT id, name FROM groups WHERE id = %s", addslashes($_GET['group']));
  $result_group = mysqli_query($connection, $query_group) or die($connection->error);
  $group_row = mysqli_fetch_assoc($result_group);
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Сведения о студентах группы <?php echo $student_row['group_name'] ?></title>
  <link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css">
</head>
<body>
	<h1>Список студентов группы</h1>
	<p>&nbsp;</p>
	<div class="fullnote">
		<h2><?php echo rowExist($result_students) ? $student_row['group_name'] : $group_row['name']; ?></h2>
	</div>
	<p>&nbsp;</p>
  <hr>
<?php if(rowExist($result_students)) { ?>
  <table width="100%"  border="1" cellspacing="2" cellpadding="1">
    <tr>
      <th width="11%" scope="col">Номер</th>
      <th width="17%" scope="col">Зачетная книжка </th>
      <th width="26%" scope="col">Фамилия</th>
      <th width="9%" scope="col">Имя</th>
      <th width="16%" scope="col">Отчество</th>
      <th width="4%" scope="col">Пол</th>
      <th width="17%" scope="col">День рождения</th>
<?php if(userHasRights($RIGHTS['MODERATOR'])) { ?>
      <th width="17%" scope="col">Администрирование</th>
<?php } ?>
    </tr>
<?php $i=1; do { ?>
    <tr>
      <td class="center"><?php echo $i; ?></td>
      <td class="center"><?php echo $student_row['number']; ?></td>
      <td class="paddingLeft"><?php echo $student_row['surname']; ?></td>
      <td class="paddingLeft"><?php echo $student_row['name']; ?></td>
      <td class="paddingLeft"><?php echo $student_row['patronymic']; ?></td>
      <td class="center"><?php echo $student_row['gender']; ?></td>
      <td class="center"><?php echo $student_row['birthday']; ?></td>
<?php if(userHasRights($RIGHTS['MODERATOR'])) { ?>
      <td>
        <a href="<?php echo makeURL('student', 'update', 'student', $student_row['id']); ?>">Изменить
        </a> | 
        <a href="<?php echo makeURL('student', 'delete', 'student', $student_row['id']); ?>">Удалить
        </a>
      </td>
<?php } ?>      
    </tr>
<?php $i++; } while ($student_row = mysqli_fetch_assoc($result_students)); freeResult($result_students); ?>    
  </table>
<?php } else { ?>	
  <h3>Студентов в группе  пока нет!</h3>
<?php } ?>
  <p>&nbsp;</p>
<?php if(userHasRights($RIGHTS['MODERATOR'])) { ?>
  <div class="fullnote">
  <h3>Добавить студента:</h3>
  <form action="<?php echo getCurrentURL(); ?>" method="POST" autocomplete="off"> 
    <table width="900"  border="0" cellspacing="2" cellpadding="2">
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
        <td><input name="birthday" type="date" class="border-bottom" value="<?=date("Y-m-d");?>"></p></td>
      </tr>
    </table>
    <input type="submit" name="Submit" class="blueButton" value="Добавить">
    <input type="reset" name="Reset" class="redButton" value="Отмена">
    <input name="group_id" type="hidden" value="<?php echo $_GET['group']; ?>">
  </form>
 </div>
<?php } ?>
  <p><a href="<?php echo makeURL('groups'); ?>">На список групп</a> </p>
<?php includeModule('footer'); ?>