<?php

session_start();
require '../connect.php';
//setter lokale variabler utifraa session's variabler
$username=$_SESSION['username'];
$password=$_SESSION['password'];
$role=$_SESSION['role'];
$userid=$_SESSION['userid'];
$classname=$_SESSION['classname'];
$question=$_POST['question'];
$a=$_POST['A'];
$b=$_POST['B'];
$c=$_POST['C'];
$d=$_POST['D'];
$ans=$_POST['group1'];
$topic=$_POST['topic'];
$qid=$_GET['id'];
$currQuiz = $_GET['quiz'];
//setter opp query for å hente info om quizen
//HVA BRUKES DET TIL????
function showQuiz($connection,$qid){
	$showQuizName="select name from quiz where qid='$qid'";
	$result = mysqli_query ( $connection, $showQuizName ) or die ( mysqli_error ( $connection ) );
	$count= mysqli_num_rows ( $result );
	mysqli_close();
	//if($count==1){
    	$row = mysqli_fetch_array ( $result );
    	$quizName=$row['name'];
    	return $quizName;
	//}
}
$quizName=showQuiz($connection, $_GET['id']);
//lager question
function makeQuestion($connection,$qid,$classname,$question,$a,$b,$c,$d,$ans,$topic){
	$makeQuestion = "INSERT INTO questions(classid,question,A,B,C,D,Ans,tema) values((SELECT classid FROM class WHERE classname='$classname'),'$question','$a','$b','$c','$d','$ans','$topic') ";
	$putIntoQuiz = "INSERT INTO hasQuestions values($qid,(SELECT qid FROM questions WHERE question='$question')) ";
	$result = mysqli_query ( $connection, $makeQuestion) or die ( mysqli_error ( $connection ) );
	$result = mysqli_query ( $connection, $putIntoQuiz) or die ( mysqli_error ( $connection ) );
	header("Location:createQuiz.php?id=".$qid);
	}
if(isset($_POST['group1'])){
	/*$makeQuestion = "INSERT INTO questions(classid,question,A,B,C,D,Ans,tema) values((SELECT classid FROM class WHERE classname='$classname'),'$question','$a','$b','$c','$d','$ans','$topic') ";
    $putIntoQuiz = "INSERT INTO hasQuestions values($qid,(SELECT qid FROM questions WHERE question='$question')) ";
	$result = mysqli_query ( $connection, $makeQuestion) or die ( mysqli_error ( $connection ) );
    $result = mysqli_query ( $connection, $putIntoQuiz) or die ( mysqli_error ( $connection ) );
	header("Location:createQuiz.php?id=".$qid);
*/
makeQuestion($connection, $_GET['id'], $_SESSION['classname'], $_POST['question'], $_POST['A'], $_POST['B'], $_POST['C'], $_POST['D'], $_POST['group1'], $_POST['topic']);
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
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css">

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
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span> Menu <i class="fa fa-bars"></i>
                </button>
                <ul class="nav navbar-nav">
                <a class="navbar-brand" href="../index.php"><img src="../img/classmateCleanLogo.svg" width="100%" style="vertical-align: top;"></a>
                </li>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li class="hidden">
                        <a href="#page-top"></a>
                    </li>
                    <li class="#page-scroll">
                        <a href="#"><?php echo "Profile: ".$username;?></a>
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
    <!-- Contact Section -->
    <section id="contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    </br>
                    <h2>Create question</h2></br>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2">

                    <form name="sentMessage" id="contactForm" method="POST">
                        <div class="row control-group">
                            <div class="form-group col-xs-12 floating-label-form-group controls">
                                <label>Topic</label>
                                <input type="text" class="form-control" placeholder="Topic" name ="topic" id="topic" required data-validation-required-message="Please enter a topic.">
                                <p class="help-block text-danger"></p>
                            </div>
                        </div>
                        <div class="row control-group">
                            <div class="form-group col-xs-12 floating-label-form-group controls">
                                <label>Question</label>
                                <input type="text" class="form-control" placeholder="Question" name="question" id="question" required data-validation-required-message="Please enter a question.">
                                <p class="help-block text-danger"></p>
                            </div>
                        </div>
                        <div class="row control-group">
                            <div class="form-group col-xs-12 floating-label-form-group controls">
                                <label>Option A</label>
                                <input type="text" class="form-control" placeholder="Option A" name="A" id="A" required data-validation-required-message="Please enter option A.">
                                <p class="help-block text-danger"></p>
                            </div>
                        </div>
                        <div class="row control-group">
                            <div class="form-group col-xs-12 floating-label-form-group controls">
                                <label>Option B</label>
                                <input type="text" class="form-control" placeholder="Option B" name="B" id="B" required data-validation-required-message="Please enter option B.">
                                <p class="help-block text-danger"></p>
                            </div>
                        </div>
                        <div class="row control-group">
                            <div class="form-group col-xs-12 floating-label-form-group controls">
                                <label>Option C</label>
                                <input type="text" class="form-control" placeholder="Option C" name="C" id="C" required data-validation-required-message="Please enter option C.">
                                <p class="help-block text-danger"></p>
                            </div>
                        </div>
                        <div class="row control-group">
                            <div class="form-group col-xs-12 floating-label-form-group controls">
                                <label>Option D</label>
                                <input type="text" class="form-control" placeholder="Option D" name="D" id="D" required data-validation-required-message="Please enter option D.">
                                <p class="help-block text-danger"></p>
                            </div>
                        </div>
                        <div align="center"><br>
                            <label style="font-size: large;">Correct answer:  </label></br>
                            <input type="radio" name="group1" value="A">&nbsp;A&nbsp;
                            <input type="radio" name="group1" value="B">&nbsp;B&nbsp;
                            <input type="radio" name="group1" value="C">&nbsp;C&nbsp;
                            <input type="radio" name="group1" value="D">&nbsp;D&nbsp;
                        </div>
                        <br>
                        <div id="success"></div>
                        <div class="row">
                            <div class="form-group col-xs-12" align="center">
                                <button type="submit" class="btn btn-success btn-lg">Create</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    

    <!-- Footer -->
    <footer class="text-center">
    <!-- Bruk denne bare om det trengs
    
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
                    <div class="col-lg-12">
                        Copyright &copy; ClassMate 2017
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button (Only visible on small and extra-small screen sizes) -->
    <div class="scroll-top page-scroll hidden-sm hidden-xs hidden-lg hidden-md">
        <a class="btn btn-primary" href="#page-top">
            <i class="fa fa-chevron-up"></i>
        </a>
    </div>

   
    <!-- jQuery -->
    <script src="../vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Plugin JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>

    <!-- Contact Form JavaScript -->
    <script src="../js/jqBootstrapValidation.js"></script>
    <script src="../js/contact_me.js"></script>

    <!-- Theme JavaScript -->
    <script src="../js/freelancer.min.js"></script>

</body>

</html>