<?php
	session_start();
	if(isset($_POST["sid"])) include_once("initial.php");
	else header("Content-type: text/html; charset=utf-8");
	
	if(isset($_SESSION["belepve"])){
		if(isset($_POST["sid"])){
			$valasz="";
			$times=array();
			$siker=true;
			
			if(isset($_POST["filmAdatok"])){
				
				$cim=$mysqli->real_escape_string($_POST["filmAdatok"]["film_cim"]);
				$kat=$mysqli->real_escape_string($_POST["filmAdatok"]["dvd_kateg"]);
				$mufaj=$mysqli->real_escape_string($_POST["filmAdatok"]["film_mufaj"]);
				
				
				$lek="select film_id from ".FILMEK." where film_cim='$cim' and dvd_kateg='$kat' and film_mufaj='$mufaj' limit 1";
				$starttime=microtime(true);
				if($res=$mysqli->query($lek)){
					$endtime=microtime(true);
					$duration=$endtime-$starttime;
					$times[]=array("lekérdezés" => $lek, "idő" => $duration);
					if($res->num_rows>0){
						$siker=false;
						$valasz="Ez a film már szerepel az adatbázisban!";
					}
					else{
						$film_id=filmAdatRogzit($_POST["filmAdatok"]);
						if(!$film_id){
							$siker=false;
							$valasz="Hiba történt a film adatainak rögzítése közben!";
						}
						else{
							if(isset($_POST["szereplok"])){
								if(!filmSzereplokRogzit($film_id, $_POST["szereplok"])){
									$siker=false;
									$valasz="Hiba történt a film-szereplő kapcsolatok létrehozása közben!";
								}
							}
							else{
								$siker=false;
								$valasz="Hiányoznak a szereplők a hozzáadáshoz!";
							}
						}
					}
				}
				else{
					$siker=false;
					$valasz="Hiba történt lekérdezés közben!";
				}
			}
			else{
				$siker=false;
				$valasz="Hiányoznak a film adatok a hozzáadáshoz!";
			}
			
			if($siker) $valasz="A film adatai sikeresen rögzítésre kerültek.";
			echo $valasz;
			
			/*$total=0;
			for($i=0; $i<count($times); $i++){
				echo "<br />Lekérdezés: ".$times[$i]["lekérdezés"]." (".round(($times[$i]["idő"]*1000),2)." ms)";
				$total+=$times[$i]["idő"];
			}
			echo "<br />Összesen ".round(($total*1000),2)." ms";*/
		}
		else echo "Ez az oldal önállóan nem használható!";
	}
	else echo "Az oldal megtekintéséhez bejelentkezés szükséges!";
	
	//az alap film adatok hozzáadásáért felel
	function filmAdatRogzit($filmAdatok){
		global $mysqli;
		$log=false;
		$szoveg=count($filmAdatok)." adat érkezett.<br />";
		$szoveg.="Lekérdezés: ";
		
		$lek="insert into ".FILMEK." ";
		$lekMezok="(";
		$lekErtekek="(";
		$ok=false;
		$i=0;
		
		foreach($filmAdatok as $mezo => $ertek){
			$mezo=$mysqli->real_escape_string($mezo);
			$ertek=$mysqli->real_escape_string($ertek);
			if(is_null($mezo)) $mezo="";
			if($mezo=="kepsrc"){
				$ertek=(urlfajlnev($ertek)!==false) ? urlfajlnev($ertek) : "";
				if($ertek!=""){
					$vanenagy=file_exists($_SERVER["DOCUMENT_ROOT"]."/filmtar/img/boritok/nagy/".$ertek);
					$vanekicsi=file_exists($_SERVER["DOCUMENT_ROOT"]."/filmtar/img/boritok/kicsi/".$ertek);
					if($vanenagy && $vanekicsi){
						$lekErtekek.="'".$ertek."'";
					}
				}
				else $lekErtekek.="''";
				$lekMezok.=$mezo;
			}
			else{
				$lekMezok.=$mezo;
				if($ertek!="") $lekErtekek.="'".$ertek."'";
				else $lekErtekek.="''";
			}
			
			if($i<(count($filmAdatok)-1)){
				$lekMezok.=", ";
				$lekErtekek.=", ";
			}
			$i++;
		}
		$datum=date("Y-m-d");
		$aktivitas=1;
		$rate="'NULL'";
		$lekMezok.=", rogz_datum, aktiv, rate)";
		$lekErtekek.=", '".$datum."', ".$aktivitas.", ".$rate.")";
		$lek.=$lekMezok." values ".$lekErtekek;
		$szoveg.=$lek."<br />";
		
		if($log && $_SESSION["user"]=="haruko") echo $szoveg;
	
		$ok=$mysqli->query($lek);
		if($ok)
			return $mysqli->insert_id; //film_id
		else return false;
	}
	
	//a film-szereplő kapcsolatok létrehozásáért, törléséért felel
	function filmSzereplokRogzit($film_id, $szereplok){
		global $mysqli;
		$voltTorlendo=false;
		$i=0;
		$ok=true;
		$torloLek="";
		$log=false;
		
		$szoveg=count($szereplok)." szereplő érkezett, ezek a következők:<br />";
		
		foreach($szereplok as $szereplo){
			$szereploNev=stripslashes($mysqli->real_escape_string($szereplo["nev"]));
			$szoveg.="\tnév: ".$szereploNev.", ";
			$getIdLek="select szereplo_id from ".SZEREPLOK." where szereplo_nev='".$szereploNev."'";
			if($getIdRes=$mysqli->query($getIdLek)){
				if($getIdRes->num_rows){
					$szereplo_id=$getIdRes->fetch_object()->szereplo_id;
					$szoveg.="id: ".$szereplo_id."<br />";
					
					$ujKapcsLek="INSERT INTO ".KAPCSOLO." (film_fk_id, szereplo_fk_id)
					SELECT ".$film_id.", ".$szereplo_id." from ".KAPCSOLO.
					" WHERE NOT EXISTS(
						SELECT film_fk_id, szereplo_fk_id FROM ".KAPCSOLO." WHERE film_fk_id=".$film_id." and szereplo_fk_id=".$szereplo_id.
					") LIMIT 1";
					//$ujKapcsLek="insert into ".KAPCSOLO." values ('NULL', $film_id, $szereplo_id)";
					$szoveg.="Kapcsolat létrehozó lekérdezés: ".$ujKapcsLek;
					if(!$ujKapcsRes=$mysqli->query($ujKapcsLek)){
						$szoveg.="Nem sikerült a film-szereplő (".$szereploNev.") kapcsolatot létrehozni!";
						$ok=false;
					}
				}
				else{
					$szoveg.="Nem található ".$szereploNev." azonosítója!";
					$ok=false;
				}
			}
			else{
				$szoveg.="Hiba történt lekérdezés közben!";
				$ok=false;
			}
			$szoveg.="<br />";
		}
		
		if($log && $_SESSION["user"]=="haruko") echo $szoveg;
		return $ok;
	}
?>