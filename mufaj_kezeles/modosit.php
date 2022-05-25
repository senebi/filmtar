<?php
	if(isset($_POST["modosit-gomb"])){
		@session_start();
		include_once("../initial.php");
	
		$mufajDb = count($_POST["mufaj_id"]);
		
		if($mufajDb>0){
			for($i=0;$i<$mufajDb;$i++){
				$mufaj_mod=$mysqli->real_escape_string($_POST["mufaj_mod"][$i]);
				$sql="UPDATE ".MUFAJOK." set mufaj='".$mufaj_mod."' where id=".$_POST["mufaj_id"][$i];

				$mysqli->query($sql) or die("Hiba történt módosítás közben!");
			}
			
			header("Location:index.php?modositas=siker");
		}
		else header("location: index.php");
	}
	else header("location: index.php");
?>