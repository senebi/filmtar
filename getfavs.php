<?php
	session_start();
	if(isset($_POST["sid"])) include_once("initial.php");
	else header("Content-type: text/html; charset=utf-8");
?>
	<link rel="stylesheet" type="text/css" href="stilus.css" />
	<script language="javascript" src="jsfv.js"></script>
	<script language="javascript" src="jquery-<?php if(defined("jqueryverzio")) echo jqueryverzio; else echo "1.10.2.min"; ?>.js"></script>
<?php
	$valasz="";
	if(isset($_SESSION["belepve"])){
		if(isset($_POST["sid"])){
			
			$lek="select * from kedvencek inner join ".FILMEK.
			" on kedvencek.film_fk_id=".FILMEK.".film_id where user_fk_id='".$_SESSION["user"]."' order by film_cim";
			
			if($res=$mysqli->query($lek)){
				if($res->num_rows>0){
					$i=1;
					$valasz.="<table class=\"lista\">";
					
					$valasz.="<tr><th id='fejlec1'>Nr.</th><th id='fejlec2'>Cím</th>";
					$valasz.="<th id='fejlec3'>Érték</th>";
					$valasz.="<th id='fejlec4'>Kat.</th>";
					$valasz.="<th id='fejlec5'>Hozzáadva</th>";
					$valasz.="<th id='fejlec6'>Rendező</th>";
					$valasz.="<th id='fejlec7'>Típus</th>";
					$valasz.="<th id='fejlec8'>Aktivitás</th>";
					
					if($_SESSION["jog"]=="premium")
						$valasz.="<th id='fejlec9'>Kedvencek</th>";
					$valasz.="</tr></table><div id='tablahely'><table class='lista'>";
					while($sor=$res->fetch_assoc()){
						$valasz.="<tr class='";
						$valasz.= $i%2==1 ? "paratlan" : "paros";
						if(!$sor["aktiv"]) $valasz.=" inaktiv";
						$valasz.="' id='sor".$sor["film_id"]."'><td";
						if($i==1) $valasz.=" id='tartalom1'";
						$valasz.="><input type='hidden' id='h".$i."' value=".$sor["film_id"]." />".$i."</td><td";
						if($i==1) $valasz.=" id='tartalom2'";
						$valasz.="><span class='mod' id='f".$sor["film_id"]."'>";
						if($sor["kepsrc"]!="") $valasz.="<a href='img/boritok/nagy/".$sor["kepsrc"]."' target='new'>";
						$valasz.=$sor["film_cim"];
						if($sor["kepsrc"]!="") $valasz.="</a><input type='hidden' id='link".$sor["film_id"]."' value='".$sor["kepsrc"]."' />";
						$valasz.="</span></td><td";
						if($i==1) $valasz.=" id='tartalom3'";
						$valasz.=">";
						if($sor["rate"]==0){
							for($cv=0; $cv<5; $cv++)
								$valasz.="<img src='img/urescsillag16.png' />";
						}
						else{
							for($cv=0; $cv<5; $cv++){
								if($cv<$sor["rate"])
									$valasz.="<img src='img/telecsillag16.png' />";
								else $valasz.="<img src='img/urescsillag16.png' />";
							}
							/*for($cv=$sor["rate"]; $cv<5; $cv++){
								$valasz.="<img src='img/urescsillag16.png'";
								if($_SESSION["jog"]=="admin")
									$valasz.=" class='ikon' id='m$cv".$sor["film_id"]."'";
								$valasz.=" />";
							}*/
						}
						$valasz.="</td><td";
						if($i==1) $valasz.=" id='tartalom4'";
						$valasz.=">".$sor["dvd_kateg"]."</td><td";
						if($i==1) $valasz.=" id='tartalom5'";
						$valasz.=">".$sor["rogz_datum"]."</td><td";
						if($i==1) $valasz.=" id='tartalom6'";
						$valasz.=">".$sor["rendezo"]."</td><td";
						if($i==1) $valasz.=" id='tartalom7'";
						$valasz.=">".$sor["film_tipus"]."</td><td";
						if($i==1) $valasz.=" id='tartalom8'";
						$valasz.=" align='center'>";
						if($sor["aktiv"]) $valasz.="<img src='img/pipa.png' alt='aktív' title='aktív'";
						else $valasz.="<img src='img/x.png' alt='inaktív' title='inaktív'";
						
						$valasz.=" /></td>";
						if($_SESSION["jog"]=="premium")
							$valasz.="<td id='tartalom9'><a href=# class='kedvenctorol' id='kt".$sor["film_id"]."'>Eltávolítás</a></td>";	//kt=kedvencekből töröl
						$valasz.="</tr>";
						$i++;
					}
					$valasz.="</table></div>";
				}
				else $valasz="A kedvencek listája jelenleg üres.";
			}
			else $valasz="<font color='red'>Hiba történt lekérdezés közben!</font>";
		}
		else $valasz="<font color='red'>Ez az oldal önállóan nem használható!</font>";
	}
	else $valasz="<font color='red'>Az oldal megtekintéséhez bejelentkezés szükséges!</font>";
	echo $valasz;
?>