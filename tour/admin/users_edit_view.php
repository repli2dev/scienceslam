<?php
$editUserQuery = $dbInstance->prepare("SELECT * FROM users WHERE id = :id");
$editUserQuery->bindValue(':id', $_GET["editUser"], PDO::PARAM_INT);
$editUserQuery->execute();

$editUser = $editUserQuery->fetch();
if ($editUser)
{
	echo '
	<form action="users_edit_form.php" method="post">
	<table class="edit">
	<tr>
		<th colspan="2"><img src="images/edit.png" alt="Úprava" /> Úprava uživatele ' . $editUser["username"]. '</th>
	</tr>';
	if ($_SESSION["user_created"] == 1)
	{
		echo'
		<tr>
			<th colspan="2"><img src="images/ok.png" alt="OK" /> Uživatel byl úspěšně vytvořen.</th>
		</tr>';
		unset($_SESSION["user_created"]);
	}
	if ($_SESSION["user_edited"] == 1)
	{
		echo'
		<tr>
			<th colspan="2"><img src="images/ok.png" alt="OK" /> Změny byly úspěšně uloženy.</th>
		</tr>';
  		unset($_SESSION["user_edited"]);
	}
	if ($_SESSION["pass_changed"] == 1)
	{
		echo'
		<tr>
			<th colspan="2"><img src="images/ok.png" alt="OK" /> Heslo bylo změněno.</th>
		</tr>';
  		unset($_SESSION["pass_changed"]);
	}
	if ($_SESSION["error_pass_doesnt_match"] == 1)
	{
		echo'
		<tr>
			<th colspan="2"><img src="images/error.png" alt="KO" /> Heslo nebylo změněno, nesouhlasí kontrola hesla.</th>
		</tr>';
  		unset($_SESSION["error_pass_doesnt_match"]);
	}
	if ($_SESSION["error_unique_login"] == 1)
	{
		echo'
		<tr>
			<th colspan="2"><img src="images/error.png" alt="KO" /> Uživatelské jméno je již obsazeno.</th>
		</tr>';
		unset($_SESSION["error_unique_login"]);
	}
	echo'
	<tr>
		<th>Login</th>
		<td><input type="text" class="text" name="username" value="' . $editUser["username"]. '" autocomplete="off" /></td>
	</tr>
	<tr>
		<th>Popis</th>
		<td><input type="text" class="text" name="description" value="' . $editUser["description"]. '" autocomplete="off" /></td>
	</tr>
	<tr>
		<th>Změnit heslo</th>
		<td><input type="password" class="text" name="password" value="" autocomplete="off" /></td>
	</tr>
	<tr>
		<th>Kontrola hesla</th>
		<td><input type="password" class="text" name="password_check" value="" autocomplete="off" /></td>
	</tr>
	<tr>
		<th colspan="2" class="center">
			<input type="hidden" name="edit_id" value="' . $editUser["id"]. '" />
			<input type="submit" value="Upravit" class="button" />
		</th>
	</tr>
	</table>
	</form><br />';
}
?>
