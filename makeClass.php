<?php
//Gjennoptar sesjon
session_start();
//Tar i bruk koneksjonsmetoden.
require 'connect.php';
//Setter lokale variaber utifra de globale
$userid=$_SESSION['userid'];
//Sjekker om inputen ble sendt
$showClasses="SELECT * FROM class WHERE creator='$userid'";
$classes=mysqli_query($connection, $showClasses) or die(mysqli_error($connection));
$count=mysqli_num_rows($classes);
if ($count>0){
echo"<table border='1'>
		<tr>
		<th>ClassID</th>
		<th>ClassName</th>
		</tr>";
while ($row = mysqli_fetch_array($classes)) {
	echo "<tr>";
	echo "<td>".$row['classid'] . "</td>";
	echo"<td>".$row['classname']."</td>";
	echo"</tr>";
}
echo"</table>";
}else{
	echo "Du har ingen klasser.";
}
if(isset($_POST['classname'])){
	//legger til ny row i class tabellen i databasen
	$classname=$_POST['classname'];
	$makeClass="INSERT INTO class(classname, creator) values('$classname','$userid')";
	$result = mysqli_query($connection, $makeClass) or die(mysqli_error($connection));
	//setter classname variablene til empty og sender til neste side
	unset($classname);
	unset($_POST['classname']);
	header('Location:madeClass.php');
}
//mysqli_close();

?>
<html>
<head><title>Make new class</title></head>
<body> 
<h1>HER SKAT DET VÆRE OVERSIKT OVER FAG LÆREREN HAR</h1>

<!-- Input felt og knap for å sende inputen -->
<form class="form-signin" method="POST">
<div class="input-group">
      <span class="input-group-addon" id="basic-addon1">@</span>
      <input type="text" name="classname" class="form-control" placeholder="Classname" required>
      <button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
      
    </div>
    </form>

</body></html>