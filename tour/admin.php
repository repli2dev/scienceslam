<?php
session_start();
include("admin/config.php");
$dbInstance = new PDO(DBDSN, DBUSER, DBPASS);

include("admin/login.php");
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>Science slam - administrace</title>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <meta name="language" content="cs" />
  <meta name="author" content="Jiří Klimeš"/>
  <meta name="description" content="Science slam" />
  <meta name="keywords" content="scienceslam" />
  <meta name="copyright" content="Science slam" />
  <meta name="robots" content="index,follow" />
  <link rel="stylesheet" href="/admin/css/admin.css" type="text/css" media="all" />
  <script type="text/javascript" src="/admin/js/jquery.js"></script>
  <script type="text/javascript" src="/admin/js/easyTooltip.js"></script>
  <script src="https://cdn.ckeditor.com/ckeditor5/1.0.0-alpha.1/classic/ckeditor.js"></script>
  <link rel="shortcut icon" href="/images/favicon.png" />
</head>
<body>
<div id="header">
  <div id="header_left"></div>
    <a href="/admin.php" id="logo"></a>
  <div id="header_right"></div>
  <div id="menu">
<?php
if ($loggedIn >= 0)
{
	echo "<ul>
    <li class='parent'><a href='/tour/admin.php'>Kontaktní formulář</a></li>
    <li class='parent'><a href='/tour/admin.php?section=texty'>Texty</a></li>
    <li class='parent'><a href='/tour/admin.php?section=mapa'>Mapa</a></li>
    <li class='parent'><a href='/tour/admin.php?section=uzivatele'>Uživatelé</a></li>
    <li class='parent'><a href='/tour/admin.php?logout=1'>Logout</a></li>
  </ul>";
}
?>
  </div>
</div>

<div id="content_left">
<div id="content_right">
<div id="content_help">
<div id="content">
&nbsp;<br />
<?php
if ($loggedIn < 0)
{
	include("admin/login_view.php");
}
else
{
  switch (strtolower($_GET["section"])) {
    case "texty":
      include("admin/texty_view.php");
      break;
    case "mapa":
      include("admin/map_view.php");
      break;
    case "uzivatele":
      include("admin/users_view.php");
      break;
    default:
      include("admin/contacts_view.php");
  }
}
?>
</div>
</div>
</div>
</div>
<div id="bottom">
  <div id="bottom_left"></div>
  <div id="copyright">© 2017 <a href="https://www.muni.cz">Science slam</a></div>
  <div id="bottom_right"></div>
</div>
</body>
</html>
