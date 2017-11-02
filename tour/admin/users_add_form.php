<?php
session_start();
include("config.php");
$dbInstance = new PDO(DBDSN, DBUSER, DBPASS);

$newUserId = -1;
include("login.php");

if ($loggedIn >= 0)
{
  $usernameCheckQuery = $dbInstance->prepare("SELECT * FROM users WHERE username = :username");
  $usernameCheckQuery->bindValue(':username', $_POST["username"], PDO::PARAM_STR);
  $usernameCheckQuery->execute();

  $usernameCheck = $usernameCheckQuery->fetch();
  $error = false;
  if ($usernameCheck)
  {
    $error = true;
    $_SESSION["error_create_unique_login"] = 1;
  }
  if($_POST["password"] != $_POST["password_check"])
  {
    $error = true;
    $_SESSION["error_create_pass_doesnt_match"] = 1;
  }
  if(strlen($_POST["password"]) <= 0 || strlen($_POST["username"]) <= 0)
  {
    $error = true;
    $_SESSION["error_create_empty"] = 1;
  }
  if(!$error)
  {
    $query = $dbInstance->prepare("INSERT INTO users (username, password, description) VALUES (:username, :password, :description)");
    $query->bindValue(':username', $_POST["username"], PDO::PARAM_STR);
    $query->bindValue(':password', password_hash($_POST["password"], PASSWORD_DEFAULT), PDO::PARAM_STR);
    $query->bindValue(':description', $_POST["description"], PDO::PARAM_STR);
    $query->execute();

    $newUserQuery = $dbInstance->prepare("SELECT * FROM users WHERE username = :username");
    $newUserQuery->bindValue(':username', $_POST["username"], PDO::PARAM_STR);
    $newUserQuery->execute();

    $newUser = $newUserQuery->fetch();
    $newUserId = $newUser["id"];

    $_SESSION["user_created"] = 1;
  }
}
if ($newUserId >= 0)
{
  header("location: /tour/admin.php?section=uzivatele&editUser=" . $newUserId);
}
else
{
  header("location: /tour/admin.php?section=uzivatele&addUser=1");
}
?>
