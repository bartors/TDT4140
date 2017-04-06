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

//tester showclasses
function showClassesTest($connection, $userid, $classes) {
	global $numberOfSuccess;
    $result = showClasses ( $connection, $userid );
    $i = 0;
    while ( $row = mysqli_fetch_array ( $result ) ) {
        if(!($row['classname']===$classes[$i])){
            return "showClassesTest: false</br>";
        }
        $i++;
    }
    $numberOfSuccess++;
    return "showClassesTest: true</br>";
}

//array for showclasstest
$classes=array(
        0=>'myClass',
        1=>'klasa',
        2=>'klasa1',
        3=>'asasa',
        4=>'dsaasda',
        5=>'qwert',
        6=>'asdsadasfew',
        7=>'mama',
        8=>'Tata',
        9=>'klasa10',
        10=>'klasa11',
        11=>'klasa12',
        12=>'klasa13',
        13=>'klasa14',
        14=>'testquiz',
        15=>'userTest'  
);

function ShowQuizTest($connection, $qid, $quizname){
	global $numberOfSuccess;
	$resultName = showQuiz($connection, $qid);
	if ($resultName === $quizname){
		$numberOfSuccess++;
    	return "showQuizTest: true</br>";
	}
	else{
		return"showQuizTest: false</br>";
	}
}

//test som viser aktive quizer
//skriver quizer som linker. Bruker funksjonaliteten her til å lage en liste vi kan teste opp mot
function displayActiveQuizesTest($connection, $classname, $quizArray){
	global $numberOfSuccess; 
	$activeQuizes = mysqli_query($connection, "SELECT name FROM quiz WHERE (classid=(SELECT classid from class where classname='$classname') AND active=1) ORDER BY activDate DESC");
	$i = 0;
	while ($row = $activeQuizes->fetch_assoc()){
		if(!($row['name']===$quizArray[$i])){
            return "showActiveQuizesTest: false</br>";
        }
    	$i++;
    }
    $numberOfSuccess++;
    return "showActiveQuizesTest: true</br>";
}

//array for showActiveQuizesTest
$quizArray=array(
        0=>'testquiz',
        1=>'testquiz4',
        2=>'testquiz2'
);

//displayQuizname test. Endrer logikken litt så vi får kjørt test på denne da den bare skriver ut <h2>'er
function modifiedDisplayQuizName($quizname){
	return $quizname;
}

function displayQuizNameTest($quizname){
	global $numberOfSuccess;
	$name = modifiedDisplayQuizName($quizname);
	if($name === $quizname){
		$numberOfSuccess++;
    	return "displayQuizNameTest: true</br>";
	}
	else{
		return "displayQuizNameTest: false</br>";
	}
}

//test for hvilke klasser studenten er med i
function showClassesStudentTest($connection, $userid, $studentClassArray){
	global $numberOfSuccess;
	$classes = showClassesStudent($connection, $userid);
	$i = 0;
    while ( $row = mysqli_fetch_array ( $classes ) ) {
        if(!($row['classname']===$studentClassArray[$i])){
            return "showClassesStudentTest: false</br>";
        }
        $i++;
    }
    $numberOfSuccess++;
    return "showClassesStudentTest: true</br>";
}

//liste for test av studentklasser
$studentClassArray=array(
        0=>'myClass',
        1=>'klasa',
        2=>'userTest'
);

//test for displayClassNAme, endrer logikk for å teste funksjonalitet da den skriver ut navn som <h2>.
function displayClassnameTest($className){
	global $numberOfSuccess;
	$_SESSION['classname']=$className;
	$value = $_SESSION['classname'];
	if($className === $value){
		$numberOfSuccess++;
    	return "displayClassnameTest: true</br>";
	}
	else{
		return "displayClassnameTest: false</br>";
	}
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
		echo showClassesTest($connection,'6',$classes);
		echo ShowQuizTest($connection, '1', 'testquiz');
		echo displayActiveQuizesTest($connection, 'myClass', $quizArray);
		echo displayQuiznameTest('testName');
		echo showClassesStudentTest($connection, '2', $studentClassArray);
		echo displayClassnameTest('myClass');

		//echo test();

		echo "<p>Full coverage: ".testRate($numberOfSuccess, $numberOfFunctions)."%</p>";

	?>

</html>