<?php
session_start ();
require '../connect.php';
require '../lib/security.php';
if(isset($_POST['newPass1'])){
	$fmgs=changePass($_POST['newPass1'], $_POST['newPass2'], $_POST['oldPass'], $publicSalt, $_SESSION['username'], $connection);
}

if(isset($_POST['newEmail'])){
	$fmgs=changeEmail($connection, $_POST['newEmail'], $_POST['pass'], $_SESSION['username'], $publicSalt);
}


?>

<html>
<head>
<title>Change password</title>
</head>
<body>
	<h1>Bytt passord, trenger CSS</h1>
<?php if(isset($smgs)){echo $smgs;}
if(isset($fmgs)){echo $fmgs;}?>
	<!-- Input felt og knap for å sende inputen -->
	<form class="form-signin" method="POST">
		<div class="input-group">
			<span class="input-group-addon" id="basic-addon1">@</span> <input
				type="text" name="newPass1" class="form-control"
				placeholder="New password" required>
				<span class="input-group-addon" id="basic-addon1">@</span> <input
				type="text" name="newPass2" class="form-control"
				placeholder="Repeat new password" required>
				<span class="input-group-addon" id="basic-addon1">@</span> <input
				type="text" name="oldPass" class="form-control"
				placeholder="Old password" required>
			<button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>

		</div>
	</form>
	<form class="form-signin" method="POST">
		<div class="input-group">
			<span class="input-group-addon" id="basic-addon1">@</span> <input
				type="text" name="newEmail" class="form-control"
				placeholder="New email" required>
				<span class="input-group-addon" id="basic-addon1">@</span> <input
				type="text" name="pass" class="form-control"
				placeholder="Enter your password" required>
			<button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>

		</div>
	</form>

</body>
</html>