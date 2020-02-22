<link REL="StyleSheet" TYPE="text/css" HREF="stylea.css">
<html>
<head>

<?php
include_once("admin/itemFuncs.php");
include_once("admin/displayFuncs.php");
$base=str_replace("_"," ",$_GET['base']);
$prefix=str_replace("_"," ",$_GET['prefix']);
$suffix=str_replace("_"," ",$_GET['suffix']);
$lvl=$_GET['lvl'];
$sex=$_GET['sex'];
$cond = 100;
if ($_GET['cond'] != '') $cond = $_GET['cond'];
if ($_GET['gender']) $sex=$_GET['gender'];


?>

<title><?php echo ucwords($prefix)." ".ucwords($base)." ".str_replace("Of","of",ucwords($suffix)); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body style="background-color: black; font-family: Verdana, Helvetica, Arial, sans-serif; font-size: 12px; color: #C6CCD8; margin: 0px; padding: 0px">
<font face="VERDANA"><font class="bigtext"><center><br>

<?php
// RESOLVE all common terms and place in array
$itm= array($base,$prefix,$suffix,'','',$cond);

$weapon = itp($item_base[$base][0]." ".$item_ix[$prefix]." ".$item_ix[$suffix],$item_base[$base][1]);
$weapon = $weapon." ".getConditionMod($item_base[$base][1], $cond);
$weapon = $weapon." ".getTerMod($ter_bonuses,$item_base[$base][1],$prefix,$suffix,$item_base[$base][2]);

if ($item_base[$base][1] < 14) $worth = item_val($weapon);
else $worth = $item_base[$base][2];

if ($_GET[data]) echo "<font class=littletext>".$weapon;
$char_stats = cparse($weapon,0);


$type_n=ucwords($item_type[$item_base[$base][1]]);
$lvl_need = lvl_req($weapon,100);

$bonus="<font class='littletext_f'><center>".$type_n."<br><font class='foottext_f'>";
$bonus .= displayGold($worth)."<br>";
$bonus .= $cond."%";
if ($item_base[$base][1] < 14) $bonus .= "&nbsp;&nbsp;::&nbsp;&nbsp;$lvl_need pts";
$bonus .= "<font class=littletext><br><p align='left'>";
$bonus .= itm_info($char_stats);
if ($item_base[$base][1] == 8 )
{
  $bonus .= "<A HREF='weaveStats.php' target='_blank'>Click here to see current weave stats</A>";
}

$pic_name = str_replace(" ","",$base);
if ($item_base[$base][1] >14) // consumables
{
  $pic_name = "";
  $name_words = explode(" ",$base);
  for ($x=1; $x<count($name_words); $x++)
  {
    $pic_name .= $name_words[$x];
  }
}
?>

<table border="1" cellpadding="10" rules="rows" cellspacing="0" bgcolor="#0f0f0f" bordercolor="#555555" width="90%"><tr><td>
<center><font class="littletext"><b><?php echo ucwords($prefix)." ".ucwords($base)." ".str_replace("Of","of",ucwords($suffix)); ?></b><font class="littletext"><br>
</td></tr></table><br>
<img border="0" bordercolor="black" src="items/<?php echo $pic_name; ?>.gif"><br>
<br>
<table border=0 cellpadding=0 cellspacing=0><tr><td>
<font class="littletext"><?php echo $bonus; ?>
</td></tr></table>

</body>
</html>