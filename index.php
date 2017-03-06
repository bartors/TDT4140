<?php 
// Start the Session
session_start ();
require ('connect.php');
// 3. If the form is submitted or not.
// 3.1 If the form is submitted
if (isset ( $_POST ['username'] ) and isset ( $_POST ['password'] )) {
	// 3.1.1 Assigning posted values to variables.
	$username = $_POST ['username'];
	$password = $_POST ['password'];
	// 3.1.2 Checking the values are existing in the database or not
	$query = "SELECT * FROM `users` WHERE username='$username' and password='$password'";
	
	$result = mysqli_query ( $connection, $query ) or die ( mysqli_error ( $connection ) );
	$count = mysqli_num_rows ( $result );
	while ( $row = mysqli_fetch_assoc ( $result ) ) {
		$_SESSION ['role'] = $row ['role'];
		$role = $_SESSION ['role'];
		$_SESSION ['userid'] = $row ['userid'];
	}
	// 3.1.2 If the posted values are equal to the database values, then session will be created for the user.
	if ($count == 1) {
		$_SESSION ['username'] = $username;
		$_SESSION ['password'] = $password;
	} else {
		// 3.1.3 If the login credentials doesn't match, he will be shown with an error message.
		$fmsg = "Invalid Login Credentials.";
	}
}
// 3.1.4 if the user is logged in Greets the user with message
if (isset ( $_SESSION ['username'] )) {
	if ($_SESSION ['role'] == 'T') {
		header ( 'Location:mainAsTeacher.php' );
	} else {
		header ( 'Location:mainAsStudent.php' );
	}
} else {
	// 3.2 When the user visits the page first time, simple login form will be displayed.
	?>
<html>
<head>
<title>ClassMate</title>

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
				<img src="img/classmateLogo.svg">
			</h1>
			<div class="login-group">
				<form class="inputs" method="POST">
			<?php if(isset($fmsg)){ ?><div class="alert alert-danger"
						role="alert"> <?php echo $fmsg; ?> </div><?php } ?>
			<input type="text" name="username" placeholder="Username"
						class="form-control" required> <input type="password"
						name="password" placeholder="Password" class="form-control"
						required>
					<div class="buttonHolder">
						<button class="submitBtn" type="Submit" id="submitBtn">Log In</button>
					</div>
				</form>
				<div class="buttonHolder">
					<a href="register.php" id="regLink">Register</a>
					<p>-</p>
					<a href="#" id="forgotLink">Forgot Password</a>
				</div>
			</div>
		</div>
	</div>

</body>

</html>
<?php } ?>