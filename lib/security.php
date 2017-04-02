<?php
$publicSalt = 'cluFlA6+i1zi_sI_';
//lager et randomt string
 function getRandomString(){
 	return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 1).substr(md5(time()),1);
 }
 //lager nytt passord fra username, salt1, salt2, publicSalt og userdefined password
 function createPassword($publicsalt,$username,$salt1,$salt2,$password){
 	$password=hash('sha512', $publicsalt.$username.$password.$salt1);
 	return $password=$password.$salt2;
 }
 //henter salt1 fra databasen
 function getSalt1($connection,$username){
 	$query="SELECT salt1 from users where username='$username'";
 	$result = mysqli_query ( $connection, $query ) or die ( mysqli_error ( $connection ) );
 	mysqli_close();
 	$row=mysqli_fetch_assoc($result);
 	return $row['salt1'];
 }
 //henter salt 2 fra databasen
 function getSalt2($connection,$username){
 	$query="SELECT salt2 from users where username='$username'";
 	$result = mysqli_query ( $connection, $query ) or die ( mysqli_error ( $connection ) );
 	mysqli_close();
 	$row=mysqli_fetch_assoc($result);
 	return $row['salt2'];
 }
 //oppdaterer passordet og salt 1 og 2 i databasen
 function updatePassword($password,$salt1,$salt2,$username,$connection){
 	$query="UPDATE users set password='$password', salt1='$salt1', salt2='$salt2' where username ='$username'"; 
 	$result = mysqli_query ( $connection, $query ) or die ( mysqli_error ( $connection ) );
 	mysqli_close();
 }
 //funksjon for manuelt bytte av passord
 function changePass($newPass1,$newPass2,$oldPass,$publicSalt,$username,$connection){
 	if($newPass1===$newPass2){
 		$salt1=getSalt1($connection, $username);
 		$salt2=getSalt2($connection, $username);
 		$password=createPassword($publicSalt, $username, $salt1, $salt2, $oldPass);
 		$query = "SELECT * FROM `users` WHERE username='$username' and password='$password'";
 		$result = mysqli_query ( $connection, $query ) or die ( mysqli_error ($connection ) );
 		$row=mysqli_fetch_assoc($result);
 		if($row['username']==$username){
 			$salt1=getRandomString();
 			$salt2=getRandomString();
 			$password=createPassword($publicSalt, $username, $salt1, $salt2, $newPass1);
 			updatePassword($password, $salt1, $salt2, $username,$connection);
 			return "You have new password.";
 		}else{
 			return "Wrong password.";
 			}
 	
 	}else{
 		return "The new passwords has to mach.";
 	}
 }
 
 function changeEmail($connection,$email,$password,$username,$publicSalt){
 	$salt1=getSalt1($connection, $username);
 	$salt2=getSalt2($connection, $username);
 	$password=createPassword($publicSalt, $username, $salt1, $salt2, $password);
 	$query = "SELECT * FROM `users` WHERE username='$username' and password='$password'";
 	$result = mysqli_query ( $connection, $query ) or die ( mysqli_error ($connection ) );
 	$row=mysqli_fetch_assoc($result);
 	if($row['username']==$username){
 	$updateEmail="UPDATE users set email='$email' where password='$password' ";
 	$result=mysqli_query($connection, $updateEmail) or die( mysqli_error ( $connection ) );
 	return "You have new email.";
 	}else{
 		return "Wrong password.";
 	}
 
 
 }
?>