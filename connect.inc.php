<?php
  //kapcsolódás az adatbázishoz, karakterkészlet beállítása
  $mysqli=new mysqli(HOST_NAME, USER_NAME, PASSWORD, DB_NAME);
  @$mysqli->query("SET CHARACTER SET UTF8");
  @$mysqli->query("SET COLLATE utf8_hungarian_ci");
?>