<?php
    include_once("initial.php");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tömeges fájl átnevezés</title>
    <meta charset="UTF-8" />
    <style type="text/css">
        ul li{
            list-style-type: none;
        }
        div{
            width: 400px;
            float: left;
            padding: 0 3px 0 3px;
            border-right: 1px solid black;
        }
    </style>
</head>

<body>
    <h2>Tömeges fájl átnevezés</h2>
    <!--<div>
        Film borító képek fizikai fájlnevei:
        <?php
        /*$megnyitando=$_SERVER["DOCUMENT_ROOT"]."filmtar/img/boritok/kicsi";
        if ($handle = opendir($megnyitando)) {
            $files = 0; 
            
            echo "<ul>";
            foreach(scandir($megnyitando) as $fajlnev){
                if($fajlnev!="." && $fajlnev!=".."){
                    echo "<li>".$fajlnev;
                    
                    echo "</li>";
                    $files++;
                }
            }
            echo "</ul>";
            
            if($files==0) echo "Jelenleg nem található fájl a listában.";
            else echo "Jelenleg ".$files." fájl található a listában.";
            closedir($handle);
        }
        else echo "Nem sikerült megnyitni a fájlokat tartalmazó mappát. Próbáld újra később!";*/
        ?>
    </div>-->
    <div>
        Film borító képek adatbázisban tárolt nevei:
        A véletlen adatbázis manipuláció elkerülése érdekében a fájl további funkciói blokkolva.
        <?php
            /*$lek="select * from filmek order by kepsrc";
            if($res=$mysqli->query($lek)){
                $db=$res->num_rows;
                if($db>0){
                    echo "<ul>";
                    $update_lek=""; $update_res="";
                    while($sor=$res->fetch_assoc()){    //Ez nagy hiba (erőforrás pocsékolás), hogy egyenként futtatom le a lekérdezéseket!
                        echo "<li>";    //Helyette érdemes lenne először csak kigyűjteni a megfelelő film_id-ket, majd belefűzni az EGYETLEN lekérdezés szövegébe (pl film_id in(1,2,3))!
                        echo (($sor["kepsrc"]!="") ? $sor["kepsrc"] : "- nincs kép! -");
                        $update_lek="update filmek set kepsrc='".urlfajlnev($sor["kepsrc"])."' where film_id=".$sor["film_id"];
                        if($update_res=$mysqli->query($update_lek)) echo ", id: ".$sor["film_id"]."... sikeres";
                        else echo ", id: ".$sor["film_id"]."... hiba!";
                        echo "</li>";
                    }
                    unset($update_lek, $update_res);
                    echo "</ul>";
                    echo "Jelenleg ".$db." fájl található a listában.";
                }
                else echo "Nincs kép az adatbázisban!";
            }
            else echo "Hiba történt lekérdezés közben!";*/
        ?>
    </div>

</body>
</html>
