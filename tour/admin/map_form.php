<?php
session_start();
include("config.php");
$dbInstance = new PDO(DBDSN, DBUSER, DBPASS);

include("login.php");

if ($loggedIn >= 0)
{
  $mapQuery = $dbInstance->prepare("SELECT * FROM map_locations");
  $mapQuery->execute();
  $map = $mapQuery->fetchAll();
  
  $updateQuery = $dbInstance->prepare("UPDATE map_locations SET location_name = :location_name, x_coor = :x_coor, y_coor = :y_coor, label_pos = :label_pos WHERE id = :id");
  foreach($map as $row)
  {
    $updateQuery->bindValue(':id', $row["id"], PDO::PARAM_INT);
    $updateQuery->bindValue(':location_name', $_POST["map_name"][$row["id"]], PDO::PARAM_STR);
    $updateQuery->bindValue(':x_coor', $_POST["map_x"][$row["id"]], PDO::PARAM_INT);
    $updateQuery->bindValue(':y_coor', $_POST["map_y"][$row["id"]], PDO::PARAM_INT);
    $updateQuery->bindValue(':label_pos', $_POST["map_pos"][$row["id"]], PDO::PARAM_STR);
    $updateQuery->execute();
  }
  if (strlen($_POST["map_name"][0]) > 0)
  {
    $insertQuery = $dbInstance->prepare("INSERT INTO map_locations (location_name, x_coor, y_coor, label_pos) VALUES (:location_name, :x_coor, :y_coor, :label_pos)");
    $insertQuery->bindValue(':location_name', $_POST["map_name"][0], PDO::PARAM_STR);
    $insertQuery->bindValue(':x_coor', $_POST["map_x"][0], PDO::PARAM_INT);
    $insertQuery->bindValue(':y_coor', $_POST["map_y"][0], PDO::PARAM_INT);
    $insertQuery->bindValue(':label_pos', $_POST["map_pos"][0], PDO::PARAM_STR);
    $insertQuery->execute();
  }
}
header("location: /tour/admin.php?section=mapa");
?>
