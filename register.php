<?php
require 'connect.php';
require 'lib/security.php';
// If the values are posted, insert them into the database.
// registrerer brukeren
function registerUser($connection, $username, $email, $password, $teacher,$salt1,$salt2) {
	// trenger en logikk som skjekker om variablene ikke overskirder en lengde på 255
	if ($teacher) {
		$query = "INSERT INTO `users` (username, password, email , role,salt1,salt2) VALUES ('$username', '$password', '$email','T','$salt1','$salt2')";
	} else {
		$query = "INSERT INTO `users` (username, password, email , role,salt1,salt2) VALUES ('$username', '$password', '$email','S','$salt1','$salt2')";
	}
	return mysqli_query ( $connection, $query );
}

if (isset ( $_POST ['username'] ) && isset ( $_POST ['password'] )) {
	if (strlen ( $_POST ['username'] ) < 256 && strlen ( $_POST ['email'] ) < 256 & strlen ( $_POST ['password'] ) < 256) {
		$salt1=getRandomString();
		$salt2=getRandomString();
		$password=createPassword($publicSalt, $_POST ['username'], $salt1, $salt2, $_POST ['password']);
		$result = registerUser ( $connection, $_POST ['username'], $_POST ['email'], $password, $_POST ['teacher'],$salt1,$salt2 );
		if ($result) {
			$smsg = "User Created Successfully.";
		} else {
			$fmsg = "User Registration Failed";
		}
	} else {
		$fmsg = "Your email, username and password cannot have more than 255 characters.";
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
<link rel="stylesheet" href="css/index.css">

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
		<?php if(isset($smsg)){ ?><div class="alert alert-success"
						role="alert"><?php echo $smsg; ?> </div><?php }?>
        <input type="text" name="username" placeholder="Username"
						class="form-control" required> <input type="email" name="email"
						placeholder="E-Mail Address" class="form-control" required> <input
						type="password" name="password" placeholder="Password"
						class="form-control" required>
					<div class="buttonHolder">
						<input type="checkbox" name="teacher" value="teacher"> I am a
						Teacher
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