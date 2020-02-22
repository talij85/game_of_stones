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
?>

<table border="1" cellpadding="10" rules="rows" cellspacing="0" bgcolor="#0f0f0f" bordercolor="#555555" width="90%">
  <tr>
    <td>
      <center><font class="littletext"><b><?php echo str_replace('-ap-','&#39;',$quest[name]) ?></b><font class="littletext"><br>
    </td>
  </tr>
</table>
<table border=0 cellpadding=0 cellspacing=5>
  <tr>
    <td align='right'>
      <font class="littletext">Type:
    </td>
    <td>
      <font class="littletext"><?php echo $qtype;?>
    </td>    
  </tr>
<?php if ($quest['offerer'] != '') { ?>
  <tr>
    <td align='right'>
      <font class="littletext">Offered By:
    </td>
    <td>
      <font class="littletext"><?php echo str_replace("_", " ", $quest['offerer']);?>
    </td>    
  </tr>
<?php } ?>
<?php if ($quest['num_avail'] > 0) { ?>
  <tr>
    <td align='right'>
      <font class="littletext">Available:
    </td>
    <td>
      <font class="littletext"><?php echo ($quest['num_avail']-$quest['num_done']);?>
    </td>    
  </tr>
<?php } ?>  
<?php if ($quest[expire] != -1) { ?>
  <tr>
    <td align='right'>
      <font class="littletext">Expires:
    </td>
    <td>
      <font class="littletext"><?php echo $extime;?>
    </td>    
  </tr>
<?php } ?>
  <tr>
    <td align='right'>
      <font class="littletext">Requirements:
    </td>
    <td>
      <font class="littletext"><?php echo $show_reqs;?>
    </td>    
  </tr>
  <tr>
    <td align='right'>
      <font class="littletext">Reward:
    </td>
    <td>
      <font class="littletext"><?php echo $reward;?>
    </td>    
  </tr>  
</table>
<br>
<table border="1" cellpadding="0" rules="rows" cellspacing="0" width="90%">
  <tr>
    <td>
      <center><font class="littletext"><p><i><?php echo $show_goals; ?></i><p>
    </td>    
  </tr>  
</table>


</body>
</html>