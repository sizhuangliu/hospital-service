<!DOCTYPE html>
<html>
<head>
	<title>Stuff Home Page</title>
	<!-- <link rel="stylesheet" href="main.css"> -->
	</head>

	<body>
		<section id="banner">
       	 <div class="inner">
	

		<h1>Welcome, Staff!</h1>

<form method="POST" action="staff_home.php">
	<p>
		<input type="submit" value="Reset" name="reset">
		<input type="submit" value="run hardcoded queries" name="dostuff">
	</p>
</form>

<form method="POST" action="staff_home.php">
	<table border=0 cellpadding=0 cellspacing=0>
	<tr><td>Patient ID</td><td><input type="number" size=20 name="searchPID" placeholder="ID"></td></tr>
	</table>
	<br>
		<input type="submit" name="search" value="Search Room# for Patient">
</form>

<?php
    if (isset($_GET['searchPID']) && $_GET['searchPID'] != "") {
		$db_conn = OCILogon("ora_n2b1b", "a22767164", "dbhost.ugrad.cs.ubc.ca:1522/ug");
		//.$_GET['searchPID']
		$cmdstr = "select * from Patient,Resident_Live_In where Patient.id=Resident_Live_In.id and Patient.id=".$_GET['searchPID'];

		//$cmdstr = "select Patient.id,Patient.phone#,Patient.address,Patient.name,Patient.birthday,Resident_Live_In.room# from Patient,Resident_Live_In where Patient.id=".$_GET['searchPID'];
		$result = executePlainSQL($cmdstr);
		$row = OCI_Fetch_Array($result, OCI_BOTH);
		if ($row) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Phone#</th><th>Address</th><th>Name</th><th>Birthday</th><th>Room#</th></tr>";
            echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] ."</td><td>" . $row[2] ."</td><td>" . $row[3] ."</td><td>" . $row[4] ."</td><td>" . $row[5] . "</td></tr>";
            echo "</table>";
        } else {
            echo "<p>Patient ".$_GET['searchPID']." does not live in hospital</p>";
        }
        // this can comment
        //OCICommit($db_conn);
    }
?>
<form method="get">
		<font size="5">Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Doctor ID</font>
		<input type="date" name="searchAID" value="2017-02-03" size="6">
		<input type="int" name="searchDID">
		<input type="submit" value="Search Appointment">
	</p>
</form>
<?php
    if (isset($_GET['searchAID']) && isset($_GET['searchDID']) &&   $_GET['searchAID'] != "" && $_GET['searchDID'] != "") {
		$db_conn = OCILogon("ora_n2b1b", "a22767164", "dbhost.ugrad.cs.ubc.ca:1522/ug");
		//$cmdstr = "select * from Book_Appointment,Patient,Doctor_Work_In where Patient.id=Book_Appointment.pid and Doctor_Work_In.id=Book_Appointment.did and Doctor_Work_In.id= ".$_GET['searchDID'];

		$cmdstr = "select Book_Appointment.aDate,Book_Appointment.id,Patient.id,Patient.name,Patient.address,Patient.birthday,Patient.phone#  from Book_Appointment,Patient,Doctor_Work_In where Patient.id=Book_Appointment.pid and Doctor_Work_In.id=Book_Appointment.did and Doctor_Work_In.id= ".$_GET['searchDID']." and Book_Appointment.aDate= '".$_GET['searchAID']."'";
		$result = executePlainSQL($cmdstr);
		$row = OCI_Fetch_Array($result, OCI_BOTH);
		if ($row) {
            echo "<table>";
            echo "<tr><th>Appointment Date</th><th>Appointment ID</th><th>Patient ID</th><th>Patient Name</th><th>Patient Address</th><th>Patient Birthday</th><th>Patient Phone#</th></tr>";
            echo "<tr><td>" . $row[0] ."</td><td>" . $row[1] ."</td><td>" . $row[2] . "</td><td>" . $row[3] ."</td><td>" . $row[4] ."</td><td>" . $row[5] . "</td><td>" . $row[6] . "</td></tr>";
            echo "</table>";
        } else {
            //echo "<p>Doctor ".$_GET['searchDID']." does not book by any Patient at ".$_GET['searchAID']"</p>";
        }
        // this can comment
        //OCICommit($db_conn);
    }
?>
<form method="get">
	<p>
		<input type="submit" value="Find Patient Went to All the Department" name="FPWTATD">
	</p>
</form>
<?php
    if (array_key_exists('FPWTATD', $_GET)) {
		$db_conn = OCILogon("ora_n2b1b", "a22767164", "dbhost.ugrad.cs.ubc.ca:1522/ug");
		//$cmdstr = "select * from Book_Appointment,Patient,Doctor_Work_In where Patient.id=Book_Appointment.pid and Doctor_Work_In.id=Book_Appointment.did and Doctor_Work_In.id= ".$_GET['searchDID'];
		$witness = "select Doctor_Work_In.deptName from Book_Appointment, Doctor_Work_In where Book_Appointment.did=Doctor_Work_In.id";
		$bad = "select BA.pid from Book_Appointment BA where not exists ((select DWI.deptName from Doctor_Work_In DWI) except (".$witness."))";
		//$cmdstr = $temp1."Patient.birthday,Patient.phone#  from Book_Appointment,Patient,Doctor_Work_In where Patient.id=Book_Appointment.pid and Doctor_Work_In.id=Book_Appointment.did and Doctor_Work_In.id=1";
		$result = executePlainSQL($bad);
		if ($result) {
            echo "<table>";
            echo "<tr><th>Appointment Date</th><th>Appointment ID</th><th>Patient ID</th><th>Patient Name</th><th>Patient Address</th><th>Patient Birthday</th><th>Patient Phone#</th></tr>";
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
				//echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["PHONE#"] ."</td><td>" . $row["ADDRESS"] ."</td><td>" . $row["NAME"] ."</td><td>" . $row["BIRTHDAY"] . "</td></tr>"; //or just use "echo $row[0]"
				echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] ."</td><td>" . $row[2] ."</td><td>" . $row[3] ."</td><td>" . $row[4] . "</td></tr>";
			}
        } else {
            //echo "<p>Doctor ".$_GET['searchDID']." does not book by any Patient at ".$_GET['searchAID']"</p>";
        }
        // this can comment
        //OCICommit($db_conn);
    }
?>

<?php
function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
	//echo "<br>running ".$cmdstr."<br>";

	global $db_conn, $success;
	$statement = OCIParse($db_conn, $cmdstr);
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

function printPatient($result) { //prints results from a select statement
	echo "<br>Patient<br>";
	echo "<table>";
	echo "<tr><th>Patient ID</th><th>Phone#</th><th>Address</th><th>Name</th><th>Birthday</th></tr>";
	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		//echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["PHONE#"] ."</td><td>" . $row["ADDRESS"] ."</td><td>" . $row["NAME"] ."</td><td>" . $row["BIRTHDAY"] . "</td></tr>"; //or just use "echo $row[0]"
		echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] ."</td><td>" . $row[2] ."</td><td>" . $row[3] ."</td><td>" . $row[4] . "</td></tr>";
	}
	echo "</table>";
}
function printBookA($result) { //prints results from a select statement
	echo "<br>Book Appointment<br>";
	echo "<table>";
	echo "<tr><th>Appointment ID</th><th>Patient ID</th><th>Doctor ID</th><th>Date</th></tr>";
	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		//echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["PHONE#"] ."</td><td>" . $row["ADDRESS"] ."</td><td>" . $row["NAME"] ."</td><td>" . $row["BIRTHDAY"] . "</td></tr>"; //or just use "echo $row[0]"
		echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] ."</td><td>" . $row[2] ."</td><td>" . $row[3] ."</td><td>" . $row[4] . "</td></tr>";
	}
	echo "</table>";
}
function printDoctorWI($result) { //prints results from a select statement
    echo "<br>Doctor Work In<br>";
	echo "<table>";
	echo "<tr><th>Doctor ID</th><th>Name</th><th>Department Name</th><th>Title</th><th>Password</th></tr>";
	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		//echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["PHONE#"] ."</td><td>" . $row["ADDRESS"] ."</td><td>" . $row["NAME"] ."</td><td>" . $row["BIRTHDAY"] . "</td></tr>"; //or just use "echo $row[0]"
		echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] ."</td><td>" . $row[2] ."</td><td>" . $row[3] ."</td><td>" . $row[4] ."</td></tr>";
	}
	echo "</table>";
}
function printDepartment($result) { //prints results from a select statement
    echo "<br>Department<br>";
	echo "<table>";
	echo "<tr><th>Name</th><th>Location</th></tr>";
	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		//echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["PHONE#"] ."</td><td>" . $row["ADDRESS"] ."</td><td>" . $row["NAME"] ."</td><td>" . $row["BIRTHDAY"] . "</td></tr>"; //or just use "echo $row[0]"
		echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] ."</td><td>" . $row[2] ."</td></tr>";
	}
	echo "</table>";
}
function printSchedule($result) { //prints results from a select statement
    echo "<br>Schedule<br>";
	echo "<table>";
	echo "<tr><th>Date</th><th>From Time</th><th>To Time</th></tr>";
	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		//echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["PHONE#"] ."</td><td>" . $row["ADDRESS"] ."</td><td>" . $row["NAME"] ."</td><td>" . $row["BIRTHDAY"] . "</td></tr>"; //or just use "echo $row[0]"
		echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] ."</td><td>" . $row[2] ."</td></tr>";
	}
	echo "</table>";
}
function printWorkF($result) { //prints results from a select statement
    echo "<br>Work For<br>";
	echo "<table>";
	echo "<tr><th>Doctor ID</th><th>Date</th></tr>";
	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		//echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["PHONE#"] ."</td><td>" . $row["ADDRESS"] ."</td><td>" . $row["NAME"] ."</td><td>" . $row["BIRTHDAY"] . "</td></tr>"; //or just use "echo $row[0]"
		echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] ."</td></tr>";
	}
	echo "</table>";
}
function printRoom($result) { //prints results from a select statement
    echo "<br>Room<br>";
	echo "<table>";
	echo "<tr><th>Room#</th><th>Capacity</th></tr>";
	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td></tr>";
	}
	echo "</table>";
}
function printResLI($result) { //prints results from a select statement
    echo "<br>Resident Live In<br>";
	echo "<table>";
	echo "<tr><th>Patient ID</th><th>Room#</th></tr>";
	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td></tr>";
	}
	echo "</table>";
}
function printResLF($result) { //prints results from a select statement
    echo "<br>Resident Live For<br>";
	echo "<table>";
	echo "<tr><th>Patient ID</th><th>From Date</th><th>To Date</th></tr>";
	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] ."</td><td>" . $row[2] ."</td></tr>";
	}
	echo "</table>";
}

//this tells the system that it's no longer just parsing 
//html; it's now parsing PHP

$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_n2b1b", "a22767164", "dbhost.ugrad.cs.ubc.ca:1522/ug");

// Connect Oracle...
if ($db_conn) {
	if (array_key_exists('reset', $_POST)) {
		echo "<br> dropping table <br>";
		executePlainSQL("Drop table Resident_Live_For");
		executePlainSQL("Drop table Resident_Live_In");
		executePlainSQL("Drop table Book_Appointment");
		executePlainSQL("Drop table Work_For");
		executePlainSQL("Drop table Schedule");
		executePlainSQL("Drop table Doctor_Work_In");
		executePlainSQL("Drop table Department");
		executePlainSQL("Drop table Room");
		executePlainSQL("Drop table Patient");
		echo "<br> creating new table <br>";
		executePlainSQL("create table Patient (id int, phone# varchar2(20), address varchar2(20), name varchar2(20), birthday date, primary key (id))");
		executePlainSQL("create table Department (name varchar2(20), location varchar2(20), primary key (name))");
		executePlainSQL("create table Doctor_Work_In (id int, name varchar2(20), deptName varchar2(20), title varchar2(20), password varchar2(20), primary key (id), foreign key (deptName) references Department(name))");
        executePlainSQL("create table Schedule (workD date, fromTime timestamp, toTime timestamp, primary key (workD))");
        executePlainSQL("create table Work_For (id int, workD date, primary key (id, workD), foreign key (workD) references Schedule(workD), foreign key (id) references Doctor_Work_In(id))");
		executePlainSQL("create table Book_Appointment (id int, pid int, did int, aDate date, primary key (id), foreign key (pid) references Patient(id), foreign key (did) references Doctor_Work_In(id))");
        executePlainSQL("create table Room (room# int, capacity int, primary key (room#))");
        executePlainSQL("create table Resident_Live_In (id int, room# int, primary key (id), foreign key (room#) references Room(room#), foreign key (id) references Patient(id))");
        executePlainSQL("create table Resident_Live_For (id int, FromDate date, ToDate date, primary key (id), foreign key (id) references Patient(id))");
		OCICommit($db_conn);
	} else if (array_key_exists('dostuff', $_POST)) {
		executePlainSQL("insert into Patient values (1, '1234567890', 'addressP1', 'nameP1', '2008-01-02')");
		executePlainSQL("insert into Patient values (2, '2234567890', 'addressP2', 'nameP2', '2017-03-03')");
		executePlainSQL("insert into Patient values (3, '3234567890', 'addressP3', 'nameP3', '2017-02-03')");
		executePlainSQL("insert into Department values ('nameDP1', 'location1')");
		executePlainSQL("insert into Department values ('nameDP2', 'location2')");
		executePlainSQL("insert into Doctor_Work_In values (1, 'nameD1', 'nameDP1', 'title1', '123456')");
		executePlainSQL("insert into Doctor_Work_In values (2, 'nameD2', 'nameDP2', 'title1', '123456')");
		executePlainSQL("insert into Schedule values ('2017-02-03', '2008-01-02 11:11:11', '2008-01-02 11:11:11')");
		executePlainSQL("insert into Work_For values (1, '2017-02-03')");
		executePlainSQL("insert into Book_Appointment values (1, 1, 1, '2017-02-03')");
		executePlainSQL("insert into Book_Appointment values (2, 1, 2, '2017-02-03')");
		executePlainSQL("insert into Room values (1, 3)");
		executePlainSQL("insert into Room values (2, 4)");
		executePlainSQL("insert into Resident_Live_In values (1, 1)");
		executePlainSQL("insert into Resident_Live_In values (2, 2)");
		executePlainSQL("insert into Resident_Live_For values (1, '2008-01-02', '2008-01-05')");
		executePlainSQL("insert into Resident_Live_For values (2, '2008-01-01', '2008-01-03')");
		OCICommit($db_conn);
	} else if (array_key_exists('insertsubmit', $_POST)) {
		$tuple = array (
			":bind1" => $_POST['idInput'],
			":bind2" => $_POST['phoneInput'],
			":bind3" => $_POST['addressInput'],
			":bind4" => $_POST['nameInput'],
			":bind5" => $_POST['birthdayInput']
		);
		$alltuples = array (
			$tuple
		);
		executeBoundSQL("insert into Patient values (:bind1, :bind2, :bind3, :bind4, :bind5)", $alltuples);
		OCICommit($db_conn);
	} else if (array_key_exists('updatesubmit', $_POST)) {
		$tuple = array (
			":bind1" => $_POST['oldName'],
			":bind2" => $_POST['newName']
		);
		$alltuples = array (
			$tuple
		);
		executeBoundSQL("update Patient set name=:bind2 where name=:bind1", $alltuples);
		OCICommit($db_conn);
	} 
	if ($_POST && $success) {
		//POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
		header("location: staff_home.php");
	} else {
		// Select data...
		$patient = executePlainSQL("select * from Patient");
		$book_appointment = executePlainSQL("select * from Book_Appointment");
		$doctor_work_in = executePlainSQL("select * from Doctor_Work_In");
		$department = executePlainSQL("select * from Department");
		$schedule = executePlainSQL("select * from Schedule");
		$work_for = executePlainSQL("select * from Work_For");
		$room = executePlainSQL("select * from Room");
		$resident_live_in = executePlainSQL("select * from Resident_Live_In");
		$resident_live_for = executePlainSQL("select * from Resident_Live_For");
		//echo htmlentities($patient);
		printPatient($patient);
		printBookA($book_appointment);
		printDoctorWI($doctor_work_in);
		printDepartment($department);
		printSchedule($schedule);
		printWorkF($work_for);
		printRoom($room);
		printResLI($resident_live_in);
		printResLF($resident_live_for);
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
