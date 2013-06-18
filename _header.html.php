<?php
header("Content-Type: text/html; charset=utf8");
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Junge Piraten</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <script src="jquery.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
  </head>
  <body>
   <div class="visible-desktop spacer-top">&nbsp;</div>
   <div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
     <div class="container">
      <a class="brand" href="/">Mitgliederverwaltung</a>
      <ul class="nav">
      
      </ul>
     </div>
    </div>
   </div>
   <div class="container">
    <div class="page-header">
     <h1>Hallo<?php print(isset($mitglied) ? " " . (isset($mitglied->latest->natperson) ? $mitglied->latest->natperson->vorname : $mitglied->latest->jurperson->label) : "") ?>, <small><?php print($greeting) ?></small></h1>
    </div>
