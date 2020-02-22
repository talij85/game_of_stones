<?php
// establish a connection with the database
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/charFuncs.php");
include_once("admin/duelFuncs.php");
include_once("admin/locFuncs.php");
include_once("map/mapdata/coordinates.inc");
// Find all equipped items
include_once("admin/equipped.php");

$wikilink = "Inventory";

$sortBy=mysql_real_escape_string($_POST['sort']);
$sortCBy = mysql_real_escape_string($_POST['sort2']);
if ($sortBy != "") {  mysql_query("UPDATE Users SET sort_inv='".$sortBy."' WHERE id=$id"); $char[sort_inv]= $sortBy;}
if ($sortCBy != "") { mysql_query("UPDATE Users SET sort_consume='".$sortCBy."' WHERE id=$id"); $char[sort_consume] = $sortCBy;}

$invSort = sortItems($char[sort_inv]);
$consumeSort = sortItems($char[sort_consume]);

$id = $char['id'];
$itmlist="";
$listsize=0;
$wEq= 0;
$a = -1;
$b = -1;
$c = -1;
$d = -1;
$e = -1;
$currw = "";
$iresult=mysql_query("SELECT * FROM Items WHERE owner='$id' AND type<15 ".$invSort);
while ($qitem = mysql_fetch_array($iresult))
{
  $itmlist[$listsize++] = $qitem;

  if ($qitem[istatus]==1) $a = $listsize-1;
  else if ($qitem[istatus]==2) $b = $listsize-1;
  else if ($qitem[istatus]==3) $c = $listsize-1;
  else if ($qitem[istatus]==4) $d = $listsize-1;
  else if ($qitem[istatus]==5) $e = $listsize-1;
}

$currw = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND type='8'"));
if ($currw[istatus] > 0) $wEq = 1;


$clistsize=0;
$pouch="";
$stomach="";
$fullness=0;
$presult=mysql_query("SELECT * FROM Items WHERE owner='$id' AND type>=19 ".$consumeSort);
while ($qitem = mysql_fetch_array($presult))
{
  if ($qitem[istatus]==0) $pouch[$clistsize++] = $qitem;
  else $stomach[$fullness++] = $qitem;
}

$types = unserialize($char[type]);

$message = mysql_real_escape_string($_GET['message']);
$action = mysql_real_escape_string($_POST['action']);
$consume=mysql_real_escape_string($_POST['consume']);
$inv = mysql_real_escape_string($_POST['inv']);
$tab = mysql_real_escape_string($_POST['tab']);
$newWeave = mysql_real_escape_string($_POST['newweave']);

$myWeaves='';
$tskills = '';

$doequip = mysql_real_escape_string($_REQUEST['doequip']);

$bonuses1 = $stomach_bonuses1." ".$stamina_effect1." ".$clan_bonus1." ".$skill_bonuses1." ".$clan_building_bonuses;
$skills = $bonuses1;

// if a channeler, get my weaves
if (isChanneler($types))
{
  $myWeaves = getWeaves($item_list,$item_base,$skills);
}

if ($doequip)
{
  $e1 = mysql_real_escape_string($_REQUEST['equip1']);
  $e2 = mysql_real_escape_string($_REQUEST['equip2']);
  $e3 = mysql_real_escape_string($_REQUEST['equip3']);
  $e4 = mysql_real_escape_string($_REQUEST['equip4']);
  $e5 = mysql_real_escape_string($_REQUEST['equip5']);
  $e1n = mysql_real_escape_string($_REQUEST['equip1name']);
  $e2n = mysql_real_escape_string($_REQUEST['equip2name']);
  $e3n = mysql_real_escape_string($_REQUEST['equip3name']);
  $e4n = mysql_real_escape_string($_REQUEST['equip4name']);
  $e5n = mysql_real_escape_string($_REQUEST['equip5name']);

  $good = 1;
  if ($good)
  {
    $aid = $itmlist[$a][id];
    $bid = $itmlist[$b][id];
    $cid = $itmlist[$c][id];
    $did = $itmlist[$d][id];
    $eid = $itmlist[$e][id];
    $e1id = $itmlist[$e1][id];
    $e2id = $itmlist[$e2][id];
    $e3id = $itmlist[$e3][id];
    $e4id = $itmlist[$e4][id];
    $e5id = $itmlist[$e5][id];

    if ($a>=0)  mysql_query("UPDATE Items SET istatus='0' WHERE id='".$aid."'");
    if ($b>=0)  mysql_query("UPDATE Items SET istatus='0' WHERE id='".$bid."'");
    if ($c>=0)  mysql_query("UPDATE Items SET istatus='0' WHERE id='".$cid."'");
    if ($d>=0)  mysql_query("UPDATE Items SET istatus='0' WHERE id='".$did."'");
    if ($e>=0)  mysql_query("UPDATE Items SET istatus='0' WHERE id='".$eid."'");
    if ($e1>=0) mysql_query("UPDATE Items SET istatus='1' WHERE id='".$e1id."'");
    if ($e2>=0) mysql_query("UPDATE Items SET istatus='2' WHERE id='".$e2id."'");
    if ($e3>=0) mysql_query("UPDATE Items SET istatus='3' WHERE id='".$e3id."'");
    if ($e4>=0) mysql_query("UPDATE Items SET istatus='4' WHERE id='".$e4id."'");
    if ($e5>=0) mysql_query("UPDATE Items SET istatus='5' WHERE id='".$e5id."'");
    
    $listsize=0;
    $iresult=mysql_query("SELECT * FROM Items WHERE owner='$id' AND type<15 ".$invSort);
    while ($qitem = mysql_fetch_array($iresult))
    {
      $itmlist[$listsize++] = $qitem;
      
      if ($qitem[istatus]==1) $a = $listsize-1;
      else if ($qitem[istatus]==2) $b = $listsize-1;
      else if ($qitem[istatus]==3) $c = $listsize-1;
      else if ($qitem[istatus]==4) $d = $listsize-1;
      else if ($qitem[istatus]==5) $e = $listsize-1;
    }
    $message = "Equipment changed!";
  }
  else // bad
  {
    $message = "Something odd happened while equipping. Please try again.";
  }
}

// change weaves
if ($newWeave>=0 && $newWeave!= '') 
{  
  // check that weave was found
  if ($currw)
  {
    $nextw = $currw;
    $nextw[base] = $myWeaves[$newWeave][0];
    if ($wEq)
    {
      $currwPts = lvl_req(getIstats($currw, $skills),getTypeMod($skills,$currw[type]));
      $nextwPts = lvl_req(getIstats($nextw, $skills),getTypeMod($skills,$nextw[type]));
      $ptsDiff = $nextwPts-$currwPts;
      $ptsLeft = $char[equip_pts]-$char[used_pts];
      if ($ptsLeft <= $ptsDiff) $nextw[istatus]=-2;
    }
    mysql_query("UPDATE Items SET istatus='".$nextw[istatus]."', base='".$nextw[base]."' WHERE id=$currw[id]");
    $message = "You are now ready to use ".ucwords($myWeaves[$newWeave][0]);
    $currw=$nextw;
  }
  else {$message = "Something odd happened. Please try again...";}
}
$invWeave=$currw[base];

$good = 1;
$oldlist = $itmlist;
$oldpouch = $pouch;

$ustats = mysql_fetch_array(mysql_query("SELECT * FROM Users_stats WHERE id='$id'"));

// EQUIPMENT ACTIONS

// SELL ITEMS
if ($action == 1) // SELL
{
  if ($location_array[$char['location']][2])
  {
    $x=0;
    $value=0;
    $q=0;
    while ($x < $listsize)
    {
      $tmpItm = mysql_real_escape_string($_POST[$x]);
      if ($tmpItm)
      {
        $sitem=mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE id='$tmpItm'"));
        if ($sitem['society'] == 0 && $sitem[type] != 0)
        {
          if ($sitem['istatus']==0)
          {
            if ($sitem['type'] > 13) $value += intval(0.5*$item_base[$sitem['name']][2]);
            else $value += intval(0.5*item_val(iparse($sitem,$item_base, $item_ix,$ter_bonuses)));
            $result5 = mysql_query("DELETE FROM Items WHERE id='$sitem[id]'");
            $q++;
          }
        }
      }
      $x++;
    }
    $listsize = $listsize - $q;
    if ($good && $q > 0)
    {
      $s = "";
      if ($q >1) $s ="s";
      $chargold = $value + $char[gold];
      $ustats['item_earn'] += $value;
      $result = mysql_query("UPDATE Users_stats SET item_earn='".$ustats[item_earn]."' WHERE id='".$id."'");
      $result = mysql_query("UPDATE Users SET gold='".$chargold."' WHERE id='$id'");
      $message="Item$s sold for ".displayGold($value);
    }
    else 
    {
      $message = "You didn't select anything to sell!";
    }
  }
  else 
  {
    $message = "You can't do that from here! ";
  }
}
elseif ($action == 2) // CACHE / UNCACHE
{
  $itm=0;
  while ($itm < $listsize)
  {
    $tmpItm = mysql_real_escape_string($_POST[$itm]);
    if ($tmpItm)
    {
      $sitem=mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE id='$tmpItm'"));
      if ($sitem['istatus'] == 0)
      {
        $result = mysql_query("UPDATE Items SET istatus='-2' WHERE id='$tmpItm'");
      }
      elseif ($sitem['istatus'] == -2)
      {
        $result = mysql_query("UPDATE Items SET istatus='0' WHERE id='$tmpItm'");
      }
    }
    $itm++;
  }
  $message="Cache Updated!";
}
elseif ($action == 3) // DONATE
{
  $soc_name = $char['society'];
  $society = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$soc_name'"));
  $upgrades = unserialize($society['upgrades']);
  $maxvault = 50+10*$upgrades[0];
  $vaultsize = mysql_num_rows(mysql_query("SELECT * FROM Items WHERE society='$society[id]'"));
  
  $z=0;
  $x=0;
  $q=0;
  $dlist = "";
  while ($x < $listsize)
  {
    $tmpItm = mysql_real_escape_string($_POST[$x]);
    if ($tmpItm)
    {
      $sitem=mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE id='$tmpItm'"));
      if ($sitem[society]== 0 && $sitem[type] != 0)
      {
        if ($sitem['istatus']==0)
        {
          $dlist[$q]=$sitem; 
          $q++;     
        }
      }
    }
    $x++;
  }
  if ($vaultsize+$q > $maxvault)
  {
    $good = 0;
    $message = "There is not enough room in the vault!";
  }
  if ($good && $q > 0)
  {
    $nowner = 10000+$society[id];
    for ($x=0; $x<$q; $x++)
    {
      $result = mysql_query("UPDATE Items SET society='".$society[id]."', owner='$nowner', last_moved='".time()."' WHERE id='".$dlist[$x][id]."'");
      $ustats[items_donated]++;
    }
    $s = "";
    if ($q > 1) $s ="s";
    $result = mysql_query("UPDATE Users_stats SET items_donated='".$ustats[items_donated]."' WHERE id='".$id."'");        
    $message="Item$s donated to clan vault";
  }
}
elseif ($action == 4) // DROP
{
  if (!$location_array[$char['location']][2])
  {
    $z=0;
    $x=0;
    $q=0;
    while ($x < $listsize)
    {
      $tmpItm = mysql_real_escape_string($_POST[$x]);
      if ($tmpItm)
      {
        $sitem=mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE id='$tmpItm'"));
        if ($sitem['society']== 0)
        {
          if ($sitem['istatus']==0)
          {
            $result5 = mysql_query("DELETE FROM Items WHERE id='$sitem[id]'");
            $q++;
            $ustats[items_dropped]++;
          }
        }
      }
      $x++;
    }
    $listsize = $listsize - $q;
    if ($good && $q > 0)
    {
      $s = "";
      if ($q >1) $s ="s";
      $ustats['item_earn'] += $value;
      $result = mysql_query("UPDATE Users_stats SET items_dropped='".$ustats[items_dropped]."' WHERE id='".$id."'");
      $message="Item$s dropped.";
    }
    else 
    {
      $message = "You didn't select anything to drop!";
    }
  }
  else 
  {
    $message = "You can't do that from here! ";
  }
}
elseif ($action == 5) // ESTATE
{
  $myEstate= mysql_fetch_array(mysql_query("SELECT * FROM Estates WHERE owner='$id' AND location='$char[location]'"));
  $upgrades = unserialize($myEstate['upgrades']);
  $maxestinv = 3+3*$upgrades[2];
  $eid=20000+$myEstate[id];
  $estinvsize=mysql_num_rows(mysql_query("SELECT id FROM Items WHERE owner='$eid'"));
  
  $z=0;
  $x=0;
  $q=0;
  $dlist = "";
  while ($x < $listsize)
  {
    $tmpItm = mysql_real_escape_string($_POST[$x]);
    if ($tmpItm)
    {
      $sitem=mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE id='$tmpItm'"));
      if ($sitem[society] == 0 && $sitem[type] != 0)
      {
        if ($sitem[istatus]==0)
        {
          if ($estinvsize+$q<$maxestinv) 
          {
            $dlist[$q++]= $sitem;
          }
          else 
           {
            $message = "There is not enough room at your estate!";
            $good = 0;
          }        
        }
      }
    }
    $x++;
  }
  if ($good && $q > 0)
  {
    $nowner = 20000+$myEstate[id];
    for ($x=0; $x<$q; $x++)
    {
      $result = mysql_query("UPDATE Items SET owner='$nowner', last_moved='".time()."' WHERE id='".$dlist[$x][id]."'");
    }
    $s = "";
    if ($q > 1) $s ="s";
    $message="Item$s sent to Estate";
  }
}
// CONSUMABLE ACTIONS
// SELL ITEMS
if ($consume == 1) // SELL
{
  if ($location_array[$char['location']][2])
  {
    $x=0;
    $value=0;
    $q=0;
    while ($x < $clistsize)
    {
      $tmpItm = mysql_real_escape_string($_POST[$x]);
      if ($tmpItm)
      {
        $sitem=mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE id='$tmpItm'"));
        if ($sitem['istatus']==0)
        {
          $value += intval(0.5*$item_base[$pouch[$x][base]][2]);
          $result5 = mysql_query("DELETE FROM Items WHERE id='$sitem[id]'");
          $q++;
        }
      }
      $x++;
    }
    $clistsize = $clistsize - $q;

    if ($good && $q > 0)
    {
      $s = "";
      if ($q >1) $s ="s";
      $chargold = $value + $char[gold];
      $querya = "UPDATE Users SET gold='".$chargold."' WHERE id='$id'";
      $result = mysql_query($querya);
      $message="Item$s sold for ".displayGold($value);
    }
    else 
    {
      $message = "You didn't select anything!";
    }
  }
  else 
  {
    $message = "You can't do that from here! ";
  }
}
elseif ($consume == 2) // DROP
{
  if (!$location_array[$char['location']][2])
  {
    $x=0;
    $q=0;
    while ($x < $clistsize)
    {
      $tmpItm = mysql_real_escape_string($_POST[$x]);
      if ($tmpItm)
      {
        $sitem=mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE id='$tmpItm'"));
        if ($sitem['istatus']==0)
        {
          $result5 = mysql_query("DELETE FROM Items WHERE id='$sitem[id]'");
          $q++;
        }
      }
      $x++;
    }
    $clistsize = $clistsize - $q;

    if ($good && $q > 0)
    {
      $s = "";
      if ($q >1) $s ="s";
      $message="Item$s dropped.";
    }
    else 
    {
      $message = "You didn't select anything to drop!";
    }
  }
  else 
  {
    $message = "You can't do that from here! ";
  }
}
else if ($consume == 3) // consume
{
  $z=0;
  $x=0;
  $q=0;
  $eatlist="";
  
  $newstam = $char[stamina];
  
  while ($x < $clistsize)
  {
    $tmpItm = mysql_real_escape_string($_POST[$x]);
    if ($tmpItm)
    {
      if ($fullness+$q<2)
      {
        $sitem=mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE id='$tmpItm'"));
        $eatlist[$q++]=$sitem;
      }
      else 
      {
        $message = "You are too full to consume more right now!";
        $good = 0;
      }
    }
    $x++;
  }
  
  if ($good && $q > 0)
  {
    for ($x=0; $x< $q; $x++)
    {
      $stats= cparse($eatlist[$x][stats]);
      if ($eatlist[$x][type] == 20) $pro_turnup = $pro_stats[hU];
      elseif ($eatlist[$x][type] == 21) $pro_turnup = $pro_stats[dU];
      elseif ($eatlist[$x][type] == 19)
      {
        $newstam += $stats['M']+$pro_stats[fS];
        if ($newstam > $char[stamaxa]) $newstam=$char['stamaxa'];        
        $pro_turnup = $pro_stats[fU];
      }
      $digest = $stats[dC];
      $eatlist[$x][istatus]=$digest+$pro_turnup;
      $result = mysql_query("UPDATE Items SET istatus='".$eatlist[$x][istatus]."' WHERE id='".$eatlist[$x][id]."'");
      $ustats[items_consumed]++;
    }

    $s = "";
    if ($q >1) $s ="s";
    $result = mysql_query("UPDATE Users_stats SET items_consumed='".$ustats[items_consumed]."' WHERE id='".$id."'"); 
    $resulta = mysql_query("UPDATE Users SET stamina='$newstam' WHERE id='$id'");    
    $message="Item$s consumed. Mmm! Tasty!";
  }
  elseif ($good && $q==0)
  {
    $message="You didn't select anything to consume!";
  }
}

$itmlist="";
$listsize=0;
$iresult=mysql_query("SELECT * FROM Items WHERE owner='$id' AND type<15 ".$invSort);
while ($qitem = mysql_fetch_array($iresult))
{
  $itmlist[$listsize++] = $qitem;
}

$clistsize=0;
$pouch="";
$stomach="";
$fullness=0;
$presult=mysql_query("SELECT * FROM Items WHERE owner='$id' AND type>=19 ".$consumeSort);
while ($qitem = mysql_fetch_array($presult))
{
  if ($qitem[istatus]==0) $pouch[$clistsize++] = $qitem;
  else $stomach[$fullness++] = $qitem;
}

$a = '';
$b = '';
$c = '';
$d = '';
$e = '';
$a = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus='1' AND type<15"));
$b = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus='2' AND type<15"));
$c = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus='3' AND type<15"));
$d = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus='4' AND type<15"));
$e = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus='5' AND type<15"));

?>
<script type="text/javascript">
  var invinfo = new Array();
  var inv = new Array();
  var skills = '<?php echo $skills; ?>';
  var myPts = <?php echo $char['equip_pts'];?>;
  var cnation = <?php echo $char['nation'];?>;
  var ai = -1;
  var bi = -1;
  var ci = -1;
  var di = -1;
  var ei = -1;
<?php
  for ($i=0; $i<$listsize; ++$i)
  {
    echo "  inv[".$i."] = new Array('".str_replace(" ","_",$itmlist[$i][base])."','".str_replace(" ","_",$itmlist[$i][prefix])."','".str_replace(" ","_",$itmlist[$i][suffix])."','".$itmlist[$i][cond]."');";
    if ($mode==0)        { echo "  invinfo[".$i."] = \"<FIELDSET class=abox><LEGEND><font class=littletext><b>".ucwords($itmlist[$i][prefix]." ".$itmlist[$i][base])." ".str_replace("Of","of",ucwords($itmlist[$i][suffix]))."</b></font></LEGEND><center><br/><img border='0' bordercolor='black' src='items/".str_replace(' ','',$itmlist[$i][base]).".gif'><font class=littletext><br/><br/>\" + "; }
    else if ($mode == 1) { echo "  invinfo[".$i."] = \"<FIELDSET class=abox><LEGEND><font class=littletext><b>".ucwords($itmlist[$i][prefix]." ".$itmlist[$i][base])." ".str_replace("Of","of",ucwords($itmlist[$i][suffix]))."</b></font></LEGEND><center><br/><font class=littletext><br/><br/>\" + "; }
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
  
  if (isChanneler($types))
  {
    echo "  var weaveinfo = new Array();";
    for ($w=0; $w < count($myWeaves); $w++)
    {
      echo "  weaveinfo[".$w."] = \"<FIELDSET class=abox><LEGEND><font class=littletext><b>".ucwords($myWeaves[$w][0])."</b></font></LEGEND><center><br/><img border='0' bordercolor='black' src='items/".str_replace(' ','',$myWeaves[$w][0]).".gif'><font class=littletext><br/><br/>\" + ";  
      echo "itm_info(cparse(weaveStats('".$myWeaves[$w][0]."',skills)))+\"<br/><br/></FIELDSET>\";\n";
    }
  }
?>

function swapinfo(myitm)
{
  document.getElementById('itemstats').innerHTML=invinfo[myitm];
}

function weaveSwapinfo(myitm)
{
  document.weaveForm.newweave.value = myitm;
  document.getElementById('weavestats').innerHTML=weaveinfo[myitm];
}

function updateEquipLists(init)
{
  var equip1 = document.getElementById('equip1');
  var equip2 = document.getElementById('equip2');
  var equip3 = document.getElementById('equip3');
  var equip4 = document.getElementById('equip4');
  var equip5 = document.getElementById('equip5');
  var a = equip1.value;
  var b = equip2.value;
  var c = equip3.value;
  var d = equip4.value;
  var e = equip5.value;
  if (init != 0)
  {
    a = ai;
    b = bi;
    c = ci;
    d = di;
    e = ei;
  }

  var estats =  getstats(a,b,c,d,e);
  var tskills = skills+" "+estats[0];
  
  equip1.options.length = 0;
  equip2.options.length = 0;
  equip3.options.length = 0;
  equip4.options.length = 0;
  equip5.options.length = 0;
  
  var t1 = 0;
  var t2 = 0;
  var t3 = 0;
  var t4 = 0;
  var t5 = 0; 
  if (a != -1) t1 = item_base[inv[a][0]][1];
  if (b != -1) t2 = item_base[inv[b][0]][1];
  if (c != -1) t3 = item_base[inv[c][0]][1];
  if (d != -1) t4 = item_base[inv[d][0]][1];
  if (e != -1) t5 = item_base[inv[e][0]][1];
  
  var tmp = '';
  var ttype = 0;
  var tpts = 0;
  var tstats = '';
  var aielsword = 0;
  for (var i=-1; i<inv.length; i++) 
  {
    aielsword = 0;
    if (i>=0)
    {
      ttype = item_base[inv[i][0]][1];
      if (ttype == 8)
      {
        tstats = weaveStats(inv[i][0],tskills);
      }
      else
      { 
        tstats = iparse(inv[i][0],inv[i][1],inv[i][2],inv[i][3]);
      }
      if (cnation == 1 && ttype == 1) aielsword=1;
              
      tpts = (lvl_req(tstats,getTypeMod(tskills,item_base[inv[i][0]][1])));
      if (myPts > tpts*2 && aielsword == 0)
      {   
        tmp = ucwords(inv[i][1].replace(/_/g, " ")+" "+inv[i][0].replace(/_/g, " ")+" "+inv[i][2].replace(/_/g, " ") + " - " + tpts);

        if (ttype > 0)
        {
          if (ttype < 9 || ttype == 12)
          {
            if (ttype != t5) equip1.options[equip1.options.length] = new Option(tmp,i);
            if (ttype != t1) equip5.options[equip5.options.length] = new Option(tmp,i);
            if (i==a) 
            {
              equip1.selectedIndex = equip1.options.length-1;
            }
            else if (i==e) 
            {
              equip5.selectedIndex = equip5.options.length-1;
            }
          }
          else 
          {
            if (ttype != t3 && ttype != t4 ) equip2.options[equip2.options.length] = new Option(tmp,i);
            if (ttype != t2 && ttype != t4 ) equip3.options[equip3.options.length] = new Option(tmp,i);
            if (ttype != t2 && ttype != t3 ) equip4.options[equip4.options.length] = new Option(tmp,i);
            if (i==b) 
            {
              equip2.selectedIndex = equip2.options.length-1;
            } 
            else if (i==c) 
            {
              equip3.selectedIndex = equip3.options.length-1;
            }
            else if (i==d) 
            {
              equip4.selectedIndex = equip4.options.length-1;
            }
          }
        }
      }
    }
    else
    {
      equip1.options[equip1.options.length] = new Option('Unequipped',i);
      equip2.options[equip2.options.length] = new Option('Unequipped',i);
      equip3.options[equip3.options.length] = new Option('Unequipped',i);
      equip4.options[equip4.options.length] = new Option('Unequipped',i);
      equip5.options[equip5.options.length] = new Option('Unequipped',i);
    }
  }
  calcstats(a,b,c,d,e,'newstats');

}

function getstats(myitm1,myitm2,myitm3,myitm4,myitm5)
{
  var estats = new Array();
  estats[0] = "";
  if (myitm1>=0)
  {
    if (item_base[inv[myitm1][0]][1] == 8)
      estats[1] = weaveStats(inv[myitm1][0],skills);
    else
      estats[1] = iparse(inv[myitm1][0],inv[myitm1][1],inv[myitm1][2],inv[myitm1][3]);
    estats[0] += " " + (estats[1]);
  }
  if (myitm2>=0)
  {
    if (item_base[inv[myitm2][0]][1] == 8)
      estats[2] = weaveStats(inv[myitm2][0],skills);
    else
      estats[2] = iparse(inv[myitm2][0],inv[myitm2][1],inv[myitm2][2],inv[myitm2][3]);
    estats[0] += " " + (estats[2]);
  }
  if (myitm3>=0)
  {
    if (item_base[inv[myitm3][0]][1] == 8)
      estats[3] = weaveStats(inv[myitm3][0],skills);
    else
      estats[3] = iparse(inv[myitm3][0],inv[myitm3][1],inv[myitm3][2],inv[myitm3][3]);
    estats[0] += " " + (estats[3]);
  }
  if (myitm4>=0)
  {
    if (item_base[inv[myitm4][0]][1] == 8)
      estats[4] = weaveStats(inv[myitm4][0],skills);
    else
      estats[4] = iparse(inv[myitm4][0],inv[myitm4][1],inv[myitm4][2],inv[myitm4][3]);
    estats[0] += " " + (estats[4]);
  }
  if (myitm5>=0)
  {
    if (item_base[inv[myitm5][0]][1] == 8)
      estats[5] = weaveStats(inv[myitm5][0],skills);
    else
      estats[5] = iparse(inv[myitm5][0],inv[myitm5][1],inv[myitm5][2],inv[myitm5][3]);
    estats[0] += " " + (estats[5]);
  }
  
  return estats;
}

function calcstats(myitm1,myitm2,myitm3,myitm4,myitm5,target)
{
  var str = "";
  var pts_tot = 0;
  var estats =  getstats(myitm1,myitm2,myitm3,myitm4,myitm5);

  var tskills = skills+" "+estats[0];
  
  if (myitm1>=0)
  {
    pts_tot += (lvl_req(estats[1],getTypeMod(tskills,item_base[inv[myitm1][0]][1])));
  }
  if (myitm2>=0)
  {
    pts_tot += (lvl_req(estats[2],getTypeMod(tskills,item_base[inv[myitm2][0]][1])));
  }
  if (myitm3>=0)
  {
    pts_tot += (lvl_req(estats[3],getTypeMod(tskills,item_base[inv[myitm3][0]][1])));
  }
  if (myitm4>=0)
  {
    pts_tot += (lvl_req(estats[4],getTypeMod(tskills,item_base[inv[myitm4][0]][1])));
  }
  if (myitm5>=0)
  {
    pts_tot += (lvl_req(estats[5],getTypeMod(tskills,item_base[inv[myitm5][0]][1])));
  }
  
  var oldstats = '';
  if (target == "newstats")
  {
     var ostats = getstats(ai,bi,ci,di,ei);
     oldstats = cparse(ostats[0]);
  }
  var totstats = itm_info(cparse(estats[0]),oldstats);
  var style = "littletext";
  if (pts_tot > myPts) style = "littletext_r";
  var output = "<font class='"+style+"'>Equip Points: "+pts_tot+"/"+myPts+"<br/><br/>"+totstats;
  document.getElementById(target).innerHTML=output;
}

function doEquip()
{
  var equip1 = document.getElementById('equip1');
  var equip2 = document.getElementById('equip2');
  var equip3 = document.getElementById('equip3');
  var equip4 = document.getElementById('equip4');
  var equip5 = document.getElementById('equip5');
  var a = equip1.value;
  var b = equip2.value;
  var c = equip3.value;
  var d = equip4.value;
  var e = equip5.value;

  var estats =  getstats(a,b,c,d,e);
  var tskills = skills+" "+estats[0];
  var pts_tot = 0;

  
  if ((a!=ai) || (b!=bi) || (c!=ci) || (d!=di) || (e!=ei) ) 
  {
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

    if (pts_tot <= myPts)
    {
      if (a>=0)
      {
        document.getElementById('equip1name').value = ucwords(inv[a][1].replace(/_/g, " ")+" "+inv[a][0].replace(/_/g, " ")+" "+inv[a][2].replace(/_/g, " "));
      }
      if (b>=0)
      {
        document.getElementById('equip2name').value = ucwords(inv[b][1].replace(/_/g, " ")+" "+inv[b][0].replace(/_/g, " ")+" "+inv[b][2].replace(/_/g, " "));
      }
      if (c>=0)
      {
        document.getElementById('equip3name').value = ucwords(inv[c][1].replace(/_/g, " ")+" "+inv[c][0].replace(/_/g, " ")+" "+inv[c][2].replace(/_/g, " "));
      }
      if (d>=0)
      {
        document.getElementById('equip4name').value = ucwords(inv[d][1].replace(/_/g, " ")+" "+inv[d][0].replace(/_/g, " ")+" "+inv[d][2].replace(/_/g, " "));
      }
      if (e>=0)
      {
        document.getElementById('equip5name').value = ucwords(inv[e][1].replace(/_/g, " ")+" "+inv[e][0].replace(/_/g, " ")+" "+inv[e][2].replace(/_/g, " "));
      }
      document.getElementById('doequip').value=1;
      document.equipForm.submit();
    }
    else
    {
      alert("You don't have enough points to equip all those!");
    }
  }
  else
  {
    alert("You already have that equipped!");
  } 
}
</script>
<?php

if ($mode == 1) { include('header2.htm'); }
else {include('header.htm');}
?>


<div class="tabber">
  <div class="tabbertab">
    <h3>Equipment</h3>
    <center>
    <?php
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
      $tskills = $skills." ".$estats[0];
      $pts_tot = 0;
      if ($a != "") $pts_tot += (lvl_req($estats[1], getTypeMod($tskills,$a[type])));
      if ($b != "") $pts_tot += (lvl_req($estats[2], getTypeMod($tskills,$b[type])));
      if ($c != "") $pts_tot += (lvl_req($estats[3], getTypeMod($tskills,$c[type])));
      if ($d != "") $pts_tot += (lvl_req($estats[4], getTypeMod($tskills,$d[type])));      
      if ($e != "") $pts_tot += (lvl_req($estats[5], getTypeMod($tskills,$e[type])));

      if ($pts_tot != $char['used_pts'])
      {
        $result = mysql_query("UPDATE Users SET used_pts='$pts_tot' WHERE id=$id");
      } 
    ?>
    <!-- MAIN PAGE -->
    <form name="equipForm" action="items.php" method="post">
    <table width="100%" align="center" border="1" cellspacing="0" cellpadding="5" valign="top">
      <tr>
        <td width=550>
          <table border=0 cellspacing=0 cellpadding="5">
            <tr>
<?php
              // DRAW EQUIPPED ITEMS
              $time = time();
              $item_one = 0;
              $last_blit = 0;
              for ($placeequip = 1; $placeequip < 6; $placeequip++)
              {
                $itemtoblit = 0;
                $blited = 0;
                $itemtoblit = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus='$placeequip' AND type<15"));
                if ($itemtoblit[id])
                {
                  $blited = 1;
                  $weapon = $estats[$placeequip];
                  $pointcost = (lvl_req($weapon, getTypeMod($tskills,$itemtoblit[type])));
?>
                    <td width="100"><center>                     
                      <table border=0 cellspacing=0 cellpadding=0>
                        <tr>
                          <td valign="center" height=35>
                            <center><b>
                              <a href="javascript:popUp('itemstat.php?<?php echo "base=".$itemtoblit[base]."&amp;prefix=".$itemtoblit[prefix]."&amp;suffix=".$itemtoblit[suffix]."&amp;lvl=".$char[level]."&amp;gender=".$char[sex]."&amp;cond=".$itemtoblit[cond]; ?>')">
                              <?php 
                                echo iname($itemtoblit)."</a></b><br/><font class=foottext>(";
                                if ($itemtoblit[type] < 12 && $itemtoblit[type] != 8) echo $itemtoblit[cond]."% :: ";
                                echo $pointcost."pts)"; 
                              ?>
                            </font></center>
                          </td>
                        </tr>
                      </table>
                      <?php 
                        if ($mode == 0)
                        {
                      ?>
                      <img border="0" bordercolor="black" src="items/<?php echo str_replace(" ","",$itemtoblit[base]); ?>.gif" valign='bottom'/>
                      <?php
                        }
                      ?>
                      <br/>
                    </center></td>
<?php
                }

                if ($mode == 0)
                {
                  // Draw nothing equipped images
                  if ($blited == 0 && $placeequip == 1)
                  {
                    echo "<td width=100 valign=top><table border=0 cellspacing=0 cellpadding=0 height=35><tr><td></td></tr></table></a><img src=items/hand1.gif border=0/></td>";
                  }
                  elseif ($blited == 0 && $placeequip == 5)
                  {
                    echo "<td width=100 valign=top><table border=0 cellspacing=0 cellpadding=0 height=35><tr><td></td></tr></table><img src=items/hand2.gif border=0/><br/><br/></td>";
                  }
                  elseif ($blited == 0)
                  {
                    echo "<td width=100 valign=top><table border=0 cellspacing=0 cellpadding=0 height=35><tr><td></td></tr></table><img src=items/torso".$char[sex].".gif border=0/></td>";
                  }
                }
                elseif ($blited == 0)
                {
                  echo "<td width=100 valign=top><table border=0 cellspacing=0 cellpadding=0 height=35><tr><td></td></tr></table></td>";
                }
              }
              $style="style='border-width: 0px; border-bottom: 1px solid #333333'";
?>
            </tr>
            
<?php
              for ($i=1; $i<=5; ++$i)
              {
                echo "<tr><td valign='top' align='right'>";
                if ($i==1 || $i==5) echo "Hand: "; else echo "Body: ";
                echo "</td>";
                echo "<td valign='top' colspan='3'><select class='form' name='equip$i' id='equip$i' style='width: 95%' onchange=\"javascript:updateEquipLists(0);\"><option value='-1'>Unequipped</option></select>";
                echo "<input type='hidden' name='equip".$i."name' id='equip".$i."name' value=''/></td>";
                if ($i==1) echo "<td rowspan='5'><input type='button' value='Change Gear' class='form' onclick='javascript:doEquip()'/></td>";
                echo "</tr>";
              }
              echo "</table>";
              echo "<input type='hidden' name='doequip' id='doequip' value='0'/>";
?>
        </td>
        <td width='200' valign='top'><center>
          <b>Current Stats</b><br/><br/>
          <div id='currstats' width='200'>
            <?php echo "<center>My Stats!</center>";?>
          </div>
        </center></td>
        <td width='200' valign='top'><center>
          <b>New Stats</b><br/><br/>
          <div id='newstats' width='200'>
            <?php echo "<center>My Stats!</center>";?>
          </div>
          <br/>
        </center></td>        
      </tr>
    </table>
    </form>
    <br/><br/>

    </center>
  </div>



  <div class="tabbertab<?php if ($tab==1) echo ' tabbertabdefault';?>">
    <h3>Inventory</h3>
    <table width='98%' cellspacing='10'>
      <tr>
        <td width='610' valign='top'>
          <center>
<!--  SECOND SET OF ITEMS        888888888888888888888888888888888888888 -->
          <form name="sortForm" action="items.php" method="post"><input type="hidden" name="sort" id="sort" value="" /><input type="hidden" name="tab" value="1"/>
          <table border="0" cellspacing="0" cellpadding="2">
            <tr>
              <td class='headrow' width="15"><input type="checkbox" name="toggler" onClick="javascript:checkedAll()"/></td>
              <td class='headrow' width="275"><center><a href="javascript:resortForm('0');"><b>Item</b></a></center></td>
              <td class='headrow' width="75"><center><a href="javascript:resortForm('1');"><b>Type</b></a></center></td>
              <td class='headrow' width="50"><center><a href="javascript:resortForm('7');"><b>Status</b></a></center></td>	
              <td class='headrow' width="110"><center><a href="javascript:resortForm('5');"><b>Worth</b></a></center></td>	
              <td class='headrow' width="60"><center><a href="javascript:resortForm('2');"><b>Points</b></a></center></td>	
            </tr>
          </table>
          </form>
          <form id="itemForm" name="itemForm" action="items.php" method="post">
          <div style="width: 610px; height: 350px; overflow: auto; ">
          <table border="0" cellspacing="0" cellpadding="2">
<?php
            $draw_some = 0;
            for ($itemtoblit = 0; $itemtoblit < $listsize; $itemtoblit++)
            {
              // DRAW ITEM IN LIST
?>
              <tr class='listtab'>
<?php
                if ($itmlist[$itemtoblit][society] == 0)
                {
?>          
                  <td width="15"><input type="checkbox" name="<?php echo $itemtoblit; ?>" value="<?php echo $itmlist[$itemtoblit][id]; ?>"/></td>
<?php
                }
                else
                  echo "<td width='15'>&nbsp;</td>";
                $iclass="item";
                if ($itmlist[$itemtoblit][type] == 8)
                {
                  $weapon = weaveStats($itmlist[$itemtoblit][base],$tskills);
                }
                else
                  $weapon = $itmlist[$itemtoblit][stats];          
                if (lvl_req($weapon, getTypeMod($tskills,$itmlist[$itemtoblit][type])) > $char['equip_pts']/2) $iclass = "highitem";
                if ($itmlist[$itemtoblit][society] != 0 || $itmlist[$itemtoblit][istatus] == -2) $iclass = "clanitem";
                if ($itmlist[$itemtoblit][type] == 8 && !isChanneler($types)) $iclass = "highitem";
                if ($itmlist[$itemtoblit][type] == 1 && $char[nation] == 1) $iclass = "highitem";
                if ($itmlist[$itemtoblit][istatus] > 0) $iclass = "equipped";
?>
                <td width="275"><center><b><font class="foottext">
                  <A class="<?php echo "$iclass"; ?>" HREF="javascript:swapinfo('<?php echo $itemtoblit;?>')"><?php echo iname($itmlist[$itemtoblit]); ?></A>
                </font></b></center></td>

<!-- DISPLAY ITEM TYPE -->
                <td width="75"><center><font class="foottext">
<?php
                  echo ucwords($item_type[$itmlist[$itemtoblit][type]]);
?>
                </font></center></td>

<!-- DISPLAY ITEM CONDITION -->
                <td width="50"><center><font class="foottext">
<?php
                  if ($itmlist[$itemtoblit][type] < 12 && $itmlist[$itemtoblit][type] != 8) echo $itmlist[$itemtoblit][cond]."%";
                  else echo "-";
?>
                </font></center></td>
<!-- STATUS -->
                <td width="110"><center><font class="foottext">
<?php
                  if ($itmlist[$itemtoblit][type] == 8)
                    $weapon = weaveStats($itmlist[$itemtoblit][base],$tskills);
                  else
                    $weapon = itp($itmlist[$itemtoblit][stats],$itmlist[$itemtoblit][type],$itmlist[$itemtoblit][cond]);               
                  $points = lvl_req($weapon, getTypeMod($tskills,$itmlist[$itemtoblit][type]));
                  
                  if ($itmlist[$itemtoblit][type] == 8) $worth = $item_base[$itmlist[$itemtoblit][base]][2];
                  else $worth = item_val($weapon);
                  echo displayGold($worth);
?>
                </font></center></td>
                <td width="60"><center><font class="foottext">
<?php
                  echo $points."pts";
?>
                </font></center></td>
              </tr>
<?php
              $draw_some = 1;
            }
            if ($draw_some == 0) 
              echo "<tr><td colspan='8'><center><font class='littletext_f'><br/><b>You are not carrying any items</b><br/><br/></font></center></td></tr>";
?>
          </table>
          </div>
          <table border="0" cellspacing="0" cellpadding="5" width='610'>
            <tr>
              <td colspan='8' style='border-width: 0px; border-top: 1px solid #333333'>&nbsp;</td>
            </tr>
          </table>
<?php 
                if ($location_array[$char['location']][2])
                {
?>
                  <input type="Button" name="sell" value="Sell Selected" onclick="javascript:popConfirmJs('Sell selected items?','javascript:submitFormItem(1);');" class="form"/>
<?php
                }
                else 
                {
?>
                  <input type="Button" name="drop" value="Drop Selected" onclick="javascript:popConfirmJs('Drop selected items?','javascript:submitFormItem(4);');" class="form"/>
<?php
                }
?>                
                <input type="hidden" name="action" value="0"/>
                <input type="hidden" name="tab" value="1"/>
                <input type="Button" name="cache" value="Cache/Uncache" onclick="javascript:submitFormItem('2');" class="form"/>
<?php
                if ($char[society]!="")
                {
?>
                  <input type="Button" name="donate" value="Donate to Vault" onclick="javascript:popConfirmJs('Donate selected items?','javascript:submitFormItem(3);');" class="form"/>    
<?php
                }
                $myEstate= mysql_fetch_array(mysql_query("SELECT * FROM Estates WHERE owner='$id' AND location='$char[location]'"));
                if ($myEstate[id])
                {
                  $upgrades = unserialize($myEstate['upgrades']);
                  $maxestinv = 3+3*$upgrades[2];
                  $eid=20000+$myEstate[id];              
                  $estinvsize=mysql_num_rows(mysql_query("SELECT * FROM Items WHERE owner='$eid' AND type<15"));
?>
                  <input type="Button" name="estate" value="Send to Estate (<?php echo $estinvsize."/".$maxestinv;?>)" onclick="javascript:popConfirmJs('Send selected items to Estate?','javascript:submitFormItem(5);');" class="form"/>    
<?php
                }
?>
          </form>
        </center></td>
        <td>
          <div id='itemstats' width='90%'>
            <?php echo "<center>Click on item to display bonuses!</center>";?>
          </div>
        </td>
      </tr>
    </table>
  </div>
  
  
    
<!-- CONSUMABLES -->  

  <div class="tabbertab<?php if ($tab==2) echo ' tabbertabdefault';?>">
    <h3>Consumables</h3>
    <center>
    <table style='border: 1px solid #333333;' cellspacing="0" cellpadding="5">
      <tr><td colspan='4' style='border: 1px solid #333333;'><center>Items in your System</center></td></tr>
      <tr>
        <td class='headrow' width="20">&nbsp;</td>
	<td class='headrow' width="150"><center><a href="javascript:resortForm2('0');"><b>Item</b></a></center></td>
	<td class='headrow' width="50"><center><a href="javascript:resortForm2('1');"><b>Turns</b></a></center></td>
	<td class='headrow' width='10'>&nbsp;</td>
      </tr>
<?php
      $draw_some = 0;
      for ($itemtoblit = 0; $itemtoblit < $fullness; $itemtoblit++)
      {      
          // DRAW ITEM IN LIST
?>
        <tr class='listtab'>
          <td width="20">&nbsp;</td>
<?php
          $iclass="item";
?>
          <td width="150"><center><b><font class="foottext">
            <A class="<?php echo "$iclass"; ?>" HREF="javascript:popUp('itemstat.php?<?php echo "base=".$stomach[$itemtoblit][base]."&amp;prefix=&amp;suffix=&amp;lvl=".$char[level]."&amp;gender=".$char[sex]; ?>')"><?php echo ucwords($stomach[$itemtoblit][base]); ?></A>
          </font></b></center></td> 
          <td width="50"><center><font class="foottext"><?php echo $stomach[$itemtoblit][istatus];?></font></center></td>
          <td width="10">&nbsp;</td>
        </tr>
<?php        
        $draw_some = 1;
      }        
      if ($draw_some == 0) 
        echo "<tr><td colspan='4'><center><font class='littletext_f'><br/><b>You have nothing in your system</b><br/><br/></font></center></td></tr>";
?>      
    </table>
    <br/><br/>

    <form name="sortForm2" action="items.php" method="post">
    <input type="hidden" name="sort2" id="sort2" value="" />
    <table border="0" cellspacing="0" cellpadding="5">
      <tr>
        <td class='listtab' width="20">&nbsp;</td>
	<td class='listtab' width="200"><center><a href="javascript:resortForm2('0');"><b>Item</b></a></center></td>
	<td class='listtab' width="100"><center><a href="javascript:resortForm2('1');"><b>Type</b></a></center></td>
	<td class='listtab' width="100"><center><a href="javascript:resortForm2('5');"><b>Worth</b></a></center></td>		
	<td class='listtab' width='10'>&nbsp;</td>
      </tr>
    </table>
    </form>  
      
    <form name="consumeForm" action="items.php" method="post">
    <table border="0" cellspacing="0" cellpadding="5" width='460'>
<?php
      $draw_some = 0;
      for ($itemtoblit = 0; $itemtoblit < $clistsize; $itemtoblit++)
      {
        if ($pouch[$itemtoblit][istatus] == 0 || $pouch[$itemtoblit][istatus] == -2)
        {
          // DRAW ITEM IN LIST
?>
          <tr class='listtab'>
       
            <td width="20"><input type="checkbox" name="<?php echo $itemtoblit; ?>" value="<?php echo $pouch[$itemtoblit][id];?>"/></td>
<?php
          $iclass="item";
?>
            <td width="200"><center><b><font class="foottext">
              <A class="<?php echo "$iclass"; ?>" HREF="javascript:popUp('itemstat.php?<?php echo "base=".$pouch[$itemtoblit][base]."&amp;prefix=".$pouch[$itemtoblit][prefix]."&amp;suffix=".$pouch[$itemtoblit][suffix]; ?>')"><?php echo iname($pouch[$itemtoblit]); ?></A>
            </font></b></center></td>

<!-- DISPLAY ITEM TYPE -->
            <td width="100"><center><font class="foottext">
<?php
              echo ucwords($item_type[$pouch[$itemtoblit][type]]);
?>
            </font></center></td>

<!-- WORTH -->
            <td width="100"><center><font class="foottext">
<?php
              $worth = $item_base[$pouch[$itemtoblit][base]][2];
              echo displayGold($worth);
?>
            </font></center></td>
            <td width="10">&nbsp;</td>
          </tr>
<?php
          $draw_some = 1;
        }
      }
      if ($draw_some == 0) 
        echo "<tr><td colspan='7'><center><font class='littletext_f'><br/><b>You are not carrying any items</b><br/><br/></font></center></td></tr>";
?>

      <tr>
        <td colspan='5' style='border-width: 0px; border-top: 1px solid #333333'>
	  &nbsp;
        </td>
      </tr>
      <tr>
        <td colspan='5' align=center>
          <?php 
            if ($location_array[$char['location']][2])
            {
          ?>
          <input type="Button" name="sell" value="Sell Selected" onclick="javascript:popConfirmJs('Sell selected items?','javascript:submitConsumeForm(1);');" class="form"/>
          <?php
            }
            else 
            {
          ?>
          <input type="Button" name="drop" value="Drop Selected" onclick="javascript:popConfirmJs('Drop selected items?','javascript:submitConsumeForm(2);');" class="form"/>
          <?php
            }
          ?>            
          <input type="hidden" name="consume" value="0"/>
          <input type="hidden" name="tab" value="2"/>
          <input type="Button" name="use" value="Use" onclick="javascript:submitConsumeForm('3');" class="form"/>
        </td>
      </tr>
    </table>
    </form>
    <br/><br/><br/>

    </center>    
  </div>
<?php
  if (isChanneler($types))
  {
    $myWeaves = getWeaves($item_list,$item_base,$tskills);
?>  
  <div class="tabbertab<?php if ($tab==3) echo ' tabbertabdefault';?>">
    <h3>Weaves</h3>
    <table cellpadding=0 cellspacing=0 width=98%>
      <tr>
        <td width=656>
          <table border=1 cellpadding=0 cellspacing=0>
            <tr>
              <td width='115'><center><b>Weave</b></center></td>
              <td width='40'><center><b>Pts</b></center></td>
              <td width='95'><center><b>Fire</b></center></td>
              <td width='95'><center><b>Water</b></center></td>
              <td width='95'><center><b>Spirit</b></center></td>
              <td width='95'><center><b>Air</b></center></td>
              <td width='95'><center><b>Earth</b></center></td>
            </tr>
          </table>
          <div style="width: 656px; height: 350px; overflow-y: auto; overflow-x: hidden;">
            <table border=1 cellpadding=0 cellspacing=0>
            <?php
              for ($w=0; $w < count($myWeaves); $w++)
              {
                $ptscost= lvl_req($myWeaves[$w][1],getTypeMod($tskills,8));
                $iclass='item';
                if ($ptscost > $char['equip_pts']/2) $iclass = "highitem";
                if ($invWeave==$myWeaves[$w][0]) $iclass='equipped';
                echo "<tr><td width=115><center><b><font class='foottext'><a class='".$iclass."' href=\"javascript:weaveSwapinfo('".$w."')\">".ucwords($myWeaves[$w][0])."</a></font></b></center></td>";
                echo "<td width=40><center><font class='foottext'>".$ptscost."</font></center></td>";
                echo "<td width=95><center><font class='foottext'>".itm_info(cparse($myWeaves[$w][2],0))."</font></center></td>";
                echo "<td width=95><center><font class='foottext'>".itm_info(cparse($myWeaves[$w][3],0))."</font></center></td>";
                echo "<td width=95><center><font class='foottext'>".itm_info(cparse($myWeaves[$w][4],0))."</font></center></td>";
                echo "<td width=95><center><font class='foottext'>".itm_info(cparse($myWeaves[$w][5],0))."</font></center></td>";
                echo "<td width=95><center><font class='foottext'>".itm_info(cparse($myWeaves[$w][6],0))."</font></center></td>";
              }
            ?>
            </table>
          </div>
        </td>
        <td><center>
          <form name="weaveForm" action="items.php" method="post">
          <div id='weavestats' width='95%'>
            <?php echo "<center><font class='littletext'>Click on a Weave to display current stats!</font></center>";?>
          </div>
          <input type="hidden" name="tab" value="3"/>
          <input type="hidden" name="newweave" value="-1"/>
          <input type="Button" name="usew" value="Switch Weave" onclick="javascript:submitWeaveForm('<?php echo $wEq;?>');" class="form"/>
          </form>
        </center></td>
      </tr>
    </table>
  </div>
<?php
  }
?>
</div>

<script type="text/javascript">
  updateEquipLists(1);
  var itma = -1;
  var itmb = -1;
  var itmc = -1;
  var itmd = -1;
  var itme = -1;
  if (ai >= 0) itma = inv[ai];
  if (bi >= 0) itmb = inv[bi];
  if (ci >= 0) itmc = inv[ci];
  if (di >= 0) itmd = inv[di];
  if (ei >= 0) itme = inv[ei];  
  calcstats(ai,bi,ci,di,ei,'currstats');
  calcstats(ai,bi,ci,di,ei,'newstats');
</script>
<?php
  include('footer.htm');
?>