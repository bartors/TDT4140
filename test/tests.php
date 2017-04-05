<?php
session_start ();
require '../connect.php';
//require "phpunit-6.0.10.phar";
require 'Allfunctions.php';



function makeClassTest($connection, $userid, $className){
	makeClass($connection, $userid, $className);
	$showClass = "SELECT classname FROM class WHERE classname='$className'";
	mysqli_close();
    $result=mysqli_query($connection, $showClass);
	if($className=$result){
    	return "true";
	}
	else{
		return "false";
	}

}





?>

<html>

	<head>
		
	</head>

	<body>

	<?php
		//alltid endre klassenavn for å unngå duplikat
		echo makeClassTest($connection, '7', 'test1234');

	?>

</html>
