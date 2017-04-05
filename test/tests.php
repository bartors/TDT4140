<?php
session_start ();
require '../connect.php';
//include "phpunit-6.0.10.phar";
include 'AllFunctions.php';

$numberOfSuccess;
$numberOfFunctions = 32;

function makeClassTest($connection, $userid, $className){
	global $numberOfSuccess;
	makeClass($connection, $userid, $className);
	$showClass = "SELECT classname FROM class WHERE classname='$className'";
	mysqli_close();
    $result=mysqli_query($connection, $showClass);
	if($className=$result){
		$numberOfSuccess++;
    	return "true";
	}
	else{
		return "false";
	}

}

function test(){
	global $numberOfSuccess;
	$newSUm = $numberOfSuccess + 1;
	$numberOfSuccess = $newSUm;
}
$rate = 0;

function testRate($x, $y){
	global $rate;
	$rate = $x/$y;
	$rate = $rate * 100;
	return $rate;

}


?>

<html>

	<head>
		
	</head>

	<body>

	<?php
		
		echo makeClassTest($connection, '7', 'assad1222');
		//echo test();

		echo "<p>".testRate($numberOfSuccess, $numberOfFunctions)."%</p>";

	?>

</html>
