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
 <?php if (!isset($mitglied->austritt)) { ?>
 <?php if (!isset($revision->flags->{VPANEL_MAILFLAG})) { ?>
 <button class="btn addMail">Junge Piraten-Mailadresse einrichten</button>
 <?php } ?>
 <?php if ($zahlung != "einzug-sepa") { ?>
 <a href="sepa.php" class="btn createSepa">SEPA-Lastschriftmandat erstellen</a>
 <?php } ?>
 <?php if ($zahlung == "selbst" && $currentBeitrag && $currentBeitrag->ausstehend > 0) { ?>
 <button class="btn requestExemption">Beitragsbefreiung beantragen</button>
 <?php } ?>
 <button class="btn btn-danger quitMembership">Austreten</button>
 <?php } ?>
</div>
<form action="index.php" method="post" class="form-horizontal modifyMitglied">
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
   <input type="text" class="input-xlarge" name="email" value="<?php print(htmlentities($revision->kontakt->email->email)) ?>" />
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
 <div class="form-actions">
  <button class="btn btn-primary submit">Ändern</button>
 </div>
</form>

 <?php if ($zahlung != "none") { ?><h3>Zahloptionen</h3><?php } ?>
<?php
switch ($zahlung) {
case "einzug-sepa":
?>
<p>Wir ziehen deinen Mitgliedsbeitrag via SEPA immer zum Jahresbeginn ein. Wir werden dich etwa eine Woche vor Einzugstermin daran erinnern.</p>
<form class="form-horizontal">
 <div class="control-group">
  <label class="control-label">Kontoinhaber</label>
  <div class="controls">
   <span class="input-xlarge uneditable-input"><?php print(htmlentities($revision->kontakt->konto->inhaber)) ?></span>
  </div>
 </div>
 <div class="control-group">
  <label class="control-label">IBAN</label>
  <div class="controls">
   <span class="input-xxlarge uneditable-input"><?php print($revision->kontakt->konto->iban) ?></span>
  </div>
 </div>
 <div class="control-group">
  <label class="control-label">Kontoinhaber</label>
  <div class="controls">
   <span class="input-medium uneditable-input"><?php print($revision->kontakt->konto->bic) ?></span>
  </div>
 </div>
 <div class="control-group">
  <label class="control-label">GläubigerID</label>
  <div class="controls">
   <span class="input-medium uneditable-input">DE03 ZZZ0 0000 2504 66</span>
  </div>
 </div>
 <div class="control-group">
  <label class="control-label">Mandatsreferenznummer</label>
  <div class="controls">
   <span class="input-medium uneditable-input">(wird mitgeteilt)</span>
  </div>
 </div>
</form>
<?php
	break;
case "einzug":
?>
<p>Wir ziehen deinen Beitrag mittels der konventionellen Lastschrift ein. Leider läuft dieses Verfahren zum 1.2.2014 aus und wird durch SEPA-Lastschriften abgelöst. Wenn uns bis dahin kein
 SEPA-Mandat von dir vorliegt, musst du deinen Beitrag wie bisher überweisen oder in Bar auf Mitgliederversammlungen zahlen. Am besten füllst du also <a href="#" class="createSepa">gleich
 ein SEPA-Mandat aus</a>.</p>
<form class="form-horizontal">
 <div class="control-group">
  <label class="control-label">Kontoinhaber</label>
  <div class="controls">
   <span class="input-xlarge uneditable-input"><?php print(htmlentities($revision->kontakt->konto->inhaber)) ?></span>
  </div>
 </div>
 <div class="control-group">
  <label class="control-label">Kontonummer</label>
  <div class="controls">
   <span class="input-large uneditable-input"><?php print(substr($revision->kontakt->konto->iban,13,10)) ?></span>
  </div>
 </div>
 <div class="control-group">
  <label class="control-label">Bankleitzahl</label>
  <div class="controls">
   <span class="input-medium uneditable-input"><?php print(substr($revision->kontakt->konto->iban,3,8)) ?></span>
  </div>
 </div>
</form>
<?php
	// TODO Infotext
	break;
case "selbst":
?>
<p>Du zahlst per Überweisung oder in Bar auf den Mitgliederversammlungen. Tipp: Wenn du uns ein SEPA-Lastschriftmandat erteilst, können wir deinen Mitgliedsbeitrag immer pünktlich abbuchen.
 Das spart dir und uns Ärger und Aufwand. <a href="#" class="createSepa">Jetzt gleich einrichten</a>.</p><?php
	if ($mitglied->schulden > 0) {
?><p>Scheinbar hast du momentan noch nicht alle Beiträge bezahlt. Bitte überweise deine offenen Posten so schnell wie möglich auf unser unten stehendes Konto:</p><ul><?php
		$labels = array();
		foreach ($mitglied->beitraege as $beitrag) {
			if ($beitrag->ausstehend > 0) {
				$labels[] = htmlentities($beitrag->beitrag->label);
				?><li><?php print(htmlentities($beitrag->beitrag->label)) ?> (<?php print(sprintf("%.2f", $beitrag->ausstehend)) ?> €)</li><?php
			}
		}
?></ul>
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
 <dd><?php print(implode("<br/>", $labels)) ?></dd>
</dl>
<?php
	}
	break;
}
?>

<?php if (isset($revision->natperson)) { ?>
<div class="addMailModal modal hide">
 <div class="modal-header">
  <button class="close" data-dismiss="modal">&times;</button>
  <h3>Junge Piraten-Mailadresse einrichten</h3>
 </div>
 <div class="modal-body step1">
  <p>Wenn du möchtest, können wir dir eine Mailadresse <strong><?php print(strtolower($revision->natperson->vorname . "." . $revision->natperson->name)) ?>@junge-piraten.de</strong> einrichten, die
   du für deine Mitarbeit benutzen kannst.</p>
  <p>Du kannst auch deine Mitgliedermails auf diese Adresse einrichten, aber warte damit bitte noch, bis die Mailadresse eingerichtet wurde. Das kann bis zu <strong>vier Stunden</strong> dauern und
   du wirst zum Abschluss eine Mail an deine bisherige Mailadresse bekommen.</p>
 </div>
 <div class="modal-footer step1">
  <button class="btn" data-dismiss="modal">Abbrechen</button>
  <button class="btn btn-primary submit">Ok</button>
 </div>
 <div class="modal-body step2 hide">
  <p>Wir haben die Einrichtung deiner Mailadresse veranlasst. Bitte warte noch damit sie zu benutzen bis du eine Bestätigungsmail erhalten hast.</p>
 </div>
 <div class="modal-footer step2 hide">
  <button class="btn btn-primary submit">Ok</button>
 </div>
</div>
<?php } ?>

<div class="createSepaModal modal hide">
 <form action="modify.createSepa.php" method="post" target="_blank" class="form-horizontal">
  <div class="modal-header">
   <button class="close" data-dismiss="modal">&times;</button>
   <h3>SEPA-Mandat erzeugen</h3>
  </div>
  <div class="modal-body step1">
   <p>Leider sind wir dazu verpflichtet, das SEPA-Mandat im handschriftlich Unterschriebenen Original aufzubewahren. Daher werden wir dir hier ein druckfertiges PDF generieren, dass du nurnoch unterschreiben
    und abschicken musst. Wenn du einen Briefumschlag mit Sichtfenster besitzt, drucken wir dir auf Wunsch eine Briefmarke auf, damit du den Brief einfach in einen Briefkasten werfen kannst.</p>
   <p>Deine IBAN und die BIC findest du auf allen Kontoauszügen sowie auf allen neueren EC-Karten deiner Bank.</p>
   <div class="control-group">
    <label class="control-label" for="inhaber">Kontoinhaber:</label>
    <div class="controls">
     <input type="text" name="inhaber" class="input-xlarge" value="<?php print(isset($revision->kontakt->konto) ? $revision->kontakt->konto->inhaber : (isset($revision->natperson) ? $revision->natperson->vorname . " " . $revision->natperson->name : $revision->jurperson->label)) ?>" />
    </div>
   </div>
   <div class="control-group">
    <label class="control-label" for="iban">IBAN:</label>
    <div class="controls">
     <input type="text" name="iban" class="input-xlarge" value="<?php print(isset($revision->kontakt->konto) ? $revision->kontakt->konto->iban : "") ?>" />
    </div>
   </div>
   <div class="control-group">
    <label class="control-label" for="iban">BIC:</label>
    <div class="controls">
     <input type="text" name="bic" class="input-medium" />
    </div>
   </div>
   <div class="control-group">
    <div class="controls">
     <label for="frankIt">
      <input type="checkbox" name="frankIt" />
      Bitte frankiert mein Mandat
     </label>
    </div>
   </div>
  </div>
  <div class="modal-footer step1">
   <button class="btn" data-dismiss="modal">Abbrechen</button>
   <button class="btn btn-primary submit">Ok</button>
  </div>
 </form>
 <div class="modal-body step2 hide">
  <p>Danke für deine Hilfe! Dein Browser sollte ein PDF mit deinem Mandat geöffnet haben, dass du nurnoch versenden musst.</p>
 </div>
 <div class="modal-footer step2 hide">
  <button class="btn btn-primary submit">Ok</button>
 </div>
</div>

<div class="quitMembershipModal modal hide">
 <div class="modal-header">
  <button class="close" data-dismiss="modal">&times;</button>
  <h3>Mitgliedschaft beenden</h3>
 </div>
 <div class="modal-body step1">
  <p><strong>Schade,</strong> dass du nicht länger unser Mitglied sein möchtest, aber du hast bestimmt gute Gründe. Wenn du möchtest, kannst du sie uns hier mitteilen.</p>
  <textarea class="reasons" rows="3" style="width:90%;"></textarea>
 </div>
 <div class="modal-footer step1">
  <button class="btn btn-danger submit">Austreten</button>
 </div>
 <div class="modal-body step2 hide">
  <p>Dein Austritt wurde eingereicht. Du wirst auch noch eine Bestätigungsmail bekommen, aber wir möchten uns hier schon für deine Mitgliedschaft bedanken und von dir verabschieden - Viel Erfolg!</p>
 </div>
 <div class="modal-footer step2 hide">
  <button class="btn btn-primary submit">Ok</button>
 </div>
</div>

<div class="modifyDoneModal modal hide">
 <div class="modal-header">
  <button class="close" data-dismiss="modal">&times;</button>
  <h3></h3>
 </div>
 <div class="modal-body">
  <p>Wir haben deine Änderungen entgegengenommen und dir eine Mail mit den neuen Daten an die neue Mailadresse geschickt.
   In dieser Mail befindet sich ein Link um die Änderungen zu bestätigen, dannach werden sie direkt wirksam!</p>
 </div>
 <div class="modal-footer">
  <a class="btn btn-primary" href="?">Ok</a>
 </div>
</div>

<script type="text/javascript">

$(".addMail").click(function (e) {
	e.preventDefault();
	$(".addMailModal").modal();
	$(".addMailModal .step1 .submit").click(function () {
		$(this).prop("disabled", true);
		$.post("modify.addMail.php", function () {
			$(".addMailModal .step1").hide();
			$(".addMailModal .step2").show();
		});
	});
	$(".addMailModal .step2 .submit").click(function () {
		$(".btn .addMail").remove();
		$(".addMailModal").modal("hide");
	});
});

$(".createSepa").click(function (e) {
	e.preventDefault();
	$(".createSepaModal").modal();
	$(".createSepaModal .step1 .submit").click(function () {
		$(".createSepaModal .step1").hide();
		$(".createSepaModal .step2").show();
	});
	$(".createSepaModal .step2 .submit").click(function () {
		$(".btn .createSepa").remove();
		$(".createSepaModal").modal("hide");
	});
});

$(".requestExemption").click(function (e) {
	e.preventDefault();
});

$(".quitMembership").click(function (e) {
	$(".quitMembershipModal").modal();
	$(".quitMembershipModal .step1 .submit").click(function () {
		$(this).prop("disabled", true);
		$.post("modify.quitMembership.php", { reason: $(".quitMembershipModal .reasons").val() }, function () {
			$(".quitMembershipModal .step1").hide();
			$(".quitMembershipModal .step2").show();
		});
	});
	$(".quitMembershipModal .step2 .submit").click(function () {
		$(".btn .quitMembership").remove();
		$(".quitMembershipModal").modal("hide");
	});
	e.preventDefault();
});

$(".modifyMitglied .submit").click(function (e) {
	$(this).prop("disabled", true);
	$.post("modify.sendModifyMail.php", function () {
		$(".modifyDoneModal").modal();
	});
	e.preventDefault();
});

</script>
<?php
include("_footer.html.php");
?>
