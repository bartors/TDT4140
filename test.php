<html><head><title>TEST</title></head>
<body><h1>HEI</h1><?php
session_start();
require 'connect.php';
$username=$_SESSION['username'];
$password=$_SESSION['password'];
$query = "SELECT * FROM `users` WHERE username='$username' and password='$password'";
$result=mysqli_query($connection, $query);
while ($row = mysqli_fetch_assoc($result)) {
	echo $row['userid'].'<br>';
	echo 'Hei '.$row['username'].'. Ditt epost er: '.$row['email'].'og ditt passord er: '.$row['password'] ;
}
?></body></html>