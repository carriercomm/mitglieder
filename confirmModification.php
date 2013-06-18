<?php

require_once(dirname(__FILE__) . "/config.inc.php");
require_once(dirname(__FILE__) . "/session.inc.php");

$mitglied = getMitgliedDetails();

if (isset($_REQUEST["changeSuccessful"])) {
	$greeting = "deine Änderungen wurden übernommen";
	include("_header.html.php");
?>
<p class="lead">
 Deine neuen Daten wurden in unsere Mitgliederverwaltung übernommen. Du kannst jetzt wieder <a href="index.php">zurück zu deinen Mitgliedsdaten</a>.
</p>
<?php
	include("_footer.html.php");
	exit;
}

$changeString = stripslashes($_REQUEST["changes"]);
$changeToken = stripslashes($_REQUEST["changeToken"]);
$changeValid = hash_hmac("md5", $mitglied->mitgliedid . "#" . $changeString, CHANGE_SHARED) == $changeToken;

$changes = unserialize(base64_decode($changeString));

if ($changeValid) {
	modifyMitgliedDetails("Online geändert, Mailadresse verifiziert", $changes);

	header("Location: ?changeSuccessful=1");
	exit;
} else {
	die("Invalid Change");
}
