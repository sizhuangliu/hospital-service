<html>
<head>

<title>Staff Login</title>
<link rel="stylesheet" href="main.css">
</head>

<body>
	<section id="banner">
        <div class="inner">
<h1>Login to W Hospital</h1>

<h2>Staff Login</h2>


<form method="POST">
		<table border=0 cellpadding=0 cellspacing=0>
        <tr><td>Username</td><td><input type="text" size=30 name="username" placeholder="Username"</td></tr>
        <tr><td>Password</td><td><input type="password" size=30 name="s_password" placeholder="Password"</td></tr></tr>
		</table>
		<br>
		<input type="submit" name="loginsubmit" border=0 value="LOGIN">
</form>

<br>
<a href="index.php" title="Home"><h3>&lt;&lt;Back</h3></a>
<?php

//this tells the system that it's no longer just parsing 
//html; it's now parsing PHP

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

function printResult($result) { //prints results from a select statement
	echo "<br>Got data from table doctor:<br>";
	echo "<table>";
	echo "<tr><th>Name</th><th>Password</th></tr>";

	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td></tr>"; //or just use "echo $row[0]" 
	}
	echo "</table>";

}

// Connect Oracle...
if ($db_conn) {

		if (isset($_POST['loginsubmit'])) {

			$username = $_POST["username"];
			$s_password = $_POST["s_password"];

			$result = executePlainSQL("select * from doctor where did = '$username' and dpw = '$s_password'");
			OCICommit($db_conn);
			$row = OCI_Fetch_Array($result, OCI_BOTH);
			if ($row) {
				header("location: success.php");
			} else {
				echo "<h1>Invalid Login</h1>";
				$success = False;
			}
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