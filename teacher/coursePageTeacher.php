<?php
session_start ();
require '../connect.php';
// setter lokale variabler utifraa session's variabler
$username = $_SESSION ['username'];
$password = $_SESSION ['password'];
$role = $_SESSION ['role'];
$userid = $_SESSION ['userid'];
// setter opp query for å hente info om brukeren
$classname = $_GET ['id'];
$_SESSION ['classname'] = $classname;
// henter quizer i fag og statistikk
function displayStatistics($connection,$classname){
	$sql = mysqli_query ( $connection, "SELECT qid, name FROM quiz WHERE classid=(SELECT classid from class where classname='$classname')" );
	mysqli_close();
	while ( $row = $sql->fetch_assoc () ) {
		$qid = mysqli_query ( $connection, "SELECT qid FROM quiz WHERE name='" . $row ['name'] . "'" )->fetch_assoc ();
		mysqli_close();
		$questionIDs = mysqli_query ( $connection, "SELECT * FROM quiz JOIN hasQuestions ON quiz.qid = hasQuestions.Quizid WHERE qid = '" . $qid ['qid'] . "'" );
		mysqli_close();
		$totalCorrect = 0;
		$totalQuestions = 0;
		while ( $scoreCalculator = $questionIDs->fetch_assoc () ) {
			$currQuestion = mysqli_query ( $connection, "SELECT question FROM questions WHERE qid=" . $scoreCalculator ['queid'] )->fetch_assoc ();
			mysqli_close();
			$currScore = mysqli_query ( $connection, "SELECT answer FROM hasAnsweredQuestion WHERE qid=" . $scoreCalculator ['qid'] . " AND questid=" . $scoreCalculator ['queid'] );
			mysqli_close();
			while ( $score = $currScore->fetch_assoc () ) {
				if ($score ['answer'] == 1) {
					$totalCorrect ++;
					$totalQuestions ++;
				} else {
					$totalQuestions ++;
				}
			}
		}
		$totalScorePercent = ($totalCorrect / $totalQuestions) * 100;
		$oneDecimalScore = number_format ( $totalScorePercent, 1 );
			
		if (($totalScorePercent <= 100) && (0 <= $totalScorePercent)) {
			echo "<a href='statisticsTeacher.php?quizId=" . $row ['qid'] . "'>" . $row ['name'] . "</a><div style='float: right';>" . $oneDecimalScore . "%</div></br>";
		} else {
			echo "<a href='statisticsTeacher.php?quizId=" . $row ['qid'] . "'>" . $row ['name'] . "</a><div style='float: right';>Not answered yet</div></br>";
		}
	}	
}
//skriver ut klassevanvet
function displayClassname(){
	Echo'<h2>Teacher - '. $_SESSION['classname'].'</h2>';
}
//finner quizer i klassen
function showQuizes($connection, $classname) {
	$showQuizes = "SELECT qid,name,active from quiz WHERE classid=(SELECT classid from class where classname='$classname')";
	$quizes = mysqli_query ( $connection, $showQuizes ) or die ( mysqli_error ( $connection ) );
	mysqli_close ();
	return $quizes;
}
$quizes = showQuizes ( $connection, $classname );
$count = mysqli_num_rows ( $quizes );
//skriver ut quizer
function displayQuizes($count,$quizes){
	if ($count > 0) {
		while ( $row = mysqli_fetch_array ( $quizes ) ) {
			echo "<form class='form-signin' method='POST'>
                                <a href='../student/quizPage.php?quiz=" . $row ['name'] . "'>" . $row ['name'] . "</a>
                                <button name='delete' class='btn btn-default btn-xs' type='submit' value=" . $row ['qid'] . " style='float: right;'>
                                    <span class='glyphicon glyphicon-trash' aria-hidden='true'></span></button>
                                <a href='createQuiz.php?id=" . $row ['qid'] . "' class='btn btn-default btn-xs' aria-label='Left Align' style='float: right;'>
                                    <span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a>";
			if ($row ['active'] == 1) {
				echo "<input type='hidden'  name='activQid' value=" . $row ['qid'] . "><button class='btn btn-default btn-xs' name='activStat' type='submit' value=" . $row ['active'] . " style='float: right;'>
                                    <li  style='color: green;'>Live</li>
                                </button></form>";
			} else {
				echo "<input type='hidden'  name='activQid' value=" . $row ['qid'] . "><button class='btn btn-default btn-xs'  name='activStat' type='submit' value=" . $row ['active'] . " style='float: right;'>
                                    <li  style='color: red;'>Live</li>
                                </button></form>";
			}
		}
	} else {
		echo "Du har ingen quizer.</br>";
	}
}
// lager quizen
function makeQuiz($connection, $quizName, $classname) {
	$createQuiz = "INSERT INTO quiz (classid,name,active) values ((SELECT classid from class where classname='$classname'),'$quizName',0)";
	$result = mysqli_query ( $connection, $createQuiz ) or die ( mysqli_error ( $connection ) );
	mysqli_close ();
	header ( 'Location:coursePageTeacher.php?id=' . $_SESSION ['classname'] );
}
if (isset ( $_POST ['quizName'] )) {
	makeQuiz ( $connection, $_POST ['quizName'], $classname );
}
// sletter quizen
function deleteQuiz($connection, $qid) {
	$deleteHasQuestions = "delete from hasQuestions where quizid='$qid'";
	$result = mysqli_query ( $connection, $deleteHasQuestions ) or die ( mysqli_error ( $connection ) );
	mysqli_close ();
	$deleteQuiz = "delete from quiz where qid='$qid'";
	$result = mysqli_query ( $connection, $deleteQuiz ) or die ( mysqli_error ( $connection ) );
	mysqli_close ();
	header ( 'Location:coursePageTeacher.php?id=' . $_SESSION ['classname'] );
}
if (isset ( $_POST ['delete'] )) {
	deleteQuiz ( $connection, $_POST ['delete'] );
}
// aktiverer quizen
function activateQuiz($connection, $qid, $status) {
	$qiz = $_POST ['activQid'];
	$status = $_POST ['activStat'];
	if ($status == 1) {
		$activateQuiz = "update quiz set active=0 where qid='$qiz' ";
	} else {
		date_default_timezone_set ( 'Europe/Oslo' );
		$date = date ( 'Y-m-d H-i-s' );
		$activateQuiz = "update quiz set active=1, activDate='$date' where qid='$qiz' ";
	}
	$result = mysqli_query ( $connection, $activateQuiz ) or die ( mysqli_error ( $connection ) );
	mysqli_close ();
	header ( 'Location:coursePageTeacher.php?id=' . $_SESSION ['classname'] );
}
if (isset ( $_POST ['activStat'] )) {
	activateQuiz ( $connection, $_POST ['activQid'], $_POST ['activStat'] );
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">

<title>ClassMate</title>

<!-- Bootstrap Core CSS -->
<link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

<!-- Theme CSS -->
<link href="../css/freelancer.css" rel="stylesheet">

<!-- Custom Fonts -->
<link href="../vendor/font-awesome/css/font-awesome.min.css"
	rel="stylesheet" type="text/css">
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700"
	rel="stylesheet" type="text/css">
<link
	href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic"
	rel="stylesheet" type="text/css">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body id="page-top" class="index">

	<!-- Navigation -->
	<nav id="mainNav" class="navbar navbar-fixed-top navbar-custom">
		<div class="container">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header page-scroll">
				<button type="button" class="navbar-toggle" data-toggle="collapse"
					data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle navigation</span> Menu <i
						class="fa fa-bars"></i>
				</button>
				<ul class="nav navbar-nav">
					<a class="navbar-brand" href="mainAsTeacher.php"><img
						src="../img/classmateCleanLogo.svg" width="100%"
						style="vertical-align: top;"></a>
					</li>
			
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse"
				id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav navbar-right">
					<li class="hidden"><a href="#page-top"></a></li>
					<li class="#page-scroll"><a href="#"><?php echo"Logged in as: ".$username?></a>
					
					<li>
                        <?php echo"<a href='logout.php'>Log out</a>"?>
                    </li>
				</ul>
			</div>
			<!-- /.navbar-collapse -->
		</div>
		<!-- /.container-fluid -->
	</nav>

	<!-- Header -->
	<section id="profil">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 text-center">
                   <?php displayClassname();?>
                </div>
			</div>
			</br>
			<div class="row">
				<div class="col-md-4">
					<div class="panel panel-default" style="width: 100%;">
						<div class="panel-heading">Create quizzes</div>
						<form class="form-signin" method="POST">
							<div class="panel-body">
								<input class="addCourseInput" type="text" name="quizName"
									required placeholder="Name of quiz">&nbsp
								<button class="btn btn-default btn" type="submit">Create quiz</button>
							</div>
						</form>
					</div>
				</div>
				<div class="col-md-4">
					<div class="panel panel-default" style="width: 100%;">
						<div class="panel-heading">Course quizzes</div>
						<div class="panel-body" style="line-height: 22px;">
                    <?php displayQuizes($count, $quizes);?>
                    </div>
					</div>
				</div>


				<div class="col-md-4">
					<div class="panel panel-default" style="width: 100%;">
						<div class="panel-heading">Statistics</div>
						<div class="panel-body" style="line-height: 22px;">

                    <?php 
                    displayStatistics($connection, $classname);
																				?>
                </div>
					</div>
				</div>
			</div>
		</div>



		<!-- Footer kan brukes senere -->
		<footer class="text-center">
			<!--
        <div class="footer-above">
            <div class="container">
                <div class="row">
                    <div class="footer-col col-md-4">
                       <p>Om Classmate</p>
                    </div>
                    <div class="footer-col col-md-4">
                        <p>Noe annet</p>
                    </div>
                    <div class="footer-col col-md-4">
                    </div>
                </div>
            </div>
        </div>
        -->
			<div class="footer-below">
				<div class="container">
					<div class="row">
						<div class="col-lg-12">Copyright &copy; Your Website 2016</div>
					</div>
				</div>
			</div>
		</footer>

		<!-- Scroll to Top Button (Only visible on small and extra-small screen sizes) -->
		<div
			class="scroll-top page-scroll hidden-sm hidden-xs hidden-lg hidden-md">
			<a class="btn btn-primary" href="#page-top"> <i
				class="fa fa-chevron-up"></i>
			</a>
		</div>

	

		<!-- jQuery -->
		<script src="../vendor/jquery/jquery.min.js"></script>

		<!-- Bootstrap Core JavaScript -->
		<script src="../vendor/bootstrap/js/bootstrap.min.js"></script>

		<!-- Plugin JavaScript -->
		<script
			src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>

		<!-- Contact Form JavaScript -->
		<script src="../js/jqBootstrapValidation.js"></script>
		<script src="../js/contact_me.js"></script>

		<!-- Theme JavaScript -->
		<script src="../js/freelancer.min.js"></script>

</body>

</html>