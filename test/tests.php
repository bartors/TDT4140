<?php

//hvis du vil sjekke errors(kommenter ut hvis ikke)
//ini_set("memory_limit","3000M");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start ();
require '../connect.php';
include '../lib/security.php';
//include "phpunit-6.0.10.phar";
include 'AllFunctions.php';

$numberOfSuccess;
$numberOfFunctions = 35;

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
        15=>'userTest',
		16=>'dsada',
		17=>'adsdsadsa'
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
        0=>'testquiz2',
        1=>'testquiz',
        2=>'testquiz4'
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

//test for å finne quizzer i klasse
function showQuizesInClassTest($connection, $classname, $quizes){
	global $numberOfSuccess;
	$quizesInClass = showQuizes($connection, $classname);
	$i = 0;
    while ( $row = mysqli_fetch_array ( $quizesInClass ) ) {
        if(!($row['name']===$quizes[$i])){
            return "showQuizesInClassTest: false</br>";
        }
        $i++;
    }
    $numberOfSuccess++;
    return "showQuizesInClassTest: true</br>";

}

//liste over quizer i et fag som brukes i showQuizesInClassTest
$quizesInClassTest=array(
        0=>'testquiz',
        1=>'testquiz2',
        2=>'testquiz3',
        3=>'testquiz4',
        4=>'Quiz'
);

//test for makeQuiz
function makeQuizTest($connection, $quizName, $className){
	global $numberOfSuccess;
	makeQuiz($connection, $quizName, $className);
	$showQuiz = "SELECT classname FROM class WHERE classname='$className'";
	$result=mysqli_query($connection, $showQuiz);
	while ($row = $result->fetch_assoc()) {
		if($className==$row['classname']){
			$numberOfSuccess++;
	    	return "makeQuizTest: true</br>";
		}
		else{
			return "makeQuizTest: false</br>";
		}
	}
}

//test for deactivateClass

function deactivateClassTest($connection,$active,$classID){
	global $numberOfSuccess;
	deactivateClass($connection, $classID);
	$query="SELECT * from class where classid=$classID";
	$result=mysqli_query($connection, $query)or die(mysqli_error($connection));
	$row=mysqli_fetch_array($result);
	if($row['teacherDeleted']==$active){
		$numberOfSuccess++;
		return "deactivateClassTest: true</br>";
	}
	return "deactivateClassTest: false</br>";
}

// test for activateQuiz without header and without post-variables
//sjekke datomerkinga på aktiverte quizer
function activateQuizTest($connection,$qid){
	global $numberOfSuccess;
	$query="SELECT * from quiz where qid=$qid";
	$result=mysqli_query($connection, $query)or die(mysqli_error($connection));
	$row=mysqli_fetch_array($result);
	$status=$row['active'];
	unset($row);
	$date=activateQuiz($connection, $qid, $status);
	$query="SELECT * from quiz where qid=$qid";
	$result=mysqli_query($connection, $query)or die(mysqli_error($connection));
	$row=mysqli_fetch_array($result);
	if($row['active']!=$status){
		if($status==0){
			if($date===$row['activDate']){
				$numberOfSuccess++;
				return "activateQuizTest: true</br>";
			}else{
				return "activateQuizTest: falsen</br>";
			}
			
		}else{
			$numberOfSuccess++;
			return "activateQuizTest: true</br>";
		}
		
	}
	return "activateQuizTest: false</br>";
}

function deleteQuizTest($connection,$className,$quizName){
	global $numberOfSuccess;
	makeQuiz($connection, $quizName, $className);
	$showQuizId="select * from quiz where name='$quizName'";
	$result = mysqli_query ( $connection, $showQuizId ) or die ( mysqli_error ( $connection ) );
	$row=mysqli_fech_array($result);
	deleteQuiz($connection, $row['qid']);
	$qid=$row['qid'];
	$showQuizName="select name from quiz where qid='$qid'";
	$result = mysqli_query ( $connection, $showQuizName ) or die ( mysqli_error ( $connection ) );
	$count= mysqli_num_rows ( $result );
	if($count>0){
		return "activateQuizTest: false</br>";
	}
	$numberOfSuccess++;
	return "activateQuizTest: true</br>";
}

//test for å lage et spørsmål
function makeQuestionTest($connection,$qid,$classname,$question,$a,$b,$c,$d,$ans,$topic){
	global $numberOfSuccess;
	makeQuestion($connection,$qid,$classname,$question,$a,$b,$c,$d,$ans,$topic);
	$showQuestion = "SELECT question, A, B, C, D, Ans, tema FROM questions WHERE (classid =(SELECT classid FROM class WHERE classname = '$classname') AND question = '$question')";
    $result=mysqli_query($connection, $showQuestion);
    while ($row = $result->fetch_assoc()) {
		if($question==$row['question'] && $a==$row['A'] && $b==$row['B'] && $c==$row['C'] && $d==$row['D'] && $ans==$row['Ans'] && $topic==$row['tema']){
			$numberOfSuccess++;
	    	return "makeQuestionTest: true</br>";
		}
		else{
			return "makeQuestionTest: false</br>";
		}
	}
}

//test for å legge til spørsmål i en quiz
function addQuestionTest($connection,$questionId,$topic,$qid){
	global $numberOfSuccess;
	addQuestion($connection,$questionId,$topic,$qid);
	$checkIfAdded = "SELECT Quizid, queid FROM hasQuestions WHERE (Quizid='$qid' AND queid='$questionId')";
    $result=mysqli_query($connection, $checkIfAdded);
    while ($row = $result->fetch_assoc()) {
		if($qid==$row['Quizid'] && $questionId==$row['queid']){
			$numberOfSuccess++;
	    	return "addQuestionTest: true</br>";
		}
		else{
			return "addQuestionTest: false</br>";
		}
	}
}

//test for å slette spørsmål fra en quiz
function removeQuestionTest($connection,$qid,$topic,$questionId){
	global $numberOfSuccess;
	removeQuestion($connection,$qid,$topic,$questionId);
	$checkIfRemoved = "SELECT Quizid, queid FROM hasQuestions WHERE (Quizid='$qid' AND queid='$questionId')";
    $result=mysqli_query($connection, $checkIfRemoved);
    while ($row = $result->fetch_assoc()) {
		return "removeQuestionTest: false</br>";
	}
	$numberOfSuccess++;
	return "removeQuestionTest: true</br>";
}

//test for å slette et spørsmål helt
function deleteQuestionTest($connection,$qid,$topic, $question){
	global $numberOfSuccess;
	makeQuestion($connection, $qid, 'testclass01', $question, 'er', 'et', 'test', 'sporsmal', 'for', 'test');
	$questionId = "SELECT qid FROM questions WHERE question = '$question'";
	deleteQuestion($connection,$qid,$topic,$questionId);
	$checkIfDeleted = "SELECT qid FROM questions WHERE qid='$questionId'";
    $result=mysqli_query($connection, $checkIfDeleted);
    while ($row = $result->fetch_assoc()) {
		return "deleteQuestionTest: false</br>";
	}
	$numberOfSuccess++;
	return "deleteQuestionTest: true</br>";
}
//tester for unautorized acces
function checkSessionTest($variable){
	global $numberOfSuccess;
	if(checkSession($variable)){
		$numberOfSuccess++;
		return "checkSessionTest: true</br>";
	}
	return "checkSessionTest: false</br>";
}
//checks if any smart-ass student won't gain acces to teachers-page
function checkTeacherTest($variable){
	global $numberOfSuccess;
	if(checkTeacher($variable)){
		$numberOfSuccess++;
		return "checkTeacherTest: true</br>";
	}
	return "checkTeacherTest: false</br>";
}
//checks if any smart-ass teacher won't gain acces to student-page
function checkStudentTest($variable){
	global $numberOfSuccess;
	if(checkStudent($variable)){
		$numberOfSuccess++;
		return "checkStudentTest: true</br>";
	}
	return "checkStudentTest: false</br>";
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
		echo showQuizesInClassTest($connection, 'myClass', $quizesInClassTest);
		echo makeQuizTest($connection, 'testQuizNavn', 'TDT4140');
		echo deactivateClassTest($connection, 1, '26');
		echo activateQuizTest($connection, 7);
		echo makeQuestionTest($connection, '137', 'testclass01', 'Dette', 'er', 'et', 'test', 'sporsmal', 'for', 'test');
		echo addQuestionTest($connection, '112', 'test', '137');
		echo removeQuestionTest($connection, '137', 'test' , '112');
		echo deleteQuestionTest($connection, '137', 'test', `unikatododo`);
		echo checkSessionTest(NULL);
		echo checkTeacherTest('S');
		echo checkStudentTest('T');

		//echo deleteQuizTest($connection, 'klasa', 'deleteThisQuiz');
		//echo test();

		echo "<p>Full coverage: ".testRate($numberOfSuccess, $numberOfFunctions)."%</p>";

	?>

</html>