<!DOCTYPE html>
<html>
<head>
<title>Make Appointment</title>
<link rel="stylesheet" href="main.css">

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>

<script>
		$(function() {
		
			$("#json-one").change(function() {
			
				var $dropdown = $(this);
			
				$.getJSON("Docdata2.json", function(data) {
				
					var key = $dropdown.val();
					var vals = [];
										
					switch(key) {
						case 'AE':
							vals = data.AE.split(",");
							break;
						case 'AN':
							vals = data.AN.split(",");
							break;
                        case 'BS':
							vals = data.BS.split(",");
							break;
                        case 'CA':
							vals = data.CA.split(",");
							break;
						case 'base':
							vals = ['Please choose from above'];
					}
					
					var $jsontwo = $("#json-two");
					$jsontwo.empty();
					$.each(vals, function(index, value) {
						$jsontwo.append("<option>" + value + "</option>");
					});
			
				});
			});

		});
	</script>
</head>

<body>

	<section id="banner">
        <div class="inner">

		<h1>Login Successful !</h1>

<form method="Post" action="bookAppointment.php">
	<div class="field half">
	<h4 class="header">Please enter your username: </h4>
	<p>
		<input type="text" name="insPid">
	</p>
    <p>
		<h4 class="header">Choose department: </h4>

		<select id="json-one" name = "aa">
			<option selected value="base">Please Select</option>
			<option value="AE">Accident and emergency (A&E)</option>
			<option value="AN">Anaesthetics</option>
			<option value="BS">Breast screening</option>
			<option value="CA">Cardiology</option>
		</select>
		<br>
		<h4 class="header">Choose doctor: </h4>

		<select id="json-two" name ="insDid">
			<option>Please choose from above</option>
		</select>
	<br>
	<h4 class="header">Choose date: </h4>
		<input type="date" name="insDateF">
	
	<br>

	<h4 class="header">Please make sure all information above are correct! </h4>
	<p>
	<br>
	<br>
	<input type="submit" value="Make Appointment" name="insertsubmit"  />
	</p>
	</div>
</form>

<a href="index.php" title="Home"><h4>&lt;&lt;Back</h4></a>



<?php

//this tells the system that it's no longer just parsing 
//html; it's now parsing PHP
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

function printResult($result) { //prints results from a select statement
	echo "<br>Got data from table appointment:<br>";
	echo "<table>";
	echo "<tr><th>AppointmentID</th><th>Patient's Username</th><th>Doctor</th><th>Date</th></tr>";

	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<tr>
		<td>" . $row[0] ."</td>
		<td>" . $row[1] ."</td>
		<td>" . $row[2] ."</td>
		<td>" . $row[3] . "</td></tr>"; //or just use "echo $row[0]" 
	}
	echo "</table>";

}

// Connect Oracle...
if ($db_conn) {

		if (array_key_exists('insertsubmit', $_POST)) {

			if (! $_POST['insPid'] ||! $_POST['insDid'] ||! $_POST['insDateF']||$_POST['aa']== "base") {
                echo "please make sure you enter all correct information!";
    			return;
  				 }
			$a = uniqid();
			
			$tuple = array (
				":bind1" => $a,
				":bind2" => $_POST['insPid'],
				":bind3" => $_POST['insDid'],
				":bind4" => $_POST['insDateF'],
			);
			$alltuples = array (
				$tuple
			);
			executeBoundSQL("insert into appointment values (:bind1,:bind2, :bind3, :bind4)", $alltuples);
			OCICommit($db_conn);
		}
		if ($_POST && $success) {
			header("location: bookSuccess.php");
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