Content-Type: text/plain; charset=utf8
From: "Mitgliederverwaltung | Junge Piraten" <mitglieder@junge-piraten.de>

Hallo <?php print(isset($mitglied->latest->natperson) ? $mitglied->latest->natperson->vorname : $mitglied->latest->jurperson->label) ?>,

du hast um eine Änderung deiner Mitgliedsdaten gebeten. Um diesen Vorgang
abzuschließen, bestätige bitte deine neuen Angaben mit dem unten stehenden Link

	<?php print(isset($mitglied->latest->natperson) ? $mitglied->latest->natperson->vorname . " " . $mitglied->latest->natperson->name : $mitglied->latest->jurperson->label) ?>

	<?php print($changes["strasse"]) ?> <?php print($changes["hausnummer"]) ?>

	<?php print($changes["adresszusatz"]) ?>

	<?php print($mitglied->latest->kontakt->ort->plz) ?> <?php print($mitglied->latest->kontakt->ort->label) ?>


https://mitglieder.junge-piraten.de/confirmModification.php?mitgliedid=<?php print($mitglied->mitgliedid) ?>&token=<?php print($token) ?>&changes=<?php print($changeString) ?>&changeToken=<?php print($changeToken) ?>&

Bei Fragen kannst du dich immer gerne an uns wenden!

Viele Grüße,
