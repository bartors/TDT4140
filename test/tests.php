<?php
session_start ();
require '../connect.php';
//require "phpunit-6.0.10.phar";
require 'Allfunctions.php';



function makeClassTest($connection, $userid, $className){
	makeClass($connection, $userid, $className);
	$showClass = "SELECT * FROM class WHERE classname='$className'";
	$classes = mysqli_query ( $connection, $showClass ) or die ( mysqli_error ( $connection ) );
	mysqli_close();
    $row=mysqli_fetch_array($classes);
	if($className=$row['classname']){
    	return true;
	}
	else{
		return false;
	}

}





?>

<html>

	<head>
		
	</head>

	<body>

	<?php
		echo makeClassTest($connection, '7', 'test1234');

	?>

</html>
