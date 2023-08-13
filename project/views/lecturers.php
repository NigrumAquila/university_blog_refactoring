<?php 
require_once(__DIR__ . '/../helpers/Authorization.php');
clearKeySession('prevURL');

if(userHasRights($RIGHTS['MODERATOR']) &&
    isset($_POST["surname"]) && !empty($_POST["surname"]) &&
    isset($_POST["name"]) && !empty($_POST["name"]) &&
    isset($_POST["patronymic"]) && !empty($_POST["patronymic"]) &&
    isset($_POST["post_id"]) && !empty($_POST["post_id"]) &&
    isset($_POST["faculty_id"]) && !empty($_POST["faculty_id"])){
  $query_insertLecturer = sprintf("INSERT INTO lecturers (surname, name, patronymic, post_id, faculty_id) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['surname'], "text"),
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['patronymic'], "text"),
                       GetSQLValueString($_POST['post_id'], "int"),
                       GetSQLValueString($_POST['faculty_id'], "int"));
  mysqli_query($connection, $query_insertLecturer) or die($connection->error);
  $nextURL = makeURL('lecturers');
  redirectWithParams($nextURL, 'faculty', $_GET['faculty']);
}

$query_lecturers = sprintf("SELECT lecturers.id, lecturers.surname, lecturers.name, lecturers.patronymic, posts.post, lecturers.faculty_id, faculties.abbrev as faculty_abbrev, faculties.name as faculty_name FROM lecturers, posts, faculties WHERE lecturers.faculty_id = %s AND posts.id = lecturers.post_id AND faculties.id = lecturers.faculty_id ORDER BY lecturers.surname ASC", $_GET['faculty']);
$result_lecturers = mysqli_query($connection, $query_lecturers) or die($connection->error);
$lecturer_row = mysqli_fetch_assoc($result_lecturers);

if(!rowExist($result_lecturers)) {
  $query_faculty = sprintf("SELECT abbrev, name FROM faculties WHERE id = %s", $_GET['faculty']);
  $result_faculty = mysqli_query($connection, $query_faculty) or die($connection->error);
  $faculty_row = mysqli_fetch_assoc($result_faculty);
}

$query_posts = "SELECT * FROM posts";
$result_posts = mysqli_query($connection, $query_posts) or die($connection->error);
$post_row = mysqli_fetch_assoc($result_posts);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Сведения о преподавателях</title>
  <link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css">
</head>
<body>
  <h1>Список преподавателей кафедры</h1>
  <p>&nbsp;</p>
  <div class="fullnote">
    <h2>
      <?php echo rowExist($result_lecturers) ? $lecturer_row['faculty_name'] . " (" . $lecturer_row['faculty_abbrev'] . ")" 
                                               : $faculty_row['name'] . " (" . $faculty_row['abbrev'] . ")"?>
    </h2>
  </div>
  <p>&nbsp;</p>
  <hr>
<?php if(rowExist($result_lecturers)) { ?> 
  <table width="100%"  border="1" cellspacing="2" cellpadding="1">
    <tr>
      <th width="11%" scope="col">Номер</th>
      <th width="22.5%" scope="col">Фамилия </th>
      <th width="22.5%" scope="col">Имя</th>
      <th width="22.5%" scope="col">Отчество</th>
      <th width="22.5%" scope="col">Должность</th>
<?php if(userHasRights($RIGHTS['MODERATOR'])) { ?>
      <th width="22.5%" scope="col">Администрирование</th>
<?php } ?>
    </tr>
<?php $i=1; do { ?>
    <tr>
      <td class="center"><?php echo $i; ?></td>
      <td class="paddingLeft"><?php echo $lecturer_row['surname']; ?></td>
      <td class="paddingLeft"><?php echo $lecturer_row['name']; ?></td>
      <td class="paddingLeft"><?php echo $lecturer_row['patronymic']; ?></td>
      <td class="paddingLeft"><?php echo $lecturer_row['post']; ?></td>
<?php if(userHasRights($RIGHTS['MODERATOR'])) { ?>
      <td>
        <a href="<?php echo makeURL('lecturer', 'update', 'lecturer', $lecturer_row['id']); ?>">Изменить</a> | 
        <a href="<?php echo makeURL('lecturer', 'delete', 'lecturer', $lecturer_row['id']); ?>">Удалить</a>
      </td>
<?php } ?>
    </tr>
<?php $i++; } while ($lecturer_row = mysqli_fetch_assoc($result_lecturers)); freeResult($result_lecturers); ?>            
  </table>
<?php } else { ?> 
  <h3>Преподавателей на данной кафедре пока нет!</h3>
<?php } ?>
  <p>&nbsp;</p>
<?php if(userHasRights($RIGHTS['MODERATOR'])) { ?>
  <div class="fullnote">
    <h3>Добавить преподавателя:</h3>
    <table  border="0" cellspacing="0" cellpadding="0">
      <tr class="tr-nohover">
        <th scope="col">Фамилия </th>
        <th scope="col">Имя</th>
        <th scope="col">Отчество</th>
        <th scope="col">Должность</th>
        <th scope="col"></th>
      </tr>
      <form action="<?php echo getCurrentURL(); ?>" method="POST" autocomplete="off"> 
      <tr class="tr-nohover">
        <td><input name="surname" type="text" class="border-bottom" size="20" maxlength="20"></td>
        <td><input name="name" type="text" class="border-bottom" size="15" maxlength="15"></td>
        <td><input name="patronymic" type="text" class="border-bottom" size="20" maxlength="20"></td>
        <td>
          <select name="post_id" class="border-bottom">
<?php do { ?>
            <option value="<?php echo $post_row['id']?>">
            <?php echo $post_row['post']?></option>
<?php } while ($post_row = mysqli_fetch_assoc($result_posts)); freeResult($result_posts); ?>
          </select>
        </td>
        <td>  
          <input type="submit" name="Submit" class="blueButton" value="Добавить">
          <input type="reset" name="Reset" class="redButton" value="Отмена">
        </td>
      </tr>
      <input name="faculty_id" type="hidden" value="<?php echo $_GET['faculty']; ?>">
      </form>
    </table>
 </div>
<?php } ?>
  <p><a href="<?php echo makeURL('faculties'); ?>">На список кафедр</a> </p>
<?php includeModule('footer'); ?>