Content-Type: text/plain; charset=utf8
From: "Mitgliederverwaltung | Junge Piraten" <mitglieder@junge-piraten.de>

Hallo <?php print(isset($mitglied->latest->natperson) ? $mitglied->latest->natperson->vorname : $mitglied->latest->jurperson->label) ?>,

https://mitglieder.junge-piraten.de/?mitgliedid=<?php print($mitglied->mitgliedid) ?>&token=<?php print($token) ?>&
