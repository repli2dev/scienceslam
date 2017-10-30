<?php
session_start();
include("admin/config.php");

$recaptcha_url = "https://www.google.com/recaptcha/api/siteverify";
$recaptcha_pk = "6LcUGDUUAAAAAIN64gsNGtwoxwNQ4bqJf6gO-kEE";
$recaptcha_response = $_POST["g-recaptcha-response"];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $recaptcha_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "secret=".$recaptcha_pk."&response=".$recaptcha_response);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec ($ch);

curl_close ($ch);

$server_output_json = json_decode($server_output, true);
if($server_output_json["success"] == true)
{
  $dbInstance = new PDO(DBDSN, DBUSER, DBPASS);
  
  $query = $dbInstance->prepare("INSERT INTO contacts (datetime, email, text, newsletter) VALUES (:datetime, :email, :text, :newsletter)");
  $query->bindValue(":datetime", date("Y-m-d H:i:s"), PDO::PARAM_STR);
  $query->bindValue(":email", $_POST["email"], PDO::PARAM_INT);
  $query->bindValue(":text", $_POST["text"], PDO::PARAM_STR);
  $query->bindValue(":newsletter", $_POST["newsletter"] ? 1 : 0, PDO::PARAM_BOOL);
  $result = $query->execute();
  
  $to = "contact@scienceslam.cz";
  $subject = "Science slam - kontaktní formulář";
  $message = "Z webu Science slam byl odeslán kontaktní formulář:\r\n\r\nEmail: " . $_POST["email"] . "\r\n" . $_POST["text"];
  $headers = "From: contact@scienceslam.cz" . "\r\n" .
      "Reply-To: contact@scienceslam.cz" . "\r\n" .
      "X-Mailer: PHP/" . phpversion();
  
  mail($to, $subject, $message, $headers);
  
  header("location: /#formular-odeslan");
}
else
{
  header("location: /#formular-chyba");
}
?>