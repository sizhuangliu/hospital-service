<html>
<head>
<title>Patient Login</title>
<link rel="stylesheet" href="main.css">
</head>

<body>
	<section id="banner">
        <div class="inner">


<h1>Login to W Hospital</h1>

<h2>Patient Login</h2>

	<form method="POST">
		<table cellpadding=0 cellspacing=0>
			<tr><td>Username</td><td><input type="text" size=30 name="username" placeholder="Username"</td></tr>
			<tr><td>Password</td><td><input type="password" size=30 name="p_password" placeholder="Password"</td></tr></tr>
		</table>
		<br>
		<input type="submit" name="psubmit" border=0 value="LOGIN">
	
	</form>
	<div>
		<br>
		<a href="patient_signup.php" title="Sign up"><h4>Don't have an account yet? No problem. Sign up!</h4></a> 	
	</div>
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

// Connect Oracle...
if ($db_conn) {

	if (isset($_POST['psubmit'])) {

			$username = $_POST["username"];
			$p_password = $_POST["p_password"];

			$result = executePlainSQL("select * from patient where pid = '$username' and pw = '$p_password'");
			OCICommit($db_conn);
			$row = OCI_Fetch_Array($result, OCI_BOTH);
			if ($row) {
				header("location: bookAppointment.php");
			} else {
				echo "<br> Invalid Login </br>";
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

</body>

</html>
