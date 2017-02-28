<html><head><title>ClasseMate</title></head>
<body><h1>HEI</h1><?php
session_start();
require 'connect.php';
//setter lokale variabler utifraa session's variabler
$username=$_SESSION['username'];
$password=$_SESSION['password'];
$role=$_SESSION['role'];
//setter opp query for å hente info om brukeren
$query = "SELECT * FROM `users` WHERE username='$username' and password='$password'";
$result=mysqli_query($connection, $query);
while ($row = mysqli_fetch_assoc($result)) {
	echo $row['userid'].'<br>';
	$_SESSION['userid']=$row['userid'];
	echo 'Hei '.$row['username'].'. Ditt epost er: '.$row['email'].' og ditt passord er: '.$row['password'].'' ;
	echo '. Ditt rolle er '.$row['role'].'<br>';
	$_SESSION['role']=$row['role'];
	$role=$_SESSION['role'];
}
//sjekker om brukeren er lærer eller stuent
if($role=='T'){
	echo "<p><a href='makeClass.php'>New Class</a><br>";
}
//
echo"<a href='attendClass.php'>Sing into a class</a><br>";
//echo"<a href='changePass.php'>Change Password</a>";
echo "<a href='logout.php'>Logout</a>";

?></body></html>