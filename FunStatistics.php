<!DOCTYPE html>
<html>
<head>
<title>Fun Statistcs</title>
<link rel="stylesheet" href="main.css">
</head>

<body>
    <section id="banner">
        <div class="inner">

<h1>Fun Statistics</h1>

</form>

<h4>You Can Find patient who have appointments with doctors from all different departments:</h4>
<form method="POST" action="FunStatistics.php">
   
<p><input type="submit" value="Check" name="findp1"></p> 
</form>

<br>
<a href="success.php" title="Home"><h3>&lt;&lt;Back</h3></a>
<br>
<br>

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
	if (array_key_exists('findp1', $_POST)) {
        $success = False;

        $all = "SELECT pid
                FROM (SELECT P.pid, D.dept
                FROM patient P, appointment A, doctor D
                WHERE A.pid = P.pid AND A.did = D.did ) tableTemp
                GROUP BY pid
                HAVING count(DISTINCT dept) = (SELECT COUNT(DISTINCT dept) FROM doctor)";

  		$result5 = executePlainSQL($all);

		
		if($result5){
            echo "<table>";
            echo "<tr><th>PatientID</th></tr>";
            
            while ($row = OCI_Fetch_Array($result5, OCI_BOTH)) {
               
                echo "<tr><td>" . $row[0] . "</td></tr>"; 
                 
            }
            echo "</table>"; 

        } else {
            echo "No patient has gone to all department yet!";
        }
        
    }


    if ($_POST && $success) {
		header("location: FunStatistics.php");
		
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