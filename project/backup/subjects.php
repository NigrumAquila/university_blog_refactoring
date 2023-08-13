<?php 
require_once(__DIR__ . '/helpers/Authorization.php');

if (userHasRights($RIGHTS['MODERATOR']) &&
    isset($_POST["subject_name"]) && !empty($_POST["subject_name"]) &&
	isset($_POST["hours"]) && !empty($_POST["hours"])) {
  $query_insertSubject = sprintf("INSERT INTO subjects (name, hours) VALUES (%s, %s)",
                        GetSQLValueString($_POST['subject_name'], "text"),
                        GetSQLValueString($_POST['hours'], "int"));
  mysqli_query($connection, $query_insertSubject) or die($connection->error);
  $nextURL = makeURL('subjects');
  redirectTo($nextURL);
}

$query_subjects = sprintf("SELECT * FROM subjects order by subjects.name asc");
$result_subjects = mysqli_query($connection, $query_subjects) or die($connection->error);
$subject_row = mysqli_fetch_assoc($result_subjects);
$subjects_totalRows = mysqli_num_rows($result_subjects);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Предметы УТиИт</title>
	<link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css">
</head>
<body>
	<h1>Предметы УТиИТ</h1>
	<p>&nbsp;</p>
	<hr>
	<p>&nbsp;</p>
<?php if(rowExist($result_subjects)) { do { ?>
	<div class="fullnote">
		<h2>
			<?php echo $subject_row['name'] . " (" . $subject_row['hours'] . ")"; ?>
		</h2>
<?php if(userHasRights($RIGHTS['MODERATOR'])) { ?>
		<p><a href="<?php echo makeURL('subject', 'update', 'subject', $subject_row['id']); ?>">Изменить</a></p>
		<p><a href="<?php echo makeURL('subject', 'delete', 'subject', $subject_row['id']); ?>">Удалить</a></p>
<?php } ?>
	</div>
<?php } while ($subject_row = mysqli_fetch_assoc($result_subjects)); freeResult($result_subjects); ?>
	<p>Предметы с 1 по <?php echo $subjects_totalRows ?> </p>
<?php } else { ?>
	<h3>Кафедр пока нет!</h3>
<?php } if(userHasRights($RIGHTS['MODERATOR'])) { ?>
  <p>&nbsp;</p>
  <form action="<?php echo getCurrentURL(); ?>" method="POST" autocomplete="off">
    <p>Предмет: 
      <input name="subject_name" type="text" class="border-bottom" size="50" maxlength="50">
    </p>
    <p>Часы: 
      <input name="hours" type="text" class="border-bottom" size="10" maxlength="10">
    </p>
    <p>
      <input type="submit" name="Submit" class="blueButton" value="Добавить">
      <input type="reset" name="Reset" class="redButton" value="Отмена">
    </p>
  </form>
<?php } ?>
<?php includeModule('footer'); ?>