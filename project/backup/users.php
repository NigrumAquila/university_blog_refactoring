<?php 
require_once(__DIR__ . '/helpers/Authorization.php');
checkAccessRedirect($RIGHTS['ADMIN']);
clearKeySession('userExist');

$query_users = "SELECT * FROM users ORDER BY name ASC";
$result_users = mysqli_query($connection, $query_users) or die($connection->error);
$user_row = mysqli_fetch_assoc($result_users);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Список пользователей</title>
	<link href="<?php echo getPathToModule('css'); ?>" rel="stylesheet" type="text/css">
</head>
<body>
	<h1>Список пользователей</h1>
	<p>Страничка администратора, предназначенная для управления пользователями. </p>
	<table width="500" border="1" cellspacing="2" cellpadding="1">
		<caption>
			Список пользователей
		</caption>
		<tr>
			<th width="120" scope="col">Пользователь</th>
			<th width="80" scope="col">Права</th>
			<th width="120" scope="col">Редактировать</th>
			<th width="100" scope="col">Удалить</th>
		</tr>
		<?php do { ?>
		<tr>
			<td class="center"><?php echo $user_row['name']; ?></td>
			<td class="center"><?php echo $user_row['rights']; ?></td>
			<td class="center">
				<a href="<?php echo makeURL('user', 'update', 'user', $user_row['id']); ?>">Изменить</a>
			</td>
			<td class="center">
				<a href="<?php echo makeURL('user', 'delete', 'user', $user_row['id']); ?>">Удалить</a>
			</td>
		</tr>
		<?php } while ($user_row = mysqli_fetch_assoc($result_users)); freeResult($result_users); ?>
	</table>
	<p><a href="<?php echo makeURL('user', 'insert'); ?>">Добавить пользователя</a></p> 
<?php includeModule('footer'); ?>