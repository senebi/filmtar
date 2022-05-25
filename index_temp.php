<?php
    @session_start();
    include_once("initial.php");
    require_once("../m.filmtar/Mobile-Detect-2.8.19/Mobile_Detect.php");
	 
	 $detect=new Mobile_Detect;
	 if($detect->isMobile() || $detect->isTablet())
		  header("location: ../m.filmtar/index.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>HTML segéd - Filmtár</title>
    <link rel="stylesheet" type="text/css" href="stilus.css" />
	 <style type="text/css">
		  h2, p{text-align: center;}
	 </style>
</head>
<body>
    <h1 align="center">Filmtár</h1>
		  <h2>Az oldal jelenleg fejlesztés alatt áll. Próbáld meg később!</h2>
		  <p><img src="img/website_under_construction.jpg" alt="Az oldal fejlesztés alatt áll." title="Az oldal fejlesztés alatt áll." /></p>
</body>
</html>
