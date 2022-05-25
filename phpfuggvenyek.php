<?php
@$mysqli->query("SET CHARACTER SET UTF8");
@$mysqli->query("SET COLLATE UTF-8_hungarian_ci");

/**
 *átnevezi a fájlokat URL-ekben használható, a gép számára könnyen értelmezhető nevű fájlokká
 *@param string $eredeti az eredeti fájlnév, amit konvertálni szeretnénk
 *@return string visszatérési érték a "barátságos", átalakított URL, amiben nincs ékezet és köz
 **/
function urlfajlnev($eredeti=""){
	if($eredeti=="") return false;
	else{
		$hibasbetuk=array('á','Á','é','É','í','Í','ó','Ó','ö','Ö','ő','Ő','ú','Ú','ü','Ü','ű','Ű',' ');
      $jobetuk=array('a','a','e','e','i','i','o','o','o','o','o','o','u','u','u','u','u','u','_');
		return strtolower(str_replace($hibasbetuk, $jobetuk, $eredeti));
	}
}
/*
 *kiemeli egy szövegben a meghatározott szavakat
 *@param string $text a szöveg, amiben keresünk
 *@param string $words a keresendő kifejezés (szó, szavak)
 *@return string a formázott szöveg
 **/
function highlight($text, $words) {
	preg_match_all('~\w+~', $words, $m);
	if(!$m)
		return $text;
	$re = '~\\b(' . implode('|', $m[0]) . ')\\b~i';
	return preg_replace($re, '<b>$0</b>', $text);
}


/**
 *meghatározza 2 szám legkisebb közös többszörösét
 *@param int $a az egyik szám
 *@param int $b a másik szám
 *@return int $lehetseges a megadott 2 szám legkisebb közös többszöröse
 **/
function lkkt($a, $b){
	if($a>=$b){
		$lehetseges=$a;
		$lepeskoz=$a;
	}
	else{
		$lehetseges=$b;
		$lepeskoz=$b;
	}
	do{
		if($lehetseges%$a==0 && $lehetseges%$b==0) return $lehetseges;
		else $lehetseges+=$lepeskoz;
	}while($lehetseges%$a!=0 || $lehetseges%$b!=0);
}

/**
 * Megvizsgálja, hogy van-e jóváhagyásra váró módosítás a filmtárban
 * @param object $dbKezeles a kívülről érkező $mysqli objektum az adatbázis kezeléshez
 **/
function vaneJovahagynivalo(){
	global $mysqli;
	$keres_sql="select * from ".VISSZAJELZES." where jovahagyva=0";
	$keres_res=$mysqli->query($keres_sql);
	
	if($keres_res){
		if($keres_res->num_rows){
			return true;
		}
		else return false;
	}
	else return false;
}

/**
 * Átkapcsolja az adatbázisban a frissítés jóváhagyás állapotát, ha igaz a paraméter.
 * @param object $dbKezeles a kívülről érkező $mysqli objektum az adatbázis kezeléshez
 * @param string $ujdonsagok mit kell jóváhagyni
 **/
function frissitesJovahagyas($ujdonsagok){
	global $mysqli;
	//a connect.inc.php egyelőre nem töltődik be, a dbnames.inc.php igen!
	$keres_sql="select * from ".VISSZAJELZES." where frissites_leiras='$ujdonsagok' and jovahagyva=0";
	$jovahagy_sql="update ".VISSZAJELZES." set jovahagyva=1, jovahagy_datum='".date("Y-m-d")."' where frissites_leiras='$ujdonsagok'";
	
	$keres_res=$mysqli->query($keres_sql);
	if($keres_res){
		if($keres_res->num_rows){
			$jovahagy_res=$mysqli->query($jovahagy_sql);
			if($jovahagy_res) echo "Sikeres visszajelzés!";
			else echo "Hiba történt visszajelzés közben!";
		}
		else echo "Nem található a jóváhagyni kívánt módosítás!";
	}
	else echo "Hiba történt a módosítás keresése közben!";
}

/**
 * Lekérdezi a műfajok teljes listáját select tagben, option alelemek formájában.
 * @param int $mufajId megadható a műfaj azonosítója, amit kiválasztottként szeretnénk látni,
 * de ez elhagyható
 * @param string $mufaj megadható a műfaj megnevezése, amit kiválasztottként szeretnénk látni,
 * de ez elhagyható
 **/
function getMufajok($mufajId=null, $mufaj=null){
	global $mysqli;
	
	$sql="select * from ".MUFAJOK." order by mufaj";
	$res=$mysqli->query($sql) or die("Hiba történt lekérdezés közben!");
	
	if($res->num_rows){
		while($sor=$res->fetch_assoc()){
			echo "<option value=".$sor["id"];
			if(!is_null($mufajId) && ($sor["id"]==$mufajId || $sor["mufaj"]==$mufajId)) echo " selected='selected'";
			if(!is_null($mufaj) && $sor["mufaj"]==$mufaj) echo " selected='selected'";
			echo ">".$sor["mufaj"]."</option>";
		}
	}
}

/**
 * Megjeleníti a paraméterként megadott adatbázis mező értékét egy táblázat cellájában.
 * Szükség esetén a másik paraméterben tárolt sorszámot adja azonosítóként a cellának.
 * @param string $dbnev a lekérdezendő adatbázis mező neve
 * @param array $adat az adatbázis tábla aktuális sora oszlopaiból alkotott tömb
 * @param int $sorszam az oszlop sorszáma (soron belül hanyadik cella)
 * @param boolean $kellId megadja, hogy az első sorban vagyunk-e (kell-e tartalom id)
 * @return string egy teljes cella a tartalmával
 */
function getCellaTartalom($dbnev, $adat, $sorszam, $kellId){
	$tartalom="<td";
	
	switch($dbnev){
		case "film_cim":{
			if($kellId) $tartalom.=" id='tartalom".$sorszam."'";
			$tartalom.="><span class='mod' id='f".$adat["film_id"]."'>";
			$tartalom.=$adat[$dbnev];
			if($adat["kepsrc"]!="" && !is_null($adat["kepsrc"])) $tartalom.="<input type='hidden' id='link".$adat["film_id"]."' value='".$adat["kepsrc"]."' />";
			$tartalom.="</span>";
			if($_SESSION["jog"]=="admin")
				$tartalom.=" <img src='img/modositas.png' class='ikon ceruza' id='t".$adat["film_id"]."' />";
			$tartalom.="<div id='et_".$adat["film_id"]."'></div>";
		}; break;
		case "mufaj":{
			if($_SESSION["jog"]=="admin")
				$tartalom.=" class='omufaj' id='omufaj_".$adat["film_id"]."'";
			$tartalom.=">";
			if(is_null($adat[$dbnev])) $tartalom.="?";
			else $tartalom.=$adat[$dbnev];
		}; break;
		case "poz":{
			if($_SESSION["jog"]=="admin")
				$tartalom.=" class='modpoz' id='modpoz_".$adat["film_id"]."'";
			$tartalom.=">".$adat[$dbnev];
		}; break;
		case "rate":{
			if($kellId) $tartalom.=" id='tartalom".$sorszam."'";
			$tartalom.=">";
			for($cv=1; $cv<=5; $cv++){
				if($cv<=$adat[$dbnev])
					$tartalom.="<img src='img/telecsillag16.png'";
				else $tartalom.="<img src='img/urescsillag_atlatszohatter.png'";
				if($_SESSION["jog"]=="admin")
					$tartalom.=" class='ikon' id='m".($cv-1).$adat["film_id"]."'";
				$tartalom.=" />";
			}
		}; break;
		case "dvd_kateg":{
			if($kellId) $tartalom.=" id='tartalom".$sorszam."'";
			$tartalom.=" class='okat'>".$adat[$dbnev];
		}; break;
		case "rogz_datum":{
			if($kellId) $tartalom.=" id='tartalom".$sorszam."'";
			$tartalom.=">".$adat[$dbnev];
		}; break;
		case "rendezo":{
			if($kellId) $tartalom.=" id='tartalom".$sorszam."'";
			$tartalom.=" class='orendezo'>".$adat[$dbnev];
		}; break;
		case "besz_ar":{
			if($kellId) $tartalom.=" id='tartalom".$sorszam."'";
			$tartalom.=" class='obeszar'>".$adat[$dbnev];
			if($adat[$dbnev]!="") $tartalom.=" Ft";
		}; break;
		case "ar_datum":{
			if($kellId) $tartalom.=" id='tartalom".$sorszam."'";
			$tartalom.=" class='obeszdatum'>";
			if($adat[$dbnev]!="0000-00-00") $tartalom.=$adat[$dbnev];
		}; break;
		case "aktiv":{
			if($kellId) $tartalom.=" id='tartalom".$sorszam."'";
			$tartalom.=" align='center' class='oaktivitas'>";
			if($adat[$dbnev]) $tartalom.="<img src='img/pipa.png' alt='aktív' title='aktív'";
			else $tartalom.="<img src='img/x.png' alt='inaktív' title='inaktív'";
			if($_SESSION["jog"]=="admin")
				$tartalom.=" class='ikon' id='a".$adat["film_id"]."'";
			$tartalom.=" />";
		}; break;
		default:{
			$tartalom.=$adat[$dbnev];
		}
	}
	$tartalom.="</td>";
	
	return $tartalom;
}

function teszt(){
	echo "Teszt";
}
?>