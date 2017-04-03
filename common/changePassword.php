<?php
session_start ();
require '../connect.php';
require '../lib/security.php';

$username = $_SESSION ['username'];
$password = $_SESSION ['password'];
$role = $_SESSION ['role'];
$userid = $_SESSION ['userid'];


if(isset($_POST['newPass1'])){
	$fmgs=changePass($_POST['newPass1'], $_POST['newPass2'], $_POST['oldPass'], $publicSalt, $_SESSION['username'], $connection);
}

if(isset($_POST['newEmail'])){
	$fmgs=changeEmail($connection, $_POST['newEmail'], $_POST['pass'], $_SESSION['username'], $publicSalt);
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
					<li class="#page-scroll"><a href="../common/changePassword.php"><?php echo"Logged in as: ".$username;?></a>
					
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
					<h2>Profile</h2>
					<?php if(isset($fmgs) && ($fmgs == "Wrong password." || $fmgs == "The new passwords has to match.")){echo "<div class='alert alert-danger' role='alert'>".$fmgs."</div>";}
					else if(isset($fmgs) && ($fmgs == "Password succesfully changed." || $fmgs == "Email succesfully changed.")){echo "<div class='alert alert-success' role='alert'>".$fmgs."</div>";}
					?>
					 
				</div>
			</div>
			</br>
			<div class="row">
				<div class="col-md-4">
					<div class="panel panel-default" style="width: 100%;">
						<div class="panel-heading"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span>  Change password</div>
						
							<div class="panel-body" style="line-height: 22px;">
								<form class="form-signin" method="POST">
									<div class="input-group">
										<input type="text" name="newPass1" class="form-control" placeholder="New password" required></br>
										<input type="text" name="newPass2" class="form-control" placeholder="Repeat new password" required></br>
										<input type="text" name="oldPass" class="form-control" placeholder="Old password" required></br>
										<button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>  Save changes</button>
										<?php
										if ($role == 'S') { ?>
                       				 	<a href="../student/mainAsStudent.php" class="btn btn-default" style="margin-left: 2px;"><span class="glyphicon glyphicon-home" aria-hidden="true"></span>  Home</a>
                    					<?php
                    					}
                    					else { ?>
                        				<a href="../teacher/mainAsTeacher.php" class="btn btn-default" style="margin-left: 2px;"><span class="glyphicon glyphicon-home" aria-hidden="true"></span>  Home</a>
                    					<?php } ?>
									</div>
								</form>	
							</div>	
					</div>
				</div>
				<div class="col-md-4">
					<div class="panel panel-default" style="width: 100%;">
						<div class="panel-heading"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>  Change email</div>
						<div class="panel-body">
							<form class="form-signin" method="POST">
								<div class="input-group">
									<input type="text" name="newEmail" class="form-control" placeholder="New email" required></br>
									<input type="text" name="pass" class="form-control" placeholder="Enter your password" required></br>
									<button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>  Save changes</button>
									<?php
									if ($role == 'S') { ?>
                       				 <a href="../student/mainAsStudent.php" class="btn btn-default" style="margin-left: 2px;"><span class="glyphicon glyphicon-home" aria-hidden="true"></span>  Home</a>
                    				<?php
                    				}
                    				else { ?>
                        			<a href="../teacher/mainAsTeacher.php" class="btn btn-default" style="margin-left: 2px;"><span class="glyphicon glyphicon-home" aria-hidden="true"></span>  Home</a>
                    				<?php } ?>
								</div>
							</form>
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