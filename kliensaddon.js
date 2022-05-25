var ketyego;
var mp;

	/*$.get("upload_frame.php?progress_key=<?php //echo $up_id; ?>&randval="+ Math.random(),{}, function(data){
		 var ertek=parseInt(data);
		 //$('#progress_container').fadeIn(100);	//fade in progress bar
		 //$('#progress_bar').width(ertek+"%");	//set width of progress bar based on the $status value (set at the top of this page)
		 //$('#progress_completed').html(ertek +"%");	//display the % completed within the progress bar
		 if(ertek==100){
			clearInterval(ketyego);
			//$("#progress_container").toggleClass("rejtett");
			$("#folyamat_doboz").toggleClass("rejtett");
		 }
	  }
	)*/

function meroindit(kat){
	 mp=0;
	 $("#folyamat_doboz_"+kat).show(); //ebben van a "pörgettyű"
	 ketyego=setInterval(function(){
		  if((mp%3)==0) $("#pottyok_"+kat).text(".");
		  else $("#pottyok_"+kat).append(".");
		  mp++;
	 },1000);
}

function meroleallit(kat){
	 $("#folyamat_doboz_"+kat).hide();
	 clearInterval(ketyego);
}

function get_kiterjesztes(fajlnev){
	var reszek=fajlnev.split(".");
	return reszek[reszek.length-1];
}

function kep_e(fajlnev){
	var kiterjesztes=get_kiterjesztes(fajlnev);
	switch(kiterjesztes.toLowerCase()){
		case "jpg":
		case "jpeg":
		case "gif":
		case "png":
			return true;
	}
	return false;
}