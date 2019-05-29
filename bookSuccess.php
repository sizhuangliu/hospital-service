<!DOCTYPE html>
<html>
<head>
<title>Book Appointment Successful</title>
<link rel="stylesheet" href="main.css">
</head>

<body>
    <section id="banner">
        <div class="inner">



<p>
<?php
ini_set('session.save_path',realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/../session'));
session_start();

$db_conn = OCILogon("ora_e2k0b", "a25122145", "dbhost.ugrad.cs.ubc.ca:1522/ug");


// Connect Oracle...
if ($db_conn) {

echo "Your appointment id is " .$_SESSION["appid"] .".<br>";

echo "You have successfully made an appointment!";

}
?>
</p>

<p>
<?php
echo "Notice: Doctor's working hour is from 9am to 5pm! Please come during working hour!";
?>
</p>
<br>
<br>
<a href="index.php " title="Login instead"><h3>&lt;&lt;Back</h3></a >
<br>
<br>
<br>
<br>
<br>
<br>
<br>
</div>
</body>
</html>