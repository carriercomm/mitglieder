<?php

require_once(dirname(__FILE__) . "/config.inc.php");
require_once(dirname(__FILE__) . "/session.inc.php");

requireAuth();

include("_header.html.php");

$mitglied = getMitgliedDetails();
$revision = $mitglied->latest;

if (isset($revision->kontakt->konto)) {
	if (isset($revision->kontakt->konto->mandate)) {
		$zahlung = "einzug-sepa";
	} else {
		$zahlung = "einzug";
	}
} else if ($revision->beitrag > 0 || $mitglied->schulden > 0) {
	$zahlung = "selbst";
} else {
	$zahlung = "none";
}

$currentBeitrag = null;
$currentBeitragLabel = strftime($revision->beitragtimeformat->format, time());
foreach ($mitglied->beitraege as $beitrag) {
	if ($beitrag->beitrag->label == $currentBeitragLabel) {
		$currentBeitrag = $beitrag;
	}
}

?>
<div class="page-header">
 <h1>Hallo <?php print(isset($revision->natperson) ? $revision->natperson->vorname : $revision->jurperson->label) ?>, <small>und guten Morgen</small></h1>
</div>
<p class="lead">
 Hier kannst du deine Daten in unserer Mitgliederverwaltung einsehen und ändern! Deine Mitgliedsnummer lautet <strong><?php print($mitglied->mitgliedid) ?></strong> und du bist
 <strong><?php print($revision->mitgliedschaft->label) ?></strong> im <strong><?php print($revision->gliederung->label) ?></strong>.
</p>
<div class="btn-group">
 <?php if (!isset($revision->flags->{VPANEL_MAILFLAG})) { ?>
 <button class="btn">Junge Piraten-Mailadresse einrichten</button>
 <?php } ?>
 <?php if ($zahlung != "einzug-sepa") { ?>
 <a href="sepa.php" class="btn">SEPA-Lastschriftmandat erstellen</a>
 <?php } ?>
 <?php if ($zahlung == "selbst" && $currentBeitrag && $currentBeitrag->ausstehend > 0) { ?>
 <button class="btn">Beitragsbefreiung beantragen</button>
 <?php } ?>
 <?php if (!isset($mitglied->austritt)) { ?>
 <button class="btn btn-danger">Austreten</button>
 <?php } ?>
</div>
<form action="index.php" method="post" class="form-horizontal">
 <h3>Stammdaten</h3>
 <p>Wenn du deinen Namen oder dein Geburtsdatum ändern möchtest, sende uns bitte eine Mail an <a href="mailto:mitglieder@junge-piraten.de">mitglieder@junge-piraten.de</a>.
 <div class="control-group">
  <label class="control-label">Name</label>
  <div class="controls">
   <span class="input-xlarge uneditable-input">
    <?php print(htmlentities(isset($revision->natperson) ? $revision->natperson->vorname . " " . $revision->natperson->name : $revision->jurperson->label)) ?>
   </span>
  </div>
 </div>
 <?php if (isset($revision->natperson)) { ?>
 <div class="control-group">
  <label class="control-label">Geburtsdatum</label>
  <div class="controls">
   <span class="input-small uneditable-input">
    <?php print(date("d.m.Y", $revision->natperson->geburtsdatum)) ?>
   </span>
  </div>
 </div>
 <?php } ?>
 <div class="control-group">
  <label class="control-label">Straße</label>
  <div class="controls">
   <input type="text" class="input-xlarge" name="strasse" value="<?php print(htmlentities($revision->kontakt->strasse)) ?>" />
   <input type="text" class="input-mini" name="hausnummer" value="<?php print(htmlentities($revision->kontakt->hausnummer)) ?>" />
  </div>
 </div>
 <div class="control-group">
  <label class="control-label">Adresszusatz</label>
  <div class="controls">
   <input type="text" class="input-large" name="adresszusatz" value="<?php print(htmlentities($revision->kontakt->adresszusatz)) ?>" />
  </div>
 </div>
 <div class="control-group">
  <label class="control-label">Ort</label>
  <div class="controls">
   <?php print(htmlentities($revision->kontakt->ort->plz)) ?>
   <?php print($revision->kontakt->ort->label) ?> (<?php print($revision->kontakt->ort->state->label) ?>)
  </div>
 </div>
 <div class="control-group">
  <label class="control-label">Mailadresse</label>
  <div class="controls">
   <input type="text" class="input-xlarge" name="mail" value="<?php print(htmlentities($revision->kontakt->email->email)) ?>" />
  </div>
 </div>
<!--
 <div class="control-group">
  <label class="control-label">Telefonnummer</label>
  <div class="controls">
   <?php print($revision->kontakt->telefon) ?>
  </div>
 </div>
 <div class="control-group">
  <label class="control-label">Handynummer</label>
  <div class="controls">
   <?php print($revision->kontakt->handy) ?>
  </div>
 </div>
-->
 <?php if ($zahlung != "none") { ?><h3>Zahloptionen</h3><?php } ?>
<?php
switch ($zahlung) {
case "einzug-sepa":
	break;
case "einzug":
	break;
case "selbst":
?><p>Du zahlst per Überweisung oder in Bar auf den Mitgliederversammlungen. Tipp: SEPA ist episch!111 <a href="sepa.php">Jetzt gleich einrichten</a>.</p><?php
	if ($mitglied->schulden > 0) {
?><p>Scheinbar hast du momentan noch nicht alle Beiträge bezahlt</p><ul><?php
		foreach ($mitglied->beitraege as $beitrag) {
			if ($beitrag->ausstehend > 0) {
				?><li><?php print(htmlentities($beitrag->beitrag->label)) ?> (<?php print(sprintf("%.2f", $beitrag->ausstehend)) ?> €)</li><?php
			}
		}
?></ul>
<p>Bitte überweise deine offenen Posten so schnell wie möglich auf unser Konto:</p>
<dl class="dl-horizontal">
 <dt>Kontoinhaber</dt>
 <dd>Junge Piraten e.V.</dd>
 <dt>Konto</dt>
 <dd>6016506900</dd>
 <dt>BLZ</dt>
 <dd>43060967 (GLS Gemeinschaftsbank)</dd>
 <dt>IBAN</dt>
 <dd>DE76 4306 0967 6016 5069 00</dd>
 <dt>BIC</dt>
 <dd>GENODEM1GLS</dd>
 <dt>Verwendungszweck</dt>
 <dd></dd>
</div>
<?php
	}
	break;
}
?>

 <div class="form-actions">
 </div>
</form>
<?php
include("_footer.html.php");
?>
