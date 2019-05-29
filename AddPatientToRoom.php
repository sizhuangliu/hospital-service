<!DOCTYPE html>
<html>
<head>
<title>Add Patient to Room</title>
<link rel="stylesheet" href="main.css">
</head>

<body>
    <section id="banner">
        <div class="inner">

<h1>Add Patient to Room</h1>
<h4> Register your patient's a room if he/she needs to be hospitalized</h4>
<form method="POST" action="AddPatientToRoom.php">
<!--refresh page when submit-->
	<table border=0 cellpadding=0 cellspacing=0>
	<tr><td>PatientID</td><td><input type="text" size=20 name="insNo" placeholder="Patient Username"],></td></tr>
	<tr><td>Room#</td><td><input type="text" size=20 name="insRoom" placeholder="Room number"></td></tr>
	<tr><td>From Date</td><td> <input type="date" size=20 name="insFrom" placeholder="From Date"></td></tr>
	<tr><td>To Date</td><td> <input type="date" size=20 name="insTo" placeholder="To Date"></td></tr>
	</table>
	<br>
		<input type="submit" name="insertrrecord" border=0 value="Register">
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

function printResult1($result) { //prints results from Room
	echo "<table>";
	echo "<tr><th>Room#</th><th>capacity</th></tr>";

	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<tr><td>" . $row["ROOM#"] . "</td><td>" . $row["CAPACITY"] . "</td></tr>"; //or just use "echo $row[0]" 
	}
	echo "</table>";

}

if ($db_conn) {
      if (array_key_exists('insertrrecord', $_POST)) {
        // Update tuple using data from user
        
        $tuple = array (
            ":bind1" => $_POST['insNo'],
            ":bind2" => $_POST['insRoom'],
            ":bind3" => $_POST['insFrom'],
            ":bind4" => $_POST['insTo']
        );
        
        $alltuples = array (
            $tuple
        );
        executeBoundSQL("insert into Resident_Live_Room_For values (:bind1,:bind2, :bind3, :bind4)", $alltuples);
        
            $roomnum = $_POST["insRoom"];
            $result1 = executePlainSQL("select * from Roombig where room#=$roomnum");
        
            $row = OCI_Fetch_Array($result1, OCI_BOTH);
            if ($row[1] > 0) {

                executePlainSQL("update Roombig set capacity=$row[1]-1 where room#=$roomnum");
                executePlainSQL("delete Roombig where capacity=0");
                OCICommit($db_conn);
            } else {
                echo "<p>Invalid Command</p>";
                $success = False;
            }

            OCICommit($db_conn);
    }
    
    if ($_POST && $success) {
       header("location: AddPatientToRoom.php");
       
    } else {
    // Select data...
   $result1 = executePlainSQL("select * from Roombig");
    printResult1($result1);


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