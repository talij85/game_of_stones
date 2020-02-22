<?php

/* get the incoming ID and password hash */
$user = $_POST["userid"];
$last = $_POST["lastname"];
$pass = sha1($_POST["pswd"]);
$time=time();

/* establish a connection with the database */
include_once("admin/connect.php");
  
/* SQL statement to query the database */
$query = "SELECT * FROM Users WHERE name = '$user' AND lastname = '$last' AND password = '$pass'";

/* query the database */
$result = mysql_query($query);

/* Allow access if a matching record was found and cookies enabled, else deny access. */
if ($char = mysql_fetch_array($result))
{
$id = $char[id];
$user = $char[name];
$lastname = $char[lastname];
$mode = 0;
if ($_POST["mode"]) $mode = mysql_real_escape_string($_POST["mode"]);
setcookie("id", "$id", time()+99999999, "/");
setcookie("name", "$user", time()+99999999, "/");
setcookie("lastname", "$lastname", time()+99999999, "/");
setcookie("password", "$pass", time()+99999999, "/");
setcookie("mode", "$mode", time()+99999999, "/");
header("Location: $server_name/bio.php?time=$time");
exit;
}
elseif (!$GET[enabled])
{
$skipVerify = 1;
include('header.htm');
?>

<text class="littletext">

<br><br>
<?php
echo "<center><b>Access Denied:</b> No such matching Character and Password";
}
else
{
include('headerno.htm');
?>
<br><br>
<?php
echo "<center><b>You must have cookies enabled in order to log in.</b><br><br>The fact that you are viewing this message likely means that you do not.</center>";
?>
<br><br><center>This website will help you to enable your cookies<br><a href="http://scholar.google.com/cookies.html">Google's Help Website on Enabling Cookies</a>
<?php
} 

include('footer.htm');
?>