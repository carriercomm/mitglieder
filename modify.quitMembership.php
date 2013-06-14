<?php

require_once(dirname(__FILE__) . "/config.inc.php");
require_once(dirname(__FILE__) . "/session.inc.php");

requireAuth();
$mitglied = getMitgliedDetails();
$reason = stripslashes($_POST["reason"]);

modifyMitgliedDetails("Austritt gewünscht: " . $reason, array(
	"austritt" => date("Y-m-d", time())
));
