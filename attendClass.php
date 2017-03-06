<html>
<head>
<title>Sign into a class</title>
</head>
<body>
	<form class="form-signin" method="POST">
		<div class="input-group">
			<span class="input-group-addon" id="basic-addon1">@</span> <input
				type="text" name="classname" class="form-control"
				placeholder="Classname" required>
			<button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>

		</div>
	</form>
    <?php
				session_start ();
				require 'connect.php';
				$userid = $_SESSION ['userid'];
				// skriver ut feilmelding
				function error($string) {
					if ($string == "Column 'classid' cannot be null") {
						print 'Denne klassen er ikke i vår database, har du stavet riktig?';
					} else {
						print 'Noe gikk feil. Være så snill, kontakt våre utviklere.';
					}
					echo "<p><a href='classMate.php'>Tilbake til hovedsiden</a>";
					exit ();
				}
				if (isset ( $_POST ['classname'] )) {
					$classname = $_POST ['classname'];
					$query = "INSERT INTO attends(userid,classid) values('$userid',(SELECT classid FROM class WHERE classname='$classname'))";
					// utører sqloperasjonen eller skriver ut en feilmelding
					$result = mysqli_query ( $connection, $query ) or error ( mysqli_error ( $connection ) );
					unset ( $classname );
					unset ( $_POST ['classname'] );
					header ( 'Location:attendsClass.php' );
				}
				echo "<p><a href='classMate.php'>Tilbake til hovedside</a>";
				?></body>
</html>