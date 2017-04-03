<?php
// Start the Session
session_start ();
require ('../connect.php');
ini_set('display_errors', 1);



//setter lokale variabler utifraa session's variabler
$username   =   $_SESSION['username'];
$password   =   $_SESSION['password'];
$role       =   $_SESSION['role'];
$userid     =   $_SESSION['userid'];
$classname  =   $_SESSION['classname'];
$corrAns    =   $_SESSION['corrAns'];
$lastQuiz   =   $_SESSION['lastQuiz'];


//setter opp query for å hente info om brukeren
$query = "SELECT * FROM users WHERE username='$username' and  password='$password'";

function popAndGrade($connection, $role, $userid, $classname, $corrAns, $lastQuiz)
{
    $noOfCorrect = 0;

    //Her er det noe "off by one" tull. corrAns er 1-indeksert?!
    for ($i=0; $i < sizeof($corrAns); $i++) { 

        $answer = $_POST['question-'.($i+1).'-answers'];

        echo "<li>
        
                <h3>
                    Question ".($i+1).": 
                </h3>
                
                <div>
                    Your answer: $answer<br>".$corrAns[$i][0]." was correct.

                </div>";
                
        
        if ($role == 'S') {
            //Sjekker om dette spørsmålet har blitt besvart fra før:
            $ifExists = mysqli_query($connection, "SELECT * FROM hasAnsweredQuestion where userid=$userid and qid=$lastQuiz and questid=".$corrAns[$i][1]);
                                 
            
            if (mysqli_num_rows($ifExists) > 0) {
                //Oppdaterer spørsmål i DB
                if ($corrAns[$i][0] == $answer) {
                    $query = "UPDATE hasAnsweredQuestion SET answer=1 WHERE userid=$userid and qid=$lastQuiz and questid=".$corrAns[$i][1];
                    $noOfCorrect++;
                    priorityChange($connection,($corrAns[$i][0] == $answer), $userid, $lastQuiz, $corrAns, $i);
                    
                }
                else{
                    $query = "UPDATE hasAnsweredQuestion SET answer=0 WHERE userid=$userid and qid=$lastQuiz and questid=".$corrAns[$i][1];
                    priorityChange($connection,($corrAns[$i][0] == $answer), $userid, $lastQuiz, $corrAns, $i);
                }

            }else{
                //Lagrer nytt besvart spørsmål i DB
                if ($corrAns[$i][0] == $answer) {
                    $query = "INSERT INTO hasAnsweredQuestion (userid, qid, questid, answer) VALUES ($userid, $lastQuiz, ".$corrAns[$i][1].", 1)";
                    $noOfCorrect++;
                    priorityChange($connection,($corrAns[$i][0] == $answer), $userid, $lastQuiz, $corrAns, $i);
                }
                else{
                    $query = "INSERT INTO hasAnsweredQuestion (userid, qid, questid, answer) VALUES ($userid, $lastQuiz, ".$corrAns[$i][1].", 0)";
                    priorityChange($connection,($corrAns[$i][0] == $answer), $userid, $lastQuiz, $corrAns, $i);
                }
            }
            
            //Gjennomfører oppdatering
            mysqli_query($connection,$query);
            
        }
    }

    echo "
        <div> <br>
            You answered ".$noOfCorrect."/".sizeof($corrAns)." questions correctly.
        </div>";
}


function priorityChange($connection, $isCorrect, $userid, $lastQuiz, $corrAns, $i)
{
    $query = mysqli_query($connection, "SELECT priority FROM hasAnsweredQuestion where userid=$userid and qid=$lastQuiz and questid=".$corrAns[$i][1]);
    $minOrMax = mysqli_fetch_assoc($query);

    if (($minOrMax['priority'] == 5) and $isCorrect) {
        # do nothing

    }
    elseif (($minOrMax['priority'] == 1) and !$isCorrect) {
        # do nothing
    }
    elseif ($isCorrect) {
        # plusone
        $query = "UPDATE hasAnsweredQuestion SET priority = priority + 1 WHERE userid=$userid and qid=$lastQuiz and questid=".$corrAns[$i][1];
        mysqli_query($connection,$query);
    }
    else{
        # minusone
         $query = "UPDATE hasAnsweredQuestion SET priority = priority - 1 WHERE userid=$userid and qid=$lastQuiz and questid=".$corrAns[$i][1];
         mysqli_query($connection,$query);
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
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Theme CSS -->
    <link href="../css/freelancer.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and  Respond.js IE8 support of HTML5 elements and  media queries -->
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
            <!-- Brand  and  toggle get grouped for better mobile display -->
            <div class="navbar-header page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span> Menu <i class="fa fa-bars"></i>
                </button>
                <ul class="nav navbar-nav">
                <a class="navbar-brand " href="../index.php"><img src="../img/classmateCleanLogo.svg" width="100%" style="vertical-align: top;"></a>
                </li>
            </div>

            <!-- Collect the nav links, forms, and  other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li class="hidden">
                        <a href="#page-top"></a>
                    </li>
                    <li class="#page-scroll">
                        <a href="#"><?php echo"Logged in as: ".$username;?></a>
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
                <div id="page-wrap">

                <!-- Her kommer resultatene av quizet-->
                    <h1>Results</h1>
                    <ol>
                        <?php
                            popAndGrade($connection, $role, $userid, $classname, $corrAns, $lastQuiz);
                        ?>
                    </ol>
                    <?php
                    if ($role == 'S') { ?>
                        <a href="../student/mainAsStudent.php" class="btn btn-default" style="margin-left: 40px;"><span class="glyphicon glyphicon-home" aria-hidden="true"></span>  Home</a>
                    <?php
                    }
                    else { ?>
                        <a href="../teacher/mainAsTeacher.php" class="btn btn-default" style="margin-left: 40px;"><span class="glyphicon glyphicon-home" aria-hidden="true"></span>  Home</a>
                    <?php } ?>
                </div>
                
            <script type="text/javascript">
            var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
            document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
            </script>
            <script type="text/javascript">
            var pageTracker = _gat._getTracker("UA-68528-29");
            pageTracker._initData();
            pageTracker._trackPageview();
            </script>


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

    <!-- Scroll to Top Button (Only visible on small and  extra-small screen sizes) -->
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
