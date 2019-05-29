<!DOCTYPE html>
<html>
<head>
<title>W Hospital</title>
<link rel="stylesheet" href="main.css">
</head>

<body>
<section id="banner">
        <div class="inner">
	

	<h1>Welcome</h1>

        <form method="POST" action = Home_2.php>
                <input type="submit" name="Check Your Schedule" value="Check Your Schedule">
        </form>

        <br>
        
        <form method="POST" action = Home_1.php>
                <input type="submit" name="Search Patient Info" value="Search Patient Info">
        </form>

        <br>

        <form method="POST" action = AddMedicalRecord.php>
                <input type="submit" name="Add Medical Record" value="Add Medical Record">
        </form>

        <br>

        <form method="POST" action = AddPatientToRoom.php>
                <input type="submit" name="Add Patient To Room" value="Add Patient To Room">
        </form>

        <br>

        <form method="POST" action = searchp.php>
                <input type="submit" name="Search Patient Resident Record" value="Search Patient Resident Record">
        </form>

        <br>

        <form method="POST" action = not_nested.php>
                <input type="submit" name="Check Statistics" value="Check Statistics">
        </form>

        <br>

         <form method="POST" action = FunStatistics.php>
                <input type="submit" name="Fun Statistics" value="Fun Statistics">
        </form>	

        <br>
                <a href="index.php" title="Home"><h3>&lt;&lt;Back</h3></a>
        </div>
	
</body>

</html>
