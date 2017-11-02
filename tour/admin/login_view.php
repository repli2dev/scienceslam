<form action='login_form.php' method='post'>
  <table id='login'>

    <tr>
      <th colspan='2'>Přihlášení</th>
    </tr>
    <?php
      if ($loginFailed > 0)
      {
        echo "
        <tr>
          <th colspan='2'><img src='images/error.png' alt='Error' /> Nesprávné přihlašovací údaje.</th>
        </tr>";
      }
      if ($loggedOut > 0)
      {
        echo "
        <tr>
          <th colspan='2'><img src='images/ok.png' alt='OK' /> Byl jste úspěšně odhlášen.</th>
        </tr>";
      }
    ?>
    <tr>
      <td>Uživatelské jméno:</td>
      <td><input class='text' name='login' type='text' size='15' /></td>
    </tr>
    <tr>

      <td>Heslo:</td>
      <td><input class='text' name='password' type='password' size='15' /></td>
    </tr>
    <tr>
      <td colspan='2' align='center'><input class='button' type='submit' value='Přihlásit' /></td>
    </tr>
  </table>
</form>
