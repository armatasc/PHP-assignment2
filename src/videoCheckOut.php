<html>
<body>

	<h2><b>Movie Mania - Online Video Rental</b></h2>

	<h4><u>Add a Video</u></h4>

<form action="" method="POST">
	Title: <input type="text" name="title" required><br>
	Category: <input type="text" name="category"><br>
	Length: <input type="number" name="length" min="1"><br>
	<br>
	<input type="submit" name="submit" value="Add Video">
</form>

<?php
	ini_set('display_errors', 'On');

	// Connect to DB
	$mysqli = new mysqli("oniddb.cws.oregonstate.edu", "armatasc-db", "98xf7xW8S5pkhLuR", "armatasc-db");
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: ".$mysqli->connect_errno." : ".$mysqli->connect_error."<br>";
	} else {
	//	echo "Connection to database successful!<br><br>";
	}
	// InsertPOST data into database
	if (isset($_POST['submit'])) {
		$title = $_POST['title'];
			
		// Variables for POST values
		if ($_POST['category']) {
			$category = $_POST['category'];
		} else {
			$category = NULL;
		}
		if ($_POST['length']) {
			$len = $_POST['length'];
		} else {
			$len = NULL;
		}
		
		// INSERT information into MySQLi DB
		if(!($stmt = $mysqli->prepare("INSERT INTO videoRental(title, category, length) VALUES('$title', '$category', '$len')"))){
			echo "Prepare INSERT into videoRental-db failed: ". $mysqli->errno . " : ". $mysqli->error."<br>";
		} else {
			$stmt->execute();
			$stmt->close();
		}
	} 
	
	// delete button here
	echo '<form action="videoFunctions.php" method="POST">';
	echo '<input type="submit" name="deleteAll" value="Delete All Videos">';
	echo '</form>';
	
	// prepared statement for filtering table by all categories
	// Step 1: prepare statement
	if(!($stmt = $mysqli->prepare("SELECT DISTINCT category FROM videoRental"))) {
		echo "Prepare Select: Step 1 failed: ". $stmt->errno . " : ". $stmt->error."<br>";
	} 
	// Step 2: execute
	if (!$stmt->execute()) {
		echo "Execute Select: Step 2 failed: ". $stmt->errno . " : ". $stmt->error."<br>";
	}
	// Step 3: create variables and bind results
	$out_filter = NULL;
	if(!$stmt->bind_result($out_filter)) {
		echo "Result binding failed: ". $stmt->errno . " : ". $stmt->error."<br>";
	}
	// Start of form with option filters for each category
	echo '<form method="GET">';
	echo '<select name="catSelected">';
		echo '<option value="All Movies">All Movies</option>';
		// Fetch remaining categories in database as drop down options
		while($stmt->fetch()){
			$val = $out_filter;
			if ($val != NULL) {
				echo '<option value="'.$val.'">'.$val.'</option>';
			}
		}
	echo '</select>';
	echo '<input type="submit" value="Filer Table">';
	echo '</form>';
	// Step 4: close statement
	$stmt->close();
	
	// Check for which category is selected
	if (isset($_GET['catSelected'])) {
		if ($_GET['catSelected'] == 'All Movies') {
			$temp = "*";
		} else {
			$temp = $_GET['catSelected'];
		}
	//	echo 'Category: ('.$temp.') was selected for filtering the table...<br>';
	//	echo 'Your table will now be filtered...<br>';
	} else {
		$temp = NULL;
	}
	
	// Step 1: prepare MySQLi stmt
	if ($temp === NULL || $temp === '*') {
		if(!($stmt = $mysqli->prepare("SELECT title, category, length, rented, id FROM videoRental ORDER BY id ASC"))) {
			echo "Prepare Select: Step 1 failed: ". $stmt->errno . " : ". $stmt->error."<br>";
		}
	} else {	
		if(!($stmt = $mysqli->prepare("SELECT title, category, length, rented, id FROM videoRental WHERE category = '$temp' ORDER BY id ASC"))) {
			echo "Prepare Select: Step 1 failed: ". $stmt->errno . " : ". $stmt->error."<br>";
		}
	}
	// Step 2: statement execute
	if (!$stmt->execute()) {
		echo "Execute Select: Step 2 failed: ". $stmt->errno . " : ". $stmt->error."<br>";
	}
	// Step 3: bind results
	$out_title = NULL;
	$out_category = NULL;
	$out_len = NULL;
	$out_rented = NULL;
	$out_id = NULL;
	
	if(!$stmt->bind_result($out_title, $out_category, $out_len, $out_rented, $out_id)) {
		echo "Result binding failed: ". $stmt->errno . " : ". $stmt->error."<br>";
	}
	// Set up for printing videos in a table w/ a delete button
	echo '<form action="videoFunctions.php" method="POST">';
	echo '<h3>Video Inventory</h3>
		<table border="2">
		<tr>';
	echo '<td><b>Title</b></td><td><b>Category</b></td><td><b>Length</b></td><td><b>Status</b></td><td><b>Check In/Out</b></td><td><b>Delete movie?</b></td></tr>';	// this is the top row of keys		
	
	while ($stmt->fetch()) {
		if($out_rented === 1) {
			$out_rented = "Checked-Out";
		} else { 
			$out_rented = "Available";
		}
		echo '<tr><td>'.$out_title.'</td><td>'.$out_category.'</td><td>'.$out_len.'</td><td>'.$out_rented.'</td>';
		echo '<td><input type="submit" name="checkItem" value="'.$out_id.'"></td>';
		echo '<td><input type="submit" name="deleteItem" value="'.$out_id.'"></td></tr>';
	}
	// Done creating HTML table
	echo '</table></form>';
	
	// Step 4: close statement
	$stmt->close();
	
	
	
?>

</body>
</html>
