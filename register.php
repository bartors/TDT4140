<?php
require ('connect.php');
// If the values are posted, insert them into the database.
//registrerer brukeren
function registerUser($connection,$username,$email,$password,$teacher){
	//trenger en logikk som skjekker om variablene ikke overskirder en lengde på 255
	if ($teacher) {
		$query = "INSERT INTO `users` (username, password, email ,active, role) VALUES ('$username', '$password', '$email', 1,'T')";
	} else {
		$query = "INSERT INTO `users` (username, password, email ,active, role) VALUES ('$username', '$password', '$email', 1,'S')";
	}
	return mysqli_query ( $connection, $query );
}

if (isset ( $_POST ['username'] ) && isset ( $_POST ['password'] )) {
	/*$username = $_POST ['username'];
	$email = $_POST ['email'];
	$password = $_POST ['password'];
	
	if ($_POST ['teacher']) {
		$query = "INSERT INTO `users` (username, password, email ,active, role) VALUES ('$username', '$password', '$email', 1,'T')";
	} else {
		$query = "INSERT INTO `users` (username, password, email ,active, role) VALUES ('$username', '$password', '$email', 1,'S')";
	}
	$result = mysqli_query ( $connection, $query );*/
	$result=registerUser($connection, $_POST['username'], $_POST['email'], $_POST['password'], $_POST['teacher']);
	if ($result) {
		$smsg = "User Created Successfully.";
	} else {
		$fmsg = "User Registration Failed";
	}
	unset ( $_POST ['username'] );
	unset ( $_POST ['email'] );
	unset ( $_POST ['password'] );
	unset ( $_POST ['teacher'] );
	
	// header('Location:index.php');
}

?>
<html>
<head>
<title>User Registeration Using PHP & MySQL</title>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet"
	href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="index.css">

<!-- Latest compiled and minified JavaScript -->
<script
	src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>

	<div class="jumbotron">
		<div class="container">
			<h1>
				<img src="img/classmateRegLogo.svg">
			</h1>
			<div class="login-group">
				<form class="inputs" method="POST">
        <?php if(isset($fmsg)){ ?><div class="alert alert-danger"
						role="alert"> <?php echo $fmsg; ?> </div><?php }?>
		<?php if(isset($smsg)){ ?><div class="alert alert-success" role="alert"><?php echo $smsg; ?> </div><?php }?>
        <input type="text" name="username" placeholder="Username"
						class="form-control" required> <input type="email" name="email"
						placeholder="E-Mail Address" class="form-control" required> <input
						type="password" name="password" placeholder="Password"
						class="form-control" required>
					<div class="buttonHolder">
						<input type="checkbox" name="teacher" value="teacher"> I am a Teacher
						<button class="submitBtn" type="Submit" id="submitBtn"
							name="submitBtn">Register</button>
					</div>
				</form>
				<div class="buttonHolder">
					<a href="index.php" id="regLink" style="">Back to log in</a>
				</div>
			</div>
		</div>
	</div>


</body>

</html>

</body>

</html>