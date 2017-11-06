<?php
$loggedIn = -1;
$loginFailed = -1;
$loggedOut = -1;

if(isset($_GET["logout"]) && strlen($_GET["logout"]) > 0)
{
  session_destroy();
  $_SESSION = [];
  $loggedOut = 1;
}
if (isset($_SESSION["user"]) && strlen($_SESSION["user"]) > 0)
{
  $query = $dbInstance->prepare("SELECT * FROM users WHERE username = :username");
  $query->bindValue(':username', $_SESSION["user"], PDO::PARAM_STR);
  $query->execute();

  $result = $query->fetchAll();
  if (count($result) == 1 && isset($_SESSION["password"]) && $_SESSION["password"] == $result[0]["password"])
  {
    $loggedIn = $result[0]["id"];
  }
}
if (isset($_SESSION["loginFailed"]) && strlen($_SESSION["loginFailed"]) == 1)
{
  $loginFailed = 1;
  unset($_SESSION["loginFailed"]);
}
?>