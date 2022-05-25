<?php
	if(isset($_POST["mod-mufaj-gomb"])){
		@session_start();
		include_once("../initial.php");
		$mufajDb = count($_POST["mufaj_chk"]);
		
		if($mufajDb>0){
?>
<head>
	<title>Műfaj módosítás | Filmtár</title>
	<style type="text/css">
		div{
			margin-bottom: 0.5rem;
		}
		fieldset{
			display: inline-block;
		}
	</style>
</head>
<body>
<h1>Műfaj kezelés</h1>
<fieldset>
	<legend>Módosításra kijelölt műfajok</legend>
			<form name="mufaj_modositas" method="post" action="modosit.php">
<?php
			$sql="select * from ".MUFAJOK." where id in (".implode(",",$_POST["mufaj_chk"]).") order by mufaj";
			$res=$mysqli->query($sql) or die("Hiba történt lekérdezés közben!");
			
			$i=1;
			while($sor=$res->fetch_assoc()){
				echo "\n<div><input type='hidden' name='mufaj_id[]' value=".$sor["id"]." />".$i.". <input type='text' name='mufaj_mod[]' value='".$sor["mufaj"]."' /></div>";
				$i++;
			}
			
?>
				<p>
					<input type="submit" name="modosit-gomb" value="Mentés" />
				</p>
			</form>
</fieldset>
<?php
		}
		else header("location: index.php");
	}
	else header("location: index.php");
?>
<p>
	<a href="index.php">Vissza</a>
</p>
</body>