<?php
$mapQuery = $dbInstance->prepare("SELECT * FROM map_locations");
$mapQuery->execute();
$map = $mapQuery->fetchAll();

function getLabelPositionDropdown($id, $selected)
{
  $selectedLower = strtolower($selected);
  return "<select class='text' name=\"map_pos[" . $id . "]\">
    <option value=\"left\"" . ($selectedLower == "left" ? " selected=\"selected\"" : "") . ">Vlevo</option>
    <option value=\"right\"" . ($selectedLower == "right" ? " selected=\"selected\"" : "") . ">Vpravo</option>
    <option value=\"top\"" . ($selectedLower == "top" ? " selected=\"selected\"" : "") . ">Nahoře</option>
    <option value=\"bottom\"" . ($selectedLower == "bottom" ? " selected=\"selected\"" : "") . ">Dole</option>
  </select>";
}
?>

<form action="/admin/map_form.php" method="post">
<table class="edit">
<tr>
	<th colspan="4"><img src="/admin/images/edit.png" alt="Úprava" /> Úprava lokací mapy</th>
</tr>
<tr>
	<th>Název</th>
  <th>X souřadnice</th>
  <th>Y souřadnice</th>
  <th>Poloha štítku</th>
</tr>

<?php
foreach($map as $row)
{
  echo "<tr>
    <td><input class='text' name='map_name[" . $row["id"] . "]' value='" . $row["location_name"] . "' /></td>
    <td><input class='text' name='map_x[" . $row["id"] . "]' value='" . $row["x_coor"] . "' /></td>
    <td><input class='text' name='map_y[" . $row["id"] . "]' value='" . $row["y_coor"] . "' /></td>
    <td>" . getLabelPositionDropdown($row["id"], $row["label_pos"]) . "</td>
  </tr>";
}
?>
<tr>
	<th colspan="4"><img src="/admin/images/plus.png" alt="Přidat" /> Přidat lokaci mapy</th>
</tr>
<tr>
  <td><input class='text' name='map_name[0]' value='' /></td>
  <td><input class='text' name='map_x[0]' value='' /></td>
  <td><input class='text' name='map_y[0]' value='' /></td>
  <td><?php echo getLabelPositionDropdown(0, "left"); ?></td>
</tr>
<tr>
	<th colspan="4" class="center">
		<input type="submit" value="Upravit" class="button" />
	</th>
</tr>
</table>
</form>