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

// establish a connection with the database
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/skills.php");
include_once("admin/itemFuncs.php");
include_once("map/mapdata/coordinates.inc");

$wikilink = "Vault";

if (!$char['society']) header("Location: $server_name/viewclans.php?autologin=$time");

$id=$char['id'];

$soc_name = $char['society'];

if (!$char['society']) header("Location: $server_name/viewclans.php?autologin=$time");

// LOAD SOCIETY TABLE
$query = "SELECT * FROM Soc WHERE name='$soc_name'";
$result = mysql_query($query);
$society = mysql_fetch_array($result);

$action = mysql_real_escape_string($_POST['action']);
$sortBy=  mysql_real_escape_string($_POST['sort']);

$upgrades= unserialize($society[upgrades]);
$maxsize = 50+10*$upgrades[0];
if ($sortBy != "") {  mysql_query("UPDATE Users SET sort_vault='".$sortBy."' WHERE id=$id"); $char[sort_vault]= $sortBy;}
$invSort = sortItems($char[sort_vault]);

$vid = 10000+$society[id];
$listsize = 0;
$iresult=mysql_query("SELECT * FROM Items WHERE society='".$society[id]."' ".$invSort);
while ($qitem = mysql_fetch_array($iresult))
{
  $itmlist[$listsize++] = $qitem;
}

$subleaders = unserialize($society['subleaders']);
$subs = $society['subs'];

$b = 0; 
if (strtolower($name) == strtolower($society[leader]) && strtolower($lastname) == strtolower($society[leaderlast]) ) 
{
  $b=1;
}
if ($subs > 0)
{
  foreach ($subleaders as $c_n => $c_s)
  {
    if (strtolower($name) == strtolower($c_s[0]) && strtolower($lastname) == strtolower($c_s[1]) )
    {
      $b=1;
    }
  }
}    

// ACTIONS
$good = 1;
if ($action==1) // withdraw
{
  $num_itmso=0;
  $iresult=mysql_query("SELECT * FROM Items WHERE owner='$id' AND type<15");
  while ($qitem = mysql_fetch_array($iresult))
  {
    $itmlistchar[$num_itmso++] = $qitem;
  }

  $x=0;
  $q=0;
  $dlist = "";
  while ($x < $listsize)
  {
    $tmpItm = mysql_real_escape_string($_POST[$x]);
    if ($tmpItm)
    {
      $sitem=mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE id='$tmpItm'"));
      if ($sitem[owner] == $vid)
      {
        $dlist[$q]=$sitem; 
        $q++;     
      }
      else
      {
        $message = "You cannot withdraw an item that is in use!";
        $good = 0;
      }
    }
    $x++;
  }
  if ($num_itmso+$q > $inv_max)
  {
    $good = 0;
    $message = "There is not enough room in your inventory!";
  }  
  if ($good && $q > 0)
  {
    $nowner= $char[id];
    for ($x=0; $x<$q; $x++)
    {
      $result = mysql_query("UPDATE Items SET society='".$society[id]."', owner='$nowner', last_moved='".time()."' WHERE id='".$dlist[$x][id]."'");
    }
    $s = "";
    if ($q > 1) $s ="s";    
    $message="Item$s withdrawn from clan vault";
  }
}
elseif ($action==2) // return
{
  $x=0;
  $q=0;
  $cheat=0;
  while ($x < $listsize)
  {
    $tmpItm =mysql_real_escape_string($_POST[$x]);
    if ($tmpItm)
    {
      $sitem=mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE id='$tmpItm'"));
      if ($b || ($sitem[owner] == $char['id']))
      {
        $q++;
        $result = mysql_query("UPDATE Items SET owner='".$vid."', last_moved='".time()."', istatus='0' WHERE id='".$tmpItm."'");
      }
      else $cheat++;
    }
    $x++;
  }
  if ($q)
  {
    $s = "";
    if ($q > 1) $s ="s";    
    if (!$cheat) $message="Item$s returned to clan vault";
    else $message="YOUR Item$s returned to clan vault";
  }
  elseif ($cheat) $message="You don't have permission to return any of those!";
}
elseif ($action==3) // sell
{
  if ($b == 0)
  {
    $message = "You're pretty good, but I try to cover my bases...";
    $good = 0;
  }
  else
  {
    $x=0;
    $z=0;
    $value=0;
    $q=0;
    while ($x < $listsize)
    {
      $tmpItm = mysql_real_escape_string($_POST[$x]);
      if ($tmpItm)
      {
        $sitem=mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE id='$tmpItm'"));
        if ($sitem['owner']== $vid)
        {
          if ($sitem['type'] > 13) $value += intval(0.25*$item_base[$sitem['name']][2]);
          else $value += intval(0.25*item_val(iparse($sitem,$item_base, $item_ix,$ter_bonuses)));
          $result5 = mysql_query("DELETE FROM Items WHERE id='$sitem[id]'");
          $q++;
        }
        else 
        {
          $z++;
        }
      }      
      $x++;
    }
    $listsize = $listsize - $q;  

    if ($good && $q > 0)
    {
      $s = "";
      if ($q >1) $s ="s";
      $clangold = $value + $society[bank];
      mysql_query("UPDATE Soc SET bank='$clangold' WHERE name='$soc_name'");
      if (!$z) $message="Item$s sold for ".displayGold($value);
      else $message="Non-used item$s sold for ".displayGold($value);
    }
    else if ($z) { $message="You cannot sell items that are in use!"; }
    else { $message = "You didn't select anything to sell!"; }
  }
}

// update item list
$itmlist='';
$listsize = 0;
$iresult=mysql_query("SELECT * FROM Items WHERE society='".$society[id]."' ".$invSort);
while ($qitem = mysql_fetch_array($iresult))
{
  $itmlist[$listsize++] = $qitem;
}

for ($i=0; $i<$listsize; ++$i)
{
  $vaultinfo[$i] = "<div class='panel panel-danger' style='width: 150px;'><div class='panel-heading'><h3 class='panel-title'>".ucwords($itmlist[$i][prefix]." ".$itmlist[$i][base])." ".str_replace("Of","of",ucwords($itmlist[$i][suffix]))."</h3></div><div class='panel-body abox' align='center'><img class='img-responsive hidden-xs img-optional-nodisplay' border='0' bordercolor='black' src='items/".str_replace(' ','',$itmlist[$i][base]).".gif'/>";
  $vaultinfo[$i] .= itm_info(cparse(iparse($itmlist[$i],$item_base, $item_ix, $ter_bonuses)));

  $vaultinfo[$i] .= "</div></div>";
}
if ($message == '') 
  $message = $listsize."/".$maxsize." items";

include('header.htm');

// INVENTORY
  $style="stle='border-width: 0px; border-bottom: 1px solid #333333'";
?>
  <div class="row">
    <div class="col-sm-12">
      <div class='row'>
        <form name="sortForm" action="vault.php" method="post">
          <input type="hidden" name="sort" id="sort" value="" />
          <input type="hidden" name="tab" value="1"/>
        </form>
        <form name="vaultForm" action="vault.php" method="post">
          <table class="table table-condensed table-responsive table-hover small solid-back">
            <tr>
              <th width="20">&nbsp;</td>
	      <th width="200"><a class="btn btn-danger btn-md btn-block" href="javascript:resortForm('0');">Item</a></th>
              <th width="100"><a class="btn btn-danger btn-md btn-block" href="javascript:resortForm('1');">Type</a></th>
              <th width="100"><a class="btn btn-danger btn-md btn-block" href="javascript:resortForm('7');">Condition</a></th>
              <th width="75"><a class="btn btn-danger btn-md btn-block" href="javascript:resortForm('2');">Points</a></th> 
              <th width="200"><a class="btn btn-danger btn-md btn-block" href="javascript:resortForm('4');">Location</a></th>
            </tr>
<?php
      $draw_some = 0;
      for ($itemtoblit = 0; $itemtoblit < $listsize; $itemtoblit++)
      {
        {
          // DRAW ITEM IN LIST
?>
            <tr>
<?php
          if ($b || ($itmlist[$itemtoblit][owner] == $vid) || ($itmlist[$itemtoblit][owner] == $char['id']))
          {
?>
              <td width="20" align="left"><input type="checkbox" name="<?php echo"$itemtoblit"; ?>" value="<?php echo $itmlist[$itemtoblit][id];?>"></td>
<?php            
          }
          else
          {
?>
              <td width="20">&nbsp;</td>
<?php
          }
          $itmStyle = "primary";
          if (!(($itmlist[$itemtoblit][owner] == $vid) || ($itmlist[$itemtoblit][owner] == $char['id'])))
          {
            $itmStyle = "danger";
          }
          else if ($itmlist[$itemtoblit][owner] == $char['id'])
          {
            $itmStyle = "info";
          }
?>            
              <td width="200" align='center' class="popcenter">         
                <button type="button" class="btn btn-<?php echo $itmStyle; ?> btn-xs btn-block btn-wrap link-popover" data-toggle="popover" data-html="true" data-placement="bottom" data-content="<?php echo $vaultinfo[$itemtoblit];?>"><?php echo iname($itmlist[$itemtoblit]); ?></button>
              </td>

<!-- DISPLAY ITEM TYPE -->
              <td width="100" align='center'>
<?php
              echo ucwords($item_type[$itmlist[$itemtoblit][type]]);
?>
              </td>
<!-- DISPLAY ITEM CONDITION -->
              <td width="100" align='center'>
<?php
              echo $itmlist[$itemtoblit][cond]."%";
?>
              </td>            
              <td width="75" align='center'> 
            <?php 
              $weapon = itp($itmlist[$itemtoblit][stats],$itmlist[$itemtoblit][type],$itmlist[$itemtoblit][cond]);
              $points = lvl_req($weapon, 100);
              //$points = lvl_req(iparse($itmlist[$itemtoblit],$item_base, $item_ix,$ter_bonuses),getTypeMod($skills,$itmlist[$itemtoblit][type]));
              echo $points."pts"; 
            ?>
              </td>
              <td width="200" align="left">
<!-- DIFFERENT ACTIONS -->
<?php 
                if ($itmlist[$itemtoblit][owner] == $vid) 
                { 
                   echo "Item is currently in vault";
                }
                else 
                {
                  $iown=mysql_fetch_array(mysql_query("SELECT id, name, lastname FROM Users WHERE id='".$itmlist[$itemtoblit][owner]."'"));
                   echo "Used by ".ucwords($iown[name])." ".ucwords($iown[lastname]);
                }
?>
              </td>
            </tr>
<?php
          $draw_some = 1;
        }
      }
?>

      
<?php
      if ($draw_some == 0) 
        echo "<tr><td colspan='6' align='center'><br/><b>Clan vault is empty!</b><br/><br/></td></tr>";
?>
          </table>

          <input type="hidden" name="action" value="0">
          <a data-href="javascript:submitFormVault('1');" data-toggle="confirmation" data-placement="top" title="Withdraw selected items?" class="btn btn-primary btn-sm btn-wrap">Withdraw Selected</a>
          <a data-href="javascript:submitFormVault('2');" data-toggle="confirmation" data-placement="top" title="Return selected items?" class="btn btn-danger btn-sm btn-wrap">Return Selected</a>
<?php
  if ($b != 0)
  {
?>
          <a data-href="javascript:submitFormVault('3');" data-toggle="confirmation" data-placement="top" title="Sell selected items?" class="btn btn-success btn-sm btn-wrap">Sell Selected</a>
<?php
  }
?>

        </form>
      </div>
    </div>
  </div>
  <script type='text/javascript'>
  function submitFormVault(act)
  {
    document.vaultForm.action.value= act;
    document.vaultForm.submit();
  }
  </script>  
<?php
  include('footer.htm');
?>