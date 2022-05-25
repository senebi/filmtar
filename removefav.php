<?php
	session_start();
	if(isset($_POST["sid"])) include_once("initial.php");
	else header("Content-type: text/html; charset=utf-8");
	
	//MEGNÉZNI: Ha már szerepel a kedvencek között 1 film, akkor már ne adhassuk hozzá!!
	
	if(isset($_SESSION["belepve"])){
		if(isset($_POST["sid"])){
			$valasz="";
			$film_id=$mysqli->real_escape_string($_POST["film_id"]);
			/*
			$cim lekérdezése (film_cim)
			$kat (dvd_kateg)
			$rendezo (rendezo)
			$tipus (film_tipus)
			*/
			
			$lek="select * from kedvencek where film_fk_id=$film_id";
			if($res=$mysqli->query($lek)){
				if($res->num_rows>0){
					$gets_removefav="delete from kedvencek where film_fk_id=$film_id";
					if($getq_removefav=$mysqli->query($gets_removefav)){
						$valasz="torolve";
					}
					else $valasz="<font color='red'>Hiba az eltávolítás közben!</font>";
				}
				else
					$valasz="<font color='red'>Nincs ilyen film!</font>";
			}
			else $valasz="<font color='red'>Hiba a lekérdezés közben!</font>";
			echo $valasz;
		}
		else echo "<font color='red'>Ez az oldal önállóan nem használható!</font>";
	}
	else echo "<font color='red'>Az oldal megtekintéséhez bejelentkezés szükséges!</font>";
?>