<?php
	session_start();
	header("Content-type: text/html; charset=utf-8");
	if(isset($_POST["sid"])) include_once("initial.php");
	
	$valasz="";
	if(isset($_SESSION["belepve"])){
		if(isset($_POST["sid"], $_POST["id"], $_POST["mit"])){
			$id=$mysqli->real_escape_string($_POST["id"]);
			$mit=$mysqli->real_escape_string($_POST["mit"]);
			
			$lek="delete from ".KAPCSOLO." where";
			if($mit=="film") $lek.=" film_fk_id=$id";
			else $lek.=" szereplo_fk_id=$id";
			
			if($res=$mysqli->query($lek)){
				if($mit=="film"){
					$boritopath=$_SERVER["DOCUMENT_ROOT"]."filmtar/img/boritok/";
					$lek_kepnev="select film_id, kepsrc from ".FILMEK." where film_id=$id";
					//$keptorles_sikeres=false;
					//itt lekérni a $film_id-hez tartozó film kepsrc mezőjét, amit aztán az unlink fv. $_SERVER["DOCUMENT_ROOT"]."filmtar/img/boritok/kicsi/".$kepsrc paraméterével törlünk (fizikailag)!
					if($res_kepnev=$mysqli->query($lek_kepnev)){
						$kepsrc=$res_kepnev->fetch_object()->kepsrc;
						if($kepsrc!=""){
							$tmp1=file_exists($boritopath."kicsi/".$kepsrc);
							$tmp2=file_exists($boritopath."nagy/".$kepsrc);
							if($tmp1 && $tmp2){
								unlink($boritopath."kicsi/".$kepsrc);
								unlink($boritopath."nagy/".$kepsrc);
							}
						}
					}
				}
				
				if($mit=="film") $lek2="delete from ".FILMEK." where film_id=$id";
				else $lek2="delete from ".SZEREPLOK." where szereplo_id=$id";
				
				if($res2=$mysqli->query($lek2)){
					$valasz="Sikeres törlés.";
				}
				else{
					if($mit=="film")
						$valasz="Hiba történt a film törlése közben!";
					else $valasz="Hiba történt a szereplő törlése közben!";
				}
			}
			else{
				if($mit=="film")
					$valasz="Hiba történt a filmhez tartozó kapcsolatok törlése közben!";
				else $valasz="Hiba történt a szereplőhöz tartozó kapcsolatok törlése közben!";
			}
		}
		else $valasz="Ez az oldal önállóan nem használható!";
	}
	else $valasz="Az oldal megtekintéséhez bejelentkezés szükséges!";
	echo $valasz;
?>