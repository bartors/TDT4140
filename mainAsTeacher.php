<?php
session_start ();
require '../connect.php';
// setter lokale variabler utifraa session's variabler
$username = $_SESSION ['username'];
$password = $_SESSION ['password'];
$role = $_SESSION ['role'];


//henter klasser
function showClasses($connection,$userid){
$showClasses = "SELECT * FROM class WHERE creator='$userid'";
$classes = mysqli_query ( $connection, $showClasses ) or die ( mysqli_error ( $connection ) );
mysqli_close();
return $classes;
}
$classes = showClasses($connection, $_SESSION['userid']);
$count = mysqli_num_rows ( $classes );
//lager ny klasse
function makeClass($connection,$userid,$classname){
	$makeClass = "INSERT INTO class(classname, creator) values('$classname','$userid')";
	return mysqli_query ( $connection, $makeClass );
	mysqli_close();
}
if (isset ( $_POST ['classname'] )) {
	$check = makeClass($connection, $_SESSION ['userid'], $_POST ['classname']);
	if ($check) {
		unset ( $_POST ['classname'] );
		header ( 'Location:mainAsTeacher.php' );
	}
	else {
		unset ( $_POST ['classname'] );
		$fmsg = "Course already existing.";
	}
}
//deaktiverer et fag
function deactivateClass($connection,$classID){
	$deleteCourse="UPDATE class SET teacherDeleted = 1 WHERE classid = $classID;";
	$result = mysqli_query ( $connection, $deleteCourse ) or die ( mysqli_error ( $connection ) );
	mysqli_close();
}

if(isset($_POST['delete'])){
	deactivateClass($connection, $_POST['delete']);
	unset ( $_POST ['delete'] );
	header('Location:mainAsTeacher.php');
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
					<li class="#page-scroll"><a href="../common/changePassword.php"><?php echo $username." - My profile";?></a>
					
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
					<h2>ClassMate - Teacher</h2>
				</div>
			</div>
			</br>
			<div class="row"></div>
			<div class="col-md-4">
				<div class="panel panel-default" style="width: 100%;">
					<div class="panel-heading"><span class="glyphicon glyphicon-blackboard" aria-hidden="true"></span>  Courses</div>
					<div class="panel-body" style="line-height: 22px;">
						<?php 
						if ($count > 0) {
						while ( $row = mysqli_fetch_array ( $classes ) ) {
							if ($row['teacherDeleted']==0) {
								echo  "<form class='form-signin' method='POST'><a href='coursePageTeacher.php?id=".$row['classname']."'>".$row ['classname'] ."</a><button name='delete' class='btn btn-default btn-xs' type='submit' value='".$row['classid']."' style='float: right;'><span class='glyphicon glyphicon-trash' aria-hidden='true'></span></button></br></form>";
							}
							else {

							}
						}
						} else {
								echo "You have no classes yet.</br>";
						}?>
					</div>
				</div>
			</div>


			<div class="col-md-4">
				<div class="panel panel-default"
					style="width: 100%; vertical-align: middle;">
					<div class="panel-heading"><span class="glyphicon glyphicon-star" aria-hidden="true"></span>  Create new course</div>
					<form class="form-signin" method="POST">
					<div class="panel-body">
						<input class="addCourseInput" type="text" name="classname"
							placeholder="Course name">&nbsp
						<button class="btn btn-default" type="submit">Create course</button>
						<?php if(isset($fmsg)){ ?><div class="alert alert-danger" role="alert"> <?php echo $fmsg; ?> </div><?php }?>
					</div></form>
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
