<?php
	session_start();
	header("Content-type: text/html; charset=utf-8");
	include_once("initial.php");
?>
	<link rel="stylesheet" type="text/css" href="stilus.css" />
   <script language="javascript" src="jquery-<?php if(defined("jqueryverzio")) echo jqueryverzio; else echo "1.10.2.min"; ?>.js"></script>
	<script language="javascript" src="jsfv.js"></script>
<?php	
	if(isset($_SESSION["belepve"])){
		echo "<h3>Név-, és jelszóváltoztatás</h3>";
		echo "Név: ".sha1("admin")."<br />";
		echo "Jelszó: ".sha1("admin");
	}
	else echo "<font color='red'>Az oldal megtekintéséhez bejelentkezés szükséges!</font>";
?>