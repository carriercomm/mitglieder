<?php

require_once(dirname(__FILE__) . "/config.inc.php");
require_once(dirname(__FILE__) . "/session.inc.php");

requireAuth();
$mitglied = getMitgliedDetails();

$changes = array(
	"adresszusatz" => stripslashes($_POST["adresszusatz"]),
	"strasse" => stripslashes($_POST["strasse"]),
	"hausnummer" => stripslashes($_POST["hausnummer"]),
	"email" => stripslashes($_POST["email"]),
);

$token = getMitgliedToken();

$changeString = base64_encode(serialize($changes));
$changeToken = hash_hmac("md5", $mitglied->mitgliedid . "#" . $changeString, CHANGE_SHARED);

ob_start();
include("modifyMail.mail.php");
list($header, $body) = explode("\n\n", ob_get_contents(), 2);
ob_end_clean();

mail($changes["email"], "Änderung deiner Mitgliedsdaten", $body, $header);
