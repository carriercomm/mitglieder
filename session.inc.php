<?php

@session_start();

if (isset($_REQUEST["mitgliedid"]) && isset($_REQUEST["token"])) {
	$mitgliedid = stripslashes($_REQUEST["mitgliedid"]);
	$token = stripslashes($_REQUEST["token"]);

	if (getMitgliedToken($mitgliedid) == $token) {
		$_SESSION["mv_mitgliedid"] = $mitgliedid;
	} else {
		die("Invalid token!");
	}
}

function isAuthed() {
	return isset($_SESSION["mv_mitgliedid"]);
}

function getMitgliedToken($mitgliedid = null) {
	if ($mitgliedid == null) {
		requireAuth();
		$mitgliedid = $_SESSION["mv_mitgliedid"];
	}

	return hash_hmac("md5", $mitgliedid, VPANEL_SHARED);
}

function requireAuth() {
	if (! isAuthed()) {
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
