<?php
$textsQuery = $dbInstance->prepare("SELECT sections.name as section_name, sections.tooltip as section_tooltip, texts.id, texts.section_id, texts.name, texts.tooltip, texts.text, texts.is_wysiwyg
  FROM texts LEFT JOIN sections ON texts.section_id = sections.id
  ORDER BY sections.priority DESC, texts.priority DESC");
$textsQuery->execute();
$texts = $textsQuery->fetchAll();

?>

<form action="/tour/admin/texty_form.php" method="post">
<table class="edit">
<tr>
	<th colspan="2"><img src="/tour/admin/images/edit.png" alt="Úprava" /> Úprava textů</th>
</tr>

<?php
$lastSectionId = -1;
foreach($texts as $row)
{
  if ($lastSectionId != $row["section_id"])
  {
    $lastSectionId = $row["section_id"];
    echo "<tr>
      <th colspan='2'>";
      if (strlen($row["section_tooltip"]) > 0)
      {
        echo "
		      <abbr title='" . $row["section_tooltip"] ."'>
		        " . $row["section_name"] . "&nbsp;<img src='/tour/admin/images/question_mark.gif' />
		      </abbr>";
      }
      else
      {
        echo $row["section_name"];
      }
      echo "</th>
    </tr>";
  }
  echo "
  <tr>";
    echo"<th>";
    if (strlen($row["tooltip"]) > 0)
    {
      echo "
	      <abbr title='" . $row["tooltip"] ."'>
	        " . $row["name"] . "&nbsp;<img src='images/question_mark.gif' />
	      </abbr>";
    }
    else
    {
      echo $row["name"];
    }
    echo"</th>
      <td>";
    if ($row["is_wysiwyg"])
    {
      echo "<textarea id='ckeditor" . $row["id"] . "' name='texts[" . $row["id"] . "]'>" . $row["text"] . "</textarea>";
    }
    else
    {
      echo "<input type='text' class='text' name='texts[" . $row["id"] . "]' value='" . $row["text"] . "' />";
    }
      echo"</td>
    </tr>";
}
?>

<tr>
	<th colspan="2" class="center">
		<input type="submit" value="Upravit" class="button" />
	</th>
</tr>
</table>
</form>
<script type="text/javascript" >
$(function(){
  $("form textarea").each(function(){
    ClassicEditor
      .create(document.querySelector("#" + $(this).attr("id")), {
        removePlugins: [ "Heading" ],
        toolbar: [ "bold", "italic", "link", "bulletedList", "numberedList" ]
      })
      .catch( error => { console.error(error); });
  });
});
</script>
