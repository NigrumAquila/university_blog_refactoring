<?php 
require_once(__DIR__ . '/../helpers/Authorization.php');
checkAccessRedirect($RIGHTS['MODERATOR']);

$query_group = sprintf("SELECT * FROM groups WHERE id = %s", addslashes($_GET['group']));
$result_group = mysqli_query($connection, $query_group) or die($connection->error);
$group_row = mysqli_fetch_assoc($result_group);
freeResult($result_group);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Предупреждение</title>
	<link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css">
</head>
<body>
	<h2>Предупреждение!</h2>
	<p>В группе <?php echo $group_row['name']; ?> имеются студенты</p>
	<p>
		<a href="<?php echo makeURL('group_cascade', 'delete', 'group', $group_row['id']); ?>">Перейти к удалению группы со студентами</a>
	</p>
	<p><a href="<?php echo makeURL('groups'); ?>">На список групп</a></p>
<?php includeModule('footer'); ?>