<?php
	@session_start();
    include_once("../initial.php");
	if(!isset($_SESSION["belepve"])){
		$redirectTo=dirname($_SERVER["PHP_SELF"]);
		echo "<font color='red'>Az oldal megtekintéséhez bejelentkezés szükséges!</font>";
		echo "<p><a href='http://users.atw.hu/htmlseged/filmtar/?redirect=".$redirectTo."'>Vissza a kezdőlapra</a></p>";
		exit();
	}
	else{
		if($_SESSION["jog"]!="admin"){
			echo "<font color='red'>Az oldal megtekintéséhez nincs megfelelő jogosultság!</font>";
			echo "<p><a href='http://users.atw.hu/htmlseged/filmtar/'>Vissza a kezdőlapra</a></p>";
			exit();
		}
	}
	include_once("../config.php");
?>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Oszlop kezelés | Filmtár</title>
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<style type="text/css">
		.rendezheto{
			list-style-type: none;
			margin: 0;
			float: left;
			margin-right: 10px;
			background: #D0E0F5;
			padding: 5px;
			width: 143px;
		}
		.rendezheto li{
			margin: 5px;
			padding: 5px;
			font-size: 1.2rem;
			/*width: 120px;*/
			/*background: #FFF59D;*/
			cursor: move;
		}
		
		div{
			margin-bottom: 0.5rem;
		}
		fieldset{
			display: inline-block;
		}
	</style>
</head>
<body>
<h1 class="mt-2 ml-4">Oszlop kezelés</h1>

	<div class="container">
		Az alábbiakban választhatsz az elérhető oszlopok közül.
		Egyszerűen húzd át a megjeleníteni kívánt oszlop címkéket a megfelelő sorrendben
		a jobb oldali kék mezőbe.
		<form action="#" method="post" id="dupla_lista">
			<div class="row">
				<div class="col-sm-3">
				</div>
				<div class="col-6 col-sm-3">
					Elérhető oszlopok:
					<div class="clearfix"></div>
					<ul id="elerheto" class="rendezheto droptrue">
						<?php
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
						?>
						<!--<li class="ui-state-default">Can be dropped..</li>
						<li class="ui-state-default">..on an empty list</li>-->
					</ul>
				</div>
				<div class="col-6 col-sm-3">
					Megjelenítendő oszlopok:
					<div class="clearfix"></div>
					<ul id="kivalasztott" class="rendezheto droptrue">
						<?php
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
						?>
						<!-- Ahhoz, hogy tényleg ne legyen dobható üres mezőre, dropfalse osztállyal kell rendelkeznie! -->
						<!--<li class="ui-state-highlight">Cannot be dropped..</li>
						<li class="ui-state-highlight">..on an empty list</li>
						<li class="ui-state-highlight">Item 3</li>
						<li class="ui-state-highlight">Item 4</li>
						<li class="ui-state-highlight">Item 5</li>-->
					</ul>
				</div>
				<div class="col-sm-3">
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<input type="submit" class="btn btn-primary" name="modosit-gomb" value="Mentés" />
					<input type="button" class="btn btn-warning" name="reset-gomb" id="reset-gomb" value="Alaphelyzet" />
				</div>
			</div>
			<div class="row">
				<div class="col-12" id="siker">
				</div>
			</div>
		</form>
		<p>
			<a href="../">Vissza a Filmtárhoz</a>
		</p>
	</div>
	
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	<script language="javascript">
		$(document).ready(function(){
			$( "ul.droptrue" ).sortable({
				connectWith: "ul"
			});
	   
			$( "ul.dropfalse" ).sortable({
				connectWith: "ul",
				dropOnEmpty: false
			});
	   
			$( ".rendezheto" ).disableSelection();
			
			$(document).on("submit", "#dupla_lista", function(){
				let i=0;
				let index="";
				let adatok={
					elerheto: "",
					kivalasztott: ""
				};
				
				$("#elerheto").children("li").each(function(){
					index=this.id.split("_")[1];
					
					if(adatok.elerheto.length>0) adatok.elerheto+=",";
					adatok.elerheto+=index+":0";
					i++;
				});

				i=0;
				
				$("#kivalasztott").children("li").each(function(){
					index=this.id.split("_")[1];
					
					if(adatok.kivalasztott.length>0) adatok.kivalasztott+=",";
					adatok.kivalasztott+=index+":1";
					i++;
				});
				
				$.post("sorrend_modosit.php", adatok, function(valasz){
					let elerhetoBlokk=valasz.split("|")[0];
					let kivalasztottBlokk=valasz.split("|")[1];
					
					$("#elerheto").html(elerhetoBlokk);
					$("#kivalasztott").html(kivalasztottBlokk);
					
					let sikerMsg='<div class="alert alert-success alert-dismissible">'+
					'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+
					'<strong>Siker!</strong> Az oszlop sorrend módosítva.</div>';
					$("#siker").html(sikerMsg);
				});
				
				return false;
			});
			
			$("#reset-gomb").click(function(){
				$.post("sorrend_modosit.php", {reset: 1}, function(valasz){
					let elerhetoBlokk=valasz.split("|")[0];
					let kivalasztottBlokk=valasz.split("|")[1];
					
					$("#elerheto").html(elerhetoBlokk);
					$("#kivalasztott").html(kivalasztottBlokk);
					
					let sikerMsg='<div class="alert alert-success alert-dismissible">'+
					'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+
					'<strong>Siker!</strong> Az oszlop sorrend alaphelyzetbe állt.</div>';
					$("#siker").html(sikerMsg);
				});
			});
		});
	</script>
</body>