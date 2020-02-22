<?php
function array_delel($array,$del)
{
  $arraycount1=count($array);
  $z = $del;
  while ($z < $arraycount1) {
    $array[$z] = $array[$z+1];
    $z++;
  }
  array_pop($array);
  return $array;
}

function delete_blank($array)
{
  $new_array='';
  foreach ($array as $key => $value)
  {
    if ($value[0]) $new_array[$key]=$value;
  }
  return $new_array;
}

/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");
$doit=mysql_real_escape_string($_GET['doit']);
$id=$char[id];


$soc_name = $char['society'];
$society = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$soc_name' "));

$inCB = inClanBattle($society[id]);
// LEAVE CLAN
if ($doit == 1 && $society[id] && $inCB == 0)
{
  removeFromClan($char);
  header("Location: $server_name/viewclans.php");
  exit;
}
else
{
?>
<?php
  header("Location: $server_name/clan.php?inCB=1");
  exit;
}
  
?>