<?php

require_once(dirname(__FILE__) . "/config.inc.php");
require_once(dirname(__FILE__) . "/session.inc.php");

unset($_SESSION["mv_mitgliedid"]);
requireAuth();
