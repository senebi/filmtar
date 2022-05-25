<?php
	session_start();
	include_once("initial.php");
	//else header("Content-type: text/html; charset=utf-8");

	$valasz="";
	if(isset($_SESSION["belepve"])){
		if(isset($_GET["term"], $_GET["mit"])){
			$kezdo=$mysqli->real_escape_string($_GET["term"]);
			$mit=$mysqli->real_escape_string($_GET["mit"]);
			if(strpos($mit, "rendezo")!==false){
				$lek="select distinct rendezo as rndz from ".FILMEK." where rendezo like '$kezdo%'";
			}
			else{
				$lek="select distinct szereplo_nev as sz from ".SZEREPLOK." where szereplo_nev like '".$kezdo."%'";
			}
			if($res=$mysqli->query($lek)){
				if($res->num_rows>0){
					$tmparr=array();
                    if(strpos($mit, "rendezo")!==false)
                        $mezo="rndz";
                    else $mezo="sz";
                
                    while($sor=$res->fetch_assoc())
                        $tmparr[]=stripslashes($sor[$mezo]);//htmlspecialchars($sor[$mezo], ENT_QUOTES);

					$valasz=json_encode($tmparr);
				}
				else $valasz=json_encode($tmparr);
			}
			else $valasz="Hiba történt lekérdezés közben!";
		}
		else $valasz="Hiányzik a keresendő kifejezés!";
	}
	else $valasz="Az oldal megtekintéséhez bejelentkezés szükséges!";
	echo $valasz;
?>