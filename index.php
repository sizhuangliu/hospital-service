<!DOCTYPE html>
<html>
<head>
<title>W Hospital</title>

<link rel="stylesheet" href="main.css">
</head>

<body>

    <section id="banner">
        <div class="inner">

	<h1>Welcome to W Hospital!</h1>
    <div class="flex ">
        <div>
        <a href="patient_login.php" title="Patient's Page"><h3>Patient</h3></a>
        </div>
        <div>
        <a href="staff_login.php" title="Doctor's Page"><h3>Doctor</h3></a>
        </div>
    </div>

<form method="POST">
	<input type="submit" class="button" value="Reset Hardcoded Data" name="reset">
	<input type="submit" class="button" value="Reset Patient" name="resetP">
	<input type="submit" class="button" value="Reset Appointment" name="resetA">
	<input type="submit" class="button" value="Load Database" name="load">
</form>
<br>
<br>
<br>

<?php

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

// Connect Oracle...
if ($db_conn) {
    if (array_key_exists('reset', $_POST)) {
		// Drop old table...
		echo "<br> HardCoded Data Reset Complete </br>";
		executePlainSQL("Drop table doctor");
		executePlainSQL("Drop table Roombig");
		executePlainSQL("Drop table Resident_Live_Room_For");
		executePlainSQL("Drop table tab3");

		// Create new table...
		executePlainSQL("create table doctor (dpw varchar(30),
		did varchar(50), title varchar(50), dept varchar(50), primary key (did)) ");
		executePlainSQL("create table tab3 (pid varchar(50), age int, iname varchar2(30),Symptom varchar2(30),primary key (pid,iname))");
		executePlainSQL("create table Resident_Live_Room_For (pid varchar(50), room# int, FromDate date,ToDate date, primary key (pid, room#,FromDate,ToDate))");
		executePlainSQL("create table Roombig (room# int, capacity int, primary key (room#))");
		
		OCICommit($db_conn);

	}else
	   if (array_key_exists('resetA', $_POST)) {
		echo "<br> Appointment Reset Complete <br>";
		executePlainSQL("Drop table appointment");
		executePlainSQL("create table appointment (aid char(50),
		pid varchar(50), did varchar(50), datef date, 
		primary key (aid))");
		OCICommit($db_conn);

	} else 
		if (array_key_exists('resetP', $_POST)) {
		echo "<br> Patient Reset Complete <br>";
		executePlainSQL("Drop table patient");
		executePlainSQL("create table patient (pid varchar(10), pw varchar(30), pname varchar(20), address varchar(50), phone number, birthdate date, age int, primary key (pid))");
		OCICommit($db_conn);

	} else
		if (array_key_exists('load', $_POST)) {
			executePlainSQL("insert into doctor values ( 'staff','Ruth Riley', 'Medical Student','AE')");
			executePlainSQL("insert into doctor values ('staff', 'Genevieve Warner', 'Attending Physician','AE')");
			executePlainSQL("insert into doctor values ( 'staff','Miranda White', 'Hospitalist','AN')");
			executePlainSQL("insert into doctor values ( 'staff', 'Alexis Garrett', 'Surgeon','AN')");
			executePlainSQL("insert into doctor values ( 'staff', 'Shannon Frank', 'Surgeon','BS')");
			executePlainSQL("insert into doctor values ( 'staff', 'Ernesto Roberts', 'Medical Student','BS')");
			executePlainSQL("insert into doctor values ( 'staff', 'Morris Silva', 'Cardiologist','CA')");
			executePlainSQL("insert into doctor values ('staff', 'Jimmie Brewer', 'Cardiologist','CA')");
			
			executePlainSQL("insert into Roombig values (101, 2)");
			executePlainSQL("insert into Roombig values (102, 4)");
			executePlainSQL("insert into Roombig values (301, 1)");

			echo "<br> DataLoaded </br>";
			OCICommit($db_conn);
		}

		
	//Commit to save changes...
	OCILogoff($db_conn);
} else {
	echo "cannot connect";
	$e = OCI_Error(); // For OCILogon errors pass no handle
	echo htmlentities($e['message']);
}

?>


</body>

</html>
