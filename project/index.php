<?php 
require_once(__DIR__ . '/helpers/Authorization.php'); 
clearKeysSession(['userExist', 'prevURL']);

if(isset($_POST['secret'])) {
  $query_editUser = sprintf("UPDATE users SET rights = 'a' WHERE users.name = %s", GetSQLValueString($_SESSION['username'], "text"));
  mysqli_query($connection, $query_editUser) or die($connection->error);
  setKeySession('userRights', 'a');
  $nextURL = makeURL('index');
  redirectTo($nextURL);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>УТиИТ при ИрГУПС</title>
  <link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css">
</head>
<body>
  <h1>Иркутский государственный университет путей сообщения</h1>
  <h2>Факультет "Управление на транспорте и информационные технологии"</h2>
  <p>Здравствуйте, уважаемые посетители блога! </p>
  <p>Здесь публикуются сведения о кафедрах, преподавателях, студентах и процессе обучения в институте.</p>
  <p>Читайте на здоровье!</p>
<?php if(userIsLoggedIn()) { ?>
  <h2>Привет <?php echo $_SESSION['username'] ?></h2>
<?php } ?>
  <hr>
    <p align="center"> |
<?php if(!userIsLoggedIn()) { ?> 
  <a href="<?php echo makeURL('login'); ?>">Вход</a> | 
      <a href="<?php echo makeURL('registration'); ?>">Регистрация</a> |
<?php } else { ?> 
      <a href="<?php echo makeURL('logout'); ?>">Выход</a> | 
<?php } if(userHasRights($RIGHTS['ADMIN'])) { ?> 
      <a href="<?php echo makeURL('users'); ?>">Пользователи</a> | 
<?php }  ?> 
    </p>
<?php if(userIsLoggedIn() && !userHasRights($RIGHTS['ADMIN'])) { ?>
      <form class="center" action="<?php echo makeURL('index') ?>" method="POST">
        <input type="submit" class="redButton" value="Получить права администратора">
        <input name="secret" type="hidden">
      </form>
<?php } ?>
  <hr>
    <p align="center">  | 
      <a href="<?php echo makeURL('faculties'); ?>">Кафедры</a> | 
      <a href="<?php echo makeURL('lecturers_search'); ?>">Преподаватели</a> | 
      <a href="<?php echo makeURL('subjects'); ?>">Предметы</a> | 
    </p>
  <hr>
    <p align="center"> | 
      <a href="<?php echo makeURL('groups'); ?>">Группы</a> | 
      <a href="<?php echo makeURL('students_search'); ?>">Студенты</a> | 
      <a href="<?php echo makeURL('groups_subjects'); ?>">Программа обучения</a> | 
      <a href="<?php echo makeURL('session_results'); ?>">Результаты сессии</a> | 
    </p>
  <hr>
<?php includeModule('footer'); ?>