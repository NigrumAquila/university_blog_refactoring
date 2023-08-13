<?php 
require_once(__DIR__ . '/helpers/Authorization.php');

if(userHasRights($RIGHTS['MODERATOR']) &&
    isset($_POST['fac_name']) && !empty($_POST['fac_name']) && 
    isset($_POST['fac_abbrev']) && !empty($_POST['fac_abbrev'])) {
  $query_insertFaculty = sprintf("INSERT INTO faculties (abbrev, name) VALUES (%s, %s)",
                       GetSQLValueString($_POST['fac_abbrev'], "text"),
                       GetSQLValueString($_POST['fac_name'], "text"));
  mysqli_query($connection, $query_insertFaculty) or die($connection->error);
  $nextURL = makeURL('faculties');
  redirectTo($nextURL);
}

$query_faculties = sprintf("SELECT * FROM faculties");
$result_faculties = mysqli_query($connection, $query_faculties) or die($connection->error);
$faculty_row = mysqli_fetch_assoc($result_faculties);
$faculties_totalRows = mysqli_num_rows($result_faculties);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Кафедры УТиИт</title>
  <link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css">
</head>
<body>
	<h1>Кафедры УТиИТ</h1>
	<p>&nbsp;</p>
	<hr>
	<p>&nbsp;</p>
<?php if(rowExist($result_faculties)) { do { ?>
	<div class="fullnote">
		<h2>
			<a href="<?php echo makeURL('lecturers', '', 'faculty', $faculty_row['id']); ?>">
			<?php echo $faculty_row['name'] . " (" . $faculty_row['abbrev'] . ")"; ?>
			</a>
		</h2>
    <?php if(userHasRights($RIGHTS['MODERATOR'])) { ?>
      <p><a href="<?php echo makeURL('faculty', 'update', 'faculty', $faculty_row['id']); ?>">Изменить</a> </p>
      <p><a href="<?php echo makeURL('faculty', 'delete', 'faculty', $faculty_row['id']); ?>">Удалить</a> </p>
    <?php } ?>
	</div>
<?php } while ($faculty_row = mysqli_fetch_assoc($result_faculties)); freeResult($result_faculties); ?>            
  <p>Кафедры с 1 по <?php echo $faculties_totalRows ?> </p>
<?php } else { ?>
  <h3>Кафедр пока нет!</h3>
<?php } if(userHasRights($RIGHTS['MODERATOR'])) { ?>
  <p>&nbsp;</p>
  <form action="<?php echo getCurrentURL(); ?>" method="POST" autocomplete="off">
    <p>Кафедра: 
      <input name="fac_name" type="text" class="border-bottom" size="50" maxlength="50">
    </p>
    <p>Аббревиатура: 
      <input name="fac_abbrev" type="text" class="border-bottom" size="10" maxlength="10">
    </p>
    <p>
      <input type="submit" name="Submit" class="blueButton" value="Добавить">
      <input type="reset" name="Reset" class="redButton" value="Отмена">
    </p>
  </form>
<?php } ?>
  <p><a href="<?php echo makeURL('groups'); ?>">На список групп</a></p>
<?php includeModule('footer'); ?>