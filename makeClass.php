<html>
<head><title>Make new class</title></head>
<body> <form class="form-signin" method="POST">
<div class="input-group">
      <span class="input-group-addon" id="basic-addon1">@</span>
      <input type="text" name="classname" class="form-control" placeholder="Classname" required>
      <button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
      
    </div>
    </form>
<?php
echo '<p>Hei';
session_start();
require 'connect.php';
$userid=$_SESSION['userid'];

if(isset($_POST['classname'])){
	$classname=$_POST['classname'];
	$query="INSERT INTO class(classname, creator) values('$classname','$userid')";
	$result = mysqli_query($connection, $query) or die(mysqli_error($connection));
	unset($classname);
	unset($_POST['classname']);
	header('Location:makeClass.php');
}



?>
</body></html>