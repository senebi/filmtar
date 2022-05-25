<?php
	//konfig tömb, mely tartalmazza az adatbázis mező neveket és a hozzájuk tartozó megjelenő fejléceket
	//az összes film listázásnál és a keresési találatoknál megjelenő táblázat fejléchez használandó
	$mezok=array(array("dbnev" => "film_cim", "fejlec" => "Cím"),
				array("dbnev" => "mufaj", "fejlec" => "Műfaj"),
				array("dbnev" => "poz", "fejlec" => "Poz."),
				array("dbnev" => "rate", "fejlec" => "Érték"),
				array("dbnev" => "dvd_kateg", "fejlec" => "Kat."),
				array("dbnev" => "rogz_datum", "fejlec" => "Rögz."),
				array("dbnev" => "rendezo", "fejlec" => "Rendező"),
				array("dbnev" => "besz_ar", "fejlec" => "Besz. ár"),
				array("dbnev" => "ar_datum", "fejlec" => "Besz. dátum"),
				array("dbnev" => "aktiv", "fejlec" => "Aktív"),
				);
?>