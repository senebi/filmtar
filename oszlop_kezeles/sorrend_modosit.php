<?php
	if(isset($_POST)){
		@session_start();
		include_once("../initial.php");
		include_once("../config.php");
		
		if(isset($_POST["elerheto"], $_POST["kivalasztott"])){
			$oszlopok=$mysqli->real_escape_string($_POST["kivalasztott"]);
			if(strlen($oszlopok)>0 && strlen($_POST["elerheto"])>0) $oszlopok.=",";
			$oszlopok.=$mysqli->real_escape_string($_POST["elerheto"]);
			
			$sql="UPDATE ".FELHASZNALOK." set fejlec_sorrend='".$oszlopok."' where user='".$_SESSION["user"]."'";
			
			$mysqli->query($sql) or die("Hiba történt módosítás közben!");
			$_SESSION["fejlec_sorrend"]=$oszlopok;
		}
		else if(isset($_POST["reset"])){
			$sql="UPDATE ".FELHASZNALOK." set fejlec_sorrend='' where user='".$_SESSION["user"]."'";
			
			$mysqli->query($sql) or die("Hiba történt módosítás közben!");
			unset($_SESSION["fejlec_sorrend"]);
		}
			
			//elérhető oszlopok blokkja
			if(isset($_SESSION["fejlec_sorrend"])){
				if($_SESSION["fejlec_sorrend"]!=""){
					$fejlecTomb=explode(",",$_SESSION["fejlec_sorrend"]);
					$i=0;
					foreach($fejlecTomb as $elem){
						$elemTomb=explode(":",$elem);
						$sorszam=$elemTomb[0];
						$mutat=$elemTomb[1];
						if(!$mutat)
							echo "<li id='fejlec_".$sorszam."' class='ui-state-default'>".$mezok[$sorszam]["fejlec"]."</li>";
						
						$i++;
					}
				}
				else{
					for($j=0; $j<count($mezok); $j++)
						echo "<li id='fejlec_".$j."' class='ui-state-default'>".$mezok[$j]["fejlec"]."</li>";
				}
			}
			else{
				for($j=0; $j<count($mezok); $j++)
					echo "<li id='fejlec_".$j."' class='ui-state-default'>".$mezok[$j]["fejlec"]."</li>";
			}
			
			echo "|";
			
			//most jön a kiválasztottak blokkja
			if(isset($_SESSION["fejlec_sorrend"])){
				$fejlecTomb=explode(",",$_SESSION["fejlec_sorrend"]);
				$i=0;
				foreach($fejlecTomb as $elem){
					$elemTomb=explode(":",$elem);
					$sorszam=$elemTomb[0];
					$mutat=$elemTomb[1];
					if($mutat)
						echo "<li id='fejlec_".$sorszam."' class='ui-state-highlight'>".$mezok[$sorszam]["fejlec"]."</li>";
					
					$i++;
				}
			}
	}
	else echo "Nem érkeztek adatok!";
?>