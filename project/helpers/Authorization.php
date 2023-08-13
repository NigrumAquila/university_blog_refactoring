<?php
require_once(__DIR__ . '/../consts/rights.php');
require_once(__DIR__ . '/Path.php');
require_once(__DIR__ . '/Session.php');
require_once(__DIR__ . '/Redirect.php');
require_once(__DIR__ . '/DB.php');
require_once(__DIR__ . '/URL.php');
require_once(__DIR__ . '/../connections/StudentsDate.php');
startSessionIfNot();

function userIsLoggedIn(): bool {
  return isset($_SESSION['userRights']);
}

function userHasRights(array $requiredRights): bool {
  $userRights = userIsLoggedIn() ? $_SESSION['userRights'] : NULL;
  return in_array($userRights, $requiredRights);
}

function checkAccessRedirect(array $requiredRights): void {
  if(!(userIsLoggedIn() && userHasRights($requiredRights))) {
    setKeySession('prevURL', getCurrentURL());
    $nextURL = makeURL('login');
    redirectTo($nextURL);
  }
}

function login(string $username, string $password): void {
  global $connection;

  $query_user = sprintf("SELECT name, password, rights 
    FROM users WHERE name = %s AND password = %s", 
		GetSQLValueString($username, "text"),
		GetSQLValueString($password, "text"));
  $result_user = mysqli_query($connection, $query_user) or die($connection->error);
  $nextURL = successfullyLoggedIn($result_user) ? getAndClearKeySession('prevURL', makeURL('index')) : makeURL('login');
  freeResult($result_user);
  redirectTo($nextURL);
}

function successfullyLoggedIn(object $result_user): bool {
  if(rowExist($result_user)) {
    $user_row = mysqli_fetch_assoc($result_user);
    setKeysSession(['username', 'userRights'], [$user_row['name'], $user_row['rights']]);
    clearKeySession('userExist');
    return true;
  } 
  setKeySession('userExist', false);
  return false;
}

function checkUserExist(string $username): bool {
  global $connection;

  $query_user = sprintf("SELECT name FROM users WHERE name = %s", GetSQLValueString($username, "text"));
  $result_user = mysqli_query($connection, $query_user) or die($connection->error);
  setKeySession('userExist', rowExist($result_user));
  freeResult($result_user);
  return $_SESSION['userExist'];
}
?>