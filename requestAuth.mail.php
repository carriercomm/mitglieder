Content-Type: text/plain; charset=utf8
From: "Mitgliederverwaltung | Junge Piraten" <mitglieder@junge-piraten.de>

Hallo <?php print(isset($mitglied->latest->natperson) ? $mitglied->latest->natperson->vorname : $mitglied->latest->jurperson->label) ?>,

du hast einen Legetimationslink für das Mitgliederverwaltungsportal angefordert. Solltest du das nicht getan haben,
ignoriere diese Mail bitte, ansonsten kannst du unter

https://mitglieder.junge-piraten.de/?mitgliedid=<?php print($mitglied->mitgliedid) ?>&token=<?php print($token) ?>&

deine Mitgliederdaten einsehen und auch bearbeiten.

Viele Grüße,
