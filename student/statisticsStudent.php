<?php

session_start ();
require '../connect.php';
require '../lib/functions.php';
require '../lib/security.php';
checkSession($_SESSION['username']);
checkStudent($_SESSION['role']);
// setter lokale variabler utifraa session's variabler
$username = $_SESSION ['username'];
$password = $_SESSION ['password'];
$role = $_SESSION ['role'];
$userid = $_SESSION ['userid'];
$quizId = $_GET['quizId'];
//henter quiznavn
/*function getQuizName($connection,$quizzId){
	$query="SELECT name FROM quiz WHERE qid = '$quizId'";
	$result=mysqli_query($connection, $query);
	$getQuizName = mysqli_fetch_assoc($result);
	return $getQuizName['name'];
}*/


$quizName = showQuiz($connection,$_GET['quizId']);


/*$showClasses = "SELECT classname from class join attends on class.classid=attends.classid where attends.userid='$userid'";
$classes = mysqli_query ( $connection, $showClasses ) or die ( mysqli_error ( $connection ) );
$count = mysqli_num_rows ( $classes );*/
function studentStatistics($connection,$quizName,$userid){
	$qid = mysqli_query($connection, "SELECT qid FROM quiz WHERE name='".$quizName."'")->fetch_assoc();
	$questionIDs = mysqli_query($connection, "SELECT * FROM quiz JOIN hasQuestions ON quiz.qid = hasQuestions.Quizid WHERE qid = '".$qid['qid']."'");
	$i = 1;
	$totalCorrect = 0;
	$totalQuestions = 0;
	while ($row = $questionIDs->fetch_assoc()){
		$currQuestion = mysqli_query($connection, "SELECT question FROM questions WHERE qid=".$row['queid'])->fetch_assoc();
		$currScore = mysqli_query($connection, "SELECT answer FROM hasAnsweredQuestion WHERE qid='".$row['qid']."' AND questid='".$row['queid']."' AND userid = '".$userid."'");
		while ($score = $currScore->fetch_assoc()) {
			if ($score['answer']==1) {
				$totalCorrect++;
				$totalQuestions++;
				$grade = "<div style='color: green;'>";
			}
			else {
				$totalQuestions++;
				$grade = "<div style='color: red;'>";
			}
		}
	
		foreach ($currQuestion as $key => $val) {
			echo $grade.$i.". ".$val."</div>";
		}
		$i++;
	}
	echo "<strong>Total score: ".$totalCorrect."/".$totalQuestions."</strong>";
	
	
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
					<a class="navbar-brand" href="../index.php"><img
						src="../img/classmateCleanLogo.svg" width="100%"
						style="vertical-align: top;"></a>
					</li>
			
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse"
				id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav navbar-right">
					<li class="hidden"><a href="#page-top"></a></li>
					<li class="#page-scroll"><a href="../common/changePassword.php"><?php echo "Profile: ".$username;?></a>
					
					<li>
                        <?php echo"<a href='../common/logout.php'>Log out</a>"?>
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
					<?php displayQuizName($quizName);?>
				</div>
			</div>
			</br>
			            <div class="row">
                <div class="col-md-4">
                    <div class="panel panel-default" style="width:200%;">
                        <div class="panel-heading"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span>  <?php echo $quizName." - Statistics" ?></div>
                        <div class="panel-body">
                            <?php
                            	/*$qid = mysqli_query($connection, "SELECT qid FROM quiz WHERE name='".$quizName."'")->fetch_assoc();
                            	$questionIDs = mysqli_query($connection, "SELECT * FROM quiz JOIN hasQuestions ON quiz.qid = hasQuestions.Quizid WHERE qid = '".$qid['qid']."'");
                            	$i = 1;
                            	$totalCorrect = 0;
                            	$totalQuestions = 0;
                            	while ($row = $questionIDs->fetch_assoc()){
                            		$currQuestion = mysqli_query($connection, "SELECT question FROM questions WHERE qid=".$row['queid'])->fetch_assoc();
                            		$currScore = mysqli_query($connection, "SELECT answer FROM hasAnsweredQuestion WHERE qid='".$row['qid']."' AND questid='".$row['queid']."' AND userid = '".$userid."'");
                            		while ($score = $currScore->fetch_assoc()) {
                            			if ($score['answer']==1) {
                            				$totalCorrect++;
                            				$totalQuestions++;
                            				$grade = "<div style='float: right; color: green;'>Correct</div>";
                            			}
                            			else {
                            				$totalQuestions++;
                            				$grade = "<div style='float: right; color: red;'>Wrong</div>";
                            			}
                            		}

                            		foreach ($currQuestion as $key => $val) {
   										echo $i.". ".$val.$grade."</br>";		
									}
									$i++;
                            	}
                            	echo "<strong>Total score: ".$totalCorrect."/".$totalQuestions."</strong>";
*/
studentStatistics($connection, $quizName,$_SESSION['userid']);
                            ?>
                            </br>
                            <button class='btn btn-default' onclick='goBack()'><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>  Back</button>

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
						<div class="col-lg-12">Copyright &copy; ClassMate 2017</div>
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

		<script>
    	function goBack() {
        	window.history.back();
    	}
    	</script>

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
