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
		<td>" . $row["email"] . "</td>
		<td>" . $row["text"] . "</td>
		<td style=\"width: 60px; text-align: center;\">";
      if ($row["newsletter"])
      {
        echo "<img src=\"/tour/admin/images/ok.png\" alt=\"Ano\" />";
      }
      else
      {
        echo "<img src=\"/tour/admin/images/error.png\" alt=\"Ne\" />";
      }
    echo"</td>
	</tr>";
}
?>
</table>
