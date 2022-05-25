<?php
	session_start();
	header("Content-type: text/html; charset=utf-8");
	ini_set("memory_limit","101M");
	include_once("initial.php");
?>
	<script language="javascript" src="jquery-<?php if(defined("jqueryverzio")) echo jqueryverzio; else echo "1.10.2.min"; ?>.js"></script>
<?php
	if(isset($_SESSION["belepve"])){
?>
		<div id="folyamat" style="width: 100%; text-align: center; height: 768px; padding-top: 330px;">
			<img src='img/process.gif' /> feltöltés folyamatban...
		</div>
<?php
		if(isset($_FILES["modkep"])){
			if(isset($_POST["fajl_film_id"]))
				$fajl_film_id=$mysqli->real_escape_string($_POST["fajl_film_id"]);
			$fajl=$_FILES["modkep"];
			$cel="img/boritok";
			$eredetinev=$fajl["name"];
			$kit=substr(strrchr($eredetinev, '.'), 1);
			$kit=strtoupper($kit);
			if($kit=="JPG" || $kit=="PNG" || $kit=="GIF"){
				$hibasbetuk=array('á','é','í','ó','ö','ő','ú','ü','ű',' ');
				$jobetuk=array('a','e','i','o','o','o','u','u','u','_');
				for($i=0; $i<strlen($eredetinev); $i++){
					for($j=0; $j<count($hibasbetuk); $j++){
						if($eredetinev[$i]==$hibasbetuk[$j]) $eredetinev[$i]=$jobetuk[$j];
					}
				}
				unset($hibasbetuk);
				unset($jobetuk);
				unset($i);
				unset($j);

				$fajl["name"]=$eredetinev;
				if(is_uploaded_file($fajl["tmp_name"])){
					if(!move_uploaded_file($fajl["tmp_name"], $cel."/".$fajl["name"])){
						echo "<font color='red'>A fájlfeltöltés sikertelen!</font>";
						echo "<script language='javascript'>
							$('#folyamat').css('display', 'none');
						</script>";
					}
					else {
						echo "<script language='javascript'>
							$('#folyamat').css('display', 'block');
						</script>";
						$meret=$fajl["size"];
						if($meret>=(1024*1024)) $meret=round(($meret/(1024*1024)),2)." Mb";
						elseif($meret>=1024) $meret=round(($meret/1024),2)." Kb";
						else $meret=$meret." b";
						echo "Sikeres művelet. További adatok:<br />
						Név: ".$fajl["name"]."<br />
						Típus: ".$fajl["type"]."<br />
						Méret: ".$meret;
						
						//átméretezés, másolás
						$nagykep=$cel."/".$fajl["name"];
						list($szelesseg, $magassag)=getimagesize($nagykep);
						$kozepeskep=imagecreatetruecolor(930, 621);
						if($kit=="JPG") $forras1=imagecreatefromjpeg($nagykep);
						else if($kit=="GIF") $forras1=imagecreatefromgif($nagykep);
						else $forras1=imagecreatefrompng($nagykep);
						imagecopyresampled($kozepeskep, $forras1, 0, 0, 0, 0, 930, 621, $szelesseg, $magassag);
						if($kit=="JPG") imagejpeg($kozepeskep, "img/boritok/nagy/".$fajl["name"]);
						else if($kit=="GIF") imagegif($kozepeskep, "img/boritok/nagy/".$fajl["name"]);
						else imagepng($kozepeskep, "img/boritok/nagy/".$fajl["name"]);
						
						unset($forras1);
						unset($kozepeskep);
						
						$thumb=imagecreatetruecolor(300, 203);
						if($kit=="JPG") $forras2=imagecreatefromjpeg($nagykep);
						else if($kit=="GIF") $forras2=imagecreatefromgif($nagykep);
						else $forras2=imagecreatefrompng($nagykep);
						imagecopyresampled($thumb, $forras2, 0, 0, 0, 0, 300, 203, $szelesseg, $magassag);
						if($kit=="JPG") imagejpeg($thumb, "img/boritok/kicsi/".$fajl["name"]);
						else if($kit=="GIF") imagegif($thumb, "img/boritok/kicsi/".$fajl["name"]);
						else imagepng($thumb, "img/boritok/kicsi/".$fajl["name"]);
						
						unset($forras2);
						unset($thumb);
						echo "<br />";
						
						//ideiglenes nagy fájl törlése
						unlink($nagykep);
						
						//adatbázisba rögzítés
						$modlek="update ".FILMEK." set kepsrc='$eredetinev' where film_id=$fajl_film_id";
						if($modres=$mysqli->query($modlek)){
							echo "A kép adatai rögzítésre kerültek.";
						}
						else echo "<font color='red'>Hiba történt az adatbázis-művelet végrehajtása közben!</font>";
					}
				}
			}
			else echo "Hibás formátum!";
		}
		else echo "<font color='red'>Ez az oldal fájlfeltöltésre használatos, de hiányzik a fájl!</font>";
	}
	else echo "<font color='red'>Az oldal megtekintéséhez bejelentkezés szükséges!</font>";
?>