<?php
	session_start();
	header("Content-type: text/html; charset=utf-8");
	if(isset($_POST["sid"])) include_once("initial.php");

	if(isset($_SESSION["belepve"])){
		if(isset($_POST["sid"])){
			if(isset($_POST["film_id"])){
				$film_id=$mysqli->real_escape_string($_POST["film_id"]);
				$lek="select * from ".FILMEK." where film_id=".$film_id;
				if($res=$mysqli->query($lek)){
					if($res->num_rows>0){
						$lekszereplo="select * from ".KAPCSOLO." inner join ".SZEREPLOK." on szereplo_fk_id=szereplo_id where film_fk_id=".$film_id." order by kapcsolat_id";
						if($resszereplo=$mysqli->query($lekszereplo)){
							echo "<h4>Szereplők:</h4>";
							if($resszereplo->num_rows>0){
								while($sor=$resszereplo->fetch_assoc()){
									echo $sor["szereplo_nev"]."<br />";
								}
							}
							else echo "Nincs szereplő.";
						}
						else echo "<font color='red'>Hiba történt lekérdezés közben!</font>";
					}
					else echo "<font color='red'>Nincs ilyen film!</font>";
				}
				else echo "<font color='red'>Hiba történt lekérdezés közben!</font>";
			}
			else echo "<font color='red'>Hiba: hiányzik a film azonosítója!</font>";
		}
		else echo "<font color='red'>Ez az oldal önállóan nem használható!</font>";
	}
	else echo "<font color='red'>Az oldal megtekintéséhez bejelentkezés szükséges!</font>";
?>