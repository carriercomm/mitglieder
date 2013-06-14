<?php

@session_start();

if (isset($_REQUEST["mitgliedid"]) && isset($_REQUEST["token"])) {
	$mitgliedid = stripslashes($_REQUEST["mitgliedid"]);
	$token = stripslashes($_REQUEST["token"]);

	if (hash_hmac("md5", $mitgliedid, VPANEL_SHARED) == $token) {
		$_SESSION["mv_mitgliedid"] = $mitgliedid;

		header("Location: index.php");
		exit;
	} else {
		die("Invalid token!");
	}
}

function requireAuth() {
	if (! isset($_SESSION["mv_mitgliedid"])) {
		header("Location: requestAuth.php");
		exit;
	}
}

function getMitgliedDetails() {
	global $vpanel;

	requireAuth();
	return $vpanel->getMitglied($_SESSION["mv_mitgliedid"]);
}

function modifyMitgliedDetails($kommentar, $changes) {
	global $vpanel;

	requireAuth();
	return $vpanel->modifyMitglied($_SESSION["mv_mitgliedid"], $kommentar . " (von " . $_SERVER["REMOTE_ADDR"] . ")", $changes);
}
