<?php
	session_start();
	header("Content-type: text/html; charset=utf-8");
	if(isset($_POST["sid"])) include_once("initial.php");
	
	$valasz="";
	if(isset($_SESSION["belepve"])){
		if(isset($_POST["sid"], $_POST["szereplo"])){
			$nev=$mysqli->real_escape_string($_POST["szereplo"]);
			
			$aktszereplolek="select * from ".SZEREPLOK." where szereplo_nev='".$nev."'";
			
			if($aktszereplores=$mysqli->query($aktszereplolek)){
				if(!$aktszereplores->num_rows){
					$szereplorogzlek="insert into ".SZEREPLOK." values ('NULL','$nev')";
					if($szereplorogzres=$mysqli->query($szereplorogzlek))
						$valasz="Sikeres hozzáadás.";
					else $valasz="Hiba a szereplő rögzítésekor!";
				}
				else $valasz="Hiba: ilyen nevű szereplő már létezik!";
			}
			else $valasz="Hiba történt lekérdezés közben!";
		}
		else $valasz="Ez az oldal önállóan nem használható!";
	}
	else $valasz="Az oldal megtekintéséhez bejelentkezés szükséges!";
	echo $valasz;
?>