<?php 
// Start the Session
session_start ();
require ('connect.php');
require ('lib/security.php');
ini_set('display_errors', 1);
$_SESSION['connection']=$connection;

function recoverPassword($email,$publicSalt){
	//$email = $_POST['email'];
	$query = mysqli_query($_SESSION['connection'], "SELECT * FROM users WHERE email='".$email."'");
	if(mysqli_num_rows($query) == 1){
		while ($row = mysqli_fetch_assoc($query)) {
			$sendUsername = $row['username'];
			$salt1=getRandomString();
			$salt2=getRandomString();
			$sendPassword=getRandomString();
			$password=createPassword($publicSalt, $sendUsername, $salt1, $salt2, $sendPassword);
			updatePassword($password, $salt1, $salt2, $sendUsername,$_SESSION['connection']);
		}
		//Email sender
		$to = $email;
		$subject = "Password reminder";
		$txt = "Hello " . $sendUsername . "!\nYour new password is: " . $sendPassword ."    it is recommended that you change it as quickly as posible.";
		$headers = "From: noreply@classmate.com" . "\r\n";
		mail($to,$subject,$txt,$headers);
	}else{
		// do something
		if (!mysqli_query($_SESSION['connection'],"SELECT * FROM users WHERE email='".$email."'")){
			die('Error: ' . mysqli_error($_SESSION['connection']));
		}
	}
}

//PASSWORD RECOVERY
if (isset($_POST['email'])) {
	recoverPassword($_POST['email'],$publicSalt);
}


// 3. If the form is submitted or not.
// 3.1 If the form is submitted
if (isset ( $_POST ['username'] ) and isset ( $_POST ['password'] )) {
	$salt1=getSalt1($_SESSION['connection'], $_POST['username']);
	$salt2=getSalt2($_SESSION['connection'], $_POST['username']);
	$password=createPassword($publicSalt, $_POST['username'], $salt1, $salt2, $_POST['password']);
	$fmsg=login($_POST['username'], $password);
}
function login($usrname,$pswrd){
	$username = $usrname;
	$password = $pswrd;
	// 3.1.2 Checking the values are existing in the database or not
	$query = "SELECT * FROM `users` WHERE username='$username' and password='$password'";
	
	$result = mysqli_query ( $_SESSION['connection'], $query ) or die ( mysqli_error ( $_SESSION['connection'] ) );
	$count = mysqli_num_rows ( $result );
	while ( $row = mysqli_fetch_assoc ( $result ) ) {
		$_SESSION ['role'] = $row ['role'];
		$_SESSION ['userid'] = $row ['userid'];
	}
	// 3.1.2 If the posted values are equal to the database values, then session will be created for the user.
	if ($count == 1) {
		$_SESSION ['username'] = $username;
		$_SESSION ['password'] = $password;
	} else {
		// 3.1.3 If the login credentials doesn't match, he will be shown with an error message.
		return "Invalid Login Credentials.";
	}
}

function mainPageHeader(){
	if ($_SESSION ['role'] == 'T') {
		header ( 'Location:teacher/mainAsTeacher.php' );
	} else {
		header ( 'Location:student/mainAsStudent.php' );
	}
}
// 3.1.4 if the user is logged in Greets the user with message
if (isset ( $_SESSION ['username'] )) {
	mainPageHeader();
	
} else {
	// 3.2 When the user visits the page first time, simple login form will be displayed.
	?>
<html>
<head>
<title>ClassMate</title>


  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="css/index.css">
 
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
				<a href="register.php" id="regLink">Register</a>
		<p style="color: #fff;font-size: 12px;display: inline;">-</p>


<!-- Her er glemt passord knasten -->

		<a href="" data-toggle="modal" data-target="#myModal">Forgot Password</a>

  <!-- Modal -->
		<div class="modal fade" id="myModal" role="dialog">
		    <div class="modal-dialog modal-sm" style="vertical-align: middle;">
		      <div class="modal-content">
		        <div class="modal-header">
		          <button type="button" class="close" data-dismiss="modal">&times;</button>
		          <h4 class="modal-title">Password recovery</h4>
		        </div>
		        <div class="modal-body">
		          <form class="inputs" method="POST">
		          	<input type="email" name="email" placeholder="E-Mail Address" class="form-control" required>
		          	<button type="Submit" id="submitBtn" style="min-width: 100%;height: 40px;">Send Password</button>
		          </form>
		        </div>
		        <div class="modal-footer">
		          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		        </div>
		      </div>
		    </div>
		  </div>

<!-- her sluttern -->
			</div>
		</div>
	</div>

</body>

</html>
<?php } ?>