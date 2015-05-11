<?php

// Connect to DB
	$mysqli = new mysqli("oniddb.cws.oregonstate.edu", "armatasc-db", "98xf7xW8S5pkhLuR", "armatasc-db");
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: ".$mysqli->connect_errno." : ".$mysqli->connect_error."<br>";
	} else {
	//	echo "Connection to database successful!<br><br>";
	}
		
	// Now, if a deleteItem button was pressed, delete that respective row in SQL
	if (isset($_POST['deleteItem']) && is_numeric($_POST['deleteItem'])) {
		$delID = $_POST['deleteItem'];
		$mysqli->query("DELETE FROM videoRental WHERE id = '$delID'");
		echo 'Successfully deleted row '.$delID.' from the videoRental database! <br><br>';
	}	
	
	// Similair thing for checked-in/out...	
	if (isset($_POST['checkItem']) && is_numeric($_POST['checkItem'])) {
		$checkID = $_POST['checkItem'];
		$mysqli->query("UPDATE videoRental SET rented = NOT rented WHERE id = '$checkID'");
		echo 'The selected movie (where ID = '.$checkID.') status has now been updated! <br><br>';
	}
	
	// DELETE ALL
	if (isset($_POST['deleteAll'])) {
		$mysqli->query("TRUNCATE TABLE videoRental");
		echo 'All data from table videoRental has been truncated! <br><br>';
	}
	
	echo 'Please click <a href="http://web.engr.oregonstate.edu/~armatasc/cs290/PHP-assignment2/src/videoCheckOut.php" target="_self">here</a> to return to inventory.'
	
?>
