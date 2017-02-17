<?php
$connection = mysqli_connect('mysql.stud.ntnu.no', 'jorgtho_demo', 'demo');
if (!$connection){
    die("Database Connection Failed" . mysqli_error($connection));
}
$select_db = mysqli_select_db($connection, 'jorgtho_friends');
if (!$select_db){
    die("Database Selection Failed" . mysqli_error($connection));
}