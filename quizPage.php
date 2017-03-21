<?php
session_start();
require 'connect.php';
ini_set('display_errors', 1);
//setter lokale variabler utifraa session's variabler
$username   =$_SESSION['username'];
$password   =$_SESSION['password'];
$role       =$_SESSION['role'];
$userid     =$_SESSION['userid'];
$classname  =$_SESSION['classname'];
$qid        =$_GET['id'];
$currQuiz   =$_GET['quiz'];

//setter opp query for å hente info om quizen
$showQuizName="select name from quiz where qid='$qid'";
$result = mysqli_query ( $connection, $showQuizName ) or die ( mysqli_error ( $connection ) );
$count= mysqli_num_rows ( $result );
if($count==1){
    $row = mysqli_fetch_array ( $result );
    $quizName=$row['name'];
}

function popQuiz($connection, $currQuiz)
{
    $qid = mysqli_query($connection, "SELECT qid FROM quiz WHERE name='".$currQuiz."'")->fetch_assoc();

    //Holder styr på forrige (denne) quizen for grade.php
    $_SESSION['lastQuiz'] = $qid['qid'];
    $questionIDs = mysqli_query($connection, "SELECT * FROM quiz JOIN hasQuestions ON quiz.qid = hasQuestions.Quizid WHERE qid = '".$qid['qid']."'");

    //Setter opp et array for de rette svarene
    $corrAns = array();
    $questionNumber = 1;


    while ($row = $questionIDs->fetch_assoc()){
        $currQuestion = mysqli_query($connection, "SELECT * FROM questions WHERE qid=".$row['queid'])->fetch_assoc();
        $corrAns[] = [$currQuestion['Ans'], $currQuestion['qid']];


        echo "<li>
    
        <h3>".$currQuestion['question']."</h3>
        
        <div>
            <input type='radio' name='question-$questionNumber-answers' id='question-".$questionNumber."-answers-A' value='A' />
            <label for='question-".$questionNumber."-answers-A'>A) ".$currQuestion['A']." </label>
        </div>
        
        <div>
            <input type='radio' name='question-$questionNumber-answers' id='question-".$questionNumber."-answers-B' value='B' />
            <label for='question-".$questionNumber."-answers-B'>B) ".$currQuestion['B']." </label>
        </div>
        
        <div>
            <input type='radio' name='question-$questionNumber-answers' id='question-".$questionNumber."-answers-C' value='C' />
            <label for='question-".$questionNumber."-answers-C'>C) ".$currQuestion['C']." </label>
        </div>
        
        <div>
            <input type='radio' name='question-$questionNumber-answers' id='question-".$questionNumber."-answers-D' value='D' />
            <label for='question-".$questionNumber."-answers-D'>D) ".$currQuestion['D']." </label>
        </div>
    
        </li>";
        $questionNumber++;
        }

    //Gjør korrekte svar til sessionvariabel
    $_SESSION['corrAns']     = $corrAns;
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
                <a class="navbar-brand" href="index.php"><img src="img/classmateCleanLogo.svg" width="100%" style="vertical-align: top;"></a>
                </li>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li class="hidden">
                        <a href="#page-top"></a>
                    </li>
                    <li class="#page-scroll">
                        <a href="#"><?php echo"Logged in as: ".$username;?></a>
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
            <div class="row ">
                <div class="col-lg-12">
                        <h2><?php echo $currQuiz;?></h2></br>
                    <form action="grade.php" method="post" id="quiz">
                        <ol>
                            <?php 
                                popQuiz($connection, $currQuiz);
                            ?>

                        </ol>
                   <?php  echo "<a href='coursePageStudent.php?id=".$_SESSION['classname']."' class='btn btn-link'>Back</a>" ?>
                    <input class="btn btn-default" type="submit" value="Submit Quiz" style="margin-left: 40px;" />

            
                    </form>
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
                    <div class="col-lg-12">
                        Copyright &copy; Your Website 2016
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
    <script src="vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Plugin JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>

    <!-- Contact Form JavaScript -->
    <script src="js/jqBootstrapValidation.js"></script>
    <script src="js/contact_me.js"></script>

    <!-- Theme JavaScript -->
    <script src="js/freelancer.min.js"></script>

</body>

</html>