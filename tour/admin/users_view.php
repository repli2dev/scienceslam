<?php
if (isset($_GET["editUser"]) && strlen($_GET["editUser"]) > 0)
{
  include("users_edit_view.php");
}
elseif (isset($_GET["addUser"]) && strlen($_GET["addUser"]) > 0)
{
  include("users_add_view.php");
}

$usersQuery = $dbInstance->prepare("SELECT * FROM users");
$usersQuery->execute();
$users = $usersQuery->fetchAll();
?>

<div class="center">
	<a href='/tour/admin/index.php?section=uzivatele&amp;addUser=1'><img src='images/new.png' alt='Přidat uživatele' /> Přidat uživatele</a>
</div>
<table class="list">
<tr>
	<th>&nbsp;</th>
	<th>Login</th>
	<th>Popis</th>
</tr>

<?php
foreach($users as $row)
{
	echo '
	<tr>
		<td style="width: 30px; text-align: center;"><a href="/tour/admin/index.php?section=uzivatele&amp;editUser=' . $row["id"] .'"><img src="images/edit.png" alt="Upravit" /></a></td>
		<td><strong>' . $row["username"] . '</strong></td>
		<td>' . $row["description"] . '</td>
	</tr>';
}
?>
</table>
