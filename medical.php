<!DOCTYPE html>
<html>
<head>
<title>Medical</title>
<link rel="stylesheet" href="main.css">
</head>

<body>
	<section id="banner">
    <div class="inner">

<h1>Patient Medical Record</h1>
<form method="POST" action="medical.php">
   
<p><input type="submit" value="Reset" name="reset"></p>
</form>

<h4>Please write your patient diagnose below:</h4>

<form method="POST" action="medical.php">
<!--refresh page when submit-->
	<table border=0 cellpadding=0 cellspacing=0>
	<tr><td>PatientID</td><td><input type="text" size=20 name="insNo" placeholder="insNo"],></td></tr>
	<tr><td>Patient Age</td><td><input type="text" size=20 name="insAge" placeholder="Age"></td></tr>
	<tr><td>Illness Name</td><td> <input type="text" size=20 name="insIll" placeholder="insIll"></td></tr>
	<tr><td>Symptom</td><td> <input type="text" size=20 name="insSys" placeholder="insSys"></td></tr>
	</table>
	<br>
		<input type="submit" name="insertprecord" border=0 value="Add">
</form>
<!-- create a form to pass the values. See below for how to 
get the values--> 

<br>
<br>
<h4> Register your patient's a room if he/she needs to be hospitalized</h4>
<form method="POST" action="medical.php">
<!--refresh page when submit-->
	<table border=0 cellpadding=0 cellspacing=0>
	<tr><td>PatientID</td><td><input type="text" size=20 name="insNo" placeholder="insNo"],></td></tr>
	<tr><td>Room#</td><td><input type="text" size=20 name="insRoom" placeholder="insRoom"></td></tr>
	<tr><td>From Date</td><td> <input type="date" size=20 name="insFrom" placeholder="insFrom"></td></tr>
	<tr><td>To Date</td><td> <input type="date" size=20 name="insTo" placeholder="insTo"></td></tr>
	</table>
	<br>
		<input type="submit" name="insertrrecord" border=0 value="Register">
		<input type="submit" name="listroom" border=0 value="check room">
</form>
<br>
<br>

<h4>You can check resident patient here:</h4>
<form method="GET" action="medical.php">
	<div class="field half">
		<input type="text" name="inspid" size="6" placeholder="Enter">
		<br>
		<input type="submit" value="Search" name="searchprecord"></p> 
	</div>
</form>

<?php
    if (isset($_GET['inspid']) &&  $_GET['inspid'] != "") {
		$db_conn = OCILogon("ora_m4c0b", "a20091147", "dbhost.ugrad.cs.ubc.ca:1522/ug");

		$cmdstr = "select * from tab3,Resident_Live_Room_For where tab3.pid=Resident_Live_Room_For.pid and Resident_Live_Room_For.pid = ".$_GET['inspid']." and tab3.pid= '".$_GET['inspid']."'";
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
?>

<h4>You Can Check the Total Number of Each Disease with Different Symptom:</h4>
<form method="POST" action="medical.php">
   
<p><input type="submit" value="Check" name="totalnum"></p> 
</form>

<h4>You Can Check Which How Many Different Symptoms a Disease Has among Youngest:</h4>
<form method="POST" action="medical.php">
   
<p><input type="submit" value="Check" name="youngestest"></p> 
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

function printResult1($result) { //prints results from Room
	echo "<br>Got data from table Room:<br>";
	echo "<table>";
	echo "<tr><th>Room#</th><th>capacity</th></tr>";

	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<tr><td>" . $row["ROOM#"] . "</td><td>" . $row["CAPACITY"] . "</td></tr>"; //or just use "echo $row[0]" 
	}
	echo "</table>";

}
function printResult2($result) { //prints results from Resident_Live_Room_For
	echo "<br>Got data from table Resident_Live_Room_For:<br>";
	echo "<table>";
	echo "<tr><th>pid</th><th>room#</th><th>FromDate</th><th>ToDate</th></tr>";
	//pid int, room# int, FromDate date,ToDate date, 

	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<tr><td>" . $row["PID"] . "</td><td>" . $row["ROOM#"] . "</td><td>" . $row["FROMDATE"] . "</td><td>" . $row["TODATE"] . "</td></tr>"; //or just use "echo $row[0]" 
	}
	echo "</table>";

}
function printResult3($result) { //prints results from tabs
	echo "<br>Got data from table tab3:<br>";
	echo "<table>";
	echo "<tr><th>pid</th><th>age</th><th>iname</th><th>Symptom</th></tr>";
	//pid int, name varchar2(30), iname varchar2(30),Symptom
	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<tr><td>" . $row["PID"] . "</td><td>" . $row["AGE"] . "</td><td>" . $row["INAME"] . "</td><td>" . $row["SYMPTOM"] . "</td></tr>"; //or just use "echo $row[0]" 
	}
	echo "</table>";

}

// Connect Oracle...
if ($db_conn) {

	if (array_key_exists('reset', $_POST)) {
		// Drop old table...
		echo "<br> dropping table <br>";
		executePlainSQL("Drop table Roombig");
		executePlainSQL("Drop table Resident_Live_Room_For");
		executePlainSQL("Drop table tab3");
		//executePlainSQL("Drop table Patient");

		// Create new table...
		echo "<br> creating new table <br>";
		executePlainSQL("create table tab3 (pid int, age int, iname varchar2(30),Symptom varchar2(30),primary key (pid,iname))");
		executePlainSQL("create table Resident_Live_Room_For (pid int, room# int, FromDate date,ToDate date, primary key (pid, room#,FromDate,ToDate))");
		executePlainSQL("create table Roombig (room# int, capacity int, primary key (room#))");
		//executePlainSQL("create table Patient (age int, iname varchar2(30))");
		OCICommit($db_conn);

	} else if (array_key_exists('totalnum', $_POST)) {
        $success = False;

        $ill = "SELECT COUNT(DISTINCT pid) AS total, iname, symptom FROM tab3 GROUP BY iname, Symptom";

  		$result5 = executePlainSQL($ill);

        echo "<br>Got data from table Patient :<br>";
		echo "<table>";
		echo "<tr><th>Total</th><th>illname</th><th>Symptom</th></tr>";
		while ($row = OCI_Fetch_Array($result5, OCI_BOTH)) {
			echo "<tr><td>" . $row[0] . "</td><td>" . $row[1]. "</td><td>" . $row[2] . "</td></tr>";  
		}
		echo "</table>";
    }else if (array_key_exists('youngestest', $_POST)) {
        $success = False;

        $abc = "SELECT COUNT(iname) AS porpularity,iname
        		FROM (SELECT MIN(age) AS age, iname, symptom FROM tab3 GROUP BY iname, Symptom)
        		GROUP BY iname";

  		$resultabc = executePlainSQL($abc);

        echo "<br>Got data from table Patients :<br>";
		echo "<table>";
		echo "<tr><th>age</th><th>iname</th></tr>";
		while ($row = OCI_Fetch_Array($resultabc, OCI_BOTH)) {
			echo "<tr><td>" . $row[0] . "</td><td>" . $row[1]. "</td></tr>";  
		}
		echo "</table>";
    }
		
		else if (array_key_exists('insertprecord', $_POST)) {
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

		} else
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


			} else
				if (array_key_exists('listroom', $_POST)) {
					
					executePlainSQL("insert into Roombig values (101, 4)");
					// Inserting data into table using bound variables
					$list1 = array (
						":bind1" => 102,
						":bind2" => 2
					);
					$list2 = array (
						":bind1" => 301,
						":bind2" => 3
					);
					$allrows = array (
						$list1,
						$list2
					);
					executeBoundSQL("insert into Roombig values (:bind1, :bind2)", $allrows); 

					OCICommit($db_conn);
					
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
