	 var szamlalo=1;
	 var seged=0;
	 var aktfcim="";
	 var elozott=0;	//előző tooltip szülő div-jének az id-je
	 var csillagok=new Array();
	 var eredetiNevek=new Array();
	 var szereplo="";
	 var szereplo_id=0;
	 szereplokalap="A kijelölt filmben játszó szereplők listája itt jelenik meg.";
	 var pozicio="";
     var rendezesIrany=[
          {irany: "desc", src: "img/csokkeno.png", title: "csökkenő sorrend"},
          {irany: "asc", src: "img/novekvo.png", title: "növekvő sorrend"}
     ];
     var rendezes=0;
     var miszerint="film_cim";
	 
	 function ellenoriz(){
		  var user=$("#user").val();
		  var pass=$("#pass").val();
		  var helyes=true;
		  if(user=="" && pass==""){
				alert("A felhasználónév és a jelszó megadása kötelező!");
				$("#user").focus();
				helyes=false;
		  }
		  else{
				if(user==""){
					 alert("A felhasználónév megadása kötelező!");
					 $("#user").focus();
					 helyes=false;
				}
				else if(pass==""){
					 alert("A jelszó megadása kötelező!");
					 $("#pass").focus();
					 helyes=false;
				}
		  }
		  
		  return helyes;
	 }
	 
	 function rogzit(){
		  var szereplok=new Array();
		  var hiba=false;
		  
		  let szereplokSzama=$("#ujszereplok div").length;
		  let aktSzereplo={
				nev: "",
				letezik: false
		   };
		  
		if(szereplokSzama>0){
			$("#ujszereplok div").each(function(){
				aktSzereplo={
                   nev: $(this).children("input[type=text]").val(),
                   letezik: $(this).children("input[type=hidden]").val()
				};
				
				console.log("aktuális szereplő: "+aktSzereplo.nev+", létezik? "+aktSzereplo.letezik);
				if(aktSzereplo.nev!="" && aktSzereplo.letezik=="true"){
                   aktSzereplo.letezik=true;
                   szereplok.push(aktSzereplo);
				}
			});
		}

		console.log("szereplő beviteli mezők száma: "+szereplokSzama);
		szereplokSzama=szereplok.length;
		console.log("létező szereplők száma: "+szereplokSzama);
		if(szereplokSzama==0){
			$("#rendszeruzenet").hide().html("<div class=\"hiba\">Legalább egy szereplőt a filmhez kell rendelni!</div>").show("bounce");
			console.log("kliens oldali hiba");
			hiba=true;
		}
		else $("#rendszeruzenet").hide();
		  
		var adatok={
			sid: Math.random(),
			filmAdatok: {
			 film_cim: $("#ujcim").val(),
			 poz: $("#ujpoz").val(),
			 dvd_kateg: $("#ujkat").val(),
			 rendezo: $("#ujrendezo").val(),
			 film_mufaj: $("#ujmufaj").val(),
			 kepsrc: $("#rejtettsrc_uj").val(),
             besz_ar: $("#ujbeszar").val(),
             ar_datum: $("#ujbeszdatum").val()
			},
			szereplok: szereplok
		}
		
		if(adatok.filmAdatok.film_cim==""){
               $("#hcim").css("display", "inline");
               hiba=true;
               $("#ujcim").focus();
		  }
		  else $("#hcim").css("display", "none");
		  
		  $.each(adatok.filmAdatok, function(index, val){
			console.log(index+": "+val);
		  });
		  if(szereplokSzama>0){
			console.log("Szereplők: ");
			$.each(adatok.szereplok, function(index, val){
			  console.log(index+". név: "+val.nev);
			  console.log(index+". létezik? "+val.letezik);
			});
		  }
		  else console.log("Nincsenek szereplők, ezért hiba van.");
		  
		  if(!hiba){
               $.ajax({
                    url: "rogzit.php",
                    type: "POST",
                    cache: false,
                    data: adatok,
                    async: true,
                    success: function(valasz){
                         if(valasz.indexOf("sikeresen rögzítésre")!=-1){
                              $("#rendszeruzenet").show().html("<div class='siker'>"+valasz+"</div>");
                              alapallapot("rogzitesutan");
                              listafrissit("listaz");
                              $("#ujcim").focus();
                         }
                         else{
							$("#rendszeruzenet").hide().html("<div class='hiba'>"+valasz+"</div>").show("bounce");
							console.log("ajax hiba üzenet");
						 }
                    }
               });
		  }
	 }
	 
	 function listafrissit(fajl){
          //console.log("listafrissit(\""+fajl+"\")");
          //return;
		  var sid=Math.random();
		  if(fajl==null) fajl="listaz";
		  var fajlnev=fajl+".php";
          var tmpObj=rendezesIrany[1-rendezes];
          tmpObj.sid=sid;
          tmpObj.miszerint=miszerint;
          		  
		  $.ajax({
               url: fajlnev,
               type: "POST",
               cache: false,
               data: tmpObj,
               async: false,
               success: function(ujtabla){
                    $("#also").html(ujtabla);
                    $("#easyTooltip").html(szereplokalap);
                    szelessegbeallitas();
               }
		  });
	 }
	 
	 function ujszereplo(){
		  var id=$(this).attr("id");
		  var melyik="uj", kateg="";
		  if(id=="mujszereplo"){	//meglévő film módosítása
				szamlalo=parseInt($("#holtart").val());
				szamlalo+=parseInt(seged);
				seged++;
				melyik="modositas";
				kateg="m";
		  }
		  else szamlalo++;
		  
		  var eleje="<div id=\""+kateg+"usznum"+szamlalo+"\" style=\"display: none;\"><span id=\""+kateg+"nr"+szamlalo+"\">"+szamlalo+".</span>";
				
		  $.post("osszesszereplo.php", {sid: Math.random(), szamlalo: szamlalo, hatokor: "szmezohozzaad", modositasvagyuj: melyik}, function(regiszereplok){
				$("#"+kateg+"ujszereplok").append(eleje+" "+regiszereplok+"</div>");
				$("#"+kateg+"usznum"+szamlalo).show("slide", {direction: "right"}, function(){
                    $("#"+kateg+"regiszereplo"+szamlalo).focus();
                });
		  });
	 }
	 
	 function szereplo_eltavolit(){
		  var akt=$(this).attr("id");
		  var hossz=akt.length;
		  var mezoid=parseInt(akt.match(/\d+/));
		  var osztaly=$(this).attr("class"); //2 érték fordulhat elő: sz_torol_mod, sz_torol_uj
		  //var i=szamlalo;
		  var i=parseInt(mezoid)+1;	//eredeti
		  var modosito="";
		  if(osztaly=="sz_torol_mod") modosito="m";
		  
		  $("#"+modosito+"usznum"+mezoid).fadeOut(400,function(){
				while(i<=szamlalo){	//eredeti
					 $("#"+modosito+"uj"+(i-1)).prop("checked", $("#"+modosito+"uj"+i).prop("checked"));
					 $("#"+modosito+"regi"+(i-1)).prop("checked", $("#"+modosito+"regi"+i).prop("checked"));
					 $("#"+modosito+"szereplo"+(i-1)).val($("#"+modosito+"szereplo"+i).val());
					 $("#"+modosito+"regiszereplo"+(i-1)).val($("#"+modosito+"regiszereplo"+i).val());
					 $("#"+modosito+"szereplo"+(i-1)).prop("disabled", $("#"+modosito+"szereplo"+i).prop("disabled"));
					 $("#"+modosito+"regiszereplo"+(i-1)).prop("disabled", $("#"+modosito+"regiszereplo"+i).prop("disabled"));
					 
					 i++;
				}
				$("#"+modosito+"usznum"+mezoid).show();
				//itt az utolsót töröljük (eredeti)
				$("#"+modosito+"usznum"+szamlalo).remove();
				szamlalo--;
				if(osztaly=="sz_torol_mod") seged--;
		  });
	 }
	 
	 function tiltasvaltas(){
		  var id=$(this).attr("id");
		  var sorszam=id.match(/\d+/);
		  var osztaly=$(this).attr("class");
		  var kateg="";
		  
		  if(id.substr(0,1)=="m") kateg="m";
		  
		  if(osztaly=="regiaktivalo"){
				$("#"+kateg+"regiszereplo"+sorszam).prop("disabled", "");
				$("#"+kateg+"szereplo"+sorszam).prop("disabled", "true");
		  }
		  else{
				$("#"+kateg+"regiszereplo"+sorszam).prop("disabled", "true");
				$("#"+kateg+"szereplo"+sorszam).prop("disabled", "");
		  }
	 }
	 
	 function alapallapot(par){
		  var kapottid=$(this).attr("id");
		  if(kapottid=="ab" || par=="rogzitesutan" || par=="mindentreset"){	//"ab"=adatbevitel fülön reset gomb
				$("#ujcim").val("");
				$("#ujkat").val("DVD");
				$("#ujrendezo").val("");
				$("#ujpoz").val("");
				var eleje="<div id=\"usznum1\"><span id=\"nr1\">1.</span>";
				$.post("osszesszereplo.php", {sid: Math.random()}, function(valasz){
					 $("#ujszereplok").html(eleje+valasz+"</div>");
				});
				$("#hcim").css("display", "none");
				$("#ujmufaj").val("szinkron");
				$("#rejtettsrc_uj").val("");
				meroleallit("uj");
				$("#upload_frame_uj").hide();
				$("#ujkepul").val("");
				
				szamlalo=1;
				seged=0;
		  }
		  if(kapottid=="gyk" || par=="mindentreset"){	//"gyk"=gyorskeresés
				$("#kcim").val("");
				$("#holbarhol").prop("checked", true);
				$("#kkateg").val("");
				$("#krendezo").val("");
				$("#kmufaj").val("0");
				$("#kpoz").val("");
				$("#kszereplo").val("");
				$("#mitaktiv").prop("checked", true);
				$("#mitinaktiv").prop("checked", true);
				$(".gykdoboz").html("").hide();
				$("#krate").val("0");
		  }
	 }
	 
	 function kereses(){
		  var kcim=$("#kcim").val();
		  var kkateg=$("#kkateg").val();
		  var krendezo=$("#krendezo").val();
		  var kmufaj=$("#kmufaj").val();
		  var kpoz=$("#kpoz").val();
		  var kszereplo=$("#kszereplo").val();
		  var aktivakat=$("#mitaktiv").prop("checked");
		  var inaktivakat=$("#mitinaktiv").prop("checked");
		  var ertekeles=$("#krate").val();
		  var sid=Math.random();
	  
		  //console.log($(this).attr("id")+"="+$(this).val());
		  //console.log("Adatok küldésre: "+kcim+", "+kkateg+", "+krendezo+", "+kmufaj+", "+kszereplo+", "+elejen+", "+barhol+", "+vegen+", "+aktivakat+", "+inaktivakat+", "+ertekeles+", "+sid)
		  if(aktivakat==false && inaktivakat==false) alert("Legalább egy négyzetet kötelező bejelölni!");
		  /*else if(kcim=="" && kkateg=="" && krendezo=="" && kszereplo=="" && kmufaj==0 && ertekeles==0){
			alert("Legalább egy keresési paraméter megadása kötelező!");
			$("#kcim").focus();
		  }*/
		  else{
				$("#also").hide();
				$("img.porgettyu").show();
				$.ajax({
					 url: "kereso.php",
					 type: "POST",
					 cache: false,
					 data: {sid: sid, kcim: kcim, kkateg: kkateg, krendezo: krendezo, kmufaj: kmufaj, kpoz: kpoz, kszereplo: kszereplo, aktivakat: aktivakat, inaktivakat: inaktivakat, ertekeles: ertekeles},
					 async: true,
					 success: function(ujtabla){
						  $("img.porgettyu").hide();
						  $("#et_szer_kep").appendTo("#temptarolo").hide();
						  $("#also").html(ujtabla).show();
						  $("#easyTooltip").html(szereplokalap);
						  $("#mindent_link").show();
						  szelessegbeallitas();
					 }
				});
		  }
		  /*
		   //ez a lehetséges aszinkron AJAX kérés:
		   $.post("kereso.php", {sid: sid, kcim: kcim, kkateg: kkateg, krendezo: krendezo, kmufaj: kmufaj, kszereplo: kszereplo, elejen: elejen, barhol: barhol, vegen: vegen, aktivakat: aktivakat, inaktivakat: inaktivakat, ertekeles: ertekeles}, function(valasz){
					 $("#also").html(valasz);
					 $("#easyTooltip").html(szereplokalap);
					 setTimeout(function(){
						  szelessegbeallitas();
					 },200);
		  });*/
	 }
	 
	 function tooltipbeolvas(id){
		  aktualis=id.substr(1);
		  //AJAX kérés, a válaszban megérkezik a szereplők listája
		  //és tooltip beállítás
		  var sid=Math.random();
		  $.ajax({
				url: "szereplok.php",
				type: "POST",
				cache: false,
				data: {sid: sid, film_id: aktualis},
				success: function(szereplok){
					 $("#easyTooltip").html(szereplok);
				}
		  });
	 }
	 
	 function muvelet(event){
		  var par=0;
		  if($(this).attr("id")!=null && $(this).attr("id")!="undefined") par=$(this).attr("id");
		  else par=$(this).attr("class").split(" ")[1];
		  var muv=par.substr(0,1);
		  var kepsrc=$(this).attr("src");
		  
		  $("#rendszeruzenet").html("").hide();
		  
		  $("#et_szer_kep").appendTo("#temptarolo").hide();
		  if(muv=="r"){	//rendezés
               var adatok={
                    sid: Math.random(),
                    miszerint: par.substr(1),
                    irany: rendezesIrany[0].irany,
                    src: rendezesIrany[0].src,
                    title: rendezesIrany[0].title
               };
               
               var ajaxfajl="listaz.php";
               var index;
               if(kepsrc.indexOf("novekvo.png")!=-1)//kepsrc=="img/novekvo.png" || kepsrc=="http://users.atw.hu/htmlseged/filmtar/img/novekvo.png"){
                    index=1;
               else index=0;
               rendezes=index;
               miszerint=adatok.miszerint;
               
               adatok.irany=rendezesIrany[1-index].irany;
               adatok.src=rendezesIrany[1-index].src;
               adatok.title=rendezesIrany[1-index].title;
               
               $.ajax({
                    url: ajaxfajl,
                    type: "POST",
                    cache: false,
                    data: adatok,
                    async: false,
                    success: function(ujtabla){
                          $("#also").html(ujtabla);
                          setTimeout(szelessegbeallitas, 200);
                    }
               });
		  }
		  else if(muv=="a"){	//aktivitás állítás
               var film_id=par.substr(1);
               var sid=Math.random();
               var regi_ertek=0;
               var modtargy="aktivitas";
               if(kepsrc=="img/pipa.png" || kepsrc=="http://users.atw.hu/htmlseged/filmtar/img/pipa.png") regi_ertek=1;
               
               $.post("mod.php", {sid: sid, film_id: film_id, regi_ertek: regi_ertek, modtargy: modtargy}, function(valasz){
                    if(valasz=="1"){
                         if(kepsrc=="img/pipa.png" || kepsrc=="http://users.atw.hu/htmlseged/filmtar/img/pipa.png"){
                               $("#"+par).attr({
                                    src: "img/x.png",
                                    alt: "inaktív",
                                    title: "inaktív"
                               });
                         }
                         else if(kepsrc=="img/x.png" || kepsrc=="http://users.atw.hu/htmlseged/filmtar/img/x.png"){
                               $("#"+par).attr({
                                    src: "img/pipa.png",
                                    alt: "aktív",
                                    title: "aktív"
                               });
                         }
                         $("#sor"+film_id).toggleClass("inaktiv");
                    }
                    else $("#rendszeruzenet").hide().html("<div class=\"hiba\">"+valasz+"</div>").show("bounce");
               });
		  }
		  else if(muv=="t"){	//módosítás gombra kattintásnál "Adatmódosítás" panel előhívása
               var film_id=par.substr(1);
               szereplo_id=0;
               //$("#rendszeruzenet").html("");
               szamlalo=1;
               seged=0;
               alapallapot("mindentreset");
               aktfcim=$("#f"+film_id).text();
			   
			   let fejlesztesAlatt=false; //ezt kell igazra állítani, ha fejlesztés alatt van a funkció
			   let bejelentkezve=$("#loginmsg").children("b").text();
               
               $("#folso").css("height", $("#folso").height());
               var melyik="#ujfilmadatlap";
               if($("#gyorskereses").css("display")=="block")
                    melyik="#gyorskereses";
               else if($("#szereplokezeles").css("display")=="block")
                    melyik="#szereplokezeles";
                    
               //$("#szereplokezeles").hide();
               $(melyik).slideUp(function(){
                    $("#filmadatmod").show();
                    $("#filmadatmod fieldset").html('<legend>Adatmódosítás</legend><img src="img/process.gif" class="porgettyu" alt="folyamatban" />');
               });

			   if(!fejlesztesAlatt || fejlesztesAlatt && bejelentkezve=="haruko"){
				setTimeout(function(){
					 $("#mcim").val(aktfcim);
					 
					 $.post("osszesszereplo.php", {sid: Math.random(), hatokor: "egyfilm", film_id: film_id}, function(valasz){
						  $("#filmadatmod").html(valasz);
						  $("#upload_frame_mod").hide();
						  $("#folyamat_doboz_mod").hide();
						  var i=1;
						  eredetiNevek.length=0;
						  while($("#mregiszereplo"+i).val()!=null){
							   eredetiNevek.push($("#mregiszereplo"+i).val());
							   i++;
						  }
						  $("#folso").css("height", "auto");
					 });
					 window.scrollTo(0, 0);
				},400);
			   }
			   else $("#filmadatmod").html("A módosítás funkció jelenleg fejlesztés alatt áll.");
		  }
		  else if(muv=="m"){	//értékelés (csillagok száma) módosítása azonnal
               var film_id=par.substr(2);
               var aktualis=par.substr(1,1);
               aktualis=parseInt(aktualis);
               var s=0;
               for(var i=0; i<5; i++) s+=csillagok[i];
               var celertek;
               var modosult=true;
               if(aktualis==0){
                    //vizsgálat: 0 vagy 1 volt korábban?
                    if(s>1)
                         celertek=1;
                    else{//ha s=0 -> celertek=1-0=1, ha s=1 -> celertek=1-1=0
                         celertek=1-s;
                    }
               }
               else{
                    if(aktualis==(s-1)) modosult=false;
                    celertek=(aktualis+1);
               }
               
               if(modosult){
                    $.post("mod.php", {sid: Math.random(), modtargy: "csillag", film_id: film_id, celertek: celertek}, function(valasz){
                         if(valasz=="siker"){
                              for(var i=0; i<5; i++){
                                   if(i<=aktualis){
                                        if(i==0){
                                             if(celertek==0){
                                                  csillagok[i]=0;
                                                  $("#m"+i+film_id).attr("src", "img/urescsillag_atlatszohatter.png");
                                             }
                                             else{
                                                  csillagok[i]=1;
                                                  $("#m"+i+film_id).attr("src", "img/telecsillag16.png");
                                             }
                                        }
                                        else{
                                             csillagok[i]=1;
                                             $("#m"+i+film_id).attr("src", "img/telecsillag16.png");
                                        }
                                   }
                                   else{
                                        csillagok[i]=0;
                                        $("#m"+i+film_id).attr("src", "img/urescsillag_atlatszohatter.png");
                                   }
                              }
                         }
                         else $("#rendszeruzenet").hide().html("<div class='hiba'>"+valasz+"</div>").show("bounce");
                    });
               }
		  }
	 }
	 
	 function rollover(par){
		  var film_id=par.substr(2);
		  var aktualis=par.substr(1,1);
		  for(var i=0; i<5; i++){
				var aktsrc=$("#m"+i+film_id).attr("src");
				if(aktsrc=="img/telecsillag16.png" || aktsrc=="http://users.atw.hu/htmlseged/filmtar/img/telecsillag16.png")
					 csillagok[i]=1;
				else csillagok[i]=0;
		  }
		  //alert(csillagok);
		  aktualis=parseInt(aktualis);
		  for(var i=0; i<=aktualis; i++){
				$("#m"+i+film_id).attr("src", "img/telecsillag16.png");
		  }
		  if(aktualis<4){
				for(var i=(aktualis+1); i<=4; i++){
					 $("#m"+i+film_id).attr("src", "img/urescsillag_atlatszohatter.png");
				}
		  }
	 }
	 
	 function rollout(par){
		  var film_id=par.substr(2);
		  var aktualis=par.substr(1,1);
		  for(var i=0; i<5; i++){
				if(csillagok[i]==1) $("#m"+i+film_id).attr("src", "img/telecsillag16.png");
				else $("#m"+i+film_id).attr("src", "img/urescsillag_atlatszohatter.png");
		  }
	 }
	
	 function lapotvalt(par){
		  var id=$(this).attr("id");
		  var kijelolve=$("#lapvalaszto").val();
          var mitrejt="#ujfilmadatlap";
          var betoltendo="#gyorskereses";
		  let fejlesztesAlatt=false; //ezt kell igazra állítani, ha fejlesztés alatt van a funkció
		  let bejelentkezve=$("#loginmsg").children("b").text();
          
          if(kijelolve=="oszlop_kezeles"){
               document.location.href="oszlop_kezeles";
               return;
          }
          if($("#gyorskereses").css("display")=="block")
               mitrejt="#gyorskereses";
          else if($("#szereplokezeles").css("display")=="block")
               mitrejt="#szereplokezeles";
          else if($("#filmadatmod").css("display")=="block")
               mitrejt="#filmadatmod";
               
          if(kijelolve=="ujfilmrogzitese"){
               betoltendo="#ujfilmadatlap";
          }
          else if(kijelolve=="szereplokezeles")
               betoltendo="#szereplokezeles";
			   
			$("#szereplo_lista").html(szereplo_lista);
			$("#szereplo_szures").val("");
          
		  if(id=="megse" || par=="torles"){
               //$("#folso").css("height", $("#folso").height());
               
               //setTimeout(function(){
                    if(kijelolve=="ujfilmrogzitese"){
                         $(mitrejt).slideUp(function(){
                              $(betoltendo).slideDown(function(){
                                   if($("#regiszereplo1")=="undefined"){
                                        $.post("osszesszereplo.php", {sid: Math.random(), hatokor: "szmezohozzaad"}, function(valasz){
                                             $("#usznum1").append(valasz);
                                        });
                                   }
                                   $("#upload_frame_uj").hide();
                                   $("#kep_valaszto").appendTo($("#ujfilm_szulo"));
                                   $("#folso").css("height", "auto");
                              });
                         });
                         
                    }
                    else if(kijelolve=="szereplokezeles"){
                         if(mitrejt!=betoltendo){
                              $(mitrejt).slideUp(function(){
                                   $(betoltendo).slideDown(function(){
                                        //$("#folso").css("height", "auto");
                                        $.post("osszesszereplo.php", {sid: Math.random(), hatokor: "szereplok_listaz"}, function(valasz){
                                             $("#szereplo_lista").html(valasz);
                                        });
                                   });
                              });
                         }
                         else{
                              $.post("osszesszereplo.php", {sid: Math.random(), hatokor: "szereplok_listaz"}, function(valasz){
                                   $("#szereplo_lista").html(valasz);
                              });
                         }
                    }
                    else{     //gyorskeresés van kijelölve
                         $(mitrejt).slideUp(function(){
                              $(betoltendo).slideDown(function(){
                                   $("#folso").css("height", "auto");
                              });
                         });
                    }
               //},400);
		  }
		  else if(id=="lapvalaszto"){	//$("#lapvalaszto").change esemény történt
               $("#folso").css("height", $("#folso").height());
               $("#filmadatmod").hide();//slideUp();
               //$("#gyorskereses, #ujfilmadatlap").toggle();
               
               if(kijelolve=="ujfilmrogzitese"){
                    //$("#filmadatmod, #szereplokezeles").slideUp();
                    $(mitrejt).slideUp(function(){     //ami aktív volt, elrejti
                         $(betoltendo).slideDown(function(){
                              $("#folso").css("height", "auto");
                                   /*$.post("osszesszereplo.php", {sid: Math.random(), hatokor: "szmezohozzaad"}, function(valasz){
                                        $("#usznum1").append(valasz);
                                   });*/
                              
							  if(!fejlesztesAlatt || fejlesztesAlatt && bejelentkezve=="haruko"){
								var valaszulo=$("#kep_valaszto").parent().attr("id");
								if(valaszulo!="ujfilm_szulo")
									 $("#kep_valaszto").appendTo($("#ujfilm_szulo"));
								
								$("#upload_frame_uj").hide();
							  }
							  else $("#ujfilmadatlap").html("Az adatbevitel funkció jelenleg fejlesztés alatt áll.");
                         });
                         /*setTimeout(function(){
                              
                         },400);*/
                    });
               }
               else if(kijelolve=="szereplokezeles"){
                    $(mitrejt).slideUp(function(){
                         $(betoltendo).slideDown(function(){
                              //$("#folso").css("height", "auto");
                              $.post("osszesszereplo.php", {sid: Math.random(), hatokor: "szereplok_listaz"}, function(valasz){
                                   $("#szereplo_lista").html(valasz);
                              });
                         });
                    });
               }
               else{
                    //$("#filmadatmod, #szereplokezeles").slideUp();
                    $(mitrejt).slideUp(function(){
                         $(betoltendo).slideDown(function(){
                              $("#folso").css("height", "auto");
                         });
                    });
               }
				
		  }
		  alapallapot("mindentreset");
		  seged=0;
		  if(par!="torles") $("#rendszeruzenet").html("").hide();
	 }
	 
	 function szelessegbeallitas(){
          //console.log("A kijelző szélessége: "+screen.width);
		  //for(var i=1; i<=9; i++){
          $("#ragados .lista tbody tr th").each(function(i, fejlec){
               let fw=$(this).width();//$("#fejlec"+i).width();	//fw=fejléc width, tw=tartalom width
               //console.log((i+1)+". fejléc szélessége: "+fw+" px");
               //console.log($(this));
               if(fw!="null"){
                    let tartalom=$("#adattabla tbody tr th:nth-child("+(i+1)+")");
                    let tw=tartalom.width();//$("#tartalom"+i).width();
                    if(fw<tw) $(this).width(tw);//$("#fejlec"+i).width(tw);
                    else if (fw>tw) tartalom.width(fw);
               }
		  });
		  var krsz=$("#krendezo").width();
		  $(".gykdoboz").width(krsz+"px");
		  if($("#ragados").attr("id")!=null && $("#ragados").attr("id")!="undefined"){
               var tableOffset = $("#adattabla").offset().top;
               var offset = $(window).scrollTop(); 
               if(offset>=tableOffset)
                    $("#ragados").show();
               else $("#ragados").hide();
		  }
		  //$("#tablahely").width($("table.lista").width());
	 }
	 
	 function mibenszerepel(){
          szereplo=$(this).text();
          szereplo_id=$(this).attr("id").split("_")[1];
          $("#mibenszerepel").show();
          $("#nevmod_eredmeny").hide();
          $("#sz_szerknev").val(szereplo);
          
          $("#sz_nevmod").prop("disabled", true);
          
          $.post("osszesszereplo.php", {sid: Math.random(), hatokor: "mibenszerepel_listaz", szereplo_id: szereplo_id}, function(valasz){
               $("#szerep_filmekben").html(valasz);
          });
	 }
	 
	 function kedvencekhezad(e){
		  var sajat_id=$(this).attr("id");
		  var film_id=sajat_id.substr(2);
		  e.preventDefault();
		  //ide jön a kedvencek táblába való rögzítés AJAX-szal, az eredmény kiíratása
		  //rögzítés: id-t üresen, film_fk_id-be a film_id, user_fk_id-be a $_SESSION["user"]
		  $.post("addtofav.php",{sid: Math.random(), film_id: film_id},function(valasz){
               if(valasz=="Hozzáadva") $("#hozzaadva"+film_id).text(valasz);
               else $("#rendszeruzenet").hide().html("<div class='hiba'>"+valasz+"</div>").show("bounce");
		  });
	 }
	 
	 function kedvencboltorol(e){
		  var sajat_id=$(this).attr("id");
		  var film_id=sajat_id.substr(2);
		  $.post("removefav.php",{sid: Math.random(), film_id: film_id},function(valasz){
               if(valasz=="torolve"){
                    listafrissit("getfavs");
                    $("#rendszeruzenet").html("<div class='siker'>A film törölve a listából.</div>");
                    setTimeout(function(){
                         $("#rendszeruzenet").html("").hide();
                    },1000);
               }
               else $("#rendszeruzenet").hide().html("<div class='hiba'>"+valasz+"</div>").show("bounce");
		  });
	 }
	 
	 function modositas(e){
          var regiSzereplok=new Array();
          var ujSzereplok=new Array();
          /*
           A regiszereplok tömb elemeinek struktúrája:
           elem={
            id: <id>,
            nev: <név>,
            torlendo: true/false
           }
           
           Az ujszereplok tömb elemeinek struktúrája:
           elem={
            nev: <név>,
            letezik: true/false
           }
           Az új szereplők kiválasztásánál fontos szempontok az alábbiak:
           - szűrjük ki az üresen hagyott mezőket
           - szűrjük ki azokat a szereplőket, akik már hozzá vannak rendelve a filmhez
           - szűrjük ki a nem létező (az "új szereplő rögzítése" kérdésnél mégsét választott) szereplőket
          */
          
          //console.clear();
        
          var i=0;
          let regiSzereplokSzama=$("div.meglevoszereplo").length;
          let ujSzereplokSzama=$("#mujszereplok div").length;
          let aktSzereplo={
               id: 0,
               nev: "",
               torlendo: false
          }
          let megtartandoRegiDb=0;
		  let ido=new Date();
          let idoBelyeg=ido.getTime();
          if(regiSzereplokSzama>0){
               console.log(idoBelyeg+"> Régi szereplők:");
               $("div.meglevoszereplo").each(function(){
                    aktSzereplo={
                         id: $(this).children("input[type=hidden]").val(),
                         nev: $(this).children("input[type=text]").val(),
                         torlendo: $(this).find("input[type=checkbox]").prop("checked")
                    };
                    regiSzereplok.push(aktSzereplo);
               });
               
               regiSzereplok.forEach(function(szereplo){
					idoBelyeg=ido.getTime();
                    console.log(idoBelyeg+"> "+szereplo.nev+" (id="+szereplo.id+"), törlendő? "+szereplo.torlendo);
                    if(szereplo.torlendo==false)
                         megtartandoRegiDb++;
               });
          }
		  
          if(ujSzereplokSzama>0){
               var marVan=false;
               $("#mujszereplok div").each(function(){
					//szereplő "létezés" ellenőrzés ettől ---------------
					//ez a szereploLetezikEll(e) fv. feladata, csak debug céljából volt itt a másolata
					//szereplő "létezés" ellenőrzés eddig ---------------
					aktSzereplo={
						 nev: $(this).children("input[type=text]").val(),
						 letezik: $(this).children("input[type=hidden]").val()
					};
					
					marVan=false;
					regiSzereplok.forEach(function(szereplo){
						 if(aktSzereplo.nev==szereplo.nev)
							  marVan=true;
					});
					
					idoBelyeg=ido.getTime();
					console.log(idoBelyeg+"> Szerepel már "+aktSzereplo.nev+" nevű szereplő a filmben? "+marVan);
					if(aktSzereplo.nev!="" && !marVan && aktSzereplo.letezik=="true"){
						 aktSzereplo.letezik=true;
						 ujSzereplok.push(aktSzereplo);
					}
               });
               
               if(ujSzereplok.length>0){
					idoBelyeg=ido.getTime();
                    console.log(idoBelyeg+"> Új szereplők:");
                    ujSzereplok.forEach(function(szereplo){
						idoBelyeg=ido.getTime();
                        console.log(idoBelyeg+"> "+szereplo.nev+", létezik? "+szereplo.letezik);
                    });
               }
          }
          
		  idoBelyeg=ido.getTime();
          ujSzereplokSzama=ujSzereplok.length;
          console.log(idoBelyeg+"> Megtartandó régi szereplők száma: "+megtartandoRegiDb);
          console.log(idoBelyeg+"> Új szereplők száma: "+ujSzereplokSzama);
			
          if((megtartandoRegiDb+ujSzereplokSzama)==0){
               alert("Legalább 1 szereplőnek minden filmben lennie kell!");
               return;
          }

          var kepsrc=$("#rejtettsrc_mod").val();
          if(kepsrc=="undefined") kepsrc="";
          var adatok={
              sid: Math.random(),
              modtargy: "minden",
              film_id: $("#mfilm_id").val(),
              filmAdatok: {
               film_cim: $("#mcim").val(),
               poz: $("#mpoz").val(),
               dvd_kateg: $("#mkat").val(),
               rendezo: $("#mrendezo").val(),
               film_mufaj: $("#mmufaj").val(),
               besz_ar: $("#mbeszar").val(),
               ar_datum: $("#mbeszdatum").val(),
               kepsrc: kepsrc
              },
              regiSzereplok: regiSzereplok,
              ujSzereplok: ujSzereplok
          }
          
          $.each(adatok.filmAdatok, function(index, val){
               console.log(index+": "+val);
          });
  
          $.ajax({
              url: "mod.php",	//mod.php-nak a módosításhoz feltétlenül szüksége van: sid, modtargy, film_id
              type: "POST",
              cache: false,
              data: adatok,
              async: true,
              success: function(valasz){
                    if(valasz.indexOf("Sikeres adatmódosítás")!=-1){
                         $("#rendszeruzenet").show().html("<div class='siker'>"+valasz+"</div>");
                         if(kepsrc!=""){
                              $("#kepinfo_mod").text(kepsrc);
                              $("#keptorles").show();
                         }
                         $.post("osszesszereplo.php", {sid: Math.random(), hatokor: "egyfilm", film_id: $("#mfilm_id").val()}, function(valasz){
                              $("#filmadatmod").html(valasz);
                              $("#folyamat_doboz_mod").hide();
                              $("#upload_frame_mod").hide();
                              var i=1;
                              eredetiNevek.length=0;
                              
                              while($("#szereplo_id"+(i+1)).val()!=null){
                                   eredetiNevek.push($("#szereplo_id"+(i+1)).val());
                                   i++;
                              }
                         });
                         listafrissit("listaz");
                    }
                   else $("#rendszeruzenet").hide().html("<div class='hiba'>"+valasz+"</div>").show("bounce");
                   alapallapot("mindentreset");
              }
          });
	 }
	 
     function torles(){
          var megerosit="";
          var id="";
          var mit="szereplo";
          if($("#mibenszerepel").css("display")=="block"){
               megerosit=confirm("Biztos törlésre kerüljön a szereplő?\nMegerősítést követően a művelet visszavonhatatlan lesz!");
               id=szereplo_id;
          }
          else{
               id=$("#mfilm_id").val();
               mit="film";
               megerosit=confirm("Biztos törlésre kerüljön a film?\nMegerősítést követően a művelet visszavonhatatlan lesz!");
          }
          
          if(megerosit){
               $.ajax({
                    url: "torles.php",
                    type: "POST",
                    cache: false,
                    data: {sid: Math.random(), id: id, mit: mit},
                    async: false,
                    success: function(valasz){
                         if(valasz=="Sikeres törlés."){
                              $("#rendszeruzenet").hide().html("<div class='siker'>"+valasz+"</div>").fadeIn();
                              if(id!="") alapallapot("mindentreset");
                              lapotvalt("torles");
                              listafrissit("listaz");
                              $("#mibenszerepel").hide();
                         }
                         else{
                              $("#rendszeruzenet").hide().html("<div class='hiba'>"+valasz+"</div>").show("bounce");
                         }
                    }
               });
          }
     }
	 
	 function UpdateTableHeaders() {
		  $("div.divlista").each(function() {
               var originalHeaderRow = $(".tableFloatingHeaderOriginal", this);
               var floatingHeaderRow = $(".tableFloatingHeader", this);
               var offset = $(this).offset();
               var scrollTop = $("#tablahely").scrollTop();
               if ((scrollTop > offset.top) && (scrollTop < offset.top + $(this).height())) {
                    floatingHeaderRow.css("visibility", "visible");
                    floatingHeaderRow.css("top", Math.min(scrollTop - offset.top, $(this).height() - floatingHeaderRow.height()) + "px");
                    $("th", floatingHeaderRow).each(function(index) {
                         var cellWidth = $("th", originalHeaderRow).eq(index).css('width');
                         $(this).css('width', cellWidth);
                    });
                    floatingHeaderRow.css("width", $(this).css("width"));
               }
               else {
                    floatingHeaderRow.css("visibility", "hidden");
                    floatingHeaderRow.css("top", "0px");
               }
		  });
     }
	 
	 function filecheck(){
		  var submitid=$(this).attr("id");
		  var kateg="";
		  if(submitid=="toltes_uj") kateg="uj";
		  else if(submitid=="toltes_mod") kateg="mod";
		  var modkep=$("#"+kateg+"kepul").val();
		  if(modkep==""){
               alert("Hiba! Kötelező kiválasztani a feltöltendő képet!");
               $("#"+kateg+"kepul").focus();
               return false;
		  }
		  return true;
	 }
	 
	 function tisztitas(mit){
		  var tmp=mit.replace(" ", "_");
		  return tmp;
	 }
	 
	 function pozicioHelyiMod(){
		let kuldo=$(this);
		let id=kuldo.attr("id");
        let osztaly=kuldo.attr("class");
		let film_id=id.slice(id.indexOf("_")+1);
		let ertek=kuldo.text();
		
		let gyerekek=kuldo.children().length;
		if(gyerekek==0) pozicio=ertek;
		
		//console.log("pozicioHelyiMod() fv: pozíció értéke: "+pozicio);
		
		if(gyerekek==0){
          if(osztaly.indexOf("mufaj")>-1){
               let adatok={
                    ajax: 1,
                    fv: {
                         nev: "getMufajok",
                         param: ertek
                    }
               }
               
               
               $.post("ajax.lista.php", adatok, function(valasz){
                    kuldo.html("<select id='mm_"+film_id+"'>"+valasz+"</select>").find(">:first-child").focus();
               });
               
          }
          else
               kuldo.html("<input type='text' id='mp_"+film_id+"' size=4 value='"+ertek+"' />").find(">:first-child").focus();
		}
	 }
	 
	 function pozicioHelyiElhagy(){
		let ertek=$(this).val();
		let szulo=$(this).parent();
		let id=$(this).attr("id");
        let osztaly=szulo.attr("class");
		let film_id=id.slice(id.indexOf("_")+1);
        let targy="pozicio";

          //console.log("elhagyás: érték: "+ertek+", pozíció: "+pozicio);
		if(ertek!=pozicio && ertek!=null && ertek!=""){
			var adatok={
				sid: Math.random(),
				modtargy: "barmi",
				film_id: film_id,
				filmAdatok: {
					poz: ertek
				}
			}
            
			if(osztaly.indexOf("mufaj")>-1){
               delete adatok.filmAdatok.poz;
               adatok.filmAdatok.film_mufaj=ertek;
               var szoveg=$("#"+id+" option:selected").text();
               targy="mufaj";
            }
            
			$.post("mod.php", adatok, function(valasz){
				if(valasz=="siker"){
                    if(targy=="mufaj")
                         szulo.html(szoveg);
                    else szulo.html(ertek);
                }
				else $("#rendszeruzenet").hide().html("<div class='hiba'>"+valasz+"</div>").show("bounce");
			});
		}
		else szulo.html(pozicio);
	 }
	 
	 function pozicioHelyiEsc(szulo){
		szulo.html(pozicio);
	 }
	 
	 function szereploLetezikEll(e){
		var adatok={
			sid: Math.random(),
			szereplo: $(e.target).val(),
			vizsgalando: true,
			hozzaadando: false
		};
		let letezikInput=$(e.target).parent().children("input[type=hidden]");
		let ido=new Date();
		let idoBelyeg=ido.getTime();
		
		if(adatok.szereplo!=""){
			$.ajaxSetup({async: false});
			$.post("szereplo_hozzaad.php", adatok, function(valasz){
				if(valasz.indexOf("nem létezik")>=0){
					letezikInput.val(false);
					if(confirm(valasz)){
						adatok.sid=Math.random();
						adatok.vizsgalando=false;
						adatok.hozzaadando=true;
						
						$.ajaxSetup({async: false});
						$.post("szereplo_hozzaad.php", adatok, function(rogzites){
							let szoveg="<div class='";
							if(rogzites.indexOf("Hiba")<0){
								szoveg+="siker'>"+rogzites+"</div>";
								letezikInput.val(true);
								$("#rendszeruzenet").html(szoveg).fadeIn();
							}
							else{
								szoveg+="hiba'>"+rogzites+"</div>";
								letezikInput.val(false);
								$("#rendszeruzenet").html(szoveg).show("bounce");
							}
							szoveg+="'>"+rogzites+"</div>";
						});
					}
				}
				else letezikInput.val(true);
				idoBelyeg=ido.getTime();
			});
			$.ajaxSetup({async: true});
		}
		idoBelyeg=ido.getTime();
	 }
	 
	 function teszt(e){
		let ido=new Date();
		let idoBelyeg=ido.getTime();
		console.log(idoBelyeg+"> "+e.type+" esemény történt");
	 }