<?php
echo '
<form action="users_add_form.php" method="post">
<table class="edit">
<tr>
	<th colspan="2"><img src="images/new.png" alt="Přidání" /> Vytvoření uživatele</th>
</tr>';
if ($_SESSION["error_create_empty"] == 1)
{
	echo'
	<tr>
		<th colspan="2"><img src="images/error.png" alt="KO" /> Některý z povinných údajů nebyl vyplněn.</th>
	</tr>';
	unset($_SESSION["error_create_empty"]);
}
if ($_SESSION["error_create_pass_doesnt_match"] == 1)
{
	echo'
	<tr>
		<th colspan="2"><img src="images/error.png" alt="KO" /> Hesla nesouhlasí, uživatel nebyl vytvořen.</th>
	</tr>';
	unset($_SESSION["error_create_pass_doesnt_match"]);
}
if ($_SESSION["error_create_unique_login"] == 1)
{
	echo'
	<tr>
		<th colspan="2"><img src="images/error.png" alt="KO" /> Uživatelské jméno je již obsazeno.</th>
	</tr>';
	unset($_SESSION["error_create_unique_login"]);
}
echo'
<tr>
	<th>Login</th>
	<td><input type="text" class="text" name="username" autocomplete="off" /></td>
</tr>
<tr>
	<th>Popis</th>
	<td><input type="text" class="text" name="description" autocomplete="off" /></td>
</tr>
<tr>
	<th>Heslo</th>
	<td><input type="password" class="text" name="password" value="" autocomplete="off" /></td>
</tr>
<tr>
	<th>Kontrola hesla</th>
	<td><input type="password" class="text" name="password_check" value="" autocomplete="off" /></td>
</tr>
<tr>
	<th colspan="2" class="center">
		<input type="submit" value="Vytvořit" class="button" />
	</th>
</tr>
</table>
</form>';
?>
