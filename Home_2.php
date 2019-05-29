<!DOCTYPE html>
<html>
<head>
<title>Check Your Schedule</title>
<link rel="stylesheet" href="main.css">
</head>

<body>
    <section id="banner">
        <div class="inner">

<h1>Check Your Schedule</h1>

<form method="get">
	<table border=0 cellpadding=0 cellspacing=0>
		<tr><td>Date</td><td><input type="date" size=20 name="searchAID"],></td></tr>
		<tr><td>Doctor ID</td><td><input type="text" size=20 name="searchDID" placeholder="Enter Your Name"></td></tr>
	</table>
		<br>
		<input type="submit" name="searchA" value="Search Appointment">
		<br>
</form>
<br>
<a href="success.php" title="Home"><h3>&lt;&lt;Back</h3></a>
<br>
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

    // if (isset($_GET['searchAID']) && isset($_GET['searchDID']) &&  $_GET['searchAID'] != "" && $_GET['searchDID'] != "") {
	if (array_key_exists('searchA', $_GET)) {
		$q_did = $_GET["searchDID"];
		$q_date = $_GET["searchAID"];

		$cmdstr = "select appointment.datef, appointment.aid, appointment.pid, patient.pname, patient.phone
					from appointment,patient,doctor 
					where patient.pid=appointment.pid and doctor.did=appointment.did and doctor.did= '$q_did' and 
						  appointment.datef='$q_date'";

		// $cmdstr = "select *
		// 			from appointment,patient,doctor 
		// 			where patient.pid=appointment.pid and doctor.did=appointment.did and doctor.did= '$q_did' and 
		// 				  appointment.datef='$q_date'";

			$result = executePlainSQL($cmdstr);
			$row = OCI_Fetch_Array($result, OCI_BOTH);
			if ($row) {
            echo "<table>";
            echo "<tr><th>Appointment Date</th><th>Appointment ID</th><th>Patient ID</th><th>Patient Name</th><th>Patient Phone#</th></tr>";
            echo "<tr><td>" . $row[0] ."</td><td>" . $row[1] ."</td><td>" . $row[2] . "</td><td>" . $row[3] ."</td><td>" . $row[4] . "</td></tr>";
            echo "</table>";
        } else {
            echo "<p>Doctor ".$_GET['searchDID']." does not appointment at ".$_GET['searchAID']."</p>";
        }
			// this can comment
			OCICommit($db_conn);
    }

    if ($_POST && $success) {
		header("location: Home_2.php");
		
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