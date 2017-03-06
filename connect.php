<?php
$connection = mysqli_connect ( 'mysql.stud.ntnu.no', 'bartoszz_classma', 'admin1234' );
if (! $connection) {
	die ( "Database Connection Failed" . mysqli_error ( $connection ) );
}
$select_db = mysqli_select_db ( $connection, 'bartoszz_classmate' );
if (! $select_db) {
	die ( "Database Selection Failed" . mysqli_error ( $connection ) );
}