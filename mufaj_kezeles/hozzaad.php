<?php
	if(isset($_POST["uj-mufaj-gomb"])){
		@session_start();
		include_once("../initial.php");
		
		$mufaj_bevitel=$mysqli->real_escape_string($_POST["mufaj_bevitel"]);
		$sql="insert into ".MUFAJOK." (mufaj) values ('".$mufaj_bevitel."')";
		$res=$mysqli->query($sql) or die("Hiba történt hozzáadás közben!");
		header("location: index.php?hozzaadas=siker");
	}
	else header("location: index.php");
?>