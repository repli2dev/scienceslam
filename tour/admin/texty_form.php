<?php
session_start();
include("config.php");
$dbInstance = new PDO(DBDSN, DBUSER, DBPASS);

include("login.php");

if ($loggedIn >= 0)
{
  $textsQuery = $dbInstance->prepare("SELECT texts.id, texts.text FROM texts");
  $textsQuery->execute();
  $texts = $textsQuery->fetchAll();
  
  $updateQuery = $dbInstance->prepare("UPDATE texts SET text = :text WHERE id = :id");
  foreach($texts as $row)
  {
    $newText = $_POST["texts"][$row["id"]];
    if (strlen($newText) > 0 && $newText != $row["text"])
    {
      $updateQuery->bindValue(':id', $row["id"], PDO::PARAM_INT);
      $updateQuery->bindValue(':text', $newText, PDO::PARAM_STR);
      $updateQuery->execute();
    }
  }
}
header("location: /admin.php?section=texty");
?>