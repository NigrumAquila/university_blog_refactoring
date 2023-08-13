<?php 
require_once(__DIR__ . '/helpers/Authorization.php');
checkAccessRedirect($RIGHTS['MODERATOR']);
if(!sessionKeyExist('prevURL')) setKeySession('prevURL', getPrevURL());

if(isset($_POST["surname"]) && !empty($_POST["surname"]) &&
    isset($_POST["name"]) && !empty($_POST["name"]) &&
    isset($_POST["patronymic"]) && !empty($_POST["patronymic"]) &&
    isset($_POST["post_id"]) && !empty($_POST["post_id"]) &&
    isset($_POST["faculty_id"]) && !empty($_POST["faculty_id"]) &&
    isset($_POST["lecturer_id"]) && !empty($_POST["lecturer_id"])){
  $query_editLecturer = sprintf("UPDATE lecturers SET surname = %s, name = %s, patronymic = %s, post_id = %s, faculty_id = %s WHERE id = %s",
                       GetSQLValueString($_POST['surname'], "text"),
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['patronymic'], "text"),
                       GetSQLValueString($_POST['post_id'], "int"),
                       GetSQLValueString($_POST['faculty_id'], "int"),
                       GetSQLValueString($_POST['lecturer_id'], "int"));
  mysqli_query($connection, $query_editLecturer) or die($connection->error);
  $nextURL = prevURLcontains("lecturers_search", true) ? makeURL("lecturers_search") : makeURL("lecturers", '', 'faculty', $_POST['faculty_id']);
  redirectTo($nextURL);
}

$query_lecturer = sprintf("SELECT lecturers.id, lecturers.surname, lecturers.name, lecturers.patronymic, lecturers.post_id, lecturers.faculty_id, faculties.id as faculty_id, faculties.abbrev, faculties.name as faculty_name
FROM lecturers, faculties   
WHERE lecturers.faculty_id = faculties.id AND lecturers.id = %s", $_GET['lecturer']);
$result_lecturer = mysqli_query($connection, $query_lecturer) or die($connection->error);
$lecturer_row = mysqli_fetch_assoc($result_lecturer);
freeResult($result_lecturer);

$query_posts = "SELECT * FROM posts ORDER BY post ASC";
$result_posts = mysqli_query($connection, $query_posts) or die($connection->error);
$post_row = mysqli_fetch_assoc($result_posts);

$query_faculties = "SELECT * FROM faculties ORDER BY name ASC";
$result_faculties = mysqli_query($connection, $query_faculties) or die($connection->error);
$faculty_row = mysqli_fetch_assoc($result_faculties);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Редактирование преподавателя</title>
  <link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css">
</head>
<body>
  <h1>Редактирование преподавателя</h1>
  <form action="<?php echo getCurrentURL(); ?>" method="POST" autocomplete="off">
    <p>Фамилия: 
      <input name="surname" type="text" class="border-bottom" value="<?php echo $lecturer_row['surname']; ?>" size="20" maxlength="20">
    </p>
    <p>Имя: 
      <input name="name" type="text" class="border-bottom" value="<?php echo $lecturer_row['name']; ?>" size="15" maxlength="15">
    </p>
    <p>Отчество: 
      <input name="patronymic" type="text" class="border-bottom" value="<?php echo $lecturer_row['patronymic']; ?>" size="20" maxlength="20">
    </p>
    <p>Должность: 
      <select name="post_id" class="border-bottom">
  <?php do { ?>
        <option value="<?php echo $post_row['id']?>"
        <?php echo ($lecturer_row['post_id'] == $post_row['id']) ?  "selected" : "" ;  ?>>
        <?php echo $post_row['post']?>
        </option>
  <?php } while ($post_row = mysqli_fetch_assoc($result_posts)); freeResult($result_posts); ?>
      </select>
    </p>
    <p>Кафедра: 
      <select name="faculty_id" class="border-bottom">
<?php do { ?>
        <option value="<?php echo $faculty_row['id']?>"
        <?php echo ($lecturer_row['faculty_id'] == $faculty_row['id']) ?  "selected" : "" ;  ?>>
        <?php echo $faculty_row['name']?>
        </option>
<?php } while ($faculty_row = mysqli_fetch_assoc($result_faculties)); freeResult($result_faculties); ?>
      </select>
    </p>
    <p>
      <input name="lecturer_id" type="hidden" value="<?php echo $lecturer_row['id']; ?>">
    </p>
    <p>
      <input type="submit" name="Submit" value="Ввод" class="blueButton">
      <input type="reset" name="Reset" value="Отмена" class="redButton"> 
    </p>
  </form>
  <p>
<?php if(prevURLcontains('lecturers_search', true)) { ?>
    <a href="<?php echo makeURL('lecturers_search'); ?>">На список преподавателей кафедры</a>
<?php } else { ?>
    <a href="<?php echo makeURL('lecturers', '', 'faculty', $lecturer_row['faculty_id']); ?>">
    На список преподавателей кафедры <?php echo $lecturer_row['faculty_name']; ?></a>
<?php } ?>
  </p>
<?php includeModule('footer'); ?>