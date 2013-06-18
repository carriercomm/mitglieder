<?php

require_once(dirname(__FILE__) . "/config.inc.php");
require_once(dirname(__FILE__) . "/session.inc.php");

function normalizeMail($mail) {
	return strtolower(trim($mail));
}

$authFailed = null;

if (isset($_POST["requestAuth"])) {
	$mitgliedid = stripslashes($_POST["mitgliedid"]);
	$mailadresse = stripslashes($_POST["mailadresse"]);

	$mitglied = $vpanel->getMitglied($mitgliedid);
	if ($mitglied != null && normalizeMail($mitglied->latest->kontakt->email->email) == normalizeMail($mailadresse)) {
		$token = getMitgliedToken();

		ob_start();
		include("requestAuth.mail.php");
		list($headers, $body) = explode("\n\n", ob_get_contents(), 2);
		ob_end_clean();

		mail($mitglied->latest->kontakt->email->email, "Anmeldung zur Mitgliederverwaltung", $body, $headers);

		header("Location: ?requestSuccess=1");
	} else {
		$authFailed = true;
	}
}

$greeting = "";
include("_header.html.php");

if (isset($_REQUEST["requestSuccess"])) { ?>
<p class="lead">
 Wir haben dir eine Mail mit deinem Zugangslink geschickt - schau in dein Postfach und klicke auf diesen Link, um zu deinen Mitgliedsdaten zu kommen.
 Solltest du dort in 5 Minuten noch keine Mail sehen, schau in deinen Spamordner. Wenn du auch dort keine Mail findest, melde dich bei uns unter
 <a href="mailto:mitglieder@junge-piraten.de">mitglieder@junge-piraten.de</a>.
</p>
<?php } else { ?>
<p class="lead">
 Um deine Mitgliedschaft zu bearbeiten, müssen wir dich authentifizieren. Gib dafür bitte jetzt deine Mitgliedsnummer und deine Mailadresse an. Du findest beides
 in den Mails, die wir dir schicken unten in der Signatur. Beachte bitte, dass du die Mailadresse angeben musst, die bei uns hinterlegt ist!</p>
<p class="lead">
 Anschließend schicken wir dir per Mail einen Link, mit dem du deine Mitgliedsdaten bearbeiten kannst.
</p>
<?php if ($authFailed === false) { ?><p class="alert alert-error">Leider konnten wir diese Kombination aus Mitgliedsnummer und Mailadresse nicht zuordnen. Wenn du nicht weiter
 weißt, frage uns bitte einfach unter <a href="mailto:mitglieder@junge-piraten.de">mitglieder@junge-piraten.de</a>.</p><?php } ?>
<form action="?" method="post" class="form-horizontal">
 <div class="control-group">
  <label class="control-label" for="mitgliedid">Mitgliedsnummer:</label>
  <div class="controls">
   <input type="text" class="input-mini" name="mitgliedid" />
  </div>
 </div>
 <div class="control-group">
  <label class="control-label" for="mailadresse">Mail:</label>
  <div class="controls">
   <input type="text" class="input-xxlarge" name="mailadresse" />
  </div>
 </div>

 <div class="form-actions">
  <button class="btn btn-primary" type="submit" name="requestAuth" value="1">Token zusenden</button>
 </div>
</form>
<?php }

include("_footer.html.php");
