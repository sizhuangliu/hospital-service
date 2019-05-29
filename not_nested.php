<!DOCTYPE html>
<html>
<head>
<title>Check Statistcs</title>
<link rel="stylesheet" href="main.css">
</head>

<body>
    <section id="banner">
        <div class="inner">

<h1>Check Statistics</h1>



</form>

<h4>You Can Check the Total Number of Each Disease with Different Symptom:</h4>
<form method="POST" action="not_nested.php">
   
<p><input type="submit" value="Check" name="totalnum"></p> 
</form>
</form>

<h4>You Can Check How Many Different Symptoms a Disease Has among Youth:</h4>
<form method="POST" action="not_nested.php">
   
<p><input type="submit" value="Check" name="youngestest"></p> 
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

if ($db_conn) {
	if (array_key_exists('totalnum', $_POST)) {
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
        		where age < 18
        		GROUP BY iname";

  		$resultabc = executePlainSQL($abc);

        echo "<br>Got data from table Patients :<br>";
		echo "<table>";
		echo "<tr><th>variety</th><th>iname</th></tr>";
		while ($row = OCI_Fetch_Array($resultabc, OCI_BOTH)) {
			echo "<tr><td>" . $row[0] . "</td><td>" . $row[1]. "</td></tr>";  
		}
		echo "</table>";
    }
    if ($_POST && $success) {
		header("location: not_nested.php");
		
	} 

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