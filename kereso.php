<?php
	session_start();
	if(isset($_POST["sid"])) include_once("initial.php");
	else header("Content-type: text/html; charset=utf-8");

	$valasz="";
	if(isset($_SESSION["belepve"])){
		if(isset($_POST["sid"])){
			$times=array();
			$kcim=stripslashes($mysqli->real_escape_string($_POST["kcim"]));
			$kkateg=$mysqli->real_escape_string($_POST["kkateg"]);
			$krendezo=$mysqli->real_escape_string($_POST["krendezo"]);
			$kmufaj=$mysqli->real_escape_string($_POST["kmufaj"]);
			$kpoz=stripslashes($mysqli->real_escape_string($_POST["kpoz"]));
			$kszereplo=stripslashes($mysqli->real_escape_string($_POST["kszereplo"]));
			//$elejen=$mysqli->real_escape_string($_POST["elejen"]);
			//$barhol=$mysqli->real_escape_string($_POST["barhol"]);
			//$vegen=$mysqli->real_escape_string($_POST["vegen"]);
			$aktivakat=$mysqli->real_escape_string($_POST["aktivakat"]);
			$inaktivakat=$mysqli->real_escape_string($_POST["inaktivakat"]);
			$ertekeles=$mysqli->real_escape_string($_POST["ertekeles"]);
			
			$hanyfeltetel=0;
			$lek="select * from ".FILMEK." left outer join ".MUFAJOK." on
			".FILMEK.".film_mufaj=".MUFAJOK.".id";
			
			if($kcim!=""){
				$hanyfeltetel++;
				$nagykcim=mb_strtoupper($kcim, "UTF-8");
				//$nagykcim=strtoupper($kcim);
				$aktkcimhossz=mb_strlen($kcim, "UTF-8");
				$lek.=" where upper(film_cim) like '%$nagykcim%'";
				/*if($elejen=="true") $lek.=" '$nagykcim%'";
				else if($barhol=="true") $lek.=" '%$nagykcim%'";
				else if($vegen=="true") $lek.=" '%$nagykcim'";*/
				$lek.=" collate utf8_bin";
			}
			if($kkateg!=""){
				if($hanyfeltetel>0) $lek.=" and";
				else $lek.=" where";
				$lek.=" dvd_kateg like '$kkateg%'";
				$hanyfeltetel++;
			}
			if($krendezo!=""){
				if($hanyfeltetel>0) $lek.=" and";
				else $lek.=" where";
				$lek.=" rendezo like '%$krendezo%'";
				$hanyfeltetel++;
			}
			if($kmufaj!="0"){
				if($hanyfeltetel>0) $lek.=" and";
				else $lek.=" where";
				$lek.=" film_mufaj='$kmufaj'";
				$hanyfeltetel++;
			}
			if($kpoz!=""){
				if($hanyfeltetel>0) $lek.=" and";
				else $lek.=" where";
				$lek.=" poz='$kpoz'";
				$hanyfeltetel++;
			}
			if($kszereplo!=""){
				if($hanyfeltetel>0) $lek.=" and";
				else $lek.=" where";
				$nagykszereplo=mb_strtoupper($kszereplo, "UTF-8");;
				$lek2="select szereplo_fk_id, film_fk_id from ".KAPCSOLO." inner join ".SZEREPLOK." on szereplo_fk_id=szereplo_id where";
				$lek2.=" upper(szereplo_nev) like '%$nagykszereplo%'";
				$starttime=microtime(true);
				if($res2=$mysqli->query($lek2)){
					$endtime=microtime(true);
					$duration=$endtime-$starttime;
					$times[]=array("lekérdezés" => $lek2, "idő" => $duration);
					if($res2->num_rows>0){
						$talalatlista=array();
						$lek.=" film_id in (";
						while($a=$res2->fetch_assoc()){
							$talalatlista[]=$a["film_fk_id"];
						}
						for($i=0; $i<count($talalatlista); $i++){
							$lek.=$talalatlista[$i];
							if($i!=(count($talalatlista)-1)) $lek.=",";
						}
						$lek.=")";
					}
					else $lek.=" film_id=-1";
				}
				$hanyfeltetel++;
			}
			if($aktivakat=="true" || $inaktivakat=="true"){
				if(!($aktivakat=="true" && $inaktivakat=="true")){
					if($hanyfeltetel>0) $lek.=" and";
					else $lek.=" where";
					if($aktivakat=="true") $lek.=" aktiv=1";
					else $lek.=" aktiv=0";
					$hanyfeltetel++;
				}
			}
			else{
				if($hanyfeltetel>0) $lek.=" and";
				else $lek.=" where";
				$lek.=" aktiv=-1";
			}
			if($ertekeles!="0"){
				if($hanyfeltetel>0) $lek.=" and";
				else $lek.=" where";
				$lek.=" rate=$ertekeles";
				$hanyfeltetel++;
			}
			
			if($hanyfeltetel==1 && $kpoz!=""){
				$lek="(select * from ".FILMEK." where poz<'$kpoz' order by poz desc limit 1)
				union all
				(select * from ".FILMEK." where poz='$kpoz')
				union all
				(select * from ".FILMEK." where poz>'$kpoz' order by poz asc limit 1)";
			}
			else $lek.=" order by ".FILMEK.".film_cim";
			
			$starttime=microtime(true);
			if($res=$mysqli->query($lek)){
				$endtime=microtime(true);
				$duration=$endtime-$starttime;
				$times[]=array("lekérdezés" => $lek, "idő" => $duration);
				$talalatok=$res->num_rows;
				
				if($talalatok>0){
					//config fájl beimportálása
					include("config.php");
					
					$i=1;
					
					//mivel az adatbázisból listázandó mezők sorszáma 2-től kezdődik, $j+2 a fejléc id-je
					$valasz.="<div id=\"ragados\"><table class=\"lista\"><tr><th id='fejlec1'>Nr.";
					if(isset($_SESSION["fejlec_sorrend"])){
						$fejlecTomb=explode(",",$_SESSION["fejlec_sorrend"]);
						$j=0;
						foreach($fejlecTomb as $elem){
							$elemTomb=explode(":",$elem);
							$sorszam=$elemTomb[0];
							$mutat=$elemTomb[1];
							if($mutat){
								$valasz.="</th><th id='fejlec".($j+2)."'>".$mezok[$sorszam]["fejlec"]." ";
								if(isset($miszerint) && $miszerint==$mezok[$sorszam]["dbnev"])
									$valasz.="<img src='$src' title='$title' alt='$title' class='ikon r".$miszerint."' />";
								else $valasz.="<img src='img/novekvo.png' title='növekvő sorrend' alt='növekvő sorrend' class='ikon r".$mezok[$sorszam]["dbnev"]."' />";
							}
							$j++;
						}
					}
					else{
						for($j=0; $j<count($mezok); $j++){
							if($mezok[$j]["dbnev"]!="besz_ar" && $mezok[$j]["dbnev"]!="ar_datum" || $_SESSION["jog"]=="admin"){
								$valasz.="</th><th id='fejlec".($j+2)."'>".$mezok[$j]["fejlec"]." ";
								if(isset($miszerint) && $miszerint==$mezok[$j]["dbnev"])
									$valasz.="<img src='$src' title='$title' alt='$title' class='ikon r".$miszerint."' />";
								else $valasz.="<img src='img/novekvo.png' title='növekvő sorrend' alt='növekvő sorrend' class='ikon r".$mezok[$j]["dbnev"]."' />";
							}
						}
					}
					
					$valasz.="</th>";
					if($_SESSION["jog"]=="premium")
						$valasz.="<th id='fejlec".($j+2)."'>Kedvencek</th>";
					$valasz.="</tr>";
					$valasz.="</table></div>";
					
					//*************************** ragadós vége, jön a rendes táblázat **************************************
					
					if($hanyfeltetel>0)
						$valasz.="<b>".$talalatok."</b> találat:<br />";
					
					$valasz.="<table class=\"lista\" id=\"adattabla\">";
					$valasz.="<tr><th>Nr.";
					if(isset($_SESSION["fejlec_sorrend"])){
						$j=0;
						foreach($fejlecTomb as $elem){
							$elemTomb=explode(":",$elem);
							$sorszam=$elemTomb[0];
							$mutat=$elemTomb[1];
							if($mutat){
								$valasz.="</th><th id='fejlec".($j+2)."'>".$mezok[$sorszam]["fejlec"]." ";
								if(isset($miszerint) && $miszerint==$mezok[$sorszam]["dbnev"])
									$valasz.="<img src='$src' title='$title' alt='$title' class='ikon r".$miszerint."' />";
								else $valasz.="<img src='img/novekvo.png' title='növekvő sorrend' alt='növekvő sorrend' class='ikon r".$mezok[$sorszam]["dbnev"]."' />";
							}
							$j++;
						}
					}
					else{
						for($j=0; $j<count($mezok); $j++){
							if($mezok[$j]["dbnev"]!="besz_ar" && $mezok[$j]["dbnev"]!="ar_datum" || $_SESSION["jog"]=="admin"){
								$valasz.="</th><th>".$mezok[$j]["fejlec"]." ";
								if(isset($miszerint) && $miszerint==$mezok[$j]["dbnev"])
									$valasz.="<img src='$src' title='$title' alt='$title' class='ikon r".$miszerint."' />";
								else $valasz.="<img src='img/novekvo.png' title='növekvő sorrend' alt='növekvő sorrend' class='ikon r".$mezok[$j]["dbnev"]."' />";
							}
						}
					}
					
					$valasz.="</th>";
					if($_SESSION["jog"]=="premium")
						$valasz.="<th>Kedvencek</th>";
					$valasz.="</tr>";
					
					while($sor=$res->fetch_assoc()){
						$nagyeredeti=mb_strtoupper($sor["film_cim"], "UTF-8");
						//$nagyeredeti=strtoupper($sor["film_cim"]);
						if(isset($nagykcim)) $pozicio=mb_strpos($nagyeredeti, $nagykcim, 0, "UTF-8");
						else $pozicio=false;
						
						$valasz.="<tr class='";
						$valasz.= $i%2==1 ? "paratlan" : "paros";
						if(!$sor["aktiv"]) $valasz.=" inaktiv";
						$valasz.="' id='sor".$sor["film_id"]."'>";
						
						$kellTartalomId=false;
						$valasz.="<td";
						if($i==1){
							$valasz.=" id='tartalom1'";
							$kellTartalomId=true;
						}
						
						$valasz.="><input type='hidden' id='h".$i."' value=".$sor["film_id"]." />".$i."</td>";
						
						/*
						$j=2;
						$valasz.="<td";
						if($i==1) $valasz.=" id='tartalom".$j."'";
						$valasz.="><span class='mod' id='f".$sor["film_id"]."'>";
						//if($sor["kepsrc"]!="") $valasz.="<a href='img/boritok/nagy/".$sor["kepsrc"]."' target='new'>";
						*/
						if($hanyfeltetel>0 && $kcim!=""){
							$eredetiTalalat=mb_substr($sor["film_cim"], $pozicio, $aktkcimhossz, "UTF-8");
							
							if($pozicio!==false)
								$sor["film_cim"]=str_ireplace($eredetiTalalat, "<b>".$eredetiTalalat."</b>", $sor["film_cim"]);
							
						}
						//else $valasz.=$sor["film_cim"];
						
						if(isset($_SESSION["fejlec_sorrend"])){
							$j=0;
							foreach($fejlecTomb as $elem){
								$elemTomb=explode(":",$elem);
								$sorszam=$elemTomb[0];
								$mutat=$elemTomb[1];
								if($mutat){
									$valasz.=getCellaTartalom($mezok[$sorszam]["dbnev"], $sor, $j+2, $kellTartalomId);
								}
								$j++;
							}
						}
						else{
							for($j=0; $j<count($mezok); $j++){
								if($mezok[$j]["dbnev"]!="besz_ar" && $mezok[$j]["dbnev"]!="ar_datum" || $_SESSION["jog"]=="admin")
									$valasz.=getCellaTartalom($mezok[$j]["dbnev"], $sor, $j+2, $kellTartalomId);
							}
						}
						
						if($_SESSION["jog"]=="premium"){
							$j++;
							$valasz.="<td";
							if($i==1) $valasz.=" id='tartalom".$j."'";
							$valasz.="><span id='hozzaadva".$sor["film_id"]."'>";
							$gets_vanekedvenc="select * from kedvencek where film_fk_id=".$sor["film_id"];
							$starttime=microtime(true);
							if($getq_vanekedvenc=$mysqli->query($gets_vanekedvenc)){
								$endtime=microtime(true);
								$duration=$endtime-$starttime;
								$times[]=array("lekérdezés" => $gets_vanekedvenc, "idő" => $duration);
								if($getq_vanekedvenc->num_rows>0) $valasz.="Hozzáadva";
								else $valasz.="<a href=# class='kedvencekhez' id='kj".$sor["film_id"]."'>Hozzáadás</a>";
							}
							else $valasz.="<a href=# class='kedvencekhez' id='kj".$sor["film_id"]."'>Hozzáadás</a>";
							$valasz.="</span></td>";	//kj=kedvencnek jelöl
						}
						$valasz.="</tr>";
						$i++;
					}
					$valasz.="</table>";
					
					//$valasz.="</div>";	// + "Lekérdezésekre fordított idő (s):<br />"
					unset($i, $nagykcim, $nagyeredeti, $pozicio);
				}
				else $valasz="Nincs találat.";
			}
			else $valasz="<font color='red'>Hiba történt lekérdezés közben!</font>";
		}
		else $valasz="<font color='red'>Ez az oldal önállóan nem használható!</font>";
	}
	else $valasz="<font color='red'>Az oldal megtekintéséhez bejelentkezés szükséges!</font>";
	echo $valasz;
	/*for($i=0; $i<count($times); $i++){
		echo "<br />Lekérdezés: ".$times[$i]["lekérdezés"]." (".$times[$i]["idő"]." s)";
	}*/
?>