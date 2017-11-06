<?php
$contactsQuery = $dbInstance->prepare("SELECT * FROM contacts ORDER BY id DESC");
$contactsQuery->execute();
$contacts = $contactsQuery->fetchAll();
?>

<table class="list">
<tr>
	<th>Datum</th>
	<th>Email</th>
  <th>Text</th>
  <th>Newsletter</th>
</tr>

<?php
foreach($contacts as $row)
{
	echo "
	<tr>
		<td>" . date("j.n.Y H:i:s", strtotime($row["datetime"])) . "</td>
		<td><a href='mailto:" . htmlspecialchars($row["email"]) . "'>" . htmlspecialchars($row["email"]) . "</a></td>
		<td>" . htmlspecialchars($row["text"]) . "</td>
		<td style=\"width: 60px; text-align: center;\">";
      if ($row["newsletter"])
      {
        echo "<img src=\"images/ok.png\" alt=\"Ano\" />";
      }
      else
      {
        echo "<img src=\"images/error.png\" alt=\"Ne\" />";
      }
    echo"</td>
	</tr>";
}
?>
</table>
