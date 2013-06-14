<?php

require_once(dirname(__FILE__) . "/config.inc.php");
require_once(dirname(__FILE__) . "/session.inc.php");

requireAuth();

include("_header.html.php");

$mitglied = getMitgliedDetails();
$revision = $mitglied->latest;

?>
<div class="page-header">
 <h1>Hallo <?php print(isset($revision->natperson) ? $revision->natperson->vorname : $revision->jurperson->label) ?>, <small>und guten Morgen</small></h1>
</div>
<p class="lead">
 Hier kannst du deine Daten in unserer Mitgliederverwaltung einsehen und ändern!
</p>
<?php
include("_footer.html.php");
?>
