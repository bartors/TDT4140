<?php

//sjekker for error

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start ();
require 'connect.php';
// setter lokale variabler utifraa session's variabler
$username = $_SESSION ['username'];
$password = $_SESSION ['password'];
$role = $_SESSION ['role'];
$userid = $_SESSION ['userid'];
// setter opp query for å hente info om brukeren
$query = "SELECT * FROM `users` WHERE username='$username' and password='$password'";
function error($string) {
	if ($string == "Column 'classid' cannot be null") {
		print 'Denne klassen er ikke i vår database, har du stavet riktig?';
	} else {
		print 'Noe gikk feil. Være så snill, kontakt våre utviklere.';
	}
	echo "<p><a href='mainAsStudent.php'>Tilbake til hovedsiden</a>";
	exit ();
}
if (isset ( $_POST ['classname'] )) {
	$classname = $_POST ['classname'];
	$query = "INSERT INTO attends(userid,classid) values('$userid',(SELECT classid FROM class WHERE classname='$classname'))";
	// utører sqloperasjonen eller skriver ut en feilmelding
	$result = mysqli_query ( $connection, $query ) or error ( mysqli_error ( $connection ) );
	unset ( $classname );
	unset ( $_POST ['classname'] );
	header ( 'Location:mainAsStudent.php' );
}
//Printer ut klasser du er i
$showClasses = "SELECT classname, class.classid from class join attends on class.classid=attends.classid where attends.userid='$userid'";
$classes = mysqli_query ( $connection, $showClasses ) or die ( mysqli_error ( $connection ) );
$count = mysqli_num_rows ( $classes );

//sletter klassen
if(isset($_POST['delete'])){
	$classid=$_POST['delete'];
	$deleteAttends="delete from attends where classid='$classid' and userid='$userid'";
	$result = mysqli_query ( $connection, $deleteAttends ) or die ( mysqli_error ( $connection ) );
	mysqli_close();
	header('Location:mainAsStudent.php');
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
<link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

<!-- Theme CSS -->
<link href="css/freelancer.css" rel="stylesheet">

<!-- Custom Fonts -->
<link href="vendor/font-awesome/css/font-awesome.min.css"
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
					<a class="navbar-brand" href="index.php"><img
						src="img/classmateCleanLogo.svg" width="100%"
						style="vertical-align: top;"></a>
					</li>
			
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse"
				id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav navbar-right">
					<li class="hidden"><a href="#page-top"></a></li>
					<li class="#page-scroll"><a href="#"><?php echo"Logged in as: ".$username;?></a>
					
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
					<h2>ClassMate - Student</h2>
				</div>
			</div>
			</br>
			<div class="row">
				<div class="col-md-4">
					<div class="panel panel-default" style="width: 100%;">
						<div class="panel-heading">My Courses</div>
						
							<div class="panel-body" style="line-height: 22px;">
								<?php 
						if ($count > 0) {
						while ( $row = mysqli_fetch_array ( $classes ) ) {
							echo  "<form class='form-signin' method='POST'><a href='coursePageStudent.php?id=".$row['classname']."'>".$row ['classname'] ."</a><button name='delete' value=".$row['classid']." class='btn btn-default btn-xs' type='submit' style='float: right;'><span class='glyphicon glyphicon-trash' aria-hidden='true'></span></button></br></form>";
						}
						} else {
								echo "Du har ingen klasser.</br>";
						}?></br>
						<form class='form-signin' method='POST'>
								<input class="addCourseInput" type="text" name="classname"
									placeholder="Course name">&nbsp
								<button class="btn btn-default" type="submit"
									style="height: 30px;">Add course</button></form>
							</div>
						
					</div>
				</div>
				<div class="col-md-4">
					<div class="panel panel-default" style="width: 100%;">
						<div class="panel-heading">My Quizzes</div>
						<div class="panel-body">
							<a href="quizPage.php">Spaced-repetion quiz for you</a></br>
						</div>
					</div>
				</div>


				<div class="col-md-4">
					<div class="panel panel-default" style="width: 100%;">
						<div class="panel-heading">Statistics</div>
						<div class="panel-body">
							
							<?php //henter quizer i fag og statistikk
							$getQuizzes = mysqli_query($connection, "SELECT DISTINCT qid FROM hasAnsweredQuestion WHERE userid = '".$userid."'");
							while ($row = $getQuizzes->fetch_assoc()){
								$getQuizName = mysqli_query($connection, "SELECT qid, name FROM quiz WHERE qid = '".$row['qid']."'");
								while ($quizName = $getQuizName->fetch_assoc()){
									
	                                $questionIDs = mysqli_query($connection, "SELECT * FROM quiz JOIN hasQuestions ON quiz.qid = hasQuestions.Quizid WHERE qid = '".$quizName['qid']."'");
	                                $totalCorrect = 0;
	                                $totalQuestions = 0;
	                                while ($scoreCalculator = $questionIDs->fetch_assoc()){
	                                    $currQuestion = mysqli_query($connection, "SELECT question FROM questions WHERE qid=".$scoreCalculator['queid'])->fetch_assoc();
	                                    $currScore = mysqli_query($connection, "SELECT answer FROM hasAnsweredQuestion WHERE qid='".$scoreCalculator['qid']."' AND questid='".$scoreCalculator['queid']."' AND userid = '".$userid."'");
	                                    while ($score = $currScore->fetch_assoc()) {
	                                        if ($score['answer']==1) {
	                                            $totalCorrect++;
	                                            $totalQuestions++;
	                                        }
	                                        else {
	                                            $totalQuestions++;
	                                        }
	                                    }
	                                }


									echo "<a href='statisticsStudent.php?quizId=".$quizName['qid']."'>".$quizName['name']."</a><div style='float: right';>".$totalCorrect."/".$totalQuestions."</div></br>";
								}
							}	





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

		<!-- Portfolio Modals -->
		<div class="portfolio-modal modal fade" id="portfolioModal1"
			tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-content">
				<div class="close-modal" data-dismiss="modal">
					<div class="lr">
						<div class="rl"></div>
					</div>
				</div>
				<div class="container">
					<div class="row">
						<div class="col-lg-8 col-lg-offset-2">
							<div class="modal-body">
								<h2>Project Title</h2>
								<hr class="star-primary">
								<img src="img/portfolio/cabin.png"
									class="img-responsive img-centered" alt="">
								<p>
									Use this area of the page to describe your project. The icon
									above is part of a free icon set by <a
										href="https://sellfy.com/p/8Q9P/jV3VZ/">Flat Icons</a>. On
									their website, you can download their free set with 16 icons,
									or you can purchase the entire set with 146 icons for only $12!
								</p>
								<ul class="list-inline item-details">
									<li>Client: <strong><a href="http://startbootstrap.com">Start
												Bootstrap</a> </strong>
									</li>
									<li>Date: <strong><a href="http://startbootstrap.com">April
												2014</a> </strong>
									</li>
									<li>Service: <strong><a href="http://startbootstrap.com">Web
												Development</a> </strong>
									</li>
								</ul>
								<button type="button" class="btn btn-default"
									data-dismiss="modal">
									<i class="fa fa-times"></i> Close
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="portfolio-modal modal fade" id="portfolioModal2"
			tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-content">
				<div class="close-modal" data-dismiss="modal">
					<div class="lr">
						<div class="rl"></div>
					</div>
				</div>
				<div class="container">
					<div class="row">
						<div class="col-lg-8 col-lg-offset-2">
							<div class="modal-body">
								<h2>Project Title</h2>
								<hr class="star-primary">
								<img src="img/portfolio/cake.png"
									class="img-responsive img-centered" alt="">
								<p>
									Use this area of the page to describe your project. The icon
									above is part of a free icon set by <a
										href="https://sellfy.com/p/8Q9P/jV3VZ/">Flat Icons</a>. On
									their website, you can download their free set with 16 icons,
									or you can purchase the entire set with 146 icons for only $12!
								</p>
								<ul class="list-inline item-details">
									<li>Client: <strong><a href="http://startbootstrap.com">Start
												Bootstrap</a> </strong>
									</li>
									<li>Date: <strong><a href="http://startbootstrap.com">April
												2014</a> </strong>
									</li>
									<li>Service: <strong><a href="http://startbootstrap.com">Web
												Development</a> </strong>
									</li>
								</ul>
								<button type="button" class="btn btn-default"
									data-dismiss="modal">
									<i class="fa fa-times"></i> Close
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="portfolio-modal modal fade" id="portfolioModal3"
			tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-content">
				<div class="close-modal" data-dismiss="modal">
					<div class="lr">
						<div class="rl"></div>
					</div>
				</div>
				<div class="container">
					<div class="row">
						<div class="col-lg-8 col-lg-offset-2">
							<div class="modal-body">
								<h2>Project Title</h2>
								<hr class="star-primary">
								<img src="img/portfolio/circus.png"
									class="img-responsive img-centered" alt="">
								<p>
									Use this area of the page to describe your project. The icon
									above is part of a free icon set by <a
										href="https://sellfy.com/p/8Q9P/jV3VZ/">Flat Icons</a>. On
									their website, you can download their free set with 16 icons,
									or you can purchase the entire set with 146 icons for only $12!
								</p>
								<ul class="list-inline item-details">
									<li>Client: <strong><a href="http://startbootstrap.com">Start
												Bootstrap</a> </strong>
									</li>
									<li>Date: <strong><a href="http://startbootstrap.com">April
												2014</a> </strong>
									</li>
									<li>Service: <strong><a href="http://startbootstrap.com">Web
												Development</a> </strong>
									</li>
								</ul>
								<button type="button" class="btn btn-default"
									data-dismiss="modal">
									<i class="fa fa-times"></i> Close
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="portfolio-modal modal fade" id="portfolioModal4"
			tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-content">
				<div class="close-modal" data-dismiss="modal">
					<div class="lr">
						<div class="rl"></div>
					</div>
				</div>
				<div class="container">
					<div class="row">
						<div class="col-lg-8 col-lg-offset-2">
							<div class="modal-body">
								<h2>Project Title</h2>
								<hr class="star-primary">
								<img src="img/portfolio/game.png"
									class="img-responsive img-centered" alt="">
								<p>
									Use this area of the page to describe your project. The icon
									above is part of a free icon set by <a
										href="https://sellfy.com/p/8Q9P/jV3VZ/">Flat Icons</a>. On
									their website, you can download their free set with 16 icons,
									or you can purchase the entire set with 146 icons for only $12!
								</p>
								<ul class="list-inline item-details">
									<li>Client: <strong><a href="http://startbootstrap.com">Start
												Bootstrap</a> </strong>
									</li>
									<li>Date: <strong><a href="http://startbootstrap.com">April
												2014</a> </strong>
									</li>
									<li>Service: <strong><a href="http://startbootstrap.com">Web
												Development</a> </strong>
									</li>
								</ul>
								<button type="button" class="btn btn-default"
									data-dismiss="modal">
									<i class="fa fa-times"></i> Close
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="portfolio-modal modal fade" id="portfolioModal5"
			tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-content">
				<div class="close-modal" data-dismiss="modal">
					<div class="lr">
						<div class="rl"></div>
					</div>
				</div>
				<div class="container">
					<div class="row">
						<div class="col-lg-8 col-lg-offset-2">
							<div class="modal-body">
								<h2>Project Title</h2>
								<hr class="star-primary">
								<img src="img/portfolio/safe.png"
									class="img-responsive img-centered" alt="">
								<p>
									Use this area of the page to describe your project. The icon
									above is part of a free icon set by <a
										href="https://sellfy.com/p/8Q9P/jV3VZ/">Flat Icons</a>. On
									their website, you can download their free set with 16 icons,
									or you can purchase the entire set with 146 icons for only $12!
								</p>
								<ul class="list-inline item-details">
									<li>Client: <strong><a href="http://startbootstrap.com">Start
												Bootstrap</a> </strong>
									</li>
									<li>Date: <strong><a href="http://startbootstrap.com">April
												2014</a> </strong>
									</li>
									<li>Service: <strong><a href="http://startbootstrap.com">Web
												Development</a> </strong>
									</li>
								</ul>
								<button type="button" class="btn btn-default"
									data-dismiss="modal">
									<i class="fa fa-times"></i> Close
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="portfolio-modal modal fade" id="portfolioModal6"
			tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-content">
				<div class="close-modal" data-dismiss="modal">
					<div class="lr">
						<div class="rl"></div>
					</div>
				</div>
				<div class="container">
					<div class="row">
						<div class="col-lg-8 col-lg-offset-2">
							<div class="modal-body">
								<h2>Project Title</h2>
								<hr class="star-primary">
								<img src="img/portfolio/submarine.png"
									class="img-responsive img-centered" alt="">
								<p>
									Use this area of the page to describe your project. The icon
									above is part of a free icon set by <a
										href="https://sellfy.com/p/8Q9P/jV3VZ/">Flat Icons</a>. On
									their website, you can download their free set with 16 icons,
									or you can purchase the entire set with 146 icons for only $12!
								</p>
								<ul class="list-inline item-details">
									<li>Client: <strong><a href="http://startbootstrap.com">Start
												Bootstrap</a> </strong>
									</li>
									<li>Date: <strong><a href="http://startbootstrap.com">April
												2014</a> </strong>
									</li>
									<li>Service: <strong><a href="http://startbootstrap.com">Web
												Development</a> </strong>
									</li>
								</ul>
								<button type="button" class="btn btn-default"
									data-dismiss="modal">
									<i class="fa fa-times"></i> Close
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- jQuery -->
		<script src="vendor/jquery/jquery.min.js"></script>

		<!-- Bootstrap Core JavaScript -->
		<script src="vendor/bootstrap/js/bootstrap.min.js"></script>

		<!-- Plugin JavaScript -->
		<script
			src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>

		<!-- Contact Form JavaScript -->
		<script src="js/jqBootstrapValidation.js"></script>
		<script src="js/contact_me.js"></script>

		<!-- Theme JavaScript -->
		<script src="js/freelancer.min.js"></script>

</body>

</html>
