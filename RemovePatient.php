<!DOCTYPE html>
<html>
<head>
<title>Remove Patient</title>
<link rel="stylesheet" href="main.css">
</head>

<body>
    <section id="banner">
        <div class="inner">

<h1>Remove Patient and appointment record</h1>

<h4>You can remove patient here:</h4>
<form method="GET" action="RemovePatient.php">
	<div class="field half">
		<input type="text" name="inspid" size="6" placeholder="Enter">
		<br>
        <input type="submit" value="Remove" name="removepatient"></p> 
        
        
	</div>
</form>

<?php

//this tells the system that it's no longer just parsing 
//html; it's now parsing PHP

$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_m4c0b", "a20091147", "dbhost.ugrad.cs.ubc.ca:1522/ug");

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

// Connect Oracle...
if ($db_conn) {

		if (array_key_exists('removepatient', $_POST)) {
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
					//OCICommit($db_conn);
					$row = OCI_Fetch_Array($result1, OCI_BOTH);
					if ($row[1] > 0) {

						executePlainSQL("update Roombig set capacity=$row[1]-1 where room#=$roomnum");
						executePlainSQL("delete Roombig where capacity=0");
						OCICommit($db_conn);
					} else {
						echo "<h1>Invalid Login</h1>";
						$success = False;
					}


			} 

	if ($_POST && $success) {
		header("location: medical.php");
		
	} else {
		// Select data...
		$result1 = executePlainSQL("select * from Roombig");
		$result2 = executePlainSQL("select * from Resident_Live_Room_For");
		$result3 = executePlainSQL("select * from tab3");
		
		printResult1($result1);
		printResult2($result2);
		printResult3($result3);

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