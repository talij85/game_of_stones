<?php

include("connect.php");

$result = mysql_query("SELECT id, name, lastname, email FROM Users WHERE 1",$db);

$elist = '';

while ($char = mysql_fetch_array($result))
{
  $elist[$char[email]] = 1;
}

foreach ($elist as $addy => $tmp)
{
  echo $addy.", ";
}

?>