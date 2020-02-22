<link REL="StyleSheet" TYPE="text/css" HREF="stylea.css">
<html>
<head>

<?php
include_once('admin/connect.php');
include_once("admin/itemFuncs.php");
include_once("admin/questFuncs.php");
include_once("admin/userdata.php");
include_once("admin/locFuncs.php");
$id = mysql_real_escape_string($_GET['id']);
$goodevil=$char['goodevil'];
if ($goodevil >1) $goodevil= 1;
?>

<title>Quest Information</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body style="background-color: black; font-family: Verdana, Helvetica, Arial, sans-serif; font-size: 12px; color: #C6CCD8; margin: 0px; padding: 0px">
<font face="VERDANA"><font class="bigtext"><center><br>

<?php
$quest =  mysql_fetch_array(mysql_query("SELECT * FROM Quests WHERE id=$id"));

function getQuestInfo ($quest)
{
  $qtype = $quest_type[$quest[type]];
  if ($quest[expire] != -1)
  {
    $expire = ($quest[expire]*3600) - time();
    if ($expire < 3600) $extime= number_format($expire/60)." minutes";
    elseif ($expire < 86400) $extime = number_format($expire/3600)." hours";
    else $extime = number_format($expire/86400)." days";
  }
  if ($quest[reqs] != "0")
  {
    $reqs=cparse($quest[reqs],0);
    $show_reqs=  show_quest_reqs($reqs);
  }
  else 
  {
    $show_reqs = "None";
  }
  $goals = unserialize($quest['goals']);
  $show_goals=  show_quest_goals($item_base,$item_type_pl,$enemy_list,$goodevil,$goals,$quest['type'], $quest['offerer'], $quest['special'], $npc_nation_data);
  $qreward= unserialize($quest[reward]);
  if ($qreward[0]=="G")
  { $reward = displayGold($qreward[1]); }
  elseif ($qreward[0] == "LI")
  { $reward = "Leveled ".$item_type[$qreward[1]]; }
  elseif ($qreward[0] == "I")
  {
    $item = explode("|",$qreward[1]);
    $reward = ucwords($item[1])." ".ucwords($item[0])." ".ucwords($item[2]);
  }
  elseif ($qreward[0] == "GP")
  {
    $reward = $qreward[1]."% Value";
  }
  
  $questInfo = "";

  $questInfo .= "<table border=0 cellpadding=0 cellspacing=5>";
  $questInfo .= "<tr><td align='right'>Type:</td><td>".$qtype."</td></tr>";
  if ($quest['offerer'] != '') 
  {
    $questInfo .= "<tr><td align='right'>Offered By:</td><td>".str_replace("_", " ", $quest['offerer'])."</td></tr>";
  }
  if ($quest['num_avail'] > 0) 
  {
    $questInfo .= "<tr><td align='right'>Available:</td><td>".($quest['num_avail']-$quest['num_done'])."</td></tr>";
  }
  if ($quest[expire] != -1)
  {
    $questInfo .= "<tr><td align='right'>Expires:</td><td>".$extime."</td></tr>";
  }
  $questInfo .= "<tr><td align='right'>Requirements:</td><td>".$show_reqs."</td></tr>";
  $questInfo .= "<tr><td align='right'>Reward:</td><td>".$reward."</td></tr>";
  $questInfo .= "</table><br/>";
  $questInfo .= "<table cellpadding='0' rules='rows' cellspacing='0' width='90%'>";
  $questInfo .= "<tr><td align='center'><i>".$show_goals."</i></td></tr>";
  $questInfo .= "</table>";
  
  return $questInfo;
}
?>

</body>
</html>