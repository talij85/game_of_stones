<html>
<head>
<title>GoS Reset</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<?php 
include("connect.php");
if (strtolower($name) != "the" && strtolower($lastname) != "creator")
{
  echo $name." ".$lastname;
  echo "Only the Creator has such powers!";
}
else
{
?>

<a href="reset.php">Reset Users</a><br/>
<a href="resetsoc.php">Reset Clans</a><br/>
<a href="resetquests.php">Reset Quests</a><br/>
<a href="resethordes.php">Reset Hordes</a><br/>
<a href="resetestates.php">Reset Estates</a><br/>
<a href="resetloc.php">Reset Locations</a><br/>
<a href="resetips.php">Reset IP_logs</a><br/>
<a href="resetprofs.php">Reset Profs</a><br/>
<a href="resetcontests.php">Reset Contests</a><br/>
<a href="resetnotes.php">Reset Notes</a><br/>
<?php } ?>

</body>
</html>