<?php
session_start();
include("config.php");
$dbInstance = new PDO(DBDSN, DBUSER, DBPASS);

include("login.php");

if ($loggedIn >= 0)
{
  $editUserQuery = $dbInstance->prepare("SELECT * FROM users WHERE id = :id");
  $editUserQuery->bindValue(':id', $_POST["edit_id"], PDO::PARAM_INT);
  $editUserQuery->execute();

  $editUser = $editUserQuery->fetch();

  if ($editUser["username"] != $_POST["username"])
  {
    $usernameCheckQuery = $dbInstance->prepare("SELECT * FROM users WHERE username = :username");
    $usernameCheckQuery->bindValue(':username', $_POST["username"], PDO::PARAM_STR);
    $usernameCheckQuery->execute();

    $usernameCheck = $usernameCheckQuery->fetch();
    if ($usernameCheck)
    {
      $_SESSION["error_unique_login"] = 1;
    }
  }

  if ($editUser && !$usernameCheck)
  {
    $query = $dbInstance->prepare("UPDATE users SET username=:username, description=:description WHERE id = :id");
    $query->bindValue(':id', $editUser["id"], PDO::PARAM_INT);
    $query->bindValue(':username', $_POST["username"], PDO::PARAM_STR);
    $query->bindValue(':description', $_POST["description"], PDO::PARAM_STR);
    $query->execute();
    $_SESSION["user_edited"] = 1;

    if (strlen($_POST["password"]) > 0 && $_POST["password"] == $_POST["password_check"])
    {
      $query = $dbInstance->prepare("UPDATE users SET password=:password WHERE id = :id");
      $query->bindValue(':id', $editUser["id"], PDO::PARAM_INT);
      $query->bindValue(':password', password_hash($_POST["password"], PASSWORD_DEFAULT), PDO::PARAM_STR);
      $query->execute();
      $_SESSION["pass_changed"] = 1;
    }
    elseif (strlen($_POST["password"]) > 0)
    {
      $_SESSION["error_pass_doesnt_match"] = 1;
    }
  }
}
header("location: /tour/admin/index.php?section=uzivatele&editUser=" . $_POST["edit_id"]);
?>
