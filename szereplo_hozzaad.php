<?php
	session_start();
	header("Content-type: text/html; charset=utf-8");
	if(isset($_POST["sid"])) include_once("initial.php");
	
	$valasz="";
	if(isset($_SESSION["belepve"])){
		if(isset($_POST["sid"], $_POST["szereplo"])){
			$nev=stripslashes($mysqli->real_escape_string($_POST["szereplo"]));
			$vizsgalando=true;
			$hozzaadando=true;
			
			if(isset($_POST["vizsgalando"])){
				$vizsgalando=($_POST["vizsgalando"]=="true" ? true : false);
			}
			if(isset($_POST["hozzaadando"])){
				$hozzaadando=($_POST["hozzaadando"]=="true" ? true : false);
			}
			
			//$nev=htmlspecialchars($nev, ENT_QUOTES);
			$aktszereplolek="select * from ".SZEREPLOK." where szereplo_nev='".$nev."'";
			
			if($vizsgalando){
				if($aktszereplores=$mysqli->query($aktszereplolek)){
					if(!$aktszereplores->num_rows){
						if($hozzaadando){
							$szereplorogzlek="insert into ".SZEREPLOK." values ('NULL','$nev')";
							if($szereplorogzres=$mysqli->query($szereplorogzlek))
								$valasz="A szereplő hozzáadása sikeres.";
							else $valasz="Hiba a szereplő rögzítésekor!";
						}
						else $valasz="A(z) $nev nevű szereplő nem létezik.\nHozzáadjuk az elérhető szereplők listájához?";
					}
					else $valasz="Hiba: ilyen nevű szereplő már létezik!";
				}
				else $valasz="Hiba történt lekérdezés közben!";
			}
			else{	//nem vizsgálandó
				if($hozzaadando){
					$szereplorogzlek="insert into ".SZEREPLOK." values ('NULL','$nev')";
					if($szereplorogzres=$mysqli->query($szereplorogzlek))
						$valasz="A szereplő hozzáadása sikeres.";
					else $valasz="Hiba a szereplő rögzítésekor!";
				}
				else $valasz="Hiba: nem létező opció!";
			}
		}
		else $valasz="Ez az oldal önállóan nem használható!";
	}
	else $valasz="Az oldal megtekintéséhez bejelentkezés szükséges!";
	echo $valasz;
?>