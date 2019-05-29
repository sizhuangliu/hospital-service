
<html>
<title>Patient Registration</title>
<link rel="stylesheet" href="main.css">
</head>
<body>
	<section id="banner">
        <div class="inner">

<h1>Register as an W Patient</h1>

<h3>Please fill out the following to sign up</h3>
<br>
<br>

<form method="POST" action="patient_signup.php">
<!--refresh page when submit-->
	<table border=0 cellpadding=0 cellspacing=0>
	<tr><td>Username</td><td><input type="text" size=20 name="new_username" placeholder="Username"],></td></tr>
	<tr><td>Password</td><td><input type="password" size=20 name="new_password" placeholder="Password"></td></tr>
	<tr><td>Full Name</td><td> <input type="text" size=20 name="new_fullname" placeholder="Fullname"></td></tr>
	<tr><td>Address</td><td> <input type="text" size=20 name="new_address" placeholder="Address"></td></tr>
	<tr><td>Phone Number</td><td> <input type="int" size=20 name="new_phone" placeholder="Phone#"></td></tr>
	<tr><td>Birthdate</td><td> <input type="date" size=20 name="new_birthdate"></td></tr>
	<tr><td>Age</td><td> <input type="number" size=20 name="new_age" placeholder="Age"></td></tr>
	</table>
	<br>
		<input type="submit" name="psubmit" border=0 value="SUBMIT">
</form>

<br>
<a href="patient_login.php" title="Login instead"><h3>&lt;&lt;Back</h3></a>

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
	echo "<br>Got data from table patient:<br>";
	echo "<table>";
	echo "<tr><th>Username</th><th>Password</th><th>Fullname</th><th>Address</th><th>Phone#</th><th>Birthdate</th><th>Age</th></tr>";

	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td><td>" . $row[4] . "</td><td>" . $row[5] . "</td><td>" . $row[6] . "</td></tr>"; 
		//or just use "echo $row[0]" 
	}
	echo "</table>";

}

// Connect Oracle...
if ($db_conn) {
		if (array_key_exists('psubmit', $_POST)) {
			//Getting the values from user and insert data into the table
			if (! $_POST['new_username'] ||! $_POST['new_password'] ||! $_POST['new_fullname']||
				! $_POST['new_address'] ||! $_POST['new_phone'] ||! $_POST['new_birthdate']||! $_POST['new_age']) {
                echo "please make sure you enter all correct information!";
    			return;
   			} else {
			$tuple = array (
				":bind1" => $_POST['new_username'],
				":bind2" => $_POST['new_password'],
				":bind3" => $_POST['new_fullname'],
				":bind4" => $_POST['new_address'],
				":bind5" => $_POST["new_phone"],
				":bind6" => $_POST["new_birthdate"],
				":bind7" => $_POST["new_age"],
			);
			$alltuples = array (
				$tuple
			);
			executeBoundSQL("insert into patient (pid, pw, pname, address, phone, birthdate, age) values (:bind1, :bind2, :bind3, :bind4, :bind5, :bind6, :bind7)", $alltuples);
			OCICommit($db_conn);

		}
	}
		if ($_POST && $success) {
			//POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
			header("location: patient_login.php");
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