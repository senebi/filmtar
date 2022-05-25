<?php
	session_start();
	if(isset($_POST["sid"])) include_once("initial.php");
	else
		header("Content-type: text/html; charset=utf-8");
	
	$valasz="";
	if(isset($_SESSION["belepve"])){
		if(isset($_POST["sid"])){
			$film_id=$mysqli->real_escape_string($_POST["film_id"]);
			$lek="update ".FILMEK." set kepsrc='' where film_id=".$film_id;
			if($res=$mysqli->query($lek)) $valasz="A kép törlése sikeres.";
			else $valasz="Hiba történt a kép törlése közben!";
		}
		else $valasz="Ez az oldal önállóan nem használható!";
	}
	else $valasz="Az oldal megtekintéséhez bejelentkezés szükséges!";
	echo $valasz;
?>