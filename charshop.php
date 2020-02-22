<?php
/* establish a connection with the database */
include("admin/connect.php");
include("admin/userdata.php");
include("admin/itemFuncs.php");
include("map/mapdata/coordinates.inc");

$message = "<b>$name $lastname's Shop</b>";
$s_costg=abs(intval($_REQUEST['costg']));
$s_costs=abs(intval($_REQUEST['costs']));
$s_costc=abs(intval($_REQUEST['costc']));
$s_cost= $s_costg*10000+$s_costs*100+$s_costc;
$s_item=mysql_real_escape_string($_REQUEST['itemsell']);
$s_delete=mysql_real_escape_string($_REQUEST['delete']);
$s_doit=mysql_real_escape_string($_REQUEST['doit']);
$s_collect=mysql_real_escape_string($_REQUEST['collect']);
$time=time();
$itmlist = unserialize($char['itmlist']);

$wikilink = "Market";

// SELECT AMOUNT TO SELL FOR
if (!($s_cost > 0) && $_GET[sellit] && $itmlist[$s_item][0] && $location_array[$char['location']][2])
{
  include('header.htm');
  $worth = item_val(itp($item_base[$itmlist[$s_item][0]][0]." ".$item_ix[$itmlist[$s_item][1]]." ".$item_ix[$itmlist[$s_item][2]],$item_base[$itmlist[$s_item][0]][1]));
?>
  <font class="littletext"><center><b>Sell <?php echo ucwords($itmlist[$s_item][1]." ".$itmlist[$s_item][0]." ".$itmlist[$s_item][2]); ?> in the Market</b><font class="littletext"><br>

  <img border="0" bordercolor="black" src="items/<?php echo str_replace(" ","",$itmlist[$s_item][0]); ?>.gif">
  <br>

  <form action="charshop.php?itemsell=<?php echo "$s_item"; ?>" method="post">
    <font class="littletext">
    <input type="text" name="cost" value="<?php echo $worth; ?>" id="cost" size="10" maxlength="9" class="form" /> gold 
    <input type="submit" name="submit" value="Offer" class="form" />
    <?php
// ACTUAL CHAR SHOP
}
else
{

// COLLECT GOLD

if ($s_collect)
{
  $id = $char[id];
  $collectgold = $char[gold] + $char[shopgold];
  $querya = "UPDATE Users SET gold='$collectgold', shopgold='0' WHERE id='$id' ";
  $result = mysql_query($querya);
  header("Location: $server_name/charshop.php?time=$curtime");
}

// DELETE FROM SELL LIST

if ($s_doit)
{
  $itmlist = unserialize($char['itmlist']);
  $resultb = mysql_query("SELECT * FROM Market WHERE id='$s_delete' ORDER BY cost DESC", $db);
  $listchar = mysql_fetch_array($resultb);
  if ($char['location']==$listchar['location'] && $char['name']==$listchar['sname'] && $char['lastname']==$listchar['slastname'])
  {
    $query = "DELETE FROM Market WHERE id='$s_delete'";
    $result5 = mysql_query($query, $db);
    $message = "Offer Removed";
    $z = 0;
    while (($itmlist[$z][0] != $listchar[base] || $itmlist[$z][1] != $listchar[prefix] || $itmlist[$z][2] != $listchar[suffix] || $itmlist[$z][3] != -1) && $z < count($itmlist)) $z++;

    if ($itmlist[$z][3] == -1)
    {
      $itmlist[$z][3] = 0;
      $id = $char[id];
      $itmlist2 = serialize($itmlist);
      $query = "UPDATE Users_data SET itmlist='$itmlist2' WHERE id='$id' ";
      $result = mysql_query($query);
    }
  }
}

$query = "SELECT * FROM Market WHERE sname='$name' AND slastname='$lastname' ORDER BY cost DESC";
$resultb = mysql_query($query, $db);
$numchar = mysql_num_rows($resultb);

// ADD TO SELL LIST
if ($s_cost && $numchar < 25 && $location_array[$char['location']][2])
{
  $itmlist = unserialize($char['itmlist']);
  $itmname = iname($s_item,$itmlist);
  $location=$char['location'];
  $base = $itmlist[$s_item][0];
  $prefix = $itmlist[$s_item][1];
  $suffix = $itmlist[$s_item][2];
  $itmtype = $item_base[$itmlist[$s_item][0]][1];
  $itmbonus = clbc(itp($item_base[$itmlist[$s_item][0]][0]." ".$item_ix[$itmlist[$s_item][1]]." ".$item_ix[$itmlist[$s_item][2]],$itmtype));
  if ($itmlist[$s_item][3] != -1 && $itmlist[$s_item][3] != -2 && $itmlist[$s_item][0] != '')
  {
    $itmlist[$s_item][3] = -1;
    $id = $char[id];
    $itmlist2 = serialize($itmlist);
    $query = "UPDATE Users_data SET itmlist='$itmlist2' WHERE id='$id' ";
    $result = mysql_query($query);
    $query = "INSERT INTO Market (name, base, prefix, suffix, type, bonus, cost, sname, slastname, location) VALUES ('$itmname', '$base', '$prefix', '$suffix', '$itmtype', '$itmbonus', '$s_cost', '$name', '$lastname', '$location')";
    $result = mysql_query($query, $db);
    $query = "SELECT * FROM Market WHERE sname='$name' AND slastname='$lastname' ORDER BY cost DESC";
    $resultb = mysql_query($query, $db);
    $numchar = mysql_num_rows($resultb);
  }
  $message = "Offer Added";
}
elseif ($numchar >= 25) $message = "You cannot offer more than twenty-five items at a time";

//HEADER
include('header.htm');
?>

<font face="VERDANA"><font class="littletext"><center>

<!-- SHOP TILL -->
<center><font class="foottext">
<table class="blank" border="0"><tr>
<?php
  if ($char[shopgold]) {
?>
  <td><img src="images/till.gif"></td>
<?php } ?>
  <td width="10">&nbsp;</td>
  <td><font class="foottext">
<?php
  if ($char[shopgold]) 
  {
    echo "There is ".number_format($char[shopgold])." g in your shop till ";
    if ($char[shopgold]) echo "[<a href='charshop.php?time=$time&collect=1'>Collect</a>]";
  }
  else echo "&nbsp;";
?>
  </td>
</tr></table>
<br><br>

<center><font class="littletext">

<!-- DISPLAY ITEMS FOR SALE -->

<table width="550" cellspacing="0" border="0" cellpadding="5">
  <tr><td width="25">&nbsp;</td>
    <td width="200"><font class="littletext"><b><center>Item</td>
    <td width="100"><font class="littletext"><b><center>Cost</b></td>
    <td width="100"><font class="littletext"><center><b>Location</b></td>
    <td width="50">&nbsp;</td>
    <td width="50"><font class="littletext"><center><b>remove</b></td>
    <td width="25">&nbsp;</td>
  </tr>
</table>
<?php
  echo $div_img;
  $x=0;
  while ( $listchar = mysql_fetch_array( $resultb ))
  {
?>
<table width="550" cellspacing="0" border="0" cellpadding="5" onMouseOver="this.bgColor='#111111';" onMouseOut="this.bgColor='#000000';">
  <tr>
    <td width="25">&nbsp;</td>
    <td width="200"><font class="foottext"><center><b><A HREF="javascript:popUp('itemstat.php?<?php echo "base=".$listchar[base]."&prefix=".$listchar[prefix]."&suffix=".$listchar[suffix]; ?>')"><?php echo $listchar['name']; ?></a></b></td>
    <td width="100"><font class="foottext"><center><?php echo displayGold($listchar['cost']); ?></center></td>
    <td width="100"><font class="foottext"><center><?php echo str_replace('-ap-','&#39;',$listchar['location']); /*ucwords($item_type[$item_base[$listchar['base']][1]]);*/ ?></td>
    <td width="50">&nbsp;</td>
    <td width="50" valign="middle" align="center"><font class="foottext"><?php if ($char['location']==$listchar['location']) { ?><a href="charshop.php?doit=1&delete=<?php echo $listchar['id']; ?>"><img border="0" src="images/delete.gif"></a><?php } ?></td>
    <td width="25">&nbsp;</td>
  </tr>
</table>
<?php
    echo $div_img;
    $x++;
  }
  if ( $x == 0 ) {echo "<font class=littletext><center><b><br>Nothing for Sale<br><br>$div_img";}
?>
</center>

<?php
}
include('footer.htm');
?>