<?php
	if(isset($_POST["torles-mufaj-gomb"])){
		@session_start();
		include_once("../initial.php");
	
		$mufajDb = count($_POST["mufaj_chk"]);
		
		if($mufajDb>0){
			$sql="delete from ".MUFAJOK." where id in (".implode(",",$_POST["mufaj_chk"]).")";
			$mysqli->query($sql) or die("Hiba történt törlés közben!");
			
			header("Location:index.php?torles=siker");
		}
		else header("location: index.php");
	}
	else header("location: index.php");
?>