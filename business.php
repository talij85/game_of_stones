<?php

/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/locFuncs.php");
include_once("admin/duelFuncs.php");
include_once("admin/charFuncs.php");
include_once("admin/jobFuncs.php");
// Find all equipped items
include_once("admin/equipped.php");

include_once("map/mapdata/coordinates.inc");
if ($location_array[$char['location']][2]) $is_town=1;
else $is_town=0;

$message = mysql_real_escape_string($_GET[message]);
$shop = mysql_real_escape_string($_GET[shop]);
$room = mysql_real_escape_string($_GET[room]);
$new_pouch = mysql_real_escape_string($_GET['pouch']);
$outfit = mysql_real_escape_string($_POST[outfit]);
$newpack = mysql_real_escape_string($_POST[packrad]);
$newpouch = mysql_real_escape_string($_POST[pouchrad]);
$newhorse = mysql_real_escape_string($_POST[horserad]);
$combTer = mysql_real_escape_string($_REQUEST[ters]);
$combItm = mysql_real_escape_string($_REQUEST[titm]);
$combTerN = mysql_real_escape_string($_REQUEST[combTer]);
$combItmN = mysql_real_escape_string($_REQUEST[combItm]);
$combTEq = mysql_real_escape_string($_REQUEST[combTEq]);
$combIEq = mysql_real_escape_string($_REQUEST[combIEq]);

$soc_name = $char[society];
$society = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$soc_name' "));

$loc = $char['location'];
$shopname = $town_shop_names[$loc][$shop-1];
if ($shopname == "") $shopname = $loc."'s Outfitter";

// Check if city has been destroyed. If so, don't display anything.
if (!$location[isDestroyed])
{
$type = $shop + 18;

$jobs = unserialize($char[jobs]);
$pro_stats=cparse(getAllJobBonuses($jobs));

$fv = $pro_stats[fV] + $town_bonuses[fV] + $town_bonuses[lV] + $town_bonuses[tV];
$hv = $pro_stats[hV] + $town_bonuses[hV] + $town_bonuses[lV] + $town_bonuses[tV];
$dv = $pro_stats[dV] + $town_bonuses[dV] + $town_bonuses[lV] + $town_bonuses[tV];
$iv = $pro_stats[iV] + $town_bonuses[iV] + $town_bonuses[oV] + $town_bonuses[tV];
$uv = $pro_stats[uV] + $town_bonuses[uV] + $town_bonuses[oV] + $town_bonuses[tV];
$sv = $pro_stats[sV] + $town_bonuses[sV] + $town_bonuses[oV] + $town_bonuses[tV];

$pro_minus[1] = (100+$fv)/100;
$pro_minus[2] = (100+$hv)/100;
$pro_minus[3] = (100+$dv)/100;

$ustats = mysql_fetch_array(mysql_query("SELECT * FROM Users_stats WHERE id='$id'"));

if (!$shop || $shop < 1 || $shop > 4) {$shop = 1;}

if (!$location[shopg])
{
  $location[shopg]=serialize($base_town_consumes[$loc]);
  mysql_query("UPDATE Locations SET shopg='".$location[shopg]."' WHERE name='$loc'");
}

$shopg = unserialize($location[shopg]);

$shopname = $town_shop_names[$loc][$shop-1];

$listsize=0;
$iresult=mysql_query("SELECT * FROM Items WHERE owner='$id' AND type<15 AND type>0");
while ($qitem = mysql_fetch_array($iresult))
{
  $itmlist[$listsize++] = $qitem;
}

// combine items
if ($combTer != '' && $combItm != '' && $is_town)
{
  if ($combTer != -1 && $combItm != -1 &&
      strtolower(iname($itmlist[$combTer]))== strtolower($combTerN) && 
      strtolower(iname($itmlist[$combItm])) == strtolower($combItmN))
  {
    if ($combTEq) $itmlist[$combTer][istatus] = -2;
    if ($combIEq) $itmlist[$combItm][istatus] = -2;
    
    // if clan item(s), save off clan id
    if ($itmlist[$combTer][society] != 0) $itmlist[$combItm][society]=$itmlist[$combTer][society];
  
    if ($itmlist[$combTer][prefix]) $itmlist[$combItm][prefix] = $itmlist[$combTer][prefix];
    if ($itmlist[$combTer][suffix]) $itmlist[$combItm][suffix] = $itmlist[$combTer][suffix];
    
    $istats= itp($item_base[$itmlist[$combItm][base]][0]." ".$item_ix[$itmlist[$combItm][prefix]]." ".$item_ix[$itmlist[$combItm][suffix]],$itmlist[$combItm][type]);
    $istats .= getTerMod($ter_bonuses,$itmlist[$combItm][type],$itmlist[$combItm][prefix],$itmlist[$combItm][suffix],$itmlist[$combItm][base]);
    $ipts= lvl_req($istats,100);
    $result = mysql_query("UPDATE Items SET prefix='".$itmlist[$combItm][prefix]."', suffix='".$itmlist[$combItm][suffix]."', society='".$itmlist[$combItm][society]."', stats='".$istats."', points='".$ipts."', istatus='".$itmlist[$combItm][istatus]."' WHERE id='".$itmlist[$combItm][id]."'");
    $result = mysql_query("DELETE FROM Items WHERE id='".$itmlist[$combTer][id]."'");

    $ustats[items_combined]++;
    $query = mysql_query("UPDATE Users_stats SET items_combined='".$ustats[items_combined]."' WHERE id=$id");
 
    $message = "Items Combined!";
    if ($combTEq || $combIEq) $message .= " Equipment Changed!";
  }
  else
  {
    $message = 'Something odd happened when combining. Please try again!';
  }
}

$listsize=0;
$iresult=mysql_query("SELECT * FROM Items WHERE owner='$id' AND type<15 AND type>0");
while ($qitem = mysql_fetch_array($iresult))
{
  $itmlist[$listsize++] = $qitem;
}

$bonuses1 = $stomach_bonuses1." ".$stamina_effect1." ".$clan_bonus1." ".$skill_bonuses1." ".$clan_building_bonuses;
$skills = $bonuses1;

$y = 0;
$a = "";
$b = "";
$c = "";
$d = "";
$e = "";
$a = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus='1' AND type<15 AND type>0"));
$b = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus='2' AND type<15 AND type>0"));
$c = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus='3' AND type<15 AND type>0"));
$d = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus='4' AND type<15 AND type>0"));
$e = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus='5' AND type<15 AND type>0"));
$estats = getstats($a,$b,$c,$d,$e,$skills);
$askills = $skills." ".$estats[0];
$pts_tot = 0;
if ($a != "") $pts_tot += (lvl_req($estats[1], getTypeMod($askills,$a[type])));
if ($b != "") $pts_tot += (lvl_req($estats[2], getTypeMod($askills,$b[type])));
if ($c != "") $pts_tot += (lvl_req($estats[3], getTypeMod($askills,$c[type])));
if ($d != "") $pts_tot += (lvl_req($estats[4], getTypeMod($askills,$d[type])));      
if ($e != "") $pts_tot += (lvl_req($estats[5], getTypeMod($askills,$e[type])));

if ($pts_tot != $char['used_pts'])
{
  $char['used_pts'] = $pts_tot;
  $result = mysql_query("UPDATE Users SET used_pts='$pts_tot' WHERE id=$id");
}

?>
<script type="text/javascript">
  var invinfo = new Array();
  var inv = new Array();
  var skills = '<?php echo $askills; ?>';
  var noEqSkills = '<?php echo $skills; ?>';
  var myPts = <?php echo $char['equip_pts'];?>;
  var ai=-1;
  var bi=-1;
  var ci=-1;
  var di=-1;
  var ei=-1;
<?php
  for ($i=0; $i<$listsize; ++$i)
  {
    echo "  inv[".$i."] = new Array('".str_replace(" ","_",$itmlist[$i][base])."','".str_replace(" ","_",$itmlist[$i][prefix])."','".str_replace(" ","_",$itmlist[$i][suffix])."','".$itmlist[$i][cond]."','".$itmlist[$i][istatus]."');";
    echo "  invinfo[".$i."] = \"<FIELDSET class=abox><LEGEND><b>".ucwords($itmlist[$i][prefix]." ".$itmlist[$i][base])." ".str_replace("Of","of",ucwords($itmlist[$i][suffix]))."</b></LEGEND><center><br/><img class='img-optional' border='0' bordercolor='black' src='items/".str_replace(' ','',$itmlist[$i][base]).".gif'><br/><br/>\" + "; 

    if ($itmlist[$i][type]==8)
      echo "itm_info(cparse(weaveStats('".$itmlist[$i][base]."',skills)))+\"";
    else
      echo "itm_info(cparse(iparse('".str_replace(" ","_",$itmlist[$i][base])."','".str_replace(" ","_",$itmlist[$i][prefix])."','".str_replace(" ","_",$itmlist[$i][suffix])."','".str_replace(" ","_",$itmlist[$i][cond])."')))+\"";
    echo "<br/><br/></FIELDSET>\";\n";
    if ($itmlist[$i][istatus]==1)      echo "  ai=".$i.";";
    else if ($itmlist[$i][istatus]==2) echo "  bi=".$i.";";
    else if ($itmlist[$i][istatus]==3) echo "  ci=".$i.";";
    else if ($itmlist[$i][istatus]==4) echo "  di=".$i.";";
    else if ($itmlist[$i][istatus]==5) echo "  ei=".$i.";";
  }
?>

function swapinfo(myitm,field)
{
  document.getElementById(field).innerHTML=invinfo[myitm];
}

function getstats(myitm1,myitm2,myitm3,myitm4,myitm5,nitm,nstats)
{
  var estats = new Array();
  estats[0] = "";
  if (myitm1>=0)
  {
    if (myitm1==nitm) estats[1] = nstats;
    else if (item_base[inv[myitm1][0]][1] == 8)
      estats[1] = weaveStats(inv[myitm1][0],skills);
    else
      estats[1] = iparse(inv[myitm1][0],inv[myitm1][1],inv[myitm1][2],inv[myitm1][3]);
    estats[0] += " " + (estats[1]);
  }
  if (myitm2>=0)
  {
    if (myitm2==nitm) estats[2] = nstats;
    else if (item_base[inv[myitm2][0]][1] == 8)
      estats[2] = weaveStats(inv[myitm2][0],skills);
    else
      estats[2] = iparse(inv[myitm2][0],inv[myitm2][1],inv[myitm2][2],inv[myitm2][3]);
    estats[0] += " " + (estats[2]);
  }
  if (myitm3>=0)
  {
    if (myitm3==nitm) estats[3] = nstats;
    else if (item_base[inv[myitm3][0]][1] == 8)
      estats[3] = weaveStats(inv[myitm3][0],skills);
    else
      estats[3] = iparse(inv[myitm3][0],inv[myitm3][1],inv[myitm3][2],inv[myitm3][3]);
    estats[0] += " " + (estats[3]);
  }
  if (myitm4>=0)
  {
    if (myitm4==nitm) estats[4] = nstats;
    else if (item_base[inv[myitm4][0]][1] == 8)
      estats[4] = weaveStats(inv[myitm4][0],skills);
    else
      estats[4] = iparse(inv[myitm4][0],inv[myitm4][1],inv[myitm4][2],inv[myitm4][3]);
    estats[0] += " " + (estats[4]);
  }
  if (myitm5>=0)
  {
    if (myitm5==nitm) estats[5] = nstats;
    else if (item_base[inv[myitm5][0]][1] == 8)
      estats[5] = weaveStats(inv[myitm5][0],skills);
    else
      estats[5] = iparse(inv[myitm5][0],inv[myitm5][1],inv[myitm5][2],inv[myitm5][3]);
    estats[0] += " " + (estats[5]);
  }
  
  return estats;
}


function combineItems()
{
  var allTers = document.getElementById('ters');
  var allItms = document.getElementById('titm');
  var myTer = allTers.value;
  var myItm = allItms.value;
  var teq = 0;
  var iueq = 0;

  // check if Ter is equipped
  if (inv[myTer][4] >0 ) teq = 1;
  
  // check if Item is equipped
  if (inv[myItm][4] >0 ) 
  {
    var newItm = new Array();
    var stats = '';
    var newStats = '';  
    var a = ai;
    var b = bi;
    var c = ci;
    var d = di;
    var e = ei;
    
    if (teq)
    {
      if (a==myTer) a=-1;
      if (b==myTer) b=-1;
      if (c==myTer) c=-1;
      if (d==myTer) d=-1;
      if (e==myTer) e=-1;
    }

    newItm[0] = inv[myItm][0];
    if (inv[myItm][1] != '')
      newItm[1] = inv[myItm][1];
    else
      newItm[1] = inv[myTer][1];
    if (inv[myItm][2] != '')
      newItm[2] = inv[myItm][2];
    else
      newItm[2] = inv[myTer][2];
    newItm[3] = inv[myItm][3];
    stats = (iparse(newItm[0],newItm[1],newItm[2],newItm[3]));    
    
    var estats =  getstats(a,b,c,d,e,myItm,stats);
    var tskills = noEqSkills+" "+estats[0];
    var pts_tot = 0;
    
    if (a>=0)
    {
      pts_tot += (lvl_req(estats[1],getTypeMod(tskills,item_base[inv[a][0]][1])));
    }
    if (b>=0)
    {
      pts_tot += (lvl_req(estats[2],getTypeMod(tskills,item_base[inv[b][0]][1])));
    }
    if (c>=0)
    {
      pts_tot += (lvl_req(estats[3],getTypeMod(tskills,item_base[inv[c][0]][1])));
    }
    if (d>=0)
    {
      pts_tot += (lvl_req(estats[4],getTypeMod(tskills,item_base[inv[d][0]][1])));
    }
    if (e>=0)
    {
      pts_tot += (lvl_req(estats[5],getTypeMod(tskills,item_base[inv[e][0]][1])));
    }  
    
    if (pts_tot > myPts || (lvl_req(stats,getTypeMod(tskills,item_base[inv[myItm][0]][1])) > myPts/2)) iueq = 1;
  }

  var cTer = inv[myTer][0].replace(/_/g, " ");
  var cItm = inv[myItm][0].replace(/_/g, " ");
  if (inv[myTer][1] != "") cTer = inv[myTer][1].replace(/_/g, " ")+" "+cTer;
  if (inv[myTer][2] != "") cTer = cTer+" "+inv[myTer][2].replace(/_/g, " ");
  if (inv[myItm][1] != "") cItm = inv[myItm][1].replace(/_/g, " ")+" "+cItm;
  if (inv[myItm][2] != "") cItm = cItm+" "+inv[myItm][2].replace(/_/g, " ");
  document.getElementById('combTer').value = cTer;
  document.getElementById('combItm').value = cItm;
  document.getElementById('combTEq').value = teq;
  document.getElementById('combIEq').value = iueq;
  var aText = "";
  if (teq > 0) aText += "The selected Ter'angreal is currently equipped. ";
  if (iueq > 0) aText += "Combining with this item will cause it to be unequipped. ";
  aText += "Are you sure you want to combine these items?";

  document.getElementById('combineButton').title = aText;
  document.getElementById('combineButton').setAttribute('data-original-title',aText);
  //popConfirmJs(aText,'submitTerForm();');
}

function submitTerForm()
{
  document.terForm.submit();
}

function updateTerLists()
{
  var allTers = document.getElementById('ters');
  var allItms = document.getElementById('titm');

  var myTer = allTers.value;
  var myItm = allItms.value;
  
  allTers.options.length = 0;
  allItms.options.length = 0;
  
  var tmp = '';
  var ttype = 0;
  var tpts = 0;
  for (var i=-1; i<inv.length; i++) 
  {
    if (i>=0)
    { 
        tpts = (lvl_req(iparse(inv[i][0],inv[i][1],inv[i][2],inv[i][3]),getTypeMod(skills,item_base[inv[i][0]][1])));
        tmp = ucwords(inv[i][1].replace(/_/g, " ")+" "+inv[i][0].replace(/_/g, " ")+" "+inv[i][2].replace(/_/g, " ") + " - " + tpts);
        ttype = item_base[inv[i][0]][1];
        if (ttype >= 12)
        {
          if (myItm < 0 || (myItm >=0 && ((inv[myItm][1] == '' || inv[i][1] == '') && (inv[myItm][2] == '' || inv[i][2] == ''))))
          {
            allTers.options[allTers.options.length] = new Option(tmp,i);
            if (i==myTer) 
            {
              allTers.selectedIndex = allTers.options.length-1;
            }
          }
        }
        else if (ttype != 8)
        {
          if (myTer >=0 && ((inv[myTer][1] == '' || inv[i][1] == '') && (inv[myTer][2] == '' || inv[i][2] == '')))
          {
            allItms.options[allItms.options.length] = new Option(tmp,i);
            if (i==myItm) 
            {
              allItms.selectedIndex = allItms.options.length-1;
            }
          }        
        }
    }
    else
    {
      allTers.options[allTers.options.length] = new Option('None',i);
      if (myTer >=0)
         allItms.options[allItms.options.length] = new Option("Select Item",i);
      else
         allItms.options[allItms.options.length] = new Option("Select Ter'angreal",i);
         
    }
  }
  var newItm = new Array();
  var stats = '';
  var newStats = '';
  var mode = '<?php echo $mode;?>';
  if (allTers.value != -1)
  {
    swapinfo(allTers.value,'terstats');
  }
  if (allItms.value != -1)
  {
    swapinfo(allItms.value,'istats');
    newItm[0] = inv[myItm][0];
    if (inv[myItm][1] != '')
      newItm[1] = inv[myItm][1];
    else
      newItm[1] = inv[myTer][1];
    if (inv[myItm][2] != '')
      newItm[2] = inv[myItm][2];
    else
      newItm[2] = inv[myTer][2];
    newItm[3] = inv[myItm][3];
    stats = (iparse(newItm[0],newItm[1],newItm[2],newItm[3]));
    var ostats = (iparse(inv[myItm][0],inv[myItm][1],inv[myItm][2],inv[myItm][3]));

    newStats = "<FIELDSET class=abox><LEGEND><b>"+ucwords(newItm[1].replace(/_/g, " ")+" "+newItm[0].replace(/_/g, " ")+" "+newItm[2].replace(/_/g, " "))+"</b></LEGEND><center><br><br>Equip Pts: "+lvl_req(stats,getTypeMod(skills,item_base[inv[myItm][0]][1]))+"<br><br>"+ itm_info(cparse(stats), cparse(ostats))+"<br></FIELDSET>";

    document.getElementById('newstats').innerHTML = newStats + "<br>";
    combineItems();
  }
}
</script>

<?php

function displayItemTable($ditmlist, $dnum)
{
  global $item_base, $item_type, $shopg, $type, $pro_minus, $shop, $char, $consumable_quality;
  echo "<table class='table table-condensed table-striped table-responsive solid-back'>";
  echo "  <tr>";
  echo "    <td class='headrow' width='150' align='center'><b>".ucwords($consumable_quality[$type][$dnum]." ".$item_type[$type])."</b></td>";
  echo "    <td class='headrow' width='80' align='center'><b>Worth</b></td>";
  echo "    <td class='headrow' width='40' align='center'><b>Num</b></td>";	
  echo "    <td class='headrow' width='50' align='center'><b>Action</b></td>";
  echo "  </tr>";
  
  if (count($ditmlist) > 0)
  {
  foreach ($ditmlist as $x => $sitm)
  {
    if ($shopg[$type][$sitm[4]][$sitm[5]]!=0)
    {
      echo "  <tr class='listtab'>";
      $worth = floor($item_base[$sitm[0]][2]*$pro_minus[$shop]);
      $btnstyle='btn-default';
      if ($worth > $char[gold]) 
        $btnstyle = "btn-danger"; 
      if($shopg[$sitm[3]][$sitm[4]][$sitm[5]] > 0)
      {
        $quant= $shopg[$sitm[3]][$sitm[4]][$sitm[5]];
      }
      else
      {
        $quant= '-';
      }
      
      $pic_name = "";
      $name_words = explode(" ",$sitm[0]);
      for ($y=1; $y<count($name_words); $y++)
      {
        $pic_name .= $name_words[$y];
      }      
      $coninfo = "<div class='panel panel-warning' style='width: 150px;'><div class='panel-heading'><h3 class='panel-title'>".ucwords($sitm[0])."</h3></div><div class='panel-body abox' align='center'><img class='img-responsive hidden-xs img-optional-nodisplay' border='0' bordercolor='black' src='items/".str_replace(' ','',$pic_name).".gif'/>";
      $coninfo .= itm_info(cparse($item_base[$sitm[0]][0]));
      $coninfo .= "</div></div>"; 
      
      echo "    <td width='150' align='center'><button type='button' class='btn ".$btnstyle." btn-sm btn-block btn-wrap link-popover' data-toggle='popover' data-html='true' data-placement='bottom' data-content=\"".$coninfo."\">".ucwords($sitm[0])."</button></td>";
      echo "    <td width='80' align='center'>".displayGold($worth)."</td>";
      echo "    <td width='40' align='center'>".$quant."</td>";
      echo "    <td width='50' align='center'><a class='btn btn-success btn-xs btn-block' href=\"business.php?shop=".$shop."&buy=".($x+1)."&name=".$sitm[0]."\">Buy</a></td>";
      echo "  </tr>";
    }
  }
  }
  else
  {
    echo "  <tr><td align='center' colspan='4' width=350><b>None currently available</b></td></tr>";
  }
  echo "  <tr><td colspan='4' style='border-width: 0px; border-top: 1px solid #333333'>&nbsp;</td></tr>";
  echo "</table>";
}

if ($room == 1 && $is_town)
{
  if ($shop == 1)
  {
    $room_cost = (($char[stamaxa]-$char[stamina])*60);
    if ($iv) $room_cost = floor($room_cost*((100+$iv)/100));
    if ($char[gold] >= $room_cost)
    {
      $char[gold] -= $room_cost;
      $char[stamina] = $char[stamaxa];
      $town_share = intval($room_cost/2);
      if ($town_share > 0) $location[bank] += $town_share;
      if ($society[id]) updateSocRep($society, $location[id], $room_cost);
      mysql_query("UPDATE Locations SET bank='$location[bank]' WHERE name='$loc'");
      mysql_query("UPDATE Users SET gold='".$char[gold]."', stamina='".$char[stamina]."' WHERE id='$id'");
      mysql_query("UPDATE Users_stats SET inn_use= inn_use + 1 WHERE id='$id'");
      $message = "You enjoyed your rest for ".displayGold($room_cost);
    }
    else {$message = "You can't afford a room here!"; }
  }
  else
  {
    $message = "This business doesn't offer rooms...";
  }
}

if ($_GET['feed'] && $location_array[$char['location']][2] && $is_town)
{
  $cost=intval(($char['feedneed']*$char['travelmode']*20)*($uv+100)/100);
  if ($cost<=$char['gold'])
  {
    $char['gold']-=$cost;
    $char['feedneed']=0;
    $message=$char['travelmode_name']." has been fed and is ready to travel";
    $town_share = intval($cost);
    if ($town_share > 0) $location[bank] += $town_share;
    if ($society[id]) updateSocRep($society, $location[id], $cost);
    mysql_query("UPDATE Locations SET bank='$location[bank]' WHERE name='$loc'");
    mysql_query("UPDATE Users SET gold='".$char['gold']."', feedneed='0' WHERE id='".$char['id']."'");
  }
  else $message="You do not have that much money";
}

if ($outfit && $is_town)
{
  if ($pack_name[$newpack][2] <= $travel_mode[$newhorse][2])
  {
    $myitmlist=unserialize($char['itmlist']);
    $mypouch = unserialize($char['pouch']);
    $mylistsize = count($myitmlist);
    $myclistsize = count($mypouch);
    if (($pack_name[$newpack][1]+$base_inv_max+$pro_stats[eS] >= $mylistsize) && ($pouch_name[$newpouch][1]+4+$pro_stats[cS] >= $myclistsize))
    {
      $change = 0;
      $totalcost = 0;
      if ($newpack != $char[travelmode2])
      {
        $totalcost += intval(($pack_cost[$newpack]-($pack_cost[$char[travelmode2]]/2))*(100+$sv)/100);
        $change = 1;
      }
      if ($newhorse != $char[travelmode])
      {
        $totalcost += intval(($travel_mode_cost[$newhorse]-($travel_mode_cost[$char[travelmode]]/2))*(100+$uv)/100);
        $change = 1;        
      }
      if ($newpouch != $char[pouch_type])
      {
        $totalcost += intval(($pouch_cost[$newpouch]-($pouch_cost[$char[pouch_type]]/2))*(100+$sv)/100);
        $change = 1;    
      }   
      
      if ($change)
      {
        if ($totalcost <= $char[gold])
        {
          $char[gold] -= $totalcost;
          if ($newhorse != $char[travelmode]) 
          { 
            $char[feedneed] = 0;
            if ($newhorse > 0) $char['travelmode_name']=HorseNamer();
            else $char['travelmode_name']='';
          }
          $char[travelmode] = $newhorse;
          $char[travelmode2] = $newpack;
          $char[pouch_type] = $newpouch;
          $town_share = intval($totalcost/2);
          if ($town_share > 0) $location[bank] += $town_share;
          if ($society[id]) updateSocRep($society, $location[id], $totalcost);
          mysql_query("UPDATE Locations SET bank='$location[bank]' WHERE name='$loc'");
          mysql_query("UPDATE Users SET gold='".$char['gold']."', pouch_type='".$char['pouch_type']."', travelmode='".$char[travelmode]."', travelmode_name='".$char['travelmode_name']."', travelmode2='".$char[travelmode2]."', feedneed='".$char[feedneed]."' WHERE id='".$char['id']."'");
          mysql_query("UPDATE Users_stats SET outfit_use = outfit_use + 1 WHERE id='$id'");
          $message = "Gear updated for ".displayGold($totalcost);
        }
        else
          $message = "You don't have enough money!";
      } 
      else
        $message = "You didn't change anything!";
    }
    else
      $message = "Your items won't fit in the storage you've chosen!";
  }
  else
    $message = "That horse isn't strong enough to haul your supplies!";
}
      
if ($message == '')
{
  if ($shop == 4) $message = "Welcome to the local Outfitter";
}
$buy = mysql_real_escape_string($_GET[buy]);

$shop_inv=$town_consumables[$loc];

$x=0;
for ($i =0; $i < 4; $i++)
{
  for ($j=0; $j<12; $j++)
  {
    if ($shopg[$type][$i][$j] != 0)
    { 
      $sitmlist[$x][0] = $consumable_quality[$type][$i]." ".$item_list[$type][$j];
      $sitmlist[$x][1] = "";
      $sitmlist[$x][2] = "";
      $sitmlist[$x][3] = $type;
      $sitmlist[$x][4] = $i;
      $sitmlist[$x][5] = $j; 
      $x++;
    }
  }  
}
$slistsize=count($sitmlist);

// BUY ITEM
$time = time();

if (strtolower(iname_list($buy-1,$sitmlist)) == strtolower($_GET[name]) && $buy<=$slistsize && $buy>0 && $is_town)
{
  $buy--;
  $worth = $item_base[$sitmlist[$buy][0]][2]*$pro_minus[$shop];
  if ($char[gold] >= $worth)
  {
    $gold = $char[gold];
    $itmsize = mysql_num_rows(mysql_query("SELECT * FROM Items WHERE owner='$id' AND type>=19 AND istatus=0"));
    if ($itmsize < $pouch_max)
    {
      $gold = $char[gold]-$worth; 
      $char[gold]=$gold;
      $base= $sitmlist[$buy][0];
      $itype=$item_base[$base][1];
      $istats= $item_base[$base][0];
      $itime = time();
      $result = mysql_query("INSERT INTO Items (owner,type,    cond, istatus,points,society,last_moved,base,   prefix,suffix,stats) 
                                        VALUES ('$id','$itype','100','0',    '0',   '',     '$itime',  '$base','',    '',    '$istats')");         
        
      if ($shopg[$type][$sitmlist[$buy][4]][$sitmlist[$buy][5]]>0)
        $shopg[$type][$sitmlist[$buy][4]][$sitmlist[$buy][5]] -= 1;
      $location[shopg]=serialize($shopg);
      $town_share = intval($worth/2);
      if ($town_share > 0) $location[bank] += $town_share;
      if ($society[id]) updateSocRep($society, $location[id], $worth);
      mysql_query("UPDATE Locations SET shopg='".$location[shopg]."', bank='$location[bank]' WHERE name='$loc'");
      mysql_query("UPDATE Users SET gold='$gold', lastbuy='$time' WHERE id=$id");
        
      $message = strtolower($_GET[name])." purchased for ".displayGold($worth);
    }
    else 
    {
      $message = "You have no more room to carry that.";
    }
    $bought_something = 1;
  }
  else
    $message = "You do not have enough gold";
}
else if ($buy >0)
  $message = "The pattern shifted in an odd way. Please try again...";

// Regrab shop list   
$x=0;
$sitmlist='';
for ($i =0; $i < 4; $i++)
{
  for ($j=0; $j<12; $j++)
  {
     if ($shopg[$type][$i][$j] != 0)
     { 
       $sitmlist[$x][0] = $consumable_quality[$type][$i]." ".$item_list[$type][$j];
       $sitmlist[$x][1] = "";
       $sitmlist[$x][2] = "";
       $sitmlist[$x][3] = $type;
       $sitmlist[$x][4] = $i;
       $sitmlist[$x][5] = $j; 
       $x++;
     }
  }  
}
$slistsize=count($sitmlist);

// split list into 4 lists for display
if ($shop < 4)
{
  $x=0;
  while ($x < $slistsize)
  {
    $ditmlist[$sitmlist[$x][4]][$x] = $sitmlist[$x];
    $x++;
  }
}

$wikilink = $wikilinks[$shop-1];

// DRAW PAGE
  $town_img_name = str_replace(' ','_',strtolower($char['location']));
  $town_img_name = str_replace('&#39;','',strtolower($town_img_name));

if (!$is_town) $message = "There are no businesses in ".str_replace('-ap-','&#39;',$char['location']);
$bg="";
if ($mode != 1) 
{
  $bg = "background-image:url('images/townback/".$town_img_name.".jpg'); ";
}
include('header.htm');

if ($is_town)
{
if ($shop < 4)
{
  echo "<div style=\"".$bg."\">";
?>
<div class='row'>
  <div class='col-sm-3'>
    <img class='img-optional-nodisplay' src='images/ShopKeeps/<?php echo str_replace(' ','_',$char['location']).$shop;?>.jpg' /><br/><br/>
    <?php
      if ($shop == 2) $kmessage = "Welcome to<br/>the local $shopname";
      else $kmessage = "Welcome to<br/>$shopname";
      echo "<i>".$kmessage."!</i><br/>";
    ?>
  </div>
<?php
}

if ($shop==1)
{
  if ($char[stamaxa]>$char[stamina])
  {
    $room_cost = ($char[stamaxa]-$char[stamina])*60;
    if ($iv) $room_cost = floor($room_cost*((100+$iv)/100));
?>
  <div class='col-sm-9'>
    Take a room for <?php echo displayGold($room_cost);?>?<br/>
    <a class='btn btn-default btn-sm' href="business.php?shop=<?php echo $shop;?>&room=1">Rent</a>
  </div>
<?php
  }
}
else if ($shop == 2)
{
?>
<form name="terForm" action="business.php?shop=2" method="post">
  <input type='hidden' name='combTer' id='combTer' value=''><input type='hidden' name='combItm' id='combItm' value=''>
  <input type='hidden' name='combTEq' id='combTEq' value=''><input type='hidden' name='combIEq' id='combIEq' value=''>
  <div class='col-sm-9'>
    <div class='row'>
      <div class='col-sm-4'>
        <div id='terstats' width='200'>
          <?php echo "Select a Ter'angreal<br/><br/><br/><br/><br/><br/>";?>
        </div>
      </div>
      <div class='col-sm-4'>
        <div id='istats' width='200'>
          <?php echo "Select an Item!<br/><br/><br/><br/><br/><br/>";?>
        </div>
      </div>
      <div class='col-sm-4'>
        <div id='newstats' width='200'>
          <?php echo "Combine Ter'angreal with an Item!<br/><br/><br/><br/><br/><br/>";?>
        </div>
      </div>
    </div>
    <div class='row'>
      <div class='col-sm-8'>
        <div class='row'>
          <div class='col-sm-5 col-md-4 col-lg-3 solid-back' align='right'>
            <?php echo "Use Ter'angreal:";?>
          </div>
          <div class='col-sm-7 col-md-8 col-lg-9'>
            <select class="form-control gos-form" name='ters' id='ters' style='width: 95%' onchange="updateTerLists()"><option value='-1'>None</option></select>
          </div>
        </div>
        <div class='row'>
          <div class='col-sm-5 col-md-4 col-lg-3 solid-back' align='right'>         
            Combine With:
          </div>
          <div class='col-sm-7 col-md-8 col-lg-9'>
            <select class="form-control gos-form" name='titm' id='titm' style='width: 95%' onchange="updateTerLists()"><option value='-1'><?php echo "Select Ter'angreal"; ?></option></select>
          </div>
        </div>
      </div>
      <div class='col-sm-4'>
        <!--<button class='btn btn-default btn-md btn-block' onClick="javascript:combineItems()">Combine Items</button>-->
        <a id='combineButton' data-href="javascript:submitTerForm();" data-toggle="confirmation" data-placement="top" title="Are you sure you want to combine these items?" class='btn btn-default btn-md btn-block'>Combine Items</a>
      </div>
    </div>
  </div>
</form>
<script type="text/javascript">
  updateTerLists();
</script>  
<?php
}
else if ($shop == 3)
{
?>
  <div class='col-sm-9'>
    Join a dice game?<br/>
    <a class='btn btn-default btn-sm' href="dice.php">Roll</a>
  </div>
<?php
}
else if ($shop == 4)
{
?>
<form name='outfitform' method='post' action='business.php?shop=4'>
  <div class='row'>
    <div class='col-sm-4'>
      <table class='table table-condensed table-striped table-responsive table-bordered small solid-back'>
        <tr><td colspan='5' align='center'><h4 class="text-success">Equipment Storage</h4></td></tr>
        <tr>
          <td>&nbsp;</td>
          <td>Name</td>
          <td>Storage</td>
          <td>Req. Pull</td>
          <td>Cost</td>
        </tr>

        <?php
          for ($x=0; $x<count($pack_name); $x++)
          {
            if ($x == $char[travelmode2])
              $color = 'info';
            else
              $color = '';

            echo "<tr class='".$color."'>";
            echo "<td><input type='radio' name='packrad' value='".$x."'";
            if ($x == $char[travelmode2]) echo " checked></td>"; else echo "></td>";
            echo "<td>".ucwords($pack_name[$x][0])."</td><td>".($base_inv_max+$pack_name[$x][1])."</td><td>".$pack_name[$x][2]."</td>";
            if ($x == $char[travelmode2]) echo "<td>&nbsp;</td>"; else echo "<td>".displayGold(intval(($pack_cost[$x]-($pack_cost[$char[travelmode2]]/2))*(100+$sv)/100))."</td>";
            echo "</tr>";
          }
        ?>
      </table>
    </div>
    <div class='col-sm-4'>    
      <table class='table table-condensed table-striped table-responsive table-bordered small solid-back'>
        <tr><td colspan='4' align='center'><h4 class="text-primary">Consumable Storage</h4></td></tr>
        <tr>
          <td>&nbsp;</td>
          <td>Name</td>
          <td>Storage</td>
          <td>Cost</td>
        </tr>

        <?php
          for ($x=0; $x<count($pouch_name); $x++)
          {
            if ($x == $char[pouch_type])
              $color = 'info';
            else
              $color = '';

            echo "<tr class='".$color."'>";
            echo "<td><input type='radio' name='pouchrad' value='".$x."'";
            if ($x == $char[pouch_type]) echo " checked></td>"; else echo "></td>";
            echo "<td>".ucwords($pouch_name[$x][0])."</td><td>".(4+$pouch_name[$x][1])."</td>";
            if ($x == $char[pouch_type]) echo "<td>&nbsp;</td>"; else echo "<td>".displayGold(intval(($pouch_cost[$x]-($pouch_cost[$char[pouch_type]]/2))*(100+$sv)/100))."</td>";
            echo "</tr>";
          }
        ?>
      </table>
    </div>
    <div class='col-sm-4'>
      <table class='table table-condensed table-striped table-responsive table-bordered small solid-back'>
        <tr><td colspan='5' align='center'><h4 class="text-warning">Horse</h4></td></tr>
        <tr>
          <td>&nbsp;</td>
          <td>Name</td>
          <td>Stamina</td>
          <td>Pull</td>
          <td>Cost</td>
        </tr>

        <?php
          for ($x=0; $x<count($travel_mode); $x++)
          {
            if ($x == $char[travelmode])
              $color = 'info';
            else
              $color = '';

            echo "<tr class='".$color."'>";
            echo "<td><input type='radio' name='horserad' value='".$x."'";
            if ($x == $char[travelmode]) echo " checked></td>"; else echo "></td>";
            echo "<td>".ucwords($travel_mode[$x][0])."</td><td>".($travel_mode[$x][1])."</td><td>".$travel_mode[$x][2]."</td>";
            if ($x == $char[travelmode]) echo "<td>".$char[travelmode_name]."</td>"; else echo "<td>".displayGold(intval(($travel_mode_cost[$x]-($travel_mode_cost[$char[travelmode]]/2))*(100+$uv)/100))."</td>";
            echo "</tr>";
          }
        ?>
      </table>
    </div>
  </div>
  <div class='row'>
    <div class='col-sm-12'>
    <?php
      if ($char['feedneed']*$char['travelmode'])
      {
    ?>
        <a class='btn btn-warning btn-sm' href="business.php?shop=4&feed=1"><?php echo "Feed ".$char['travelmode_name'];?></a>
    <?php
        $fcost=intval(($char['feedneed']*$char['travelmode']*20)*($uv+100)/100);
        echo displayGold($fcost)."</b><br/><br/>";
      }  
    ?>
      <input type='hidden' name='outfit' value='1'/>
      <input type='submit' class='btn btn-success btn-sm' name='ofsub' value='Change Gear'/>
    </div>
  </div>
</form>
<?php
}

if ($shop <4)
{
?>
</div>
<div class='row'>
  <div class='col-sm-6'>
    <?php displayItemTable($ditmlist[0], 0); ?>
  </div>
  <div class='col-sm-6'>
    <?php displayItemTable($ditmlist[1], 1); ?>
  </div>
  <div class='col-sm-6'>
    <?php displayItemTable($ditmlist[2], 2); ?>
  </div>
  <div class='col-sm-6'>
    <?php displayItemTable($ditmlist[3], 3); ?>
  </div>
</div>
</div>
<?php
} // shop < 4
} // isTown
} // !isDestroyed
else
{
  $message = "The remains of ".$shopname;
  if ($mode != 1)
  {
    $bg = "background-image:url('images/townback/".str_replace(' ','_',strtolower($char['location'])).".jpg'); ";
  }
  include('header.htm');
}
include('footer.htm');
?>