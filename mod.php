<?php
	session_start();
	header("Content-type: text/html; charset=utf-8");
	if(isset($_POST["sid"])) include_once("initial.php");
	
	$valasz="";
	if(isset($_SESSION["belepve"])){
		if(isset($_POST["sid"], $_POST["film_id"], $_POST["modtargy"])){
			$film_id=$mysqli->real_escape_string($_POST["film_id"]);
			$modtargy=$mysqli->real_escape_string($_POST["modtargy"]);
			$times=array();
			
			if($modtargy=="aktivitas"){	//aktivitás változtatás esetén 2 lekérdezés
				$regi_ertek=$mysqli->real_escape_string($_POST["regi_ertek"]);
				$lek="select * from ".FILMEK." where film_id=$film_id";
				$starttime=microtime(true);
				if($res=$mysqli->query($lek)){
					$endtime=microtime(true);
					$duration=$endtime-$starttime;
					$times[]=array("lekérdezés" => $lek, "idő" => $duration);
					if($res->num_rows>0){
						$uj_ertek=1-$regi_ertek;
						$lek2="update ".FILMEK." set aktiv=$uj_ertek where film_id=".$film_id;
						$starttime=microtime(true);
						if($res2=$mysqli->query($lek2)){
							$endtime=microtime(true);
							$duration=$endtime-$starttime;
							$times[]=array("lekérdezés" => $lek2, "idő" => $duration);
							$valasz="1";
						}
						else $valasz="Hiba történt lekérdezés közben!";
					}
					else $valasz="Nincs ilyen film!";
				}
				else $valasz="Hiba történt lekérdezés közben!";
			}
			else if($modtargy=="minden"){	//bármilyen adat, kivéve aktivitás és értékelés módosítása esetén 10 lekérdezés
				//$film_id már feltételtől függetlenül értéket kapott
				$siker=true;
				
				if(isset($_POST["filmAdatok"])){
					if(!filmAdatMod($film_id, $_POST["filmAdatok"])){
						$siker=false;
						$valasz="Hiba történt a film adatainak módosítása közben!";
					}
				}
				else{
					$siker=false;
					$valasz="Hiányoznak a film adatok a módosításhoz!";
				}
				
				if(isset($_POST["regiSzereplok"]) || isset($_POST["ujSzereplok"])){
					if(!filmSzereplokMod($film_id, $_POST["regiSzereplok"], $_POST["ujSzereplok"])){
						$siker=false;
						$valasz="Hiba történt a film-szereplő kapcsolatok módosítása közben!";
					}
				}
				else{
					$siker=false;
					$valasz="Hiányoznak a szereplők a módosításhoz!";
				}

				if($siker) $valasz="Sikeres adatmódosítás.";
			}
			else if($modtargy=="csillag"){	//értékelés változtatása esetén 1 lekérdezés
				$celertek=$mysqli->real_escape_string($_POST["celertek"]);
				$lek="update ".FILMEK." set rate=$celertek where film_id=$film_id";
				$starttime=microtime(true);
				if($res=$mysqli->query($lek)){
					$endtime=microtime(true);
					$duration=$endtime-$starttime;
					$times[]=array("lekérdezés" => $lek, "idő" => $duration);
					$valasz="siker";
				}
				else $valasz="Hiba történt módosítás közben!";
			}
			else if($modtargy=="barmi"){
				if(count($_POST["filmAdatok"])>0){
					$lek="update ".FILMEK." set ";
					$i=0;
					foreach($_POST["filmAdatok"] as $mezo => $ujErtek){
						$mezo=$mysqli->real_escape_string($mezo);
						$ujErtek=$mysqli->real_escape_string($ujErtek);
						$lek.=$mezo."='".$ujErtek."'";
						if($i<(count($_POST["filmAdatok"])-1)) $lek.=",";
						$i++;
					}
					$lek.=" where film_id=".$film_id;
					
					$starttime=microtime(true);
					if($res=$mysqli->query($lek)){
						$endtime=microtime(true);
						$duration=$endtime-$starttime;
						$times[]=array("lekérdezés" => $lek, "idő" => $duration);
						$valasz="siker";
					}
					else $valasz="Hiba történt módosítás közben!";
				}
				else $valasz="Nincs megadva módosítandó adat!";
			}
		}
		else $valasz="Ez az oldal önállóan nem használható!";
	}
	else $valasz="Az oldal megtekintéséhez bejelentkezés szükséges!";
	echo $valasz;
	/*$total=0;
	for($i=0; $i<count($times); $i++){
		echo "<br />Lekérdezés: ".$times[$i]["lekérdezés"]." (".round(($times[$i]["idő"]*1000),2)." ms)";
		$total+=$times[$i]["idő"];
	}
	echo "<br />Összesen ".round(($total*1000),2)." ms";*/
	
	//az alap film adatok módosításáért felel
	function filmAdatMod($film_id, $filmAdatok){
		global $mysqli;
		$lek="update ".FILMEK." set ";
		$ok=false;
		$i=0;
		
		foreach($filmAdatok as $mezo => $ujErtek){
			$mezo=$mysqli->real_escape_string($mezo);
			$ujErtek=$mysqli->real_escape_string($ujErtek);
			if(is_null($mezo)) $mezo="";
			if($mezo=="kepsrc"){
				$ujErtek=(urlfajlnev($ujErtek)!==false) ? urlfajlnev($ujErtek) : "";
				if($ujErtek!=""){
					$vanenagy=file_exists($_SERVER["DOCUMENT_ROOT"]."/filmtar/img/boritok/nagy/".$ujErtek);
					$vanekicsi=file_exists($_SERVER["DOCUMENT_ROOT"]."/filmtar/img/boritok/kicsi/".$ujErtek);
					if($vanenagy && $vanekicsi) $lek.=$mezo."='".$ujErtek."'";
					else $lek.=$mezo."=''";
				}
				else $lek.=$mezo."=''";
			}
			else if($mezo=="ar_datum"){
				if($ujErtek=="0000-00-00") $lek.=$mezo."=NULL";
				else $lek.=$mezo."='".$ujErtek."'";
			}
			else $lek.=$mezo."='".$ujErtek."'";
			
			if($i<(count($filmAdatok)-1)) $lek.=", ";
			$i++;
		}
		$lek.=" where film_id=".$film_id;

		$ok=$mysqli->query($lek);
		
		return $ok;
	}
	
	//a film-szereplő kapcsolatok létrehozásáért, törléséért felel
	function filmSzereplokMod($film_id, $regiSzereplok, $ujSzereplok){
		global $mysqli;
		$voltTorlendo=false;
		$i=0;
		$ok=true;
		$torloLek="";
		$log=false;
		
		$szoveg=count($regiSzereplok)." régi, ".count($ujSzereplok)." új szereplő érkezett<br />";
		if(isset($regiSzereplok)){
			foreach($regiSzereplok as $szereplo){
				$szereploId=$mysqli->real_escape_string($szereplo["id"]);
				$szereploTorlendo=$mysqli->real_escape_string($szereplo["torlendo"]);
				if($szereploTorlendo=="true"){
					if(!$voltTorlendo){
						$torloLek="delete from ".KAPCSOLO." where szereplo_fk_id in (".$szereploId;
						$voltTorlendo=true;
					}
					else $torloLek.=",".$szereploId;
				}
			}
			
			if($voltTorlendo){
				$torloLek.=") and film_fk_id=".$film_id;
				$szoveg.="Régiek törlő lekérdezése:<br />".$torloLek;
				if(!$torloRes=$mysqli->query($torloLek))
					$ok=false;
			}
			else $szoveg.="Nincs törlendő régi szereplő.";
			$szoveg.="<br />";
		}
		
		//------------------innentől az új szereplők hozzákapcsolása következik----------
		//új szereplők nem biztos, hogy érkeztek
		if(isset($ujSzereplok)){
			$szoveg.="Újak:<br />";
			foreach($ujSzereplok as $szereplo){
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
							$szoveg.="Nem sikerült a film-szereplő kapcsolatot létrehozni!";
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
		}
		
		if($log && $_SESSION["user"]=="haruko") echo $szoveg;
		return $ok;
	}
?>