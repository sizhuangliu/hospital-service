<!DOCTYPE html>
<html>
<head>
<title>Search Patient Info</title>
<link rel="stylesheet" href="main.css">
</head>

<body>
    <section id="banner">
        <div class="inner">

<h1>Search Patient Info</h1>

<!-- <form method="POST" action="Home_1.php">
	<p>
		<input type="submit" value="Reset" name="reset">
		<input type="submit" value="run hardcoded queries" name="dostuff">
	</p>
</form> -->

<form method="get">
	<table border=0 cellpadding=0 cellspacing=0>
		<tr><td>Patient ID</td><td><input type="text" size=20 name="searchPID" placeholder="Username"],></td></tr>
	</table>
	<br>
		<input type="submit" name="search" value="Search Room# for Patient">
</form>
<br>
<a href="success.php" title="Home"><h3>&lt;&lt;Back</h3></a>

<?php

$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_e2k0b", "a25122145", "dbhost.ugrad.cs.ubc.ca:1522/ug");

function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
	//echo "<br>running ".$cmdstr."<br>";
	global $db_conn, $success;
	$statement = OCIParse($db_conn, $cmdstr); //There is a set of comments at the end of the file that describe some of the OCI specific functions and how they work

	if (!$statement) {
		echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
		$e = OCI_Error($db_conn); // For OCIParse errors pass the       
		// connection handle
		echo htmlentities($e['message']);
		$success = False;
	}

	$r = OCIExecute($statement, OCI_DEFAULT);
	if (!$r) {
		echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
		$e = oci_error($statement); // For OCIExecute errors pass the statementhandle
		echo htmlentities($e['message']);
		$success = False;
	} else {

	}
	return $statement;

}

function executeBoundSQL($cmdstr, $list) {
	/* Sometimes a same statement will be excuted for severl times, only
	 the value of variables need to be changed.
	 In this case you don't need to create the statement several times; 
	 using bind variables can make the statement be shared and just 
	 parsed once. This is also very useful in protecting against SQL injection. See example code below for       how this functions is used */

	global $db_conn, $success;
	$statement = OCIParse($db_conn, $cmdstr);

	if (!$statement) {
		echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
		$e = OCI_Error($db_conn);
		echo htmlentities($e['message']);
		$success = False;
	}

	foreach ($list as $tuple) {
		foreach ($tuple as $bind => $val) {
			//echo $val;
			//echo "<br>".$bind."<br>";
			OCIBindByName($statement, $bind, $val);
			unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype

		}
		$r = OCIExecute($statement, OCI_DEFAULT);
		if (!$r) {
			echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
			$e = OCI_Error($statement); // For OCIExecute errors pass the statementhandle
			echo htmlentities($e['message']);
			echo "<br>";
			$success = False;
		}
	}  
}

if ($db_conn) {

    // if (array_key_exists('reset', $_POST)) {
	// 	// Drop old table...
	// 	echo "<br> dropping table <br>";
	// 	executePlainSQL("Drop table room CASCADE CONSTRAINTS");
	// 	executePlainSQL("Drop table patient CASCADE CONSTRAINTS");
    
	// 	echo "<br> creating table <br>";
	// 	executePlainSQL("create table patient (pid varchar(50), pw varchar(30), pname varchar(20), address varchar(50), phone number, birthdate date, age int, primary key (pid))");
	// 	executePlainSQL("create table room(room# int, capacity int, primary key (room#))");

	// }  else 
	
	// if (array_key_exists('dostuff', $_POST)) {
	// 	executePlainSQL("insert into patient values (1, 'ppw1', 'pn1', 'pa1', 6041, '2008-01-02', 18)");
	// 	executePlainSQL("insert into patient values (2, 'ppw2', 'pn2', 'pa2', 6042, '2008-02-02', 28)");
	// 	executePlainSQL("insert into patient values (3, 'ppw3', 'pn3', 'pa3', 6043, '2008-03-02', 38)");
	// 	executePlainSQL("insert into room values (1, 3)");
	// 	executePlainSQL("insert into room values (2, 4)");
	// 	OCICommit($db_conn);

	// } else
	if (array_key_exists('search', $_GET)) {
			$cmdstr = "select * from patient,Resident_Live_Room_For where patient.pid=Resident_Live_Room_For.pid and patient.pid='".$_GET['searchPID']."'";

			$result = executePlainSQL($cmdstr);
			$row = OCI_Fetch_Array($result, OCI_BOTH);
			if ($row) {
				echo "<table>";
				echo "<tr><th>ID</th><th>Phone#</th><th>Address</th><th>Name</th><th>Birthday</th><th>Room#</th></tr>";
				echo "<tr><td>" . $row[0] . "</td><td>" . $row[4] ."</td><td>" . $row[3] ."</td><td>" . $row[2] ."</td><td>" . $row[5] ."</td><td>" . $row[8] . "</td></tr>";
				echo "</table>";
			} else {
				echo "<p>Patient ".$_GET['searchPID']." does not live in hospital</p>";
			}
			// this can comment
			OCICommit($db_conn);
    }

    if ($_POST && $success) {
		header("location: Home_1.php");
	}

	//Commit to save changes...
	OCILogoff($db_conn);
} else {
	echo "cannot connect";
	$e = OCI_Error(); // For OCILogon errors pass no handle
	echo htmlentities($e['message']);
}
?>
	</div>
	</body>

</html>