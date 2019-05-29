<!DOCTYPE html>
<html>
<head>
<title>Add Medical Record</title>
<link rel="stylesheet" href="main.css">
</head>

<body>
    <section id="banner">
        <div class="inner">

<h1>Search Patient Residence Record</h1>

<h4>You can check resident patient here:</h4>
<form method="GET" action="searchp.php">
	<div class="field half">
		<input type="text" name="inspid" size="6" placeholder="Enter">
		<br>
		<input type="submit" value="Search" name="searchprecord"></p> 
	</div>
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

if ($db_conn) {
	if (isset($_GET['inspid']) &&  $_GET['inspid'] != "") {
		$db_conn = OCILogon("ora_e2k0b", "a25122145", "dbhost.ugrad.cs.ubc.ca:1522/ug");

		$cmdstr = "select * from tab3,Resident_Live_Room_For where tab3.pid=Resident_Live_Room_For.pid and Resident_Live_Room_For.pid = '".$_GET['inspid']."' and tab3.pid= '".$_GET['inspid']."'";
		$result = executePlainSQL($cmdstr);
		$row = OCI_Fetch_Array($result, OCI_BOTH);
		if ($row) {
            echo "<table>";
            echo "<tr><th>ID</th><th>age</th><th>INAME</th><th>Symptom</th><th>ROOM#</th><th>FROMDATE</th><th>TODATE</th></tr>";
            echo "<tr><td>" . $row["PID"] . "</td><td>" . $row["AGE"] ."</td><td>" . $row["INAME"] ."</td><td>" . $row["SYMPTOM"] ."</td><td>" . $row["ROOM#"] ."</td><td>" . $row["FROMDATE"] . "</td><td>" . $row["TODATE"] . "</td></tr>";
            echo "</table>";
        } else {
            echo "<p>Invalid PatientID</p>";
        }
    }
    if ($_POST && $success) {
		header("location: searchp.php");
		
	} 

	OCILogoff($db_conn);
} else {
	echo "cannot connect";
	$e = OCI_Error(); // For OCILogon errors pass no handle
	echo htmlentities($e['message']);
}
?>