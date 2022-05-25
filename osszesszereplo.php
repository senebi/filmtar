<?php
	session_start();
	$included=strtolower(realpath(__FILE__))!=strtolower(realpath($_SERVER["SCRIPT_FILENAME"]));
	if(!$included && !isset($_POST["sid"])) header("Content-type: text/html; charset=utf-8");
	if(isset($_POST["sid"]) && !$included) include_once("initial.php");
	
	if($_POST["hatokor"]!="szmezohozzaad"){
?>

	<!--Progress Bar and iframe Styling-->
	<!--<link href="style_progress.css" rel="stylesheet" type="text/css" />-->
	
	<!--Get jQuery-->
	<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.0/jquery.js" type="text/javascript"></script>-->
	
	<!--display bar only if file is chosen-->
	<!--<script language="javascript" src="kliensaddon.js"></script>-->
	<!-- eddig az upload.php szükséges elemei voltak -->
	<!-- Ez eredetileg nem volt benne (UI)! -->
	<!--<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>-->

	<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script language="javascript">
		$(document).ready(function(){
			//$("#ulmod").load("upload.php");
<?php
	if($_POST["hatokor"]!="szereplok_listaz"){
?>
			$(".ulf input[type='file']").change(function(){
				var fajlnev=$(this).val();
				var fajlmezoid=$(this).prop("id");
				var kateg="mod";
				if(fajlmezoid=="ujkepul") kateg="uj";
				var utolsoperpos=fajlnev.lastIndexOf("\\")+parseInt(1);
				fajlnev=fajlnev.substr(utolsoperpos);
				$("#rejtettsrc_"+kateg).val(fajlnev);
			});
			$("#keptorles").click(function(){
				var mod_filmid=$("#mfilm_id").val();
				var biztos=confirm("Biztos törlésre kerüljön a kép?\nErre a módosításra nem vonatkozik a mentés/mégse gomb, csak a többi adatra!\nAz Ok megnyomásával azonnal törlődik a filmhez rendelt kép!");
				if(biztos){
					$.post("keptorles.php", {sid: Math.random, film_id: mod_filmid}, function(valasz){
						if(valasz=="A kép törlése sikeres."){
							$("#kepinfo_mod").html("nincs kép");
							$("#rejtettsrc_mod").val("");
							$("#keptorles").hide();
							$("#rendszeruzenet").html("<div class='siker'>"+valasz+"</div>");
						}
						else $("#rendszeruzenet").hide().html("<div class='hiba'>"+valasz+"</div>").show("bounce");
						listafrissit("listaz");
					});
				}
			});
			//$("#rejtettsrc_uj").val($("#rejtettsrc_mod").val());
<?php
	}
?>
			$("#folyamat_doboz_mod").hide();
			
		});
	</script>
	
<?php
	}
	if(isset($_SESSION["belepve"])){
		if($included || isset($_POST["sid"])){
			if(isset($_POST["hatokor"])) $hatokor=$_POST["hatokor"];
			else $hatokor="szmezohozzaad";
			if(isset($_POST["modositasvagyuj"])) $modositasvagyuj=$_POST["modositasvagyuj"];
			else $modositasvagyuj="uj";
			if($hatokor=="szmezohozzaad"){
				if(isset($_POST["szamlalo"])) $szamlalo=$_POST["szamlalo"];
				else $szamlalo=1;
				echo "<input id='";
				if($modositasvagyuj=="modositas") echo "m";
				echo "regiszereplo".$szamlalo."' class='szereplo' type='text' />";
				echo "<input type='hidden' class='letezik' value=false />";
				
				if($modositasvagyuj!="uj" || $szamlalo>1){
				?>
					<img src="img/x.png" alt="Mező törlése" title="Mező törlése" id="<?php if($modositasvagyuj=="modositas") echo "m"; ?>mezotorol<?php echo $szamlalo; ?>" class="<?php if($modositasvagyuj=="modositas") echo "sz_torol_mod"; else echo "sz_torol_uj"; ?>" />
				<?php
				}
			}
			else if($hatokor=="egyfilm"){
				if(isset($_POST["film_id"])) $film_id=$mysqli->real_escape_string($_POST["film_id"]);
				echo "<script src=\"http://code.jquery.com/ui/1.11.4/jquery-ui.js\"></script>";
				$filmadatok="select * from ".FILMEK." left outer join ".MUFAJOK." on
			".FILMEK.".film_mufaj=".MUFAJOK.".id where film_id=$film_id";
				if($rfilmadatok=$mysqli->query($filmadatok)){
					$sor=$rfilmadatok->fetch_assoc();
?>
					<fieldset><legend>Adatmódosítás</legend>
						<table>
							<tr>
								<td>Film címe: </td>
								<td><input type="text" style="width: 20em;" id="mcim" value="<?php echo $sor["film_cim"]; ?>" /></td>
								<td align="right"><button id="torles"><img src="img/x.png" /> Film törlése</button></td>
							</tr>
							<tr>
								<td>Kategória: </td>
								<td colspan="2">
									<select id="mkat">
										<option value="DVD" <?php if($sor["dvd_kateg"]=="DVD") echo "selected='selected'"; ?>>DVD</option>
										<option value="Xvid" <?php if($sor["dvd_kateg"]=="Xvid") echo "selected='selected'"; ?>>Xvid</option>
										<option value="Blu-ray" <?php if($sor["dvd_kateg"]=="Blu-ray") echo "selected='selected'"; ?>>Blu-ray</option>
									</select>
								</td>
							</tr>
							<tr>
								<td>Rendező: </td>
								<td colspan="2"><input type="text" id="mrendezo" value="<?php echo $sor["rendezo"]; ?>" /></td>
							</tr>
							<tr>
								<td>Műfaj: </td>
								<td colspan="2">
									<select id="mmufaj">
										<?php
											$aktMufajId=$sor["film_mufaj"];
											if(is_null($sor["mufaj"])) echo "<option value=0>válassz</option>";
											getMufajok($aktMufajId);
										?>
									</select>
									<a href="mufaj_kezeles/index.php" title="műfaj kezelés">lista szerkesztése</a>
								</td>
							</tr>
							<tr>
								<td>Pozíció: </td>
								<td colspan="2">
									<input type="text" id="mpoz" value="<?php echo $sor["poz"]; ?>" />
								</td>
							</tr>
							<tr>
								<td>Beszerzési ár: </td>
								<td colspan="2">
									<input type="number" min="0" maxlength="6" id="mbeszar" value="<?php echo $sor["besz_ar"]; ?>" /> Ft
								</td>
							</tr>
							<tr>
								<td>Beszerzés dátuma: </td>
								<td colspan="2">
									<input type="date" id="mbeszdatum" value="<?php echo $sor["ar_datum"]; ?>" />
								</td>
							</tr>
							<tr>
								<td colspan="3">
									Jelenlegi borító: <span id="kepinfo_mod"><?php echo ($sor["kepsrc"]!="") ? $sor["kepsrc"]." <input type='button' id='keptorles' value='Kép törlése' />" : "nincs kép"; ?></span> <!-- korábbi id: mkepinfo -->
									<?php if($sor["kepsrc"]!=""){ ?>
										<div style="text-align: center">
											<a href="img/boritok/nagy/<?php echo $sor["kepsrc"]; ?>" target="_blank">
												<img src="img/boritok/kicsi/<?php echo $sor["kepsrc"]; ?>" alt="borító kép" title="borító kép" />
											</a>
										</div>
									<?php } ?>
									<input type="hidden" id="rejtettsrc_mod" value="<?php echo $sor["kepsrc"]; ?>" /> <!-- korábbi id: segedborito-->
									<div id="ultarto_mod"> <!-- korábbi id: ulmod -->
									<?php
										include_once("upload_mod.php");
									?>
									</div>
								</td>
							</tr>
							<tr>
								<td>Szereplők: </td>
								<td colspan="2"><button id="mujszereplo">Új szereplő</button></td>
							</tr>
							<tr>
								<td colspan="3">
<?php
										$szereploadatok="select * from ".KAPCSOLO." inner join ".SZEREPLOK." on szereplo_fk_id=szereplo_id where film_fk_id=".$film_id." order by kapcsolat_id";
										if($rszereploadatok=$mysqli->query($szereploadatok)){
											$i=1;
											if($rszereploadatok->num_rows>0){
												while($szsor=$rszereploadatok->fetch_assoc()){
													echo "<div id=\"musznum".$i."\" class=\"meglevoszereplo\">".$i.". ";
													$szereploNev=stripslashes(htmlspecialchars($szsor["szereplo_nev"], ENT_QUOTES));
													//echo $szereploNev."<br />";
													echo "<input type='text' id='mregiszereplo".$i."' class='szereplo' value='".$szereploNev."' disabled='disabled' /> ";
													//régi: "szereplohonnantorol".$i id-jű select lista
													echo "<label><input type='checkbox' class='szereplo-film_torol' /> Törlés</label>";
													echo "<input type='hidden' id='szereplo_id".$i."' value=".$szsor["szereplo_id"]." /></div>";
													$i++;
												}
											}
											else echo "Ebben a filmben jelenleg nincsenek szereplők rögzítve.";
											echo "<input type='hidden' id='mfilm_id' value=".$film_id." />";
											echo "<input type='hidden' id='holtart' value=".$i." />";
										}
										else echo "<font color='red'>Hiba történt lekérdezés közben!</font>";
?>
									<div id="mujszereplok">
									</div>
								</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td colspan="2"><button id="mentes"><img src="img/mentes.png" /> Mentés</button> <button id="megse">Mégse/Vissza</button></td>
							</tr>
						</table>
						
					</fieldset>
<?php
				}
				else echo "<font color='red'>Hiba az adatok lekérdezése közben!</font>";
			}
			else if($hatokor=="szereplok_listaz"){
				$szereplok_sql="select * from ".SZEREPLOK;
				if(isset($_POST["mezo_ertek"])){
					$mezo_ertek=stripslashes($mysqli->real_escape_string($_POST["mezo_ertek"]));
					$szereplok_sql.=" where szereplo_nev like '".$mezo_ertek."%'";
				}
				$szereplok_sql.=" order by szereplo_nev";
				if($szereplok_res=$mysqli->query($szereplok_sql)){
					if($szereplok_res->num_rows>0){
						?><ul>
						<?php
						while($sor=$szereplok_res->fetch_assoc()){
							echo "<li id='sz_".$sor["szereplo_id"]."'>".stripslashes($sor["szereplo_nev"])."</li>";
						}
						?>
						</ul>
						<?php
					}
					else{
						if(isset($mezo_ertek)) echo "A szűrés nem adott eredményt.";
						else echo "Nincsenek szereplők az adatbázisban.";
					}
				}
				else echo "<div class='hiba'>Hiba történt lekérdezés közben!</div>";
			}
			else if($hatokor=="mibenszerepel_listaz"){
				$szereplo_id=$_POST["szereplo_id"];
				
				$szereplofilmek="select * from ".KAPCSOLO." inner join ".FILMEK." on film_fk_id=film_id where szereplo_fk_id=".$szereplo_id." order by film_cim";
				if($res=$mysqli->query($szereplofilmek)){
					if($res->num_rows>0){
						?><ul>
						<?php
						while($sor=$res->fetch_assoc()){
							echo "<li id='k_".$sor["kapcsolat_id"]."'>".$sor["film_cim"]."</li>";
						}
						?>
						</ul>
						<?php
					}
					else echo "A szereplő jelenleg egyetlen filmben se szerepel.";
				}
				else echo "<font color='red'>Hiba az adatok lekérdezése közben!</font>";
			}
			else if($hatokor=="szereplo_ujnev"){
				$szereplo_id=$_POST["szereplo_id"];
				$ujnev=stripslashes($mysqli->real_escape_string($_POST["ujnev"]));
				$lek="update ".SZEREPLOK." set szereplo_nev='".$ujnev."' where szereplo_id=".$szereplo_id;
				if($res=$mysqli->query($lek)){
					echo "<div class='siker'>Sikeres módosítás.</div>";
				}
				else echo "<font color='red'>Hiba a szereplő nevének módosításakor! Próbáld újra!</font>";
			}
		}
		else echo "<font color='red'>Ez az oldal önállóan nem használható!</font>";
	}
	else echo "<font color='red'>Az oldal megtekintéséhez bejelentkezés szükséges!</font>";
?>