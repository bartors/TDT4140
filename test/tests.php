<?php

//hvis du vil sjekke errors(kommenter ut hvis ikke)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start ();
require '../connect.php';
include '../lib/security.php';
//include "phpunit-6.0.10.phar";
include 'AllFunctions.php';

$numberOfSuccess;
$numberOfFunctions = 32;

function makeClassTest($connection, $userid, $className){
	global $numberOfSuccess;
	makeClass($connection, $userid, $className);
	$showClass = "SELECT classname FROM class WHERE classname='$className'";
    $result=mysqli_query($connection, $showClass);
    while ($row = $result->fetch_assoc()) {
		if($className==$row['classname']){
			$numberOfSuccess++;
	    	return "makeClassTest: true</br>";
		}
		else{
			return "makeClassTest: false</br>";
		}
	}
}


//test for brukernavn i registrering
function registerUserTestUsername($connection, $username, $email, $password){
	global $numberOfSuccess;
	$salt1=getRandomString();
	$salt2=getRandomString();
	$teacher=false;
	$publicSalt = 'cluFlA6+i1zi_sI_';
	$password=createPassword($publicSalt, $username, $salt1, $salt2, $password);
	registerUser($connection, $username, $email, $password, $teacher, $salt1, $salt2);
	$showUsername = "SELECT username FROM users WHERE username='$username'";
    $result=mysqli_query($connection, $showUsername);
    while ($row = $result->fetch_assoc()) {
		if($username==$row['username']){
			$numberOfSuccess++;
	    	return "registerUserTestUsername: true</br>";
		}
		else{
			return "registerUserTestUsername: false</br>";
		}
	}
}

//test for epost i registrering
function registerUserTestEmail($connection, $username, $email, $password){
	global $numberOfSuccess;
	$salt1=getRandomString();
	$salt2=getRandomString();
	$teacher=false;
	$publicSalt = 'cluFlA6+i1zi_sI_';
	$password=createPassword($publicSalt, $username, $salt1, $salt2, $password);
	registerUser($connection, $username, $email, $password, $teacher, $salt1, $salt2);
	$showUsername = "SELECT email FROM users WHERE username='$username'";
    $result=mysqli_query($connection, $showUsername);
    while ($row = $result->fetch_assoc()) {
		if($email==$row['email']){
			$numberOfSuccess++;
	    	return "registerUserTestEmail: true</br>";
		}
		else{
			return "registerUserTestEmail: false</br>";
		}
	}
}

//test for å sjekke om studenter blir lagt til i klasse
function attendsTest($connection, $classname, $userid){
	global $numberOfSuccess;
	attends($connection, $classname, $userid);
	$testIfInClass = "SELECT userid FROM attends WHERE (classid=(SELECT classid FROM class WHERE classname='$classname') AND userid='$userid')";
    $result=mysqli_query($connection, $testIfInClass);
    while ($row = $result->fetch_assoc()) {
    	if($userid==$row['userid']){
			$numberOfSuccess++;
    		return "attendsTest: true</br>";
		}
		else{
			return "attendsTest: false</br>";
		}
	}	
}

//test for å sjekke om student er meldt ut av klasse
function stopAttendingTest($connection,$userid,$classid){
	global $numberOfSuccess;
	stopAttending($connection, $userid, $classid);
	$testIfInClass = "SELECT userid FROM attends WHERE (classid='$classid' AND userid='$userid')";
    $result=mysqli_query($connection, $testIfInClass);
    while ($row = $result->fetch_assoc()) {
    	return "stopAttendingTest: false</br>";
	}
	$numberOfSuccess++;
	return "stopAttendingTest: true</br>";
}


//tester for coverage
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
		//husk å endre variablene når du tester
		echo makeClassTest($connection, '7', 'testclass01');
		echo registerUserTestUsername($connection, 'testbruker01', 'testemail01', 'testpassord01');
		echo registerUserTestEmail($connection, 'testbruker02', 'testemail02', 'testpassord02');
		echo attendsTest($connection, 'testclass01', '95');
		echo stopAttendingTest($connection, '95', '138');


		//echo test();

		echo "<p>Full coverage: ".testRate($numberOfSuccess, $numberOfFunctions)."%</p>";

	?>

</html>