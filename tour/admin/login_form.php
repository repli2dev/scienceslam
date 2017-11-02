<?php
session_start();
include("config.php");
$dbInstance = new PDO(DBDSN, DBUSER, DBPASS);

if(strlen($_POST["login"]) > 0)
{
  $query = $dbInstance->prepare("SELECT * FROM users WHERE username = :username");
  $query->bindValue(':username', $_POST["login"], PDO::PARAM_STR);
  $query->execute();

  $result = $query->fetchAll();
  if (count($result) == 1 && password_verify($_POST["password"], $result[0]["password"]))
  {
    $_SESSION["user"] = $_POST["login"];
    $_SESSION["password"] = $result[0]["password"];
  }
  else
  {
    $_SESSION["loginFailed"] = 1;
  }
}
header("location: /tour/admin/index.php");
?>
