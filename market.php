<script type="text/javascript">
function marketBuy(bid,bs)
{
  document.getElementById('bizid').value=bid;
  document.getElementById('bizslot').value=bs;
  document.marketForm.submit();
}
</script>
<?php

/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/itemFuncs.php");
include_once("admin/charFuncs.php");
include_once("admin/locFuncs.php");

// Check if city has been destroyed. If so, don't display anything.
if (!$location[isDestroyed])
{
include_once("map/mapdata/coordinates.inc");
if ($location_array[$char['location']][2]) $is_town=1;

$loc=$char['location'];
$s_accept=mysql_real_escape_string($_REQUEST['item4']);
$s_doit=mysql_real_escape_string($_REQUEST['doit']);
$s_item=mysql_real_escape_string($_REQUEST['itemname']);
$s_type=mysql_real_escape_string($_REQUEST['type']);
$s_bonus=mysql_real_escape_string($_REQUEST['bonus']);
$s_costmin=(10000*intval($_REQUEST['costming']))+(100*intval($_REQUEST['costmins']))+intval($_REQUEST['costminc']);
$s_costmax=(10000*intval($_REQUEST['costmaxg']))+(100*intval($_REQUEST['costmaxs']))+intval($_REQUEST['costmaxc']);
$s_eqmin=intval($_REQUEST['eqmin']);
$s_eqmax=intval($_REQUEST['eqmax']);
$sortby = mysql_real_escape_string($_REQUEST['sortby']);
$world=mysql_real_escape_string($_REQUEST['world']);
$resultnumb=mysql_real_escape_string($_REQUEST['pagenumb']);
$bizid=mysql_real_escape_string($_REQUEST['bizid']);
$bizslot=mysql_real_escape_string($_REQUEST['bizslot']);
if (!$resultnumb) $resultnumb = 0;
$numbofresults = 20;
$time=time();
$ustats = mysql_fetch_array(mysql_query("SELECT * FROM Users_stats WHERE id='$id'"));

$wikilink = "Market";

$charip = unserialize($char[ip]); 
$alts = getAlts($charip); 

$soc_name = $char[society];
$society = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$soc_name' "));
$stance = unserialize($society['stance']);

$mybq = mysql_fetch_array(mysql_query("SELECT * FROM Profs WHERE owner='$id' AND location='$loc' AND type=0"));
$allyMult=1;
if ($mybq)
{
  $myups = unserialize($mybq['upgrades']);
  $allyMult = (100-$myups[1][1]*5 - $myups[1][2]*3)/100;
}

include("map/mapdata/coordinates.inc");
if ($location_array[$char['location']][2]) $is_market=1;
else $is_market=0;

$message = "";
if (!$is_market) $message = "There is no Marketplace in ".str_replace('-ap-','&#39;',$char['location']);

// ACCEPT TRADE

if ($bizid != '' && $bizslot != '')
{
  $bq = mysql_fetch_array(mysql_query("SELECT * FROM Profs WHERE id='$bizid'"));
  $tbis = unserialize($bq[status]);
  
  if ($bq[location]==$loc)
  {
    if ($tbis[$bizslot][0]==1) // is offer
    {
      $owner = mysql_fetch_array(mysql_query("SELECT * FROM Users WHERE id='".$bq[owner]."'"));
      $username=$owner[name]."_".$owner[lastname];
      if ($bq[owner] != $id && !$alts[$username])
      {
        $itmsize = mysql_num_rows(mysql_query("SELECT * FROM Items WHERE owner='$id' AND type<15"));
        if ($itmsize < $inv_max)
        {
          $costmult = 1;
          $bstance = 0;
          if ($char[society] != "" && $owner[society] != "")
          {
            $oups=unserialize($bq[upgrades]);
            $enemyMult= (100+$oups[1][0]*5 + $oups[1][2]*3)/100;
            
            if ($stance[str_replace(" ","_",$owner['society'])] == 1)
            {
              $costmult = $allyMult;
              $bstance = 1;
            }
            elseif ($stance[str_replace(" ","_",$owner['society'])] == 2)
            {
              $costmult = $enemyMult;
              $bstance = 2;
            }
          }
          
          $icost = intval($tbis[$bizslot][1][1]*$costmult);
          if ($icost <= $char[gold])
          {
            $char[gold] -= $icost;
            $owner[gold] += intval($icost*.2);
            $owner[bankgold] += $icost - intval($icost*.2);
            $result = mysql_query("UPDATE Items SET owner='".$id."', istatus='0', last_moved='".time()."' WHERE id='".$tbis[$bizslot][1][0][id]."'");
            $tbis[$bizslot][0]=2;
            $tbis[$bizslot][1][1] = $icost;
            $tbis[$bizslot][2]=time();
            $tbis[$bizslot][3]=$bstance;
            $bqs= serialize($tbis);
            
            $ustats[items_from_market]++;
            $ostats = mysql_fetch_array(mysql_query("SELECT * FROM Users_stats WHERE id='$owner[id]'"));
            $ostats[items_marketed]++;
            
            $result = mysql_query("UPDATE Users SET gold='$char[gold]' WHERE id='$id'");
            $result = mysql_query("UPDATE Users SET gold='$owner[gold]', bankgold='$owner[bankgold]' WHERE id='$owner[id]'");
            $result = mysql_query("UPDATE Users_stats SET items_from_market='$ustats[items_from_market]' WHERE id='$id'");
            $result = mysql_query("UPDATE Profs SET status='$bqs' WHERE id='$bizid'");
            $result = mysql_query("UPDATE Users_stats SET prof_earn=prof_earn+".($icost/2).", items_marketed='$ostats[items_marketed]' WHERE id='".$owner[id]."'");
            
            $message = "Item bought for ".displayGold($icost);
          }
          else { $message = "You cannot afford that!"; }
        }
        else { $message = "You don't have any room in you inventory!"; }
      }
      else { $message = "The seller takes one look at your face and calls off the deal!"; }
    }
    else { $message = "That item is no longer for sale!"; }
  }
  else { $message = "You cannot buy that from the current location!"; }
}

// DISPLAY RESULTS
if (!$world) $loc_search = "location='$loc' AND ";
else $loc_search = "";
if ( $s_costmin && $s_costmax && $is_market)
{
  $foundnum=0;
  $farr='';
  $result = mysql_query("SELECT * FROM Profs WHERE ".$loc_search."type=0");  
  while ($bq = mysql_fetch_array( $result ) )
  {
    $tbis = unserialize($bq[status]);
    for ($i=0; $i <9; $i++)
    {
      if ($tbis[$i][0]==1) // is offer
      {
        $icost = $tbis[$i][1][1];
        if ($icost>= $s_costmin && $icost<= $s_costmax)
        {
          if ($s_type == "" || ($s_type == $item_base[$tbis[$i][1][0][base]][1]) || ($s_type == 12 && $item_base[$tbis[$i][1][0][base]][1]==13))
          {
            $tname = strtolower(iname($tbis[$i][1][0]));
            if ($s_item == "" || preg_match("/".strtolower($s_item)."/",$tname))
            {
              $statstr = iparse($tbis[$i][1][0],$item_base, $item_ix,$ter_bonuses);
              $teq = lvl_req($statstr,100);
              if ($s_eqmin <= $teq && $s_eqmax >= $teq)
              {
                $tstats = cparse($statstr,0);
                if ($s_bonus == "" || $tstats[$s_bonus])
                {
                  $os = mysql_fetch_array(mysql_query("SELECT society,jobs FROM Users WHERE id='".$bq[owner]."'"));
                  
                  $costmult = 1;
                  if ($char[society] != "" && $os[society] != "")
                  {
                    $oups=unserialize($bq[upgrades]);
                    $enemyMult= (100+$oups[1][0]*5 + $oups[1][2]*3)/100;
            
                    if ($stance[str_replace(" ","_",$os['society'])] == 1)
                    {
                      $costmult = $allyMult;
                    }
                    elseif ($stance[str_replace(" ","_",$os['society'])] == 2)
                    {
                      $costmult = $enemyMult;
                    }
                  }
                 
                  $farr[$foundnum]=$tbis[$i][1];
                  $farr[$foundnum][3]=$tname;
                  $farr[$foundnum][4]=$tstats;
                  $farr[$foundnum][5]=$bq[location];
                  $farr[$foundnum][6]=$teq;
                  $farr[$foundnum][7]=0;
                  $farr[$foundnum][8]=array($bq[id],$i);
                  
                  $farr[$foundnum][1] = intval($farr[$foundnum][1] * $costmult);
                  $foundnum++;
                }
              }
            }
          }
        }
      }
    }
  }
  
  // SORT RESULTS
  $sarr='';
  for ($f=0; $f < $foundnum; $f++)
  {
    $tmin = -1;
    for ($g=0; $g < $foundnum; $g++)
    {
      if ($farr[$g][7]) // already sorted; skip
      { }
      elseif ($tmin == -1) // first unsorted
      {
        $tmin=$g;
      }
      elseif ($sortby==1)
      {
        if ($farr[$g][1] < $farr[$tmin][1])
        {
          $tmin=$g;
        }
      }
      elseif ($sortby==2)
      {
        if ($farr[$g][6] < $farr[$tmin][6])
        {
          $tmin=$g;
        }
      }
      elseif ($sortby==3)
      {
        if (($item_base[$farr[$g][0][0]][1] < $item_base[$farr[$tmin][0][0]][1]) || 
            ($item_base[$farr[$g][0][0]][1] == $item_base[$farr[$tmin][0][0]][1] && $farr[$g][1] < $farr[$tmin][1]))
        {
          $tmin=$g;
        }
      }
      elseif ($sortby==4)
      {
        if ($farr[$g][3] < $farr[$tmin][3])
        {
          $tmin=$g;
        }
      }
      elseif ($sortby==5)
      {
        if (($farr[$g][5] < $farr[$tmin][5]) ||
            ($farr[$g][5] == $farr[$tmin][5] && $farr[$g][1] < $farr[$tmin][1]))
        {
          $tmin=$g;
        }
      }
    }
    $sarr[$f]=$tmin;
    $farr[$tmin][7]=1;
  }
 
  //HEADER
  if (!$world) $search_text = "the ".str_replace('-ap-','&#39;',$char['location'])." Market";
  else $search_text = "markets around the world";
  $message = "There are ".($foundnum)." items in ".$search_text." matching your description";

$bg = "background-image:url('images/townback/".str_replace(' ','_',strtolower($char['location'])).".jpg'); ";
include('header.htm');

?>
  <div class="row solid-back">
    <form name='marketForm' method="post" action="market.php">
      <table class='table table-responsive table-condensed table-striped table-hover small'>
        <tr>
          <th>Item Info</th>
          <th>Type</th>
          <th>Points</th>
          <th>Status</th>
          <th>Cost</th>
          <th><?php if (!$world) echo "&nbsp;"; else echo "Location"; ?></th>
        </tr>
        <?php
          // DRAW RESULTS
          $x=0;
          for ($x=0; $x < $foundnum; $x++)
          {
            $invinfo = "<div class='panel panel-primary' style='width: 150px;'><div class='panel-heading'><h3 class='panel-title'>".ucwords($farr[$sarr[$x]][3])."</h3></div><div class='panel-body abox' align='center'><img class='img-responsive hidden-xs img-optional-nodisplay' border='0' bordercolor='black' src='items/".str_replace(' ','',$farr[$sarr[$x]][0][base]).".gif'/>";
            $invinfo .= itm_info(cparse($item_base[$farr[$sarr[$x]][0][base]][0]." ".$item_ix[$farr[$sarr[$x]][0][prefix]]." ".$item_ix[$farr[$sarr[$x]][0][suffix]],$item_base[$farr[$sarr[$x]][0][base]][1]));
            $invinfo .= "</div></div>";
        ?>
        <tr>
          <td class="popcenter">
            <button type='button' class='btn btn-primary btn-block btn-xs btn-wrap' data-html='true' data-toggle='popover' data-placement='bottom' data-content="<?php echo $invinfo; ?>"><?php echo ucwords($farr[$sarr[$x]][3]); ?></button>
          </td>
          <td align='center'><?php echo ucwords($item_type[$item_base[$farr[$sarr[$x]][0][base]][1]]); ?></td>
          <td align='center'><?php echo $farr[$sarr[$x]][6];?> pts</td>
          <td align='center'><?php echo $farr[$sarr[$x]][0][cond];?>%</td>          
          <td align='center'><?php if ($char[gold] < $farr[$sarr[$x]][1]) echo "<font color='ED3915'>"; echo displayGold($farr[$sarr[$x]][1]); ?></td>
          <td align='center'>
          <?php 
            if ($char[gold] >= $farr[$sarr[$x]][1] && !($farr[$sarr[$x]][2] == $id) && !$world) 
            { 
              echo "<input type='button' class='btn btn-success btn-block btn-xs btn-wrap' name='buy".$x."' value='Buy' onClick='marketBuy(".$farr[$sarr[$x]][8][0].",".$farr[$sarr[$x]][8][1].");'>";
            } 
            elseif ($world) echo str_replace('-ap-','&#39;',$farr[$sarr[$x]][5]);
          ?>
          </td>
        </tr>
        <?php
          }
          if ($x == 0 && !$world) echo "<tr><td colspan='4'><br><font class=littletext><center><b>-No results, try another town's market-</b><br><br></td></tr>";
          else if ($x == 0)  echo "<tr><td colspan='4'><br><font class=littletext><center><b>-No results found anywhere in the world-</b><br><br></td></tr>";
          // END DRAW RESULTS
        ?>
        <tr>
          <td colspan=1>&nbsp;</td>
          <td colspan=2><button class='btn btn-block btn-wrap btn-sm btn-warning' onClick="document.location='market.php'">New Search</button></td>
          <td colspan=2>&nbsp;</td>
        </tr>
      </table>
      <input type='hidden' name='bizid' id='bizid' val='-1'><input type='hidden' name='bizslot' id='bizslot' val='-1'>
    </form>
  </div>

<?php
}
else
{
  // IF NO RESULTS YET, DISPLAY SEARCH BOXES

if ($s_item || $s_type && !($s_costmin && $s_costmax && $s_bonusmin && $s_bonusmax)) $message = "You must enter a value for each of the cost and bonus fields";

if (!$is_town) $message = "There is no market in ".str_replace('-ap-','&#39;',$char['location']);
$bg = "background-image:url('images/townback/".str_replace(' ','_',strtolower($char['location'])).".jpg'); ";
include('header.htm');

if ($is_town)
{
?>
  <div class="row solid-back">
    <div class='col-sm-4'>
      <img class='img-optional'src='images/ShopKeeps/<?php echo str_replace(' ','_',$char['location']);?>4.jpg' /><br/><br/>
      <?php echo "<i>Welcome to<br/>".str_replace('-ap-','&#39;',$char['location'])."'s Marketplace!</i>"; ?><br/>
    </div>
    <div class='col-sm-8'>
      <form method="post" action="market.php" class='form-inline'>
        <div class="form-group form-group-sm">
          <label for="itemname">Item Name:</label>
          <input type="text" name="itemname" id="itemname" class="form-control gos-form input-sm" maxlength="30" />
        </div><br/>
        <div class="form-group form-group-sm">
          <label for="type">Type:</label>
          <select name="type" size="1" class="form-control gos-form input-sm">
            <option selected value="">All Items</option>
            <option value="1">Sword</option>
            <option value="2">Axe</option>
            <option value="3">Spear</option>
            <option value="4">Bows</option>
            <option value="5">Bludgeon</option>
            <option value="6">Knives</option>
            <option value="7">Shield</option>
            <option value="9">Body Armor</option>
            <option value="10">Leg Armor</option>
            <option value="11">Cloaks</option>
            <option value="12">Terangreal</option>
          </select>
        </div><br/>
        <div class="form-group form-group-sm">
          <label for="bonus">Bonus:</label>
          <select name="bonus" size="1" class="form-control gos-form input-sm">
            <option selected value="">Any</option>
            <option value="A">Damage</option>
            <option value="D">Block</option>
            <option value="O">Attack %</option>
            <option value="N">Defense %</option>
            <option value="S">Stun</option>
            <option value="F">First Strike</option>
            <option value="W">Wound</option>
            <option value="X">Speed</option>
            <option value="Y">Accuracy</option>
            <option value="V">Dodge</option>  
            <option value="G">Gain Health</option>
            <option value="P">Poison</option>
            <option value="T">Taint</option>
            <option value="L">Luck</option>
          </select>
        </div><br/>
        <div class="form-group form-group-sm">
          <label for="costmin">Cost:</label>
          <img src='images/gold.gif' width='15' style='vertical-align:middle' alt='g:'><input type="text" name="costming" size="4" id="costming" value="0" class="form-control gos-form input-sm" maxlength="4" />
          <img src='images/silver.gif' width='15' style='vertical-align:middle' alt='s:'><input type="text" name="costmins" size="2" id="costmins" value="0" class="form-control gos-form input-sm" maxlength="2" />
          <img src='images/copper.gif' width='15' style='vertical-align:middle' alt='c:'><input type="text" name="costminc" size="2" id="costminc" value="1" class="form-control gos-form input-sm" maxlength="2" />
          <br/>to 
          <img src='images/gold.gif' width='15' style='vertical-align:middle' alt='g:'><input type="text" name="costmaxg" size="4" id="costmaxg" value="9999" class="form-control gos-form input-sm" maxlength="2" />
          <img src='images/silver.gif' width='15' style='vertical-align:middle' alt='s:'><input type="text" name="costmaxs" size="2" id="costmaxs" value="99" class="form-control gos-form input-sm" maxlength="2" />
          <img src='images/copper.gif' width='15' style='vertical-align:middle' alt='c:'><input type="text" name="costmaxc" size="2" id="costmaxc" value="99" class="form-control gos-form input-sm" maxlength="2" />
        </div><br/>
        <div class="form-group form-group-sm">
          <label for="eqmin">Points Range:</label>
          <input type="text" name="eqmin" size="4" id="eqmin" value="1" class="form-control gos-form input-sm" maxlength="4" />
          to 
          <input type="text" name="eqmax" size="4" id="eqmax" value="9999" class="form-control gos-form input-sm" maxlength="4" />
        </div><br/> 
        <div class="form-group form-group-sm">
          <label for="sortby">Sort By:</label>
          <select name="sortby" size="1" class="form-control gos-form input-sm">
            <option selected value="1">Cost</option>
            <option value="2">Equip Points</option>
            <option value="3">Type</option>
            <option value="4">Name</option>
            <option value="5">Location</option>
          </select>
        </div><br/>
        <div class="form-group form-group-sm">
          <label for="world">Item Location:</label>
          <select name="world" size="1" class="form-control gos-form input-sm">
            <option selected value="0"><?php echo str_replace('-ap-','&#39;',$char['location']); ?></option>
            <option value="2">Anywhere</option>
          </select>
        </div><br/>
        <input type="Submit" name="submit" value="Search" class="btn btn-sm btn-success">
      </form>
    </div>
  </div>
<?php
}
?>

<br>
</center>

<?php
}
} // !isDestroyed
else
{
  $message = "The remains of the Market in ".$location[name];
  if ($mode == 1) { include('header2.htm'); }
  else 
  {
    $bg = "background-image:url('images/townback/".str_replace(' ','_',strtolower($char['location'])).".jpg'); ";
    include('header.htm');
  }
}
include('footer.htm');
?>