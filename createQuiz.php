<?php

session_start();
require 'connect.php';
//setter lokale variabler utifraa session's variabler
$username=$_SESSION['username'];
$password=$_SESSION['password'];
$role=$_SESSION['role'];
$userid=$_SESSION['userid'];
$classname=$_SESSION['classname'];
//skriver ut navnet op quizen
function displayQuizName($quizName){
	echo "<h2>".$quizName."</h2>";
}
//finner spørsmål
function showQuestions($connection,$classname){
	$query = "SELECT question FROM questions WHERE classid=(SELECT classid from class where classname='$classname')";
	$questions = mysqli_query ( $connection, $query ) or die ( mysqli_error ( $connection ) );
	$count = mysqli_num_rows ( $questions );
	mysqli_close();
	return $count;
}
$count=showQuestions($connection, $_SESSION['classname']);
$qid=$_GET['id'];
$topic=$_GET['topic'];
//setter opp query for å hente info om quizen
function getQuizInfo($connection,$qid){
	$showQuizName="select name from quiz where qid='$qid'";
	$result = mysqli_query ( $connection, $showQuizName ) or die ( mysqli_error ( $connection ) );
	$count= mysqli_num_rows ( $result );
	if($count==1){
		$row = mysqli_fetch_array ( $result );
		$quizName=$row['name'];
		return $quizName;
	}
	
}
$currQuiz = $_GET['quiz'];
$quizName=getQuizInfo($connection, $_GET['id']);

//setter opp form for å legge til quiz fra høyre panel inn i quizen
function addQuestion($connection,$questionId,$topic,$qid){
	$addToHasQuestions="INSERT INTO hasQuestions (Quizid, queid) VALUES ($qid, $questionId)";
	$result = mysqli_query ( $connection, $addToHasQuestions ) or die ( mysqli_error ( $connection ) );
	mysqli_close();
	header('Location:createQuiz.php?id='.$qid.'&topic='.$topic);
}

if(isset($_POST['addQuestionToQuiz'])){
	//$quizId=$_GET['id'];
	//$questionId=$_POST['addQuestionToQuiz'];
	addQuestion($connection, $_POST['addQuestionToQuiz'], $_GET['topic'], $_GET['id']);
    
}

//setter opp form for å slette spørsmål fra question-table
function deleteQuestion($connection,$qid,$topic,$questionId){
	$deleteQuestion="delete from questions where qid='$questionId'";
	$deleteFromHasquestions="delete from hasQuestions where queid='$questionId'";
	$result = mysqli_query ( $connection, $deleteFromHasquestions ) or die ( mysqli_error ( $connection ) );
	$result = mysqli_query ( $connection, $deleteQuestion ) or die ( mysqli_error ( $connection ) );
	mysqli_close();
	header('Location:createQuiz.php?id='.$qid.'&topic='.$topic);
}
if(isset($_POST['delete'])){
    //$questionId=$_POST['delete'];
    deleteQuestion($connection, $_GET['id'], $_GET['topic'], $_POST['delete']);
    
}

//setter opp form for å slette spørsmål fra hasquestion-table
function removeQuestion($connection,$qid,$topic,$questionId){
	$deleteFromHasquestions="delete from hasQuestions where queid='$questionId'";
	$result = mysqli_query ( $connection, $deleteFromHasquestions ) or die ( mysqli_error ( $connection ) );
	mysqli_close();
	header('Location:createQuiz.php?id='.$qid.'&topic='.$topic);
}
if(isset($_POST['remove'])){
    //$questionId=$_POST['remove'];
    removeQuestion($connection, $_GET['id'], $_GET['topic'], $_POST['remove']);
    
    
}

//skriver ut questions
function displayQuestions($connection,$topic,$classname,$qid){
	if ($topic == '') {
		 
		$sql = mysqli_query($connection, "SELECT qid, question, tema FROM questions WHERE classid=(SELECT classid from class where classname='$classname')");
		while ($row = $sql->fetch_assoc()){
	
			$alreadyInQuiz = mysqli_query($connection, "SELECT Quizid FROM hasQuestions WHERE queid='".$row['qid']."' AND quizId='".$qid."'");
	
			if ($alreadyInQuiz->num_rows == 0){
				echo  "<form class='form-signin' method='POST'>
		                                <button name='addQuestionToQuiz' class='btn btn-default btn-xs' type='submit' value=".$row['qid'].">
		                                    ".$row['question']."</button>
		                                    <button name='delete' class='btn btn-default btn-xs' type='submit' value=".$row['qid']." style='float: right;'>
                                    		<span class='glyphicon glyphicon-trash' aria-hidden='true'></span></button></form>";
	
			}
			else {
				/* Bruk denne for disabled buttons
				 echo  "<form class='form-signin' method='POST'>
				 <button class='btn btn-default btn-xs disabled' value=".$row['qid'].">
				 ".$row['question']."</button></form>";
				 */
			}
	
		}
	
	}
	
	else {
	
		$sql = mysqli_query($connection, "SELECT qid, question, tema FROM questions WHERE classid=(SELECT classid from class where classname='$classname') AND tema='$topic'");
		while ($row = $sql->fetch_assoc()){
	
			$alreadyInQuiz = mysqli_query($connection, "SELECT Quizid FROM hasQuestions WHERE queid='".$row['qid']."' AND quizId='".$qid."'");
	
			if ($alreadyInQuiz->num_rows == 0){
				echo  "<form class='form-signin' method='POST'>
		                                <button name='addQuestionToQuiz' class='btn btn-default btn-xs' type='submit' value=".$row['qid'].">
		                                    ".$row['question']."</button>
		                                    <button name='delete' class='btn btn-default btn-xs' type='submit' value=".$row['qid']." style='float: right;'>
                                    		<span class='glyphicon glyphicon-trash' aria-hidden='true'></span></button></form>";
	
			}
			else {
				/* Bruk denne for disabled buttons
				 echo  "<form class='form-signin' method='POST'>
				 <button class='btn btn-default btn-xs disabled' value=".$row['qid'].">
				 ".$row['question']."</button></form>";
				 */
			}
		}
	}
}

//skriver ut spørsmål i quizen
function displayQuestionsInQuiz($connection,$quizName){
	$qid = mysqli_query($connection, "SELECT qid FROM quiz WHERE name='".$quizName."'")->fetch_assoc();
	$questionIDs = mysqli_query($connection, "SELECT * FROM quiz JOIN hasQuestions ON quiz.qid = hasQuestions.Quizid WHERE qid = '".$qid['qid']."'");
	$i = 1;
	while ($row = $questionIDs->fetch_assoc()){
		$currQuestion = mysqli_query($connection, "SELECT question FROM questions WHERE qid=".$row['queid'])->fetch_assoc();
		foreach ($currQuestion as $key => $val) {
			echo "<form class='form-signin' method='POST'>".$i.". ".$val."<button name='remove' class='btn btn-default btn-xs' type='submit' value=".$row['queid']." style='float: right;'>
                                    			<span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button></form>";
		}
		$i++;
	}
	
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
					<a class="navbar-brand" href="mainAsTeacher.php"><img
						src="img/classmateCleanLogo.svg" width="100%"
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
					<?php   displayQuizName($quizName);?>
					</br>
				</div>
			</div>
			</br>
			<div class="row">
				<div class="col-md-4">
					<div class="panel panel-default" style="width: 100%;">
						<div class="panel-heading"><?php   echo $quizName;?> - Added questions</div>
						<form class="form-signin" method="POST">
							<div class="panel-body" style="line-height: 22px;">
							<?php


								echo "<a href='createQuestion.php?id=".$qid."' class='btn btn-default'>Create new question</a></br>
								</br>";
								?>
		                            <!--GET QUESTIONS IN QUIZ-->
		                        	<?php displayQuestionsInQuiz($connection, $quizName);
                            		/*$qid = mysqli_query($connection, "SELECT qid FROM quiz WHERE name='".$quizName."'")->fetch_assoc();
                            		$questionIDs = mysqli_query($connection, "SELECT * FROM quiz JOIN hasQuestions ON quiz.qid = hasQuestions.Quizid WHERE qid = '".$qid['qid']."'");
                            		$i = 1;
                            		while ($row = $questionIDs->fetch_assoc()){
                            			$currQuestion = mysqli_query($connection, "SELECT question FROM questions WHERE qid=".$row['queid'])->fetch_assoc();
                            				foreach ($currQuestion as $key => $val) {
   												echo "<form class='form-signin' method='POST'>".$i.". ".$val."<button name='remove' class='btn btn-default btn-xs' type='submit' value=".$row['queid']." style='float: right;'>
                                    			<span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button></form>";
											}
										$i++;
                            			}
                            		
                            		*/?>

								</br>
								</br>
								<?php 
								echo "<a href='coursePageTeacher.php?id=".$_SESSION['classname']."' class='btn btn-default'>Create quiz</a>";
								?>
							</div>
						</form>
					</div>
				</div>
				<div class="col-md-4">
					<div class="panel panel-default" style="width: 100%;">
						<div class="panel-heading">Choose a topic to find questions</div>
						<div class="panel-body">
							<!--GET TOPICS-->
		                    <?php
		        			echo "<a href='createQuiz.php?id=".$_GET['id']."&topic=''>All</br></a>";
		                    $sql = mysqli_query($connection, "SELECT DISTINCT tema FROM questions WHERE classid=(SELECT classid from class where classname='$classname')");
		                    while ($row = $sql->fetch_assoc()){
		                    	echo "<a href='createQuiz.php?id=".$_GET['id']."&topic=".$row['tema']."''>".$row['tema'] ."</a></br>";
		                    }?>
						</div>
					</div>
				</div>


				<div class="col-md-4">
					<div class="panel panel-default" style="width: 100%;">
						<div class="panel-heading"><?php 
						if ($topic == '') {
							echo "Topic: All</div>";
						}
						else {
							echo "Topic: ".$topic."</div>";
						}

						?>
						<div class="panel-body" style="line-height: 22px;">
							<!--GET QUESTIONS FOR PANEL FAR RIGHT-->
							Press a question to add it to your quiz</br>
		                    <?php displayQuestions($connection, $_GET['topic'], $_SESSION['classname'], $_GET['id']);

		                /*    if ($topic == '') {
			                    
			                    $sql = mysqli_query($connection, "SELECT qid, question, tema FROM questions WHERE classid=(SELECT classid from class where classname='$classname')");
			                    while ($row = $sql->fetch_assoc()){
			                    	
			                    	$alreadyInQuiz = mysqli_query($connection, "SELECT Quizid FROM hasQuestions WHERE queid='".$row['qid']."' AND quizId='".$_GET['id']."'");
			                    	
			                    	if ($alreadyInQuiz->num_rows == 0){
			                    		echo  "<form class='form-signin' method='POST'> 
		                                <button name='addQuestionToQuiz' class='btn btn-default btn-xs' type='submit' value=".$row['qid'].">
		                                    ".$row['question']."</button>
		                                    <button name='delete' class='btn btn-default btn-xs' type='submit' value=".$row['qid']." style='float: right;'>
                                    		<span class='glyphicon glyphicon-trash' aria-hidden='true'></span></button></form>";

			                    	}
			                    	else {
			                    		 Bruk denne for disabled buttons
				                    	echo  "<form class='form-signin' method='POST'> 
		                                <button class='btn btn-default btn-xs disabled' value=".$row['qid'].">
		                                    ".$row['question']."</button></form>";
		                                
		                            }		                    	

		                    	}

		                    }

		                    else {

			                    $sql = mysqli_query($connection, "SELECT qid, question, tema FROM questions WHERE classid=(SELECT classid from class where classname='$classname') AND tema='$topic'");
			                    while ($row = $sql->fetch_assoc()){
			                    	
			                    	$alreadyInQuiz = mysqli_query($connection, "SELECT Quizid FROM hasQuestions WHERE queid='".$row['qid']."' AND quizId='".$_GET['id']."'");
			                    	
			                    	if ($alreadyInQuiz->num_rows == 0){
			                    		echo  "<form class='form-signin' method='POST'> 
		                                <button name='addQuestionToQuiz' class='btn btn-default btn-xs' type='submit' value=".$row['qid'].">
		                                    ".$row['question']."</button>
		                                    <button name='delete' class='btn btn-default btn-xs' type='submit' value=".$row['qid']." style='float: right;'>
                                    		<span class='glyphicon glyphicon-trash' aria-hidden='true'></span></button></form>";

			                    	}
			                    	else {
			                    		 Bruk denne for disabled buttons
				                    	echo  "<form class='form-signin' method='POST'> 
		                                <button class='btn btn-default btn-xs disabled' value=".$row['qid'].">
		                                    ".$row['question']."</button></form>";
		                                
		                            }
		                        }
		                    }*/?>

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
