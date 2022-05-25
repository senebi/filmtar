<?php
require_once("initial.php");

if(isset($_POST["ajax"], $_POST["fv"])){
	$fv=$_POST["fv"]["nev"];
	$param=$_POST["fv"]["param"];
	
	//var_dump($_POST);
	//echo "Itt jönne a ".$fv."() nevű fv. meghívása, ha létezik...";
	if(function_exists($fv))
		call_user_func($fv, $param);
}
else echo "Nem érkezett AJAX hívás.";

?>