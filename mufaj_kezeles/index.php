<?php
	@session_start();
    include_once("../initial.php");
	if(!isset($_SESSION["belepve"])){
		$redirectTo=dirname($_SERVER["PHP_SELF"]);
		echo "<font color='red'>Az oldal megtekintéséhez bejelentkezés szükséges!</font>";
		echo "<p><a href='http://users.atw.hu/htmlseged/filmtar/?redirect=".$redirectTo."'>Vissza a kezdőlapra</a></p>";
		exit();
	}
?>
<head>
	<title>Műfaj kezelés | Filmtár</title>
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
	<legend>Hozzáadás</legend>
	<form method="post" action="hozzaad.php">
		
			Műfaj: <input type="text" name="mufaj_bevitel" />
			<p>
				<input type="submit" name="uj-mufaj-gomb" value="Mentés" />
			</p>
		
	</form>
</fieldset>
<p>
	<?php
		if(isset($_GET["hozzaadas"]) && $_GET["hozzaadas"]=="siker")
			echo "Sikeres hozzáadás.";
		else echo "&nbsp;";
	?>
</p>
<fieldset>
	<legend>Tárolt műfajok</legend>
	<?php
		$sql="select * from ".MUFAJOK." order by mufaj";
		$res=$mysqli->query($sql) or die("Hiba történt lekérdezés közben!");
		
		if(!$res->num_rows)
			echo "A lista üres.";
		else{
	?>
			<form name="mufaj_muveletek" method="post" action="">
	<?php
			$i=1;
			while($sor=$res->fetch_assoc()){
				echo "\n<div><input type='checkbox' name='mufaj_chk[]' value=".$sor["id"]." /><label for='mufaj_chk[]'>".$i.". ".$sor["mufaj"]."</label></div>";
				$i++;
			}
	?>
			<p>
				A kijelöltekkel végzendő művelet:
				<input type="submit" name="mod-mufaj-gomb" value="Módosítás" onClick="setUpdateAction()" /> <input type="submit" name="torles-mufaj-gomb" value="Törlés" onClick="setDeleteAction()" />
			</p>
			</form>
	<?php
		}
	?>
</fieldset>

<p>
	<?php
		if(isset($_GET["modositas"]) && $_GET["modositas"]=="siker")
			echo "Sikeres módosítás.";
		elseif(isset($_GET["torles"]) && $_GET["torles"]=="siker")
			echo "Sikeres törlés.";
		else echo "&nbsp;";
	?>
</p>
<p>
	<a href="../">Vissza a Filmtárhoz</a>
</p>
	<script language="javascript">
		
		function setUpdateAction(){
			document.mufaj_muveletek.action = "mod_elokeszit.php";
			document.mufaj_muveletek.submit();
		}
		
		function setDeleteAction(){
			document.mufaj_muveletek.action = "torol.php";
			document.mufaj_muveletek.submit();
		}
	</script>
</body>