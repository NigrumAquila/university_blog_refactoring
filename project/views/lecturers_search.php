<?php 
require_once(__DIR__ . '/../helpers/Authorization.php');
clearKeySession('prevURL');

if(userHasRights($RIGHTS['MODERATOR'])) {
  if(isset($_POST["surname"]) && !empty($_POST["surname"]) &&
    isset($_POST["name"]) && !empty($_POST["name"]) &&
    isset($_POST["patronymic"]) && !empty($_POST["patronymic"]) &&
    isset($_POST["post_id"]) && !empty($_POST["post_id"]) &&
    isset($_POST["faculty_id"]) && !empty($_POST["faculty_id"])){
    $query_addLecturer = sprintf("INSERT INTO lecturers (surname, name, patronymic, post_id, faculty_id) VALUES (%s, %s, %s, %s, %s)",
                        GetSQLValueString($_POST['surname'], "text"),
                        GetSQLValueString($_POST['name'], "text"),
                        GetSQLValueString($_POST['patronymic'], "text"),
                        GetSQLValueString($_POST['post_id'], "int"),
                        GetSQLValueString($_POST['faculty_id'], "int"));
    mysqli_query($connection, $query_addLecturer) or die($connection->error);
    $nextURL = makeURL('lecturers_search');
    redirectTo($nextURL);
  }

  $query_posts = "SELECT * FROM posts ORDER BY post ASC";
  $result_posts = mysqli_query($connection, $query_posts) or die($connection->error);
  $post_row = mysqli_fetch_assoc($result_posts);

  $query_faculties = "SELECT * FROM faculties ORDER BY name ASC";
  $result_faculties = mysqli_query($connection, $query_faculties) or die($connection->error);
  $faculty_row = mysqli_fetch_assoc($result_faculties);
}

$keywords = ['surname' => '', 'name' => '', 'patronymic' => '', 'post' => '', 'fac_abbrev' => ''];

if(isset($_GET['surname'])) $keywords['surname'] = $_GET['surname'];
if(isset($_GET['name'])) $keywords['name'] = $_GET['name'];
if(isset($_GET['patronymic'])) $keywords['patronymic'] = $_GET['patronymic'];
if(isset($_GET['post'])) $keywords['post'] = $_GET['post'];
if(isset($_GET['fac_abbrev'])) $keywords['fac_abbrev'] = $_GET['fac_abbrev'];

$query_lecturers = "SELECT lecturers.id, lecturers.surname, lecturers.name, lecturers.patronymic, posts.post, faculties.abbrev 
    FROM lecturers, posts, faculties  
    WHERE posts.id = lecturers.post_id AND lecturers.faculty_id = faculties.id %s %s %s %s %s
    ORDER BY surname ASC";

$search_query_lecturers_surname = $keywords['surname'] ? sprintf("AND lecturers.surname LIKE '%%%s%%' ",  $keywords['surname']): "";
$search_query_lecturers_name = $keywords['name'] ? sprintf("AND lecturers.name LIKE '%%%s%%' ",  $keywords['name']) : "";
$search_query_lecturers_patronymic = $keywords['patronymic'] ? sprintf("AND lecturers.patronymic LIKE '%%%s%%' ",  $keywords['patronymic']) : "";
$search_query_lecturers_post = $keywords['post'] ? sprintf("AND posts.post LIKE '%%%s%%' ",  $keywords['post']) : "";
$search_query_lecturers_fac_abbrev = $keywords['fac_abbrev'] ? sprintf("AND faculties.abbrev LIKE '%%%s%%' ",  $keywords['fac_abbrev']) : "";

$query_lecturers = sprintf($query_lecturers, $search_query_lecturers_surname, 
$search_query_lecturers_name, $search_query_lecturers_patronymic, 
$search_query_lecturers_post, $search_query_lecturers_fac_abbrev);

$result_lecturers = mysqli_query($connection, $query_lecturers) or die($connection->error);
$lecturer_row = mysqli_fetch_assoc($result_lecturers);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Преподаватели УТиИТ</title>
	<link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css" charset="utf-8">
</head>
<body>
	<h1>Полный список преподавателей УТиИТ</h1>
  <h3>Поиск:</h3>
  <table border="0" cellspacing="2" cellpadding="1">
    <tr class="tr-nohover">
      <th width="153px" scope="col">Фамилия </th>
      <th width="153px" scope="col">Имя</th>
      <th width="153px" scope="col">Отчество</th>
      <th width="153px" scope="col">Должность</th>
      <th width="153px" scope="col">Кафедра</th>
    </tr>
  </table>    
  <form action="<?php echo getCurrentURL(); ?>" method="get" enctype="text/plain" autocomplete="off">
    <input name="surname" type="text" class="border-bottom" value="<?php echo $keywords['surname']; ?>" size="13">    
    <input name="name" type="text" class="border-bottom" value="<?php echo $keywords['name']; ?>"size="13" >
    <input name="patronymic" type="text" class="border-bottom" value="<?php echo $keywords['patronymic']; ?>"size="13">
    <input name="post" type="text" class="border-bottom" value="<?php echo $keywords['post']; ?>"size="13">
    <input name="fac_abbrev" type="text" class="border-bottom" value="<?php echo $keywords['fac_abbrev']; ?>"size="13"> 
    <input type="submit" name="Submit" value="Найти" class="blueButton">    
  </form>
  <form action="<?php echo getCurrentURL(); ?>" method="get" enctype="text/plain">
    <input name="surname" type="hidden" value="">
    <input name="name" type="hidden" value="">
    <input name="patronymic" type="hidden" value="">
    <input name="post" type="hidden" value="">
    <input name="fac_abbrev" type="hidden" value="">
    <input type="submit" name="Submit" value="Все" class="blueButton" style="margin-top: 10px;">
  </form>
	<p>&nbsp;</p>
	<hr>
<?php if (rowExist($result_lecturers)) { ?>
  <table width="100%"  border="1" cellspacing="2" cellpadding="1">
    <tr>
      <th width="11%" scope="col">Номер</th>
      <th width="18%" scope="col">Фамилия </th>
      <th width="18%" scope="col">Имя</th>
      <th width="18%" scope="col">Отчество</th>
      <th width="18%" scope="col">Должность</th>
      <th width="18%" scope="col">Кафедра</th>
<?php if(userHasRights($RIGHTS['MODERATOR'])) { ?>
      <th width="3%" scope="col">Администрирование</th>
<?php } ?>
    </tr>
<?php $i=1; do { ?>
    <tr>
      <td class="center"><?php echo $i; ?></td>
      <td class="paddingLeft"><?php echo $lecturer_row['surname']; ?></td>
      <td class="paddingLeft"><?php echo $lecturer_row['name']; ?></td>
      <td class="paddingLeft"><?php echo $lecturer_row['patronymic']; ?></td>
      <td class="paddingLeft"><?php echo $lecturer_row['post']; ?></td>
      <td class="center"><?php echo $lecturer_row['abbrev']; ?></td>
<?php if(userHasRights($RIGHTS['MODERATOR'])) { ?>
      <td>
        <a href="<?php echo makeURL('lecturer', 'update', 'lecturer', $lecturer_row['id']); ?>">Изменить</a>| 
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
    <form action="<?php echo getCurrentURL(); ?>" method="POST" autocomplete="off"> 
      <table width="900"  border="0" cellspacing="2" cellpadding="2">
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
          <th width="150" scope="col">Должность: </th>
          <td>
            <select name="post_id" class="border-bottom">
<?php do { ?>
              <option value="<?php echo $post_row['id']?>"><?php echo $post_row['post']?></option>
  <?php } while ($post_row = mysqli_fetch_assoc($result_posts)); freeResult($result_posts); ?>
            </select>
          </td>
        </tr>
        <tr class="tr-nohover">
          <th width="150" scope="col">Кафедра: </th>
          <td>
            <select name="faculty_id" class="border-bottom">
<?php do { ?>
              <option value="<?php echo $faculty_row['id']?>"><?php echo $faculty_row['name']?></option>
<?php } while ($faculty_row = mysqli_fetch_assoc($result_faculties)); freeResult($result_faculties); ?>
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
  <p><a href="<?php echo makeURL('faculties'); ?>">На список кафедр</a> </p>
<?php includeModule('footer'); ?>