<?php
	session_start();
	$included=strtolower(realpath(__FILE__))!=strtolower(realpath($_SERVER["SCRIPT_FILENAME"]));
	if(!$included) header("Content-type: text/html; charset=utf-8");
	if(isset($_POST["sid"])){
		include_once("initial.php");
	}
	
	if(isset($_SESSION["belepve"])){
		if(isset($_POST["sid"]) || $included){
			$lek="select * from ".FILMEK." left outer join ".MUFAJOK." on
			".FILMEK.".film_mufaj=".MUFAJOK.".id order by";
			if(isset($_POST["miszerint"]) && isset($_POST["irany"])){
				$miszerint=$mysqli->real_escape_string($_POST["miszerint"]);
				$irany=$mysqli->real_escape_string($_POST["irany"]);
				
				if(isset($_POST["src"]) && isset($_POST["title"])){
					$src=$mysqli->real_escape_string($_POST["src"]);
					$title=$mysqli->real_escape_string($_POST["title"]);
				}
				
				$lek.=" ".$miszerint." ".$irany;
				if($miszerint=="rogz_datum"){
					if($irany=="asc") $lek.=", film_id";
					else $lek.=", film_id desc";
				}
			}
			else $lek.=" film_cim";
			
			if($res=$mysqli->query($lek)){
				if($res->num_rows!=0){
					$i=1;
					
					// TESZT --------------------------------------------------
					/*while($sor=$res->fetch_row()){
						for($k=0; $k<10; $k++)
							echo $sor[$k]." ";
						echo "<br />";
					}*/
					// TESZT --------------------------------------------------
					
					//config fájl beimportálása
					include("config.php");
					
					//mivel az adatbázisból listázandó mezők sorszáma 2-től kezdődik, $j+2 a fejléc id-je
					echo "<div id=\"ragados\"><table class=\"lista\"><tr><th id='fejlec1'>Nr.";
					if(isset($_SESSION["fejlec_sorrend"])){
						$fejlecTomb=explode(",",$_SESSION["fejlec_sorrend"]);
						$j=0;
						foreach($fejlecTomb as $elem){
							$elemTomb=explode(":",$elem);
							$sorszam=$elemTomb[0];
							$mutat=$elemTomb[1];
							if($mutat){
								echo "</th><th id='fejlec".($j+2)."'>".$mezok[$sorszam]["fejlec"]." ";
								if(isset($miszerint) && $miszerint==$mezok[$sorszam]["dbnev"])
									echo "<img src='$src' title='$title' alt='$title' class='ikon r".$miszerint."' />";
								else echo "<img src='img/novekvo.png' title='növekvő sorrend' alt='növekvő sorrend' class='ikon r".$mezok[$sorszam]["dbnev"]."' />";
							}
							$j++;
						}
					}
					else{
						for($j=0; $j<count($mezok); $j++){
							if($mezok[$j]["dbnev"]!="besz_ar" && $mezok[$j]["dbnev"]!="ar_datum" || $_SESSION["jog"]=="admin"){
								echo "</th><th id='fejlec".($j+2)."'>".$mezok[$j]["fejlec"]." ";
								if(isset($miszerint) && $miszerint==$mezok[$j]["dbnev"])
									echo "<img src='$src' title='$title' alt='$title' class='ikon r".$miszerint."' />";
								else echo "<img src='img/novekvo.png' title='növekvő sorrend' alt='növekvő sorrend' class='ikon r".$mezok[$j]["dbnev"]."' />";
							}
						}
					}
					
					echo "</th>";
					if($_SESSION["jog"]=="premium")
						echo "<th id='fejlec".($j+2)."'>Kedvencek</th>";
					echo "</tr>";
					echo "</table></div>";
					
					//**************** ragadós vége, jön a rendes táblázat fejléce, majd a tartalma... ****************************
					
					echo "<table class=\"lista\" id=\"adattabla\"><tr><th>Nr.";
					if(isset($_SESSION["fejlec_sorrend"])){
						$j=0;
						foreach($fejlecTomb as $elem){
							$elemTomb=explode(":",$elem);
							$sorszam=$elemTomb[0];
							$mutat=$elemTomb[1];
							if($mutat){
								echo "</th><th id='fejlec".($j+2)."'>".$mezok[$sorszam]["fejlec"]." ";
								if(isset($miszerint) && $miszerint==$mezok[$sorszam]["dbnev"])
								echo "<img src='$src' title='$title' alt='$title' class='ikon r".$miszerint."' />";
								else echo "<img src='img/novekvo.png' title='növekvő sorrend' alt='növekvő sorrend' class='ikon r".$mezok[$sorszam]["dbnev"]."' />";
							}
							$j++;
						}
					}
					else{
						for($j=0; $j<count($mezok); $j++){
							if($mezok[$j]["dbnev"]!="besz_ar" && $mezok[$j]["dbnev"]!="ar_datum" || $_SESSION["jog"]=="admin"){
								echo "</th><th>".$mezok[$j]["fejlec"]." ";
								if(isset($miszerint) && $miszerint==$mezok[$j]["dbnev"])
									echo "<img src='$src' title='$title' alt='$title' class='ikon r".$miszerint."' />";
								else echo "<img src='img/novekvo.png' title='növekvő sorrend' alt='növekvő sorrend' class='ikon r".$mezok[$j]["dbnev"]."' />";
							}
						}
					}
					
					echo "</th>";
					if($_SESSION["jog"]=="premium")
						echo "<th>Kedvencek</th>";
					echo "</tr>";
					
					while($sor=$res->fetch_assoc()){
						echo "<tr class='";
						echo $i%2==1 ? "paratlan" : "paros";
						if(!$sor["aktiv"]) echo " inaktiv";
						echo "' id='sor".$sor["film_id"]."'>";
						
						$kellTartalomId=false;
						echo "<td";
						if($i==1){
							echo " id='tartalom1'";
							$kellTartalomId=true;
						}
						echo "><input type='hidden' id='h".$i."' value=".$sor["film_id"]." />".$i."</td>";
						
						if(isset($_SESSION["fejlec_sorrend"])){
							$j=0;
							foreach($fejlecTomb as $elem){
								$elemTomb=explode(":",$elem);
								$sorszam=$elemTomb[0];
								$mutat=$elemTomb[1];
								if($mutat){
									echo getCellaTartalom($mezok[$sorszam]["dbnev"], $sor, $j+2, $kellTartalomId);
								}
								$j++;
							}
						}
						else{
							for($j=0; $j<count($mezok); $j++){
								if($mezok[$j]["dbnev"]!="besz_ar" && $mezok[$j]["dbnev"]!="ar_datum" || $_SESSION["jog"]=="admin")
									echo getCellaTartalom($mezok[$j]["dbnev"], $sor, $j+2, $kellTartalomId);
							}
						}
						
						if($_SESSION["jog"]=="premium"){
							$j++;
							echo "<td";
							if($kellTartalomId) echo " id='tartalom".$j."'";
							echo "><span id='hozzaadva".$sor["film_id"]."'>";
							$gets_vanekedvenc="select * from kedvencek where film_fk_id=".$sor["film_id"];
							if($getq_vanekedvenc=$mysqli->query($gets_vanekedvenc)){
								if($getq_vanekedvenc->num_rows>0) echo "Hozzáadva";
								else echo "<a href=# class='kedvencekhez' id='kj".$sor["film_id"]."'>Hozzáadás</a>";
							}
							else echo "<a href=# class='kedvencekhez' id='kj".$sor["film_id"]."'>Hozzáadás</a>";
							echo "</span></td>";	//kj=kedvencnek jelöl
						}
						
						echo "</tr>";
						$i++;
					}
					
					$lek2="select sum(besz_ar) as osszeg, avg(besz_ar) as atlag from ".FILMEK;
					if($res2=$mysqli->query($lek2)){
						if($_SESSION["jog"]=="admin"){
							echo "<tr><td colspan=".($j-2)."><b>Összeg, átlag: </b></td>";
							$stat=$res2->fetch_assoc();
							
							echo "<td><b>".$stat["osszeg"]."</b></td><td><b>".round($stat["atlag"],0)."</b></td>";
							echo "<td>&nbsp;</td>";
							echo "</tr>";
						}
					}
					echo "</table>";
				}
				else echo "A filmek listája jelenleg üres.";
			}
		}
		else echo "<font color='red'>Ez az oldal önállóan nem használható!</font>";
	}
	else echo "<font color='red'>Az oldal megtekintéséhez bejelentkezés szükséges!</font>";
?>