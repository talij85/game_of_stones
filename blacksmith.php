<?php

/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/jobFuncs.php");
include_once("admin/locFuncs.php");
include_once("admin/skills.php");
include_once("admin/charFuncs.php");
include_once("admin/duelFuncs.php");
include_once("admin/equipped.php");

// Check if city has been destroyed. If so, don't display anything.
if (!$location[isDestroyed])
{
include_once("map/mapdata/coordinates.inc");
if ($location_array[$char['location']][2]) $is_town=1;
else $is_town=0;

$wikilink = "Blacksmith";

$time=time();
$listsize=0;

$soc_name = $char[society];
$society = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$soc_name' "));

$bonuses1 = $stomach_bonuses1." ".$stamina_effect1." ".$clan_bonus1." ".$skill_bonuses1." ".$clan_building_bonuses;
$skills = $bonuses1;

$y = 0;
$a = "";
$b = "";
$c = "";
$d = "";
$e = "";
$a = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus='1' AND type<15"));
$b = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus='2' AND type<15"));
$c = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus='3' AND type<15"));
$d = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus='4' AND type<15"));
$e = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus='5' AND type<15"));
$estats = getstats($a,$b,$c,$d,$e,$skills);
$skills = $skills." ".$estats[0];
$pts_tot = 0;
if ($a != "") $pts_tot += (lvl_req($estats[1], getTypeMod($skills,$a[type])));
if ($b != "") $pts_tot += (lvl_req($estats[2], getTypeMod($skills,$b[type])));
if ($c != "") $pts_tot += (lvl_req($estats[3], getTypeMod($skills,$c[type])));
if ($d != "") $pts_tot += (lvl_req($estats[4], getTypeMod($skills,$d[type])));      
if ($e != "") $pts_tot += (lvl_req($estats[5], getTypeMod($skills,$e[type])));

if ($pts_tot != $char['used_pts'])
{
  $char['used_pts'] = $pts_tot;
  $result = mysql_query("UPDATE Users SET used_pts='$pts_tot' WHERE id=$id");
}

$iresult=mysql_query("SELECT * FROM Items WHERE owner='$id' AND type<15");
while ($qitem = mysql_fetch_array($iresult))
{
  $itmlist[$listsize++] = $qitem;
}

$jobs = unserialize($char[jobs]);
$pro_stats=cparse(getAllJobBonuses($jobs));

$loc_query = $char['location'];

$rv = $pro_stats[rV] + $town_bonuses[rV] + $town_bonuses[tV];
$myPoints = $char[equip_pts]-$char[used_pts];

$ustats = mysql_fetch_array(mysql_query("SELECT * FROM Users_stats WHERE id='$id'"));

$action = mysql_real_escape_string($_POST['action']);

if ($action > 0 && $is_town)
{
  $total = 0;
  $itm=0;
  $overeq = 0;
  $itemp = "";
  $q=0;
  
  // Repair Selected
  if ($action == 1)
  {
    while ($itm < $listsize)
    {
      $tmpItm = mysql_real_escape_string($_POST[$itm]);
      if ($tmpItm)
      {
        $sitem=mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE id='$tmpItm'"));
        if ($sitem[id])
        {    
          $itemp[$q] = $sitem;
          $itemp[$q][cond]=100;
          $repaired = iparse($itemp[$q] ,$item_base, $item_ix,$ter_bonuses);
          $worth = item_val($repaired);
          $cost = intval($worth*((100-$sitem[cond])/100)/2);
          $total += $cost;
          $weapon = iparse($sitem,$item_base, $item_ix,$ter_bonuses);
          if ($sitem[istatus] > 0)
          {
            $points = lvl_req($weapon, getTypeMod($skills,$sitem[type]));
            $rpoints = lvl_req($repaired, getTypeMod($skills,$sitem[type]));
            if ($rpoints > ($char[equip_pts]/2)) $overeq = 1;
            $myPoints -= ($rpoints-$points);
          }
          $q++;
        }
      }
      $itm++;
    }
  }
  // Repair Equipped
  else if ($action == 2)
  {
    $eresult = mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus>0 AND type<15");
    while ($eitem = mysql_fetch_array($eresult))
    {   
      $itemp[$q] = $eitem;
      $itemp[$q][cond]=100;
      $repaired = iparse($itemp[$q] ,$item_base, $item_ix,$ter_bonuses);
      $worth = item_val($repaired);
      $cost = intval($worth*((100-$eitem[cond])/100)/2);
      $total += $cost;
      $weapon = iparse($eitem,$item_base, $item_ix,$ter_bonuses);
      $points = lvl_req($weapon, getTypeMod($skills,$eitem[type]));
      $rpoints = lvl_req($repaired, getTypeMod($skills,$eitem[type]));
      if ($rpoints > ($char[equip_pts]/2)) $overeq = 1;
      $myPoints -= ($rpoints-$points);
      $q++;
    }
  }
  // Repair Unequipped
  else if ($action == 3)
  {
    $uresult = mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus<1 AND type<15");
    while ($uitem = mysql_fetch_array($uresult))
    {   
      $itemp[$q] = $uitem;
      $itemp[$q][cond]=100;
      $repaired = iparse($itemp[$q] ,$item_base, $item_ix,$ter_bonuses);
      $worth = item_val($repaired);
      $cost = intval($worth*((100-$uitem[cond])/100)/2);
      $total += $cost;
      $weapon = iparse($uitem,$item_base, $item_ix,$ter_bonuses);
      $q++;
    }
  }

  $town_share = $total*.25;
  if ($rv) $total = floor($total*((100+$rv)/100));
  if ($total <= $char[gold])
  {
    if (!$overeq)
    {
      if ($myPoints >= 0)
      {
        for ($x=0; $x<$q; $x++)
        {
          $result = mysql_query("UPDATE Items SET cond='100' WHERE id='".$itemp[$x][id]."'");
          $ustats[items_repaired]++;
        }
        $char[gold] -= $total;
        $location[bank] += $town_share;
        $message="Items repaired!";
        $char[used_pts] = $char[equip_pts]-$myPoints;
        $ustats[spent_repair]+= $total;
        if ($society[id]) updateSocRep($society, $location[id], $total);
        $query = mysql_query("UPDATE Locations SET bank='$location[bank]' WHERE name='$loc_query'");
        $query = mysql_query("UPDATE Users SET gold='$char[gold]', used_pts='$char[used_pts]' WHERE id=$id");
        $query = mysql_query("UPDATE Users_stats SET items_repaired='".$ustats['items_repaired']."', spent_repair='".$ustats['spent_repair']."' WHERE id=$id");
      }
      else 
      { 
        $message = "You don't have enough equip points to repair all your gear!";
        $myPoints = $char[equip_pts]-$char[used_pts];
      }
    }
    else 
    { 
      $message = "Repairing one of your equipped items makes it too high to remain equipped!";
      $myPoints = $char[equip_pts]-$char[used_pts];
    }
  }
  else
  {
    $message = "You don't have enough coin!";
  }
}

$listsize=0;
$iresult=mysql_query("SELECT * FROM Items WHERE owner='$id' AND type<15");
while ($qitem = mysql_fetch_array($iresult))
{
  $itmlist[$listsize++] = $qitem;
}

if ($message == '') $message = "Welcome to the Local Blacksmith!";

//HEADER
if (!$is_town) $message = "There are no businesses in ".str_replace('-ap-','&#39;',$char['location']);

if ($mode != 1) 
{
  $town_img_name = str_replace(' ','_',strtolower($char['location']));
  $town_img_name = str_replace('&#39;','',strtolower($town_img_name));
  $bg = "background-image:url('images/townback/".$town_img_name.".jpg'); ";
}
include('header.htm');

if ($is_town)
{
$style="style='border-width: 0px; border-bottom: 1px solid #333333'";

?>
<form id="itemForm" name="itemForm" action="blacksmith.php" method="post">
    <center>
    <b>Equip Points:</b> <?php echo $char[used_pts]." / ".$char[equip_pts];?><br/>
    <b>Equip Points not used: <?php echo ($char[equip_pts]-$char[used_pts]); ?></b><br/><br/>
    <table class='table table-responsive table-striped table-hover small solid-back'>
      <tr>
        <th style="align:'left';"><input type="checkbox" name="toggler" onClick="javascript:checkedAll()"/></td>
	<th>Equipped Item</td>
	<th class='hidden-xs'>Condition</th>
	<th class='visible-xs'>Cond.</th>
	<th>Cost</th>
	<th class='hidden-xs'>Points</th>
	<th class='visible-xs'>Pts</th>
	<th class='hidden-xs'>Repaired</th>
	<th class='hidden-xs'>Increase</th>
	<th class='visible-xs'>Inc.</th>
      </tr>  
      
<?php
      $draw_somee = 0;
      $rtotal = 0;
      $rinc = 0;
      for ($itemtoblit = 0; $itemtoblit < $listsize; $itemtoblit++)
      {
        if ($itmlist[$itemtoblit][istatus] > 0 )
        {
        if (($itmlist[$itemtoblit][type] < 12 && $itmlist[$itemtoblit][type] != 8 && $itmlist[$itemtoblit][cond] < 100))
        {
          // DRAW ITEM IN LIST
?>
          <tr class='listtab'>          
            <td align='center'><input type="checkbox" name="<?php echo $itemtoblit; ?>" value="<?php echo $itmlist[$itemtoblit][id]; ?>"/></td>
<?php
          $weapon = iparse($itmlist[$itemtoblit],$item_base, $item_ix,$ter_bonuses);
          $copy = $itmlist[$itemtoblit];
          $copy[cond]=100;
          $repaired = iparse($copy,$item_base, $item_ix,$ter_bonuses);          
          $iclass="btn-primary";
          if (lvl_req($weapon, getTypeMod($tskills,$itmlist[$itemtoblit][type])) > $char['equip_pts']/2) $iclass = "btn-danger";
          if ($itmlist[$itemtoblit][society] != 0) $iclass = "btn-warning";
          if ($itmlist[$itemtoblit][type] == 1 && $char[nation] == 1) $iclass = "btn-danger";
          $invinfo = "<div class='panel panel-info' style='width:150px'><div class='panel-heading'><h3 class='panel-title'>".ucwords($itmlist[$itemtoblit][prefix]." ".$itmlist[$itemtoblit][base])." ".str_replace("Of","of",ucwords($itmlist[$itemtoblit][suffix]))."</h3></div><div class='panel-body abox' align='center'><img class='img-responsive hidden-xs img-optional-nodisplay' border='0' bordercolor='black' src='items/".str_replace(' ','',$itmlist[$itemtoblit][base]).".gif'/>";
          $invinfo .= itm_info(cparse($weapon));
          $invinfo .= "</div></div>";          
?>
            <td align='center' class="popcenter">
              <button type="button" class="btn <?php echo $iclass; ?> btn-xs btn-block btn-wrap link-popover" data-toggle="popover" data-html="true" data-placement="bottom" data-content="<?php echo $invinfo;?>"><?php echo iname($itmlist[$itemtoblit]); ?></button>
            </td>

<!-- DISPLAY ITEM CONDITION -->
            <td align='center'>
<?php
              echo $itmlist[$itemtoblit][cond]."%";
?>
            </td>

<!-- STATUS -->
            <td align='center'>
<?php
              $worth = item_val($repaired);
              $cost = intval($worth*((100-$itmlist[$itemtoblit][cond])/100)/2);
              if ($rv) $cost = floor($cost*((100+$rv)/100));
              echo displayGold($cost);
              $rtotal += $cost;
?>
            </td>
            <td align='center'>
<?php
              $points = lvl_req($weapon, getTypeMod($skills,$itmlist[$itemtoblit][type]));
              echo $points."pts";
?>
            </td>
            <td align='center' class='hidden-xs'>
<?php
              $rpoints = lvl_req($repaired, getTypeMod($skills,$itmlist[$itemtoblit][type]));
              if ($rpoints > ($char[equip_pts]/2)) $red = "text-danger"; else $red = "";
              echo "<font class='".$red."'>".$rpoints." pts</font>";
?>
            </td>
            <td align='center'>
<?php
              $inc = $rpoints-$points;
              $rinc += $inc;
              if ($rinc > $myPoints) $red = "text-danger"; else $red = "";
              echo "<font class='".$red."'>".$inc." pts</font>";
?>
            </td>         
          </tr>
<?php
          $draw_somee = 1;
        }
        }
      }

      if ($draw_somee == 0) 
        echo "<tr><td colspan='7' align='center'><p><b>You have no equipped items to repair</b></p></td></tr>";
      else
        echo "<tr>";
        echo "<td></td>";
        echo "<td align='center'><b>Total Equipment:</b></td>";
        echo "<td></td>";
        echo "<td align='center'>".displayGold($rtotal)."</td>";
        echo "<td></td>";
        echo "<td class='hidden-xs'></td>";
        echo "<td align='center'><b>".$rinc." pts</b></td>";
        echo "</tr>";
?>
      </tr>
    </table>
    <table class='table table-responsive table-striped table-hover small solid-back'>
      <tr>
        <th align='left'></td>
	<th>Item</th>
	<th class='hidden-xs'>Condition</th>
	<th class='visible-xs'>Cond.</th>
	<th>Cost</th>
	<th class='hidden-xs'>Points</th>
	<th class='visible-xs'>Pts</th>
	<th class='hidden-xs'>Repaired</th>
	<th class='hidden-xs'>Increase</th>
	<th class='visible-xs'>Inc.</th>
      </tr>  
<?php
      $draw_some = 0;
      $itotal = 0;
      for ($itemtoblit = 0; $itemtoblit < $listsize; $itemtoblit++)
      {
        if ($itmlist[$itemtoblit][istatus] == 0 || $itmlist[$itemtoblit][istatus] == -2)
        {
        if (($itmlist[$itemtoblit][type] < 12 && $itmlist[$itemtoblit][type] != 8 && $itmlist[$itemtoblit][cond] < 100))
        {
          // DRAW ITEM IN LIST
?>
          <tr>          
            <td align='center'><input type="checkbox" name="<?php echo $itemtoblit; ?>" value="<?php echo $itmlist[$itemtoblit][id]; ?>"/></td>
<?php
          $weapon = iparse($itmlist[$itemtoblit],$item_base, $item_ix,$ter_bonuses);
          $copy = $itmlist[$itemtoblit];
          $copy[cond]=100;
          $repaired = iparse($copy,$item_base, $item_ix,$ter_bonuses);

          $iclass="btn-primary";
          if (lvl_req($weapon, getTypeMod($tskills,$itmlist[$itemtoblit][type])) > $char['equip_pts']/2) $iclass = "btn-danger";
          if ($itmlist[$itemtoblit][society] != 0) $iclass = "btn-warning";
          if ($itmlist[$itemtoblit][type] == 1 && $char[nation] == 1) $iclass = "btn-danger";          
          $invinfo = "<div class='panel panel-warning' style='width:150px'><div class='panel-heading'><h3 class='panel-title'>".ucwords($itmlist[$itemtoblit][prefix]." ".$itmlist[$itemtoblit][base])." ".str_replace("Of","of",ucwords($itmlist[$itemtoblit][suffix]))."</h3></div><div class='panel-body abox' align='center'><img class='img-responsive hidden-xs img-optional-nodisplay' border='0' bordercolor='black' src='items/".str_replace(' ','',$itmlist[$itemtoblit][base]).".gif'/>";
          $invinfo .= itm_info(cparse($weapon));
          $invinfo .= "</div></div>";          
?>
            <td align='center' class="popcenter">
              <button type="button" class="btn <?php echo $iclass; ?> btn-xs btn-block btn-wrap link-popover" data-toggle="popover" data-html="true" data-placement="bottom" data-content="<?php echo $invinfo;?>"><?php echo iname($itmlist[$itemtoblit]); ?></button>
            </td>

<!-- DISPLAY ITEM CONDITION -->
            <td align='center'>
<?php
              echo $itmlist[$itemtoblit][cond]."%";
?>
            </td>

<!-- STATUS -->
            <td align='center'>
<?php
              $worth = item_val($repaired);
              $cost = intval($worth*((100-$itmlist[$itemtoblit][cond])/100)/2);
              if ($rv) $cost = floor($cost*((100+$rv)/100));
              echo displayGold($cost);
              $itotal += $cost;
?>
            </td>
            <td align='center'>
<?php
              $points = lvl_req($weapon, getTypeMod($skills,$itmlist[$itemtoblit][type]));
              if ($points > ($char[equip_pts]/2)) $red = "text-danger"; else $red = "";
              echo "<font class='".$red."'>".$points." pts";
?>
            </td>
            <td align='center' class='hidden-xs'>
<?php
              $rpoints = lvl_req($repaired, getTypeMod($skills,$itmlist[$itemtoblit][type]));
              if ($rpoints > ($char[equip_pts]/2)) $red = "text-danger"; else $red = "";
              echo "<font class='".$red."'>".$rpoints." pts";             
?>
            </td> 
            <td align='center'>
<?php
              echo $rpoints-$points." pts";
?>
            </td>            
          </tr>
<?php
          $draw_some = 1;
        }
        }
      }

      if ($draw_some == 0) 
        echo "<tr><td colspan='7' align='center'><p><b>You have no unequipped items to repair</b></p></td></tr>";
      else
      {
        echo "<tr>";
        echo "<td></td>";
        echo "<td align='center'><b>Total Inventory:</b></td>";
        echo "<td></td>";
        echo "<td align='center'>".displayGold($itotal)."</td>";
        echo "<td></td>";
        echo "<td align='center' class='hidden-xs'></td>";
        echo "<td></td>";
        echo "</tr>";
      }

      if ($draw_some || $draw_somee) 
        echo "<tr><td></td><td><center><b>Total Repairs:</b></center></td><td></td><td><center>".displayGold($itotal+$rtotal)."</center></td><td colspan='3'></td></tr>";

?>      
      <tr>
        <td colspan='7' align=center>            
          <input type="hidden" name="action" value="0"/>
          <a data-href="javascript:submitFormItem(1);" data-toggle="confirmation" data-placement="top" title="Repair selected items?" class="btn btn-success btn-sm btn-wrap">Repair Selected</a>
          <a data-href="javascript:submitFormItem(2);" data-toggle="confirmation" data-placement="top" title="Repair equipped items?" class="btn btn-info btn-sm btn-wrap">Repair Equipped</a>
          <a data-href="javascript:submitFormItem(3);" data-toggle="confirmation" data-placement="top" title="Repair unequipped items?" class="btn btn-primary btn-sm btn-wrap">Repair Unequipped</a>
        </td>
      </tr>
    </table>

    </center>
</form>

<?php
}
} // !isDestroyed
else
{
  $message = "The remains of the Blacksmith in ".$location[name];
  $bg = "background-image:url('images/townback/".str_replace(' ','_',strtolower($char['location'])).".jpg'); ";
  include('header.htm');
}
?>
<script type='text/javascript'>
  function submitFormItem(act) // also used by myquests.php
  {
    document.itemForm.action.value= act;
    document.itemForm.submit();
  }
</script>
<?php
include('footer.htm');
?>