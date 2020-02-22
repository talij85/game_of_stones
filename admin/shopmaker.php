<html>
<head>
<title>Admin Recreate Shop Table</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<u>Resets the table "Shop"</u><br><br>
<?php
// Connect
include("connect.php");

// Drop Old Table
$query  = 'DROP TABLE Shop';
$result = mysql_query($query);
echo "<b>Results</b><br><br>Drop Old Table: $result";

// Create New Table
$query = 'CREATE TABLE Shop( '.
'id int NOT NULL AUTO_INCREMENT, '.
'name varchar(30), '.
'inventory TEXT,'.
'time int,'.
'PRIMARY KEY (id),'.
'UNIQUE id (id))';
$result = mysql_query($query);
echo "<br>Create New Table: $result";

$list=array (
array ( "leather armor","cheap",""),
array ( "short sword","tarnished",""),
array ( "hatchet","",""),
array ( "iron spear","",""),
array ( "short bow","",""),
array ( "cudgel","",""),
array ( "shield","",""),
array ( "quarter staff","",""),
array ( "hand axe","",""),
array ( "short sword","",""),
array ( "bone knife","",""),
array ( "buckler","sturdy",""),
array ( "talisman","andoran",""),
array ( "talisman","","of ogier"),
array ( "talisman","","of brigands"),
);
$list=serialize($list);
$time = time();
$sql = "INSERT INTO Shop (inventory,time) VALUES ('$list','$time')";
$result = mysql_query($sql,$db);


// Close Database
mysql_close($db);
?>

<br><br>
~Table reset and recreator for the shop~
</body>
</html>