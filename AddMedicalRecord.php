<!DOCTYPE html>
<html>
<head>
<title>Add Medical Record</title>
<link rel="stylesheet" href="main.css">
</head>

<body>
    <section id="banner">
        <div class="inner">

<h1>Patient Medical Record</h1>

<h4>Please write your patient diagnose below:</h4>

<form method="POST" action="AddMedicalRecord.php">

	<table border=0 cellpadding=0 cellspacing=0>
	<tr><td>PatientID</td><td><input type="text" size=20 name="insNo" placeholder="patient username"],></td></tr>
	<tr><td>Patient Age</td><td><input type="int" size=20 name="insAge" placeholder="Age"></td></tr>
	<tr><td>Illness Name</td><td> <input type="text" size=20 name="insIll" placeholder="insIll"></td></tr>
	<tr><td>Symptom</td><td> <input type="text" size=20 name="insSys" placeholder="insSys"></td></tr>
	</table>
	<br>
		<input type="submit" name="insertprecord" border=0 value="Add">
</form>
<br>
<a href="success.php" title="Home"><h3>&lt;&lt;Back</h3></a>


<?php

$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_e2k0b", "a25122145", "dbhost.ugrad.cs.ubc.ca:1522/ug");

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

    if (array_key_exists('insertprecord', $_POST)) {
        //Getting the values from user and insert data into the table
        $tuple = array (
            ":bind1" => $_POST["insNo"],
            ":bind2" => $_POST["insAge"],
            ":bind3" => $_POST["insIll"],
            ":bind4" => $_POST["insSys"]
        );
        $alltuples = array (
            $tuple
        );
        executeBoundSQL("insert into tab3 values (:bind1, :bind2, :bind3, :bind4)", $alltuples);

        OCICommit($db_conn);

    }

    if ($_POST && $success) {
		header("location: AddMedicalRecord.php");
		
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