<?php
    @session_start();
    include_once("initial.php");
	 //Ha ez engedélyezve van (azaz nincs megjegyzésbe téve), akkor próbaüzemmódban működik a honlap (fejlesztés alatt) mindenkinek, kivéve a saját gépemen.
	 //Ez a sor nem működik belső hálózaton belül, ezért értelmetlen használni! Jobb megoldás esetén változtatok rajta.
	 //if($_SERVER["REMOTE_ADDR"]!=gethostbyname("haruko.no-ip.org"))
	 //header("location: index_temp.php");
    
    if(isset($_POST["kilepve"]) && $_POST["kilepve"]==1){
        unset($_SESSION["belepve"]);
        unset($_SESSION["user"]);
        unset($_SESSION["jog"]);
		unset($_SESSION["fejlec_sorrend"]);
        session_destroy();
        unset($_POST["user"]);
        unset($_POST["pass"]);
    }
    
    if(isset($_POST["user"]) && isset($_POST["pass"])){
        $user=$mysqli->real_escape_string($_POST["user"]);
        $pass=$mysqli->real_escape_string($_POST["pass"]);
        
        $lek="select * from ".FELHASZNALOK." where user='$user' and pass=password('$pass')";
        if($res=$mysqli->query($lek)){
            if($res->num_rows){	//mysql_num_rows($res) helyett
                $_SESSION["belepve"]=1;
                $_SESSION["user"]=$user;
				$sor=$res->fetch_assoc();
				$jog=$sor["jog"];	//mysql_result($res,0,"jog") helyett (de a 0. sor jelölése nincs benne!)
                $_SESSION["jog"]=$jog;
				if($sor["fejlec_sorrend"]!="")
					$_SESSION["fejlec_sorrend"]=$sor["fejlec_sorrend"];
                $valasz="Sikeres bejelentkezés.";
				if(isset($_POST["redirect"])){
					$redirect=$mysqli->real_escape_string($_POST["redirect"]);
					header("location: ".$redirect);
				}
            }
            else $valasz="<font color='red'>Hibás név vagy jelszó!</font>";
        }
        else $valasz="<font color='red'>Adatbázis hiba történt, próbáld meg később!</font>";
		  unset($_POST["user"], $_POST["pass"]);
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HTML segéd - Filmtár
    <?php if(basename(dirname($_SERVER["PHP_SELF"]))=="filmtar_teszt") echo " TESZT"; ?>
    </title>
    <link rel="stylesheet" type="text/css" href="stilus.css" />
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <!-- az alábbi stílus a képföltöltés elemeihez kell -->
    <link href="style_progress.css" rel="stylesheet" type="text/css" />
	 <style>
		  .ui-autocomplete {
				max-height: 200px;
				overflow-y: auto;
				/* prevent horizontal scrollbar */
				overflow-x: hidden;
		  }
		  ul.ui-autocomplete.ui-menu li{
				font-size: 12px;
		  }
		
		  /* IE 6 doesn't support max-height
		
			* we use height instead, but this forces the menu to always be this tall
	 
		  * html .ui-autocomplete {
				height: 100px;
		  }*/
	 </style>


</head>
<body>
    <h1 style="text-align: center">Filmtár</h1>
<?php
	if(basename(dirname($_SERVER["PHP_SELF"]))=="filmtar_teszt"){
?>
	<h4 style="text-align: center; margin-top:0">TESZT verzió</h4>
<?php
	}
?>
    <div>
<?php
    if(isset($_SESSION["belepve"]) && $_SESSION["belepve"]==1){
?>
        <div id="folso">
            <div style="width: 80%; display: inline; padding: 5px;">
<?php
			if(isset($valasz)) echo $valasz."<br />";
            if($_SESSION["jog"]=="admin"){
                echo 'Művelet: <select id="lapvalaszto">
                    <option value="gyorskereses">Gyorskeresés</option>
                    <option value="ujfilmrogzitese">Új film hozzáadása</option>
                    <option value="szereplokezeles">Szereplők kezelése</option>
					<option value="oszlop_kezeles">Oszlop kezelés</option>
                </select>';
            }
?>
                    <?php
                        echo "<span id='loginmsg'>";
                            echo "Bejelentkezve: <b>".$_SESSION["user"]."</b>";
                        echo "</span>";
                        echo "<form action=".$_SERVER["PHP_SELF"]." method='post' style='margin: 0; padding: 0; display: inline'>
                            <input type='hidden' name='kilepve' value=1 />
                            <input type='submit' value='Kilépés' />
                        </form>";
                        
                    ?>
                <div style="clear: both"></div>
                <div id="gyorskereses" class="lap" style="padding-bottom: 5px; z-index: 1; position: relative;">
                    <fieldset><legend>Gyorskeresés</legend>
                        <table style="text-align: left">
                            <tr><td>Film címe: </td><td><input type="text" class="keresomezo" id="kcim" autocomplete="off" /><!--<label><input type="radio" name="holkeres" value="elejen" id="holelejen" /> elején</label> <label><input type="radio" name="holkeres" value="barhol" checked="checked" id="holbarhol" /> bárhol</label> <label><input type="radio" name="holkeres" value="vegen" id="holvegen" /> végén</label>--></td></tr>
                            <tr><td>Kategória: </td><td><input type="text" class="keresomezo" id="kkateg" autocomplete="off" /></td></tr>
                            <tr><td>Rendező: </td><td><input type="text" class="keresomezo" id="krendezo" name="krendezo" autocomplete="off" /><!--<div class="gykdoboz"></div>--></td></tr>
                            <tr><td>Szereplő: </td><td><input type="text" class="keresomezo" id="kszereplo" autocomplete="off" /></td></tr>
                            <tr><td>Műfaj: </td><td><select id="kmufaj" class="keresomezo">
                                    <option value="0">bármely</option>
                                    <?php getMufajok(); ?>
                                </select>
                            <a href="mufaj_kezeles/index.php" title="műfaj kezelés">lista szerkesztése</a>
                            </td></tr>
                            <tr><td>Pozíció: </td><td><input type="text" class="keresomezo" id="kpoz" /></td></tr>
                            <tr><td>Értékelés: </td><td><select id="krate" class="keresomezo">
                                    <option value="0">tetszőleges</option>
                                    <option value="1">1 csillag</option>
                                    <option value="2">2 csillag</option>
                                    <option value="3">3 csillag</option>
                                    <option value="4">4 csillag</option>
                                    <option value="5">5 csillag</option>
                                </select></td>
                            </tr>
                            <tr><td>Megjelenítendő: </td><td><label><input type="checkbox" id="mitaktiv" checked="checked" /> aktív</label> <label><input type="checkbox" id="mitinaktiv" checked="checked" /> inaktív</label></td></tr>
                            <tr><td>&nbsp;</td><td><button class="alapallapot" id="gyk">Reset</button> <button id="keres"><img src="img/nagyito.png" /> Keresés</button> <span id="mindent_link"><a href=# id="mindentmutat">Mindent mutat</a></span></td></tr>
                        </table>
                        <!--<p style="margin-bottom: 0"></p>-->
                    </fieldset>
                </div>
<?php
            if($_SESSION["jog"]=="admin"){
?>
                <div id="ujfilmadatlap" class="lap" style="padding-bottom: 5px; z-index: 1; position: relative;">
                    <fieldset><legend>Adatbevitel</legend>
                        <table>
                            <tr><td>Film címe: </td><td><input type="text" style="width: 20em;" id="ujcim" /><span id="hcim" style="display: none; padding-left: 3px"><font color="red">Hiányzik a cím!</font></span></td></tr>
                            <tr><td>Kategória: </td><td><select id="ujkat">
                                <option value="DVD">DVD</option>
                                <option value="Xvid">Xvid</option>
										  <option value="Blu-ray">Blu-ray</option>
                            </select></td></tr>
                            <tr><td>Rendező: </td><td><input type="text" id="ujrendezo" /><!--<div class="gykdoboz" id="ujfilmgykdoboz" style="top: 99px; left: 107px"></div>--></td></tr>
                            <tr><td>Műfaj: </td><td><select id="ujmufaj">
                                <option value="0">válassz</option>
                                <?php getMufajok(); ?>
                            </select> <a href="mufaj_kezeles/index.php" title="műfaj kezelés">lista szerkesztése</a>
                            </td></tr>
                            <tr>
								<td>Pozíció: </td>
								<td colspan="2">
									<input type="text" id="ujpoz" />
								</td>
							</tr>
							<tr>
								<td>Beszerzési ár: </td>
								<td colspan="2">
									<input type="number" min="0" maxlength="6" id="ujbeszar" /> Ft
								</td>
							</tr>
							<tr>
								<td>Beszerzés dátuma: </td>
								<td colspan="2">
									<input type="date" id="ujbeszdatum" />
								</td>
							</tr>
                            <tr><td>Borító: </td>
                                 <td>
                                       <input type="hidden" id="rejtettsrc_uj" value="" />
                                       <span id="kepinfo_uj"></span>
                                       
                                            <div id="ultarto_uj">
                                                 <?php
                                                     include_once("upload_uj.php");
                                                 ?>
                                            </div>
                                       </div>
                                 </td>
                            </tr>
                            <tr><td>Szereplők (név) </td><td><button id="ujszereplo">Új szereplő</button></td></tr>
                        </table>
                            <div id="ujszereplok">
								<div id="usznum1">
									<span id="nr1">1.</span>
									<?php include_once("osszesszereplo.php"); //itt van a szereplők megadására szolgáló beviteli mező ?>
								</div>
							</div>
                        <p style="text-align: center; margin-bottom: 0"><button class="alapallapot" id="ab">Reset</button> <button id="tryinsert"><img src="img/ujadatrogzit.png" /> Film hozzáadása</button></p>
                    </fieldset>
                </div>
                <div id="filmadatmod" class="lap" style="padding-bottom: 5px;">
                    <fieldset><legend>Adatmódosítás</legend>
                          <img src="img/process.gif" class="porgettyu" alt="folyamatban" />
                    </fieldset>
				</div>
                <div id="szereplokezeles" class="lap" style="padding-bottom: 5px; z-index: 1; position: relative;">
                    <h3>Szereplők kezelése</h3>
                    <input type="text" id="szereplo_szures" placeholder="Szűrés névre" /> <a href="#" id="ujsz_megnyitlink" title="Új szereplő hozzáadása"><img src="img/hozzaad.png" class="ujsz_megnyit" alt="Hozzáadás" /></a><form id="ujsz_beviteli" method="post" action="szereplo_hozzaad.php">Név: <input type="text" name="ujszereplo" id="csakujszereplo" /> <input type="submit" value="Ok" /></form>
                    <div style="clear: both;"></div>
                    <div id="mibenszerepel">
                        <span id="bezarlink">&times;</span><div style="clear: both"></div>
                        <div style="text-align: center; margin-bottom: 0.5em"><button id="szereplo_torles"><img src="img/x.png" /> Szereplő törlése</button></div>
                        Név szerkesztése: <input type="text" name="sz_szerknev" id="sz_szerknev" />
                        <input type="button" id="sz_nevmod" value="Ok" /><span id="nevmod_eredmeny"></span><br />
                        Filmek, amelyekben szerepel:
                        <div id="szerep_filmekben">
                            <!--ide jönnek a filmek, amiben szerepel-->
                        </div>
                    </div>
                    <div id="szereplo_lista"></div>
                </div>
<?php
            }
?>
            </div>
				<div id="et_szer_kep">
					 <div id="easyTooltip">A kijelölt filmben játszó szereplők listája itt jelenik meg.</div>
					 <div id="keptarto">Ide jön majd a kép.</div>
				</div>
            <div style="clear: both;"></div>
				<span id="rendszeruzenet"></span>
				<div style="clear: both;"></div>
        </div>
<?php
        if($_SESSION["jog"]=="premium"){
            echo "<a href=# id='kedvencek'>Kedvenc filmek megtekintése</a>";
        }
?>
		  
		  <img src="img/process.gif" class="porgettyu" alt="folyamatban" />
		  <!--<div class="helyfogo"></div>-->
		  <div id="temptarolo" style="display: none;"></div>
          <div style="clear: both"></div>
        <div id="also">				
<?php
            if(file_exists("listaz.php"))
                include("listaz.php");
            else echo "<font color='red'>A kiíratásra szolgáló fájl nem létezik!</font>";
?>
        </div>
<?php
    }
    else{
        if(isset($valasz)) echo $valasz."<br />";
		?>
        Az oldal megtekintéséhez bejelentkezés szükséges.
        <form method='post' action="<?php echo $_SERVER["PHP_SELF"]; ?>" onsubmit='return ellenoriz();'>
			<?php
			if(isset($_GET["redirect"])){
				$redirect=$mysqli->real_escape_string($_GET["redirect"]);
				echo "<input type='hidden' name='redirect' value='".$redirect."' />";
			}
			?>
            <table style='margin: 0 auto'><tr><td>Felhasználónév: </td><td><input type='text' name='user' id='user' /></td></tr>
            <tr><td>Jelszó: </td><td><input type='password' name='pass' id='pass' /></td></tr>
            <tr><td colspan='2' style='text-align: center; padding-top: 10px;'><input type='submit' value='Belépés' /></td></tr></table>
        </form>
		<?php
			if(basename(dirname($_SERVER["PHP_SELF"]))=="filmtar_teszt"){
		?>
		<div style="text-align: center; margin-top:1em"><a href="../filmtar" title="Váltás az éles verzióra">Éles verzió</a></div>
		<?php
				$ujdonsagok="pozíció oszlop létrehozása, rendezés id szerint";
				if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST["frissites_ok"]))
					frissitesJovahagyas($ujdonsagok);
				if(vaneJovahagynivalo()){
		?>
		<div style="margin-top:1em">
			<p>
				Az alábbi újdonságok vannak teszt verzióban:
			</p>
			<ul>
				<li>pozíció oszlop létrehozva</li>
                <li>azonosító alapján történő rendezés megvalósítva</li>
			</ul>
			<p>Ha megnézted és minden jól működik, igazold vissza az alábbi gombbal.</p>
			<form action="<?=$_SERVER["PHP_SELF"] ?>" method="post">
				<input type="submit" name="frissites_ok" value="Megfelel" />
			</form>
		</div>
		<?php
				}
			}
			else{
		?>
				<div style="text-align: center; margin-top:1em"><a href="../filmtar_teszt" title="Váltás a teszt verzióra">Teszt verzió</a></div>
				<?php
			}
	}
?>
    </div>
	<script language="javascript" src="jquery-<?php if(defined("jqueryverzio")) echo jqueryverzio; else echo "1.10.2.min"; ?>.js"></script>
	<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script language="javascript" src="easytooltip/js/easyTooltip.js"></script>
    <script language="javascript" src="jsfv.js"></script>
    <script language="javascript" src="kliensaddon.js"></script>
	<script language="javascript">
		  var kezelt;
		  if(window.location!==window.parent.location){ 
				window.open("index.php", "_parent");
		  }else{ 
		    // The page is not in an iframe 
		  }

        $(document).ready(function(){
            if($("#user").length>0) $("#user").focus();
            kezelt=false;
            
            window.onload=szelessegbeallitas();
				
            $(window).resize(szelessegbeallitas);
			$("#ujfilmadatlap").hide();
            $("#filmadatmod").hide();
            $("#szereplokezeles").hide();
            $("#et_szer_kep").hide();
			$("#folyamat_doboz_uj").hide();
			$("#folyamat_doboz_mod").hide();
			$("img.porgettyu").hide();
            $("#mindent_link").hide();
				
            $("#lapvalaszto").change(lapotvalt);
            $("#tryinsert").click(rogzit); //itt volt még a rogzit() után a listafrissit("listaz") fv is, de talán szükségtelen
            
            $(".alapallapot").click(alapallapot);
				
            var inputid="krendezo";
            //statikusan betöltött mezők ID-jének lekérdezése
            $("#krendezo, #ujrendezo, #kszereplo").focus(function(){
                inputid=$(this).attr("id");
            });
            
            //dinamikusan betöltött mezők ID-jének lekérdezése
            $("body").on("focus", "input.szereplo, #mrendezo", function(){
				inputid=$(this).attr("id");
				$(this).autocomplete({
					source: function(request, response){
						$.getJSON("gyorsseged.php", { term: request.term, mit: inputid }, response);
					}
				});
			});
            
            //var adatok=["Albánia", "Bulgária", "Ciprus", "Csehország", "Dánia", "Etiópia", "Észtország", "Franciaország", "Finnország", "Görögország", "Hollandia", "Horvátország", "Izland", "Jugoszlávia", "Korea", "Lettország", "Litvánia", "Magyarország", "Norvégia", "Németország", "Oroszország", "Örményország", "Portugália", "Románia", "Svájc", "Svédország", "Szlovákia", "Törökország", "Ukrajna", "Vatikán"];
            $("#krendezo, #ujrendezo, #mrendezo, #kszereplo").autocomplete({
                 source: //"gyorsseged.php?mit="+inputid
                 function(request, response){
                      $.getJSON("gyorsseged.php", { term: request.term, mit: inputid }, response);
                 }
                //minLength: 2
            });
            
            $("body").on("blur", "input.szereplo", function(){
                //szereploLetezikEll
                
                szereploLetezikEll(event);
                //teszt(event);
            });
			
			$("body").on("click", ".meglevoszereplo label input[type=checkbox]", function(){
				$(this).parent().parent().children(".szereplo").toggleClass("athuzott");
			});
				
            $("#keres").click(kereses);
            $(".keresomezo").keypress(function(event){
                if(event.keyCode=="" || event.keyCode=="undefined" || event.keyCode==null){
                    if(event.which===13) kereses();
                }
                else{
                    if(event.keyCode===13) kereses();
                }
                //elvileg IE a keydownt (és event.keyCode) szereti, FF a keypresst (és event.which)
            });
            
            $("body").on("click", ".modpoz, .omufaj", pozicioHelyiMod);
            $("body").on("blur", ".modpoz input[type=text], .omufaj select", pozicioHelyiElhagy);
            $("body").on("keyup", ".modpoz input[type=text], .omufaj select", function(e){
                let szulo=$(this).parent();
                if(e.keyCode=="" || e.keyCode=="undefined" || e.keyCode==null){
                    if(e.which==27)
                        pozicioHelyiEsc(szulo);
                }
                else{
                    if(e.keyCode==27)
                        pozicioHelyiEsc(szulo);
                }
            });
            $("#ujszereplo").click(ujszereplo); //új filmnél új szereplő
            $("body").on("click", "#ujszereplok div img", szereplo_eltavolit);
            //TypeError: $(...).live is not a function: ez azért van, mert a jQuery 1.9 utáni verzióiban kivették ezt a fv-t, helyette a ().ont kell használni!!
            $("body").on("click", "#mujszereplo", ujszereplo);
            $("body").on("click", "#mujszereplok div img", szereplo_eltavolit);
				
            $("body").on("click", "#mindentmutat", function(){
				$("#et_szer_kep").appendTo("#temptarolo").hide();
                $("#mindent_link").hide();
                listafrissit("listaz");
            });
				
            $("body").on("click", "img.ikon", muvelet);
            
            if($("#adattabla").length){
                var tableOffset = $("#adattabla").offset().top;

                $(window).bind("scroll", function() {
                     var offset = $(this).scrollTop();
                     
                     if(offset>=tableOffset)
                          $("#ragados").show();
                     else $("#ragados").hide();
                });
            }
        
            $("body").on("click", "span.mod", function(){
                var parameter=$(this).attr("id");
                var id=parameter.substr(1);
                var href=$("#link"+id).val();
                var kishiv="img/boritok/kicsi/"+href, nagyhiv="img/boritok/nagy/"+href;
                
                if($("#et_szer_kep").css("display")=="block"){
                    if(("et_"+id)==$("#et_szer_kep").parent().attr("id"))
                        $("#et_szer_kep").slideUp();
                    else{
                        $("#et_szer_kep").slideUp(function(){
                            tooltipbeolvas(parameter);
                            
                            if($("#link"+id).length!=0){
                                $("#keptarto").html("<a href='"+nagyhiv+"' target='_blank'><img src='"+kishiv+"' alt='nem található: "+href+"' /></a>");
                            }
                            else $("#keptarto").html("");
                            $("#et_szer_kep").appendTo("#et_"+id).slideDown();
                            //$("#et_"+id).slideDown();
                        });
                    }
                }
                else{
                    tooltipbeolvas(parameter);
                    
                    if($("#link"+id).length!=0){
                        $("#keptarto").html("<a href='"+nagyhiv+"' target='_blank'><img src='"+kishiv+"' alt='nem található: "+href+"' /></a>");
                    }
                    else $("#keptarto").html("");
                    $("#et_szer_kep").appendTo("#et_"+id).slideDown();
                    //$("#et_"+id).slideDown();
                }
            });
				
			$("body").on("click", "#mentes", function(){
                //$("#mujszereplok div").each(function(){});
                modositas(event);
                //teszt(event);
                
                //listafrissit("listaz"); //ez nem kell
            });
            
			$("body").on("click", "#megse", lapotvalt);
            
			$("body").on("click", "#torles", function(){
                torles();
            });
            $("body").on("click", "#toltes_uj", filecheck);
			$("body").on("click", "#toltes_mod", filecheck);
            $("body").on("click", "[type='radio']", tiltasvaltas);
            $("body").on("click", "#szereplo_lista ul li", mibenszerepel);
            $("body").on("click", "#mibenszerepel span#bezarlink", function(){
                $("#mibenszerepel").hide();
                lapotvalt("torles");
            });
            $("body").on("click", "#sz_nevmod", function(){
                var ujnev=$("#sz_szerknev").val();
                $("#rendszeruzenet").hide();
                
                $.post("osszesszereplo.php", {sid: Math.random, hatokor: "szereplo_ujnev", szereplo_id: szereplo_id, ujnev: ujnev}, function(valasz){
                    szereplo=ujnev;
                    if(valasz.indexOf("Hiba")<0){
                        $("#sz_nevmod").prop("disabled", true);
                        $("#rendszeruzenet").html(valasz).fadeIn();
                    }
                    else $("#rendszeruzenet").html(valasz).show("bounce");
                });
            });
            $("body").on("click", "#szereplo_torles", function(){
                torles();
            });
            $("body").on("keyup", "#sz_szerknev", function(){
                var megegyezik=$(this).val()==szereplo;
                var ures=$(this).val()=="" || $(this).val==null;
                $("#sz_nevmod").prop("disabled", megegyezik || ures);
            });
            $("body").on("click", "#ujsz_megnyitlink", function(e){
				e.preventDefault();
                $("#ujsz_beviteli").toggle();
                if($("#ujsz_beviteli").css("display")=="block"){
                    $("#csakujszereplo").val("").focus();
                }
            });
            $("body").on("submit", "#ujsz_beviteli", function(event){
                $("#rendszeruzenet").hide();
                event.preventDefault();
                
                var adatok={
                    sid: Math.random,
                    szereplo: $("#csakujszereplo").val()
                }
                if($("#csakujszereplo").val()!=""){
                    $.post("szereplo_hozzaad.php", adatok, function(valasz){
                        var szoveg="<div class='";
                        if(valasz.indexOf("Hiba")<0) szoveg+="siker";
                        else szoveg+="hiba";
                        szoveg+="'>"+valasz+"</div>";
                        $("#rendszeruzenet").html(szoveg).fadeIn();
                        lapotvalt();
                    });
                    $(this).hide();
                }
                else{
                    $("#rendszeruzenet").html("<div class='hiba'>Az új szereplő nevét ki kell tölteni!</div>").show("bounce");
                    $("#csakujszereplo").focus();
                }
            });
            
            if($("#user").val()!=null) $("#user").focus();
            $("#regiszereplo1").attr("disabled", "true");
            
            $("body").on("mouseover", ".ikon", function(){
                if(typeof $(this).attr("id")!=="undefined"){
                    var par=$(this).attr("id");
                    var elsobetu=par.substr(0,1);
                    if(elsobetu=="m") rollover(par);
                }
            });
            
			$("body").on("mouseout", ".ikon", function(){
                if(typeof $(this).attr("id")!=="undefined"){
                    var par=$(this).attr("id");
                    var elsobetu=par.substr(0,1);
                    if(elsobetu=="m") rollout(par);
                }
            });
			
            //ha megváltozik a fájl mező tartalma, automatikusan beküldjük az űrlapot
            $("body").on("change", ".ulf input[type='file']", function(){
                var fajlmezoid=$(this).attr("id");
                var kateg="";
                $(this).parent().submit();
            });
				
			$("body").on("click", "a.kedvencekhez", kedvencekhezad);
            $("a#kedvencek").click(function(e){
                e.preventDefault();
                var szoveg=$("#kedvencek").text();
                if(szoveg=="Kedvenc filmek megtekintése"){
                    $("#kedvencek").text("Vissza az összes filmhez");
                    listafrissit("getfavs");
                }
                else{
                    $("#kedvencek").text("Kedvenc filmek megtekintése");
                    listafrissit("listaz");
                }
            });
			$("body").on("click", "a.kedvenctorol", kedvencboltorol);
            $("#szereplo_szures").bind("keyup", function(){
				let mezo_ertek=$(this).val();
				$.post("osszesszereplo.php", {sid: Math.random, hatokor: "szereplok_listaz", mezo_ertek: mezo_ertek}, function(valasz){
					$("#szereplo_lista").html(valasz);
				});
			});
            
            //upload.php-ból importált:
            //-----------------------------------------------------------------
            
            //show the progress bar only if a file field was clicked
            var show_bar = 0;
            $("body").on("click", "input[type='file']", function(){
                show_bar = 1;
            });
			
			//show iframe on form submit
			$("body").on("submit", ".ulf", function(){
                var formid=$(this).prop("id");
                if (show_bar === 1){
                    var kateg="";
                    if(formid=="ulf_uj") kateg="uj";
                    else if(formid=="ulf_mod") kateg="mod";
                    var fajl=$("#"+kateg+"kepul").val();
                    
                    if(fajl!=""){
                        if(kep_e(fajl)){
                            $("#upload_frame_"+kateg).hide();
                            $("#upload_frame_"+kateg).attr("src", "upload_result_"+kateg+".php");

                            $("#upload_frame_"+kateg).show(); //ebben lesznek az eredmények
                            $("#upload_frame_"+kateg).css("height", "auto");
                            meroindit(kateg);
                             
                            $("#upload_frame_"+kateg).on("load", function(){
                                var tartalom=$("#upload_frame_"+kateg).contents().find("body").text();
                                var hibavan=tartalom.indexOf("hiba")!=-1;
                                var sikervan=tartalom.indexOf("siker")!=-1;
                                
                                if(hibavan || sikervan){
                                    $("#folyamat_doboz_"+kateg).hide();
                                    if(sikervan){
                                        if(kateg=="mod") $("#rendszeruzenet").hide().html("<div class=\"figyelem\">A film-kép kapcsolat frissítéséhez a mentés gombra kell kattintani!</div>").show("pulsate");
                                        
                                        var utolsoperpos=fajl.lastIndexOf("\\")+parseInt(1);
                                        var fajlnev=fajl.substr(utolsoperpos);
                                        
                                        $("#rejtettsrc_"+kateg).val(fajlnev);
                                    }
                                    else{	//hibavan
                                        if(tartalom.indexOf("A kiválasztott fájl")!=-1){	//már létezik a szerveren.
                                            $("#upload_frame_"+kateg).css("height", "auto");
                                        }
                                        else $("#rendszeruzenet").html("").hide();
                                    }
                                }
                                else
                                    $("#rendszeruzenet").show().html("<div class=\"figyelem\">Nincs visszajelzés szerver oldalról!</div>");

                                meroleallit(kateg);
                            });
                        }
                        else{
                            $("#upload_frame_"+kateg).show();
                            $("#upload_frame_"+kateg).contents().find("body").html("<font color='red'>Hibás formátum!</font>");
                            return false;
                        }
                        
                        return true;
                    }
                }
                else{
                    alert("A kép kiválasztása kötelező!");
                    return false;
                }
			});
        });
    </script>
</body>
</html>
