<?php

require_once(dirname(__FILE__) . "/config.inc.php");
require_once(dirname(__FILE__) . "/session.inc.php");

requireAuth();
$mitglied = getMitgliedDetails();

$inhaber = stripslashes($_POST["inhaber"]);
$iban = stripslashes($_POST["iban"]);
$bic = stripslashes($_POST["bic"]);
$frankieren = isset($_POST["frankIt"]);
$timestamp = time();

require_once("form.inc.php");

$rand = rand(1000,9999);
fillPDFForm(
        "https://wiki.junge-piraten.de/wiki/Spezial:Dateipfad/SepaLastschriftmandat.pdf",
        "VP1",
        array(
                "Vorname" => isset($mitglied->latest->natperson) ? $mitglied->latest->natperson->vorname : $mitglied->latest->jurperson->label,
                "Name" => isset($mitglied->latest->natperson) ? $mitglied->latest->natperson->name : "",
                "Mitgliedsnummer" => $mitglied->mitgliedid,
                "Strasse" => $mitglied->latest->kontakt->strasse . " " . $mitglied->latest->kontakt->hausnummer,
                "PLZ" => $mitglied->latest->kontakt->ort->plz,
                "Ort" => $mitglied->latest->kontakt->ort->label,
                "KontoInhaber" => $inhaber,
                "KontoIBan" => $iban,
                "KontoBIC" => $bic,
                "UnterschriftDatum" => date("d.m.Y", $timestamp)
        ),
        array(),
        array(
        	"kategorieid" => 49,
        	"statusid" => 3,
        	"flags" => array(),
        	"label" => "SEPA-Mandat " . (isset($mitglied->latest->natperson) ? $mitglied->latest->natperson->vorname . " " . $mitglied->latest->natperson->name : $mitglied->latest->jurperson->label),
        	"identifier" => "BGS_SEPA_" . strtoupper(isset($mitglied->latest->natperson) ? substr($mitglied->latest->natperson->name,0,3) . "_" . substr($mitglied->latest->natperson->vorname,0,3) . "_" . date("Ymd", $mitglied->latest->natperson->geburtsdatum) : substr($mitglied->latest->jurperson->label,0,6)),
        	"data" => json_encode(array(
        		"mitglied" => $mitglied->mitgliedid,
			"inhaber" => $inhaber,
			"iban" => $iban,
        		"bic" => $bic,
        		"sigDate" => date("d.m.Y", $timestamp),
        	)),
        ),
        ($frankieren ? 58 : 0),
        "/tmp/sepa-" . $rand . ".pdf"
);

header("Content-Type:application/pdf");
readfile("/tmp/sepa-" . $rand . ".pdf");
unlink("/tmp/sepa-" . $rand . ".pdf");
