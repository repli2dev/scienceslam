<?php
include("admin/config.php");
$dbInstance = new PDO(DBDSN, DBUSER, DBPASS);

$textsQuery = $dbInstance->prepare("SELECT texts.id, texts.text FROM texts");
$textsQuery->execute();
$textsArray = $textsQuery->fetchAll();

$texts = array();
$textsStripped = array();
$textsReplaced = array();
foreach($textsArray as $row)
{
  $texts[$row["id"]] = $row["text"];
  $textsStripped[$row["id"]] = stripParagraphTags($row["text"]);
  $textsReplaced[$row["id"]] = stripParagraphTags(str_ireplace("</p><p>", "<br />", $row["text"]));
}

function stripParagraphTags($text)
{
  return preg_replace("/<\\/?p(\\s+.*?>|>)/", "", $text);
}
?><!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Science slam Tour</title>
  <meta name="description" content="Science slam je projekt Masarykovy univerzity, jehož cílem je osvětlit probíhající výzkum lidem.">
  <meta name="keywords" content="Science slam, věda, výzkum, osvětlujeme vědu, jak se dělá věda, co se zkoumá">
  <meta property="og:type" content="website">
  <meta property="og:image" content="https://scienceslam.muni.cz/images/blocks/metaphoto.jpg">
  <link href="css/MyFontsWebfontsKit.css" rel="stylesheet">
  <link href="css/style.css?v=2" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
  <script type="text/javascript">
  $(function() {
    if (document.location.hash === "#formular-odeslan")
    {
      $("#popup .contact-ok").show();
      $("#popup").fadeIn();
      document.location.hash = "";
    }
    if (document.location.hash === "#formular-chyba")
    {
      $("#popup .contact-error").show();
      $("#popup").fadeIn();
      document.location.hash = "";
    }
    
    function handleCheckboxChange()
    {
      var checkbox = $("input[name=newsletter]");
      checkbox.closest(".checkbox").toggleClass("checked", checkbox.is(":checked"));
    }
    handleCheckboxChange();
    
    $(".checkbox").click(function() {
      var checkboxInput = $("input[type=checkbox]", $(this));
      checkboxInput.prop("checked", !checkboxInput.is(":checked"));
      handleCheckboxChange();
    });
    $("input[name=newsletter]").change(function() {
      handleCheckboxChange();
    });
    $(".marker-pos-bottom,.marker-pos-top").each(function(){
      var leftPos = 18 - Math.round($(this).width() / 2);
      $(this).css("left", leftPos + "px");
    });
    
    $("#popup #cross").click(function() {
      $("#popup").fadeOut();
    });
    $("form").submit(function(e) {
       var recaptcha = $("#g-recaptcha-response").val();
       if (recaptcha === "") {
          $("#popup .contact-ok").hide();
          $("#popup .contact-error").hide();
          $("#popup .contact-captcha").show();
          $("#popup").fadeIn();
          e.preventDefault();
       }
    });
  });
  </script>
  <script src="https://www.google.com/recaptcha/api.js"></script>
  <link rel="shortcut icon" href="/images/favicon.png" />
</head>

<body>
<div id="main-container">
<!-- CONFIRMATION POPUP -->
<div id="popup">
  <div id="cross">&#x2717;</div>
  <div class="contact-ok" style="display: none;"><?php echo $textsStripped[33]; ?></div>
  <div class="contact-error" style="display: none;"><?php echo $textsStripped[41]; ?></div>
  <div class="contact-captcha" style="display: none;"><?php echo $textsStripped[42]; ?></div>
</div>
<div id="bulb-container"><div id="bulb"></div></div>
<!-- HEADER -->
<section id="header">
  <div class="header-container">&nbsp;
    <ul>
      <li><a href="#news"><?php echo $textsStripped[7]; ?></a></li>
      <li><a href="#science-video"><?php echo $textsStripped[12]; ?></a></li>
      <li><a href="#chci-science-slam"><?php echo $textsStripped[18]; ?></a></li>
      <li><a href="#jak-to-probehne"><?php echo $textsStripped[20]; ?></a></li>
    </ul>
    <p id="claim"><?php echo $textsStripped[1]; ?></p>
  </div>
</section>
<!-- SCIENCE SLAM TOUR -->
<section id="tour">
  <div class="content-container">
    <h2><?php echo $textsStripped[2]; ?></h2>
    <div class="f-left">
      <p class="column"><?php echo $textsStripped[3]; ?></p>
      <a href="<?php echo $textsStripped[6]; ?>" class="button"><?php echo $textsStripped[34]; ?></a>
    </div>
    <div class="two-columns-container f-left">
      <div class="two-columns">
        <?php echo $texts[4]; ?>
      </div>
      <div id="map">
        <?php
        $mapQuery = $dbInstance->prepare("SELECT * FROM map_locations");
        $mapQuery->execute();
        $map = $mapQuery->fetchAll();
        foreach($map as $row)
        {
          echo "<div class=\"map-marker\" style=\"left: " . $row["x_coor"] . "px; top: " . $row["y_coor"] . "px\"><div class=\"marker-pos-" . $row["label_pos"] . "\">" . $row["location_name"] . "</div></div>";
        }
        ?>
      </div>
    </div>
    
    <div class="content-claim f-right">
      <div class="yellow-box"></div>
      <?php echo $textsStripped[5]; ?>
    </div>
    <div class="clearer"></div>
  </div>
</section>
<!-- AKTUALITY -->
<section id="news">
  <div class="yellow-container">
    <div class="content-container">
      <div class="news-cover">
        <img src="images/cover.jpg" alt="Science slam" />
        <?php echo $texts[10]; ?>
        <a href="<?php echo $textsStripped[11]; ?>" class="button"><?php echo $textsStripped[39]; ?></a>
      </div>
      
      <div class="two-columns-container white-border-container f-right">
        <div class="f-left column white-border"></div><div class="f-left column white-border m-right-0"></div>
        <div class="clearer"></div>
      </div>
      <div class="clearer"></div>
      
      <div class="two-columns-container f-right">
        <h2><?php echo $textsStripped[7]; ?></h2>
        <div class="two-columns">
          <?php echo $texts[8]; ?>
        </div>
        
        <a href="<?php echo $textsStripped[9]; ?>" class="button"><?php echo $textsStripped[38]; ?></a>
      </div>
      <div class="clearer"></div>
    </div>
  </div>
</section>
<!-- CO JE SCIENCE SLAM -->
<section id="science-video">
  <div class="content-container">
    <h2><?php echo $textsStripped[12]; ?></h2>
    <div class="f-left">
      <p class="column"><?php echo $textsStripped[13]; ?></p>
      <div class="content-claim">
        <?php echo $textsStripped[16]; ?>
      </div>
      <a href="<?php echo $textsStripped[17]; ?>" class="button"><?php echo $textsStripped[35]; ?></a>
    </div>
    <div class="video-container f-left">
      <div id="video">
        <object data="https://www.youtube.com/embed/<?php echo $textsStripped[14]; ?>"></object>
      </div>
      <div class="two-columns-container f-right">
        <div class="two-columns">
          <?php echo $texts[15]; ?>
        </div>
      </div>
    </div>
    <div class="clearer"></div>
  </div>
</section>
<!-- CHCI SCIENCE SLAM VE ŠKOLE -->
<section id="chci-science-slam">
  <div class="yellow-container">
    <div class="content-container">
      <h2 class="large-header"><?php echo $textsStripped[18]; ?></h2>
      <form action="contact_form.php" method="POST">
        <input type="email" class="textbox" name="email" placeholder="Váš email" />
        <div class="checkbox-container">
          <div class="checkbox">
            <input type="checkbox" name="newsletter" />
          </div>
          <label for="newsletter">Chci dostávat NEWSLETTER</label>
        </div>
        <textarea name="text"><?php echo str_replace("<br />", "&#13;&#10;", $textsReplaced[19]); ?></textarea>
	<div class="g-recaptcha" data-sitekey="6Le9ijYUAAAAABY-OMZWk6bwR86hivSSuy_1F235" data-callback="enableBtn"></div>
        <input type="submit" class="button" value="Objednat Science slam" />
      </form>
    </div>
  </div>
</section>
<!-- PODROBNÉ INFORMACE -->
<section id="jak-to-probehne">
  <div class="content-container">
    <h2><?php echo $textsStripped[20]; ?></h2>
    <div class="f-left">
      <p class="column"><?php echo $textsStripped[21]; ?></p>
      <a href="<?php echo $textsStripped[24]; ?>" class="button"><?php echo $textsStripped[36]; ?></a>
    </div>
    <div class="two-columns-container f-left">
      <div class="two-columns">
        <?php echo $texts[22]; ?>
      </div>
    </div>
    
    <div class="content-claim f-right">
      <div class="yellow-box"></div>
      <?php echo $textsStripped[23]; ?>
    </div>
    <div class="clearer"></div>
  </div>
</section>
<!-- VÍCE O SCIENCE SLAMU -->
<section id="more-info">
  <div class="yellow-container">
    <div class="content-container">
      <h2 class="large-header"><?php echo $textsStripped[25]; ?></h2>
      <?php echo $texts[26]; ?>
      
      <a href="<?php echo $textsStripped[27]; ?>" class="button"><?php echo $textsStripped[37]; ?></a>
    </div>
  </div>
</section>
<!-- FOOTER -->
<section id="footer">
  <div class="content-container">
    <p><?php echo $textsReplaced[28]; ?></p>
    <p class="m-right-0"><?php echo $textsReplaced[29]; ?></p>
    <a href="<?php echo $textsStripped[40]; ?>" target="_blank" class="footer-button"><img src="images/button-instagram.png" alt="Science slam Instagram" /></a>
    <a href="<?php echo $textsStripped[32]; ?>" target="_blank" class="footer-button"><img src="images/button-youtube.png" alt="Science slam Youtube" /></a>
    <a href="<?php echo $textsStripped[31]; ?>" target="_blank" class="footer-button"><img src="images/button-facebook.png" alt="Science slam Facebook" /></a>
    <a href="<?php echo $textsStripped[30]; ?>" target="_blank" class="footer-button"><img src="images/button-mail.png" alt="Science slam mail" /></a>
    <div class="clearer"></div>
  </div>
</section>
</div>
</body>

</html>
