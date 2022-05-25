<?php
	header("Content-type: text/html; charset=utf-8");
	include_once("initial.php");
	ini_set("memory_limit","200M");

//var_dump($_POST);
//echo "<br />";

if(isset($_POST['APC_UPLOAD_PROGRESS'])){
	$ul_id=$_POST["APC_UPLOAD_PROGRESS"];
	$res=array("<div style='display: none'>hiba </div>", "<div style='display: none'>siker </div>");
	if(isset($_FILES["modkepul"])){
		$fajl=$_FILES["modkepul"];
		$cel="img/boritok";
		$eredetinev=$fajl["name"];
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."/filmtar/".$cel."/nagy/".$eredetinev)
			&& file_exists($_SERVER["DOCUMENT_ROOT"]."/filmtar/".$cel."/kicsi/".$eredetinev)){
			echo $res[0]."A kiválasztott fájl már létezik a szerveren!";
			die;
		}
		trim($eredetinev);
		$kit=substr(strrchr($eredetinev, '.'), 1);
		$kit=strtoupper($kit);
		if($kit=="JPG" || $kit=="PNG" || $kit=="GIF"){
			
			$fajl["name"]=urlfajlnev($eredetinev);
			
			$nagykep=$cel."/".$fajl["name"];
			list($szelesseg, $magassag)=getimagesize($fajl["tmp_name"]);
			//ez így nem mindig fér bele a memóriába!! Fatal error: Allowed memory size of 25165824 bytes exhausted (tried to allocate 13184 bytes) in /ul/upload.php on line 154
			if(is_uploaded_file($fajl["tmp_name"])){
				if(!move_uploaded_file($fajl["tmp_name"], $cel."/".$fajl["name"])){
					echo $res[0]."<font color='red'>A fájlfeltöltés sikertelen!</font>";
					die;
				}
				else{
					$meret=$fajl["size"];
					if($meret>=(1024*1024)) $meret=round(($meret/(1024*1024)),2)." Mb";
					else if($meret>=1024) $meret=round(($meret/1024),2)." Kb";
					else $meret=$meret." b";
					
					$kicsisikerult=false;
					$nagysikerult=false;
					$arany=$szelesseg/$magassag;
					
					//átméretezés, másolás
					//nagy kép mentése
					$nagymappa=$cel."/nagy";
					$kismappa=$cel."/kicsi";
					$max_nagy_szelesseg=900;
					$max_nagy_magassag=800;
					$max_kicsi_szelesseg=300;
					$max_kicsi_magassag=267;
					
					//az átméretezett, "új" nagy méretű képnek bele kell férnie 1 900x800-as téglalapba!
					//a kis képnek bele kell férnie 1 300x266,666666 (~267) méretű téglalapba!
					
					if($magassag>=$szelesseg){	//állókép vagy négyzet
						if($magassag>$max_nagy_magassag){	//össze kell nyomni
							$uj_magassag=$max_nagy_magassag;
							$uj_szelesseg=$uj_magassag*$arany;
							$nagy=imagecreatetruecolor($uj_szelesseg, $uj_magassag);
							if($kit=="JPG") $forras1=imagecreatefromjpeg($nagykep);
							else if($kit=="GIF") $forras1=imagecreatefromgif($nagykep);
							else $forras1=imagecreatefrompng($nagykep);
							imagecopyresampled($nagy, $forras1, 0, 0, 0, 0, $uj_szelesseg, $uj_magassag, $szelesseg, $magassag);
							if($kit=="JPG") $nagysikerult=imagejpeg($nagy, $cel."/nagy/".$fajl["name"]);
							else if($kit=="GIF") $nagysikerult=imagegif($nagy, $cel."/nagy/".$fajl["name"]);
							else $nagysikerult=imagepng($nagy, $cel."/nagy/".$fajl["name"]);
						}
						else{	//eredeti méretben tároljuk
							$nagy=imagecreatetruecolor($szelesseg, $magassag);
							if($kit=="JPG") $forras1=imagecreatefromjpeg($nagykep);
							else if($kit=="GIF") $forras1=imagecreatefromgif($nagykep);
							else $forras1=imagecreatefrompng($nagykep);
							imagecopyresampled($nagy, $forras1, 0, 0, 0, 0, $szelesseg, $magassag, $szelesseg, $magassag);
							if($kit=="JPG") $nagysikerult=imagejpeg($nagy, $cel."/nagy/".$fajl["name"]);
							else if($kit=="GIF") $nagysikerult=imagegif($nagy, $cel."/nagy/".$fajl["name"]);
							else $nagysikerult=imagepng($nagy, $cel."/nagy/".$fajl["name"]);
						}
						
						//kicsi álló/négyzet
						if($magassag>$max_kicsi_magassag){	//összenyomjuk
							$uj_magassag=$max_kicsi_magassag;
							$uj_szelesseg=$uj_magassag*$arany;
							$thumb=imagecreatetruecolor($uj_szelesseg, $uj_magassag);
							if($kit=="JPG") $forras2=imagecreatefromjpeg($nagykep);
							else if($kit=="GIF") $forras2=imagecreatefromgif($nagykep);
							else $forras2=imagecreatefrompng($nagykep);
							imagecopyresampled($thumb, $forras2, 0, 0, 0, 0, $uj_szelesseg, $uj_magassag, $szelesseg, $magassag);
							if($kit=="JPG") $kicsisikerult=imagejpeg($thumb, $cel."/kicsi/".$fajl["name"]);
							else if($kit=="GIF") $kicsisikerult=imagegif($thumb, $cel."/kicsi/".$fajl["name"]);
							else $kicsisikerult=imagepng($thumb, $cel."/kicsi/".$fajl["name"]);
						}
						else{	//eredeti méret
							$thumb=imagecreatetruecolor($szelesseg, $magassag);
							if($kit=="JPG") $forras2=imagecreatefromjpeg($nagykep);
							else if($kit=="GIF") $forras2=imagecreatefromgif($nagykep);
							else $forras2=imagecreatefrompng($nagykep);
							imagecopyresampled($thumb, $forras2, 0, 0, 0, 0, $szelesseg, $magassag, $szelesseg, $magassag);
							if($kit=="JPG") $kicsisikerult=imagejpeg($thumb, $cel."/kicsi/".$fajl["name"]);
							else if($kit=="GIF") $kicsisikerult=imagegif($thumb, $cel."/kicsi/".$fajl["name"]);
							else $kicsisikerult=imagepng($thumb, $cel."/kicsi/".$fajl["name"]);
						}
					}
					else{	//fekvőkép
						if($szelesseg>$max_nagy_szelesseg){	//össze kell nyomni
							$uj_szelesseg=$max_nagy_szelesseg;
							$uj_magassag=$uj_szelesseg/$arany;
							$nagy=imagecreatetruecolor($uj_szelesseg, $uj_magassag);
							if($kit=="JPG") $forras1=imagecreatefromjpeg($nagykep);
							else if($kit=="GIF") $forras1=imagecreatefromgif($nagykep);
							else $forras1=imagecreatefrompng($nagykep);
							imagecopyresampled($nagy, $forras1, 0, 0, 0, 0, $uj_szelesseg, $uj_magassag, $szelesseg, $magassag);
							if($kit=="JPG") $nagysikerult=imagejpeg($nagy, $cel."/nagy/".$fajl["name"]);
							else if($kit=="GIF") $nagysikerult=imagegif($nagy, $cel."/nagy/".$fajl["name"]);
							else $nagysikerult=imagepng($nagy, $cel."/nagy/".$fajl["name"]);
						}
						else{	//eredeti méretben tároljuk
							$nagy=imagecreatetruecolor($szelesseg, $magassag);
							if($kit=="JPG") $forras1=imagecreatefromjpeg($nagykep);
							else if($kit=="GIF") $forras1=imagecreatefromgif($nagykep);
							else $forras1=imagecreatefrompng($nagykep);
							imagecopyresampled($nagy, $forras1, 0, 0, 0, 0, $szelesseg, $magassag, $szelesseg, $magassag);
							if($kit=="JPG") $nagysikerult=imagejpeg($nagy, $cel."/nagy/".$fajl["name"]);
							else if($kit=="GIF") $nagysikerult=imagegif($nagy, $cel."/nagy/".$fajl["name"]);
							else $nagysikerult=imagepng($nagy, $cel."/nagy/".$fajl["name"]);
						}
						
						//kicsi fekvő
						if($szelesseg>$max_kicsi_szelesseg){	//összenyomjuk
							$uj_szelesseg=$max_kicsi_szelesseg;
							$uj_magassag=$uj_szelesseg/$arany;
							$thumb=imagecreatetruecolor($uj_szelesseg, $uj_magassag);
							if($kit=="JPG") $forras2=imagecreatefromjpeg($nagykep);
							else if($kit=="GIF") $forras2=imagecreatefromgif($nagykep);
							else $forras2=imagecreatefrompng($nagykep);
							imagecopyresampled($thumb, $forras2, 0, 0, 0, 0, $uj_szelesseg, $uj_magassag, $szelesseg, $magassag);
							if($kit=="JPG") $kicsisikerult=imagejpeg($thumb, $cel."/kicsi/".$fajl["name"]);
							else if($kit=="GIF") $kicsisikerult=imagegif($thumb, $cel."/kicsi/".$fajl["name"]);
							else $kicsisikerult=imagepng($thumb, $cel."/kicsi/".$fajl["name"]);
						}
						else{	//eredeti méret
							$thumb=imagecreatetruecolor($szelesseg, $magassag);
							if($kit=="JPG") $forras2=imagecreatefromjpeg($nagykep);
							else if($kit=="GIF") $forras2=imagecreatefromgif($nagykep);
							else $forras2=imagecreatefrompng($nagykep);
							imagecopyresampled($thumb, $forras2, 0, 0, 0, 0, $szelesseg, $magassag, $szelesseg, $magassag);
							if($kit=="JPG") $kicsisikerult=imagejpeg($thumb, $cel."/kicsi/".$fajl["name"]);
							else if($kit=="GIF") $kicsisikerult=imagegif($thumb, $cel."/kicsi/".$fajl["name"]);
							else $kicsisikerult=imagepng($thumb, $cel."/kicsi/".$fajl["name"]);
						}
					}
					
					if($kicsisikerult && $nagysikerult){
						echo $res[1]."<div style='float: left; margin-right: 5px;'><img src='".$cel."/kicsi/".$fajl["name"]."' alt='".$fajl["name"]."' title='".$fajl["name"]."' /></div><div style='float: left'>Sikeres művelet. További adatok:<br />";
						echo "Név: <span id='aktualiskep'>".$fajl["name"]."</span><br />";
						echo "Típus: ".$fajl["type"]."<br />";
						echo "Méret: ".$meret."</div>";
					}
					else{
						if(!$kicsisikerult && !$nagysikerult)
							echo "<font color='red'>Az átméretezett képek mentése sikertelen!</font>";
						else if(!$kicsisikerult)
							echo "<font color='red'>A kis kép mentése sikertelen!</font>";
						else
							echo "<font color='red'>A nagy kép mentése sikertelen!</font>";
					}
					
					unset($forras2);
					unset($thumb);
					echo "<br />";
					
					//ideiglenes nagy fájl törlése
					unlink($nagykep);
				}
			}
			else{
				echo $res[0]."<font color='red'>Hiba történt a feltöltés során!</font>";
				die;
			}
		}
		else{
			echo $res[0]."<font color='red'>Hibás formátum!</font>";
			die;
		}
	}
	else{
		echo $res[0]."<font color='red'>Hiányzik a fájl!</font>";
		die;
	}
}
else{
	echo "<font color='red'>Nem rendeltetésszerű használat!</font>";
	die;
}
?>