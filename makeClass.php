<html>
<head><title>Make new class</title></head>
<body> 
<!-- Input felt og knap for å sende inputen -->
<form class="form-signin" method="POST">
<div class="input-group">
      <span class="input-group-addon" id="basic-addon1">@</span>
      <input type="text" name="classname" class="form-control" placeholder="Classname" required>
      <button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
      
    </div>
    </form>
<?php
echo '<p>Hei';
//Gjennoptar sesjon
session_start();
//Tar i bruk koneksjonsmetoden.
require 'connect.php';
//Setter lokale variaber utifra de globale
$userid=$_SESSION['userid'];
//Sjekker om inputen ble sendt
if(isset($_POST['classname'])){
	//legger til ny row i class tabellen i databasen
	$classname=$_POST['classname'];
	$query="INSERT INTO class(classname, creator) values('$classname','$userid')";
	$result = mysqli_query($connection, $query) or die(mysqli_error($connection));
	//setter classname variablene til empty og sender til neste side
	unset($classname);
	unset($_POST['classname']);
	header('Location:madeClass.php');
}



?>
</body></html>