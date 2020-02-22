<?php

/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/itemJava.php");
include_once("admin/jobFuncs.php");
include_once("admin/locFuncs.php");
$color = array("000000","171717","AAAAAA");
$wikilink = "Businesses";
$cell_size = 75;
$id=$char['id'];
$loc_query = $char['location'];

$bizinfo = array('','','','','','','','','','','','','');
$bizstats = array('','','','','','','','','','','','','');
$ustats = mysql_fetch_array(mysql_query("SELECT * FROM Users_stats WHERE id='$id'"));
$jobs = unserialize($char[jobs]);
$jobs[99] = getOfficeLevel($ustats['loc_ji'.$location[id]]);
$biznum=mysql_real_escape_string($_POST['biznum']);
$bizact=mysql_real_escape_string($_POST['bizact']);
$bizval=mysql_real_escape_string($_POST['bizval']);
$bizsub=mysql_real_escape_string($_POST['bizsub']);

$soc_name = $char[society];
$society = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$soc_name' "));

// Check for businesses to update BEFORE we do anything with the display
$pquery = "SELECT * FROM Profs WHERE owner='".$id."'";
$presult = mysql_query($pquery);  
$bsubs = array(0,0,0);
$ajobs = unserialize($listchar[jobs]);
while ($bq = mysql_fetch_array( $presult ) )
{
  $bstatus = unserialize($bq['status']);
  $bupgrades = unserialize($bq['upgrades']);
  for ($i=0; $i<$bupgrades[0]; $i++)
  {
    if ($bstatus[$i][0]==1)
    {
      $timeleft = ($bstatus[$i][2][1] - time());
      if ($timeleft <0 && $bq[type] != 0)
      {
        $bstatus[$i][0]=2;
        if ($bq[type]<7)
        {
          $bstatus[$i][4]=5;
          $qualbonus = $bupgrades[1][1]*5 + $bupgrades[1][2]*3;
          $qualnum=$ajobs[$bq[type]]*5+$qualbonus+rand(1,50);
          $bstatus[$i][5]=floor($qualnum/25);
        }
        $sstatus = serialize($bstatus);
        mysql_query("UPDATE Profs SET status='".$sstatus."' WHERE id='".$bq[id]."'");          
      }
    }
    $bsubs[$bstatus[$i][0]]++;
  }
}


for ($x=0; $x<9; $x++)
{
  for ($t=0; $t<11; $t++)
  {
    if ($t == 10) $t = 99;
    $bizprod[$t][$x]=mysql_real_escape_string($_POST['product'.$x.'b'.$t]);
    $bizprod2[$t][$x]=mysql_real_escape_string($_POST['producto'.$x.'b'.$t]);
  }
  $bizdest[$x]=mysql_real_escape_string($_POST['dest'.$x]);
  $bizship[$x]=mysql_real_escape_string($_POST['shipt'.$x]);
  $biziname[$x]=mysql_real_escape_string($_POST['itmname'.$x]);
  $bizag[$x]=mysql_real_escape_string($_POST['askgold'.$x]);
  $bizas[$x]=mysql_real_escape_string($_POST['asksilver'.$x]);
  $bizac[$x]=mysql_real_escape_string($_POST['askcopper'.$x]);
}

$base_good_val = 60;
$shipg = unserialize($location[shipg]);

$bu=(100+$town_bonuses[bU])/100;
$bx = 0;

// if local war, slow businesses by 50%
if ($location[last_war])
{
  $myWar = mysql_fetch_array(mysql_query("SELECT * FROM Contests WHERE id='$location[last_war]' "));
}
else
{
  $myWar = 0;
}
if ($myWar != 0) // Previous clan battle
{
  if ($myWar[starts] <= intval(time()/3600) && $myWar[ends] > intval(time()/3600))
  {
    $bx += 50;
  }
}

$listsize=0;
$iresult=mysql_query("SELECT * FROM Items WHERE owner='$id' AND type<15 AND type>0 AND istatus <=0");
while ($qitem = mysql_fetch_array($iresult))
{
  $itmlist[$listsize++] = $qitem;
}  

if (!$location[shopg])
{
  $location[shopg]=serialize($base_town_consumes[$loc_query]);
  mysql_query("UPDATE Locations SET shopg='".$location[shopg]."' WHERE name='$loc_query'");
}
$shopg = unserialize($location[shopg]);

$myLocBiz[0] = '';
$query = "SELECT * FROM Profs WHERE owner='$id' AND location='$loc_query'";
$result = mysql_query($query);  
while ($bq = mysql_fetch_array( $result ) )
{
  $myLocBiz[$bq[type]]=1;
}

$firstbiz=-1;
if ($biznum !='') $firstbiz = $biznum;
for ($t=0; $t<11; $t++)
{
  if ($t==10) $t=99;
  if ($myLocBiz[$t])
  {
    if ($firstbiz==-1) $firstbiz=$t;  
    $query = "SELECT * FROM Profs WHERE owner='$id' AND location='$loc_query' AND type='$t'";
    $biz = mysql_fetch_array(mysql_query($query));  
    $upgrades[$t] = unserialize($biz['upgrades']);
    $status[$t] = unserialize($biz['status']);
    
    // count hires
    if ($t==7 || $t==8)
    {
      $hires[$t] = array(0,0,0);
      for ($i=0; $i < 9; $i++)
      {
         if ($upgrades[$t][2][$i] == 1) $hires[$t][1]++;
         if ($upgrades[$t][2][$i] == 2) $hires[$t][2]++;  
         if ($upgrades[$t][2][$i] > 0) $hires[$t][0]++;                
      }
    }
  
    if ($biznum==$t) // something to do
    {
      if ($bizact == 1) // buy tool
      {
        if ($upgrades[$t][1][$bizval]==0) // check that you don't already own it
        {
          if ($char[gold] >= intval($tool_stats[$biz_tools[$t][$bizval]][1]*$bu)) // can you afford it?
          {
            $upgrades[$t][1][$bizval] = 1;
            $char[gold] -= intval($tool_stats[$biz_tools[$t][$bizval]][1]*$bu);
            $location[bank]+= intval($tool_stats[$biz_tools[$t][$bizval]][1]*$bu/2);
            $ustats[prof_earn] -= intval($tool_stats[$biz_tools[$t][$bizval]][1]*$bu);
            $biz['value'] += intval($tool_stats[$biz_tools[$t][$bizval]][1]*$bu/2);
            $message = $biz_tools[$t][$bizval]." purchased!";
            $supgrades = serialize($upgrades[$t]);
            mysql_query("UPDATE Users SET gold='".$char[gold]."' WHERE id='".$char[id]."'");
            mysql_query("UPDATE Users_stats SET prof_earn='".$ustats[prof_earn]."' WHERE id='".$char[id]."'");            
            mysql_query("UPDATE Profs SET upgrades='".$supgrades."', value='".$biz['value']."' WHERE id='".$biz[id]."'");
            mysql_query("UPDATE Locations SET bank='$location[bank]' WHERE id='$location[id]'");
          }
          else {$message = "You can't afford that!";}
        }
        else { $message = "One of those is enough for this business...";}
      }
      elseif ($bizact == 2) // hire worker
      {
        if ($t==7 || $t==8) // does this business hire?
        {
          if ($hires[$t][0] < $upgrades[$t][0]) // do you have more room to hire?
          {
            if ($char[gold] >= intval(($hires[$t][0]+1)*$hirebase*$bu)) // can you afford it?
            {
              $upgrades[$t][2][$hires[$t][0]] = $bizval;
              $hires[$t][$bizval]++;
              $char[gold] -= intval(($hires[$t][0]+1)*$hirebase*$bu);
              $location[bank]+= intval(($hires[$t][0]+1)*$hirebase*$bu/2);
              $ustats[prof_earn] -= intval(($hires[$t][0]+1)*$hirebase*$bu);
              $biz['value'] += intval(($hires[$t][0]+1)*$hirebase*$bu/2);
              $message = $biz_hires[$t][($bizval-1)]." hired!";
              $supgrades = serialize($upgrades[$t]);
              $hires[$t][0]++;
              mysql_query("UPDATE Users SET gold='".$char[gold]."' WHERE id='".$char[id]."'");
              mysql_query("UPDATE Users_stats SET prof_earn='".$ustats[prof_earn]."' WHERE id='".$char[id]."'");
              mysql_query("UPDATE Profs SET upgrades='".$supgrades."', value='".$biz['value']."' WHERE id='".$biz[id]."'");
              mysql_query("UPDATE Locations SET bank='$location[bank]' WHERE id='$location[id]'");
            }
            else {$message = "You can't afford them!";}
          }
          else { $message = "You don't have room to hire anymore right now.";}
        }
        else { $message = "This business doesn't hire workers";}
      }
      elseif ($bizact == 3) // add station
      {
        if ($upgrades[$t][0] < $jobs[$t]) // you can buy more
        {
          if ($char[gold] >= intval(($upgrades[$t][0]+1)*$stationbase*$bu)) // can you afford it?
          {
            $char[gold] -= intval(($upgrades[$t][0]+1)*$stationbase*$bu);
            $location[bank] += intval(($upgrades[$t][0]+1)*$stationbase*$bu/2);
            $ustats[prof_earn] -= intval(($upgrades[$t][0]+1)*$stationbase*$bu);
            $biz['value'] += intval(($upgrades[$t][0]+1)*$stationbase*$bu/2);
            $upgrades[$t][0]++;
            if ($ustats[highest_biz]< $upgrades[$t][0]) $ustats[highest_biz]= $upgrades[$t][0];
            $message = $job_biz_names[$t][1]." purchased!";
            $supgrades = serialize($upgrades[$t]);
            mysql_query("UPDATE Users SET gold='".$char[gold]."' WHERE id='".$char[id]."'");
            mysql_query("UPDATE Users_stats SET prof_earn='".$ustats[prof_earn]."', highest_biz='".$ustats[highest_biz]."' WHERE id='".$char[id]."'");
            mysql_query("UPDATE Profs SET upgrades='".$supgrades."', value='".$biz['value']."' WHERE id='".$biz[id]."'");
            mysql_query("UPDATE Locations SET bank='$location[bank]' WHERE id='$location[id]'");
          }
          else {$message = "You can't afford that!";}
        }
        else { $message = "You can't expand yet!";}
      }
      elseif ($bizact == 4) // produce sub product
      {
        $producing=0;
        for ($s=0; $s<$upgrades[$t][0]; $s++)
        {
          if ($bizprod[$t][$s])
          {
            if ($status[$t][$s][0] == 0) // is it idle?
            {
              $timebonus = 0;
              $timebonus += $upgrades[$t][1][0]*5 + $upgrades[$t][1][2]*3 - $bx - $town_bonuses[$job_time_bonus[$t]];
              if ($t==7 || $t==8) $timebonus += $hires[$t][1]*2;
              $status[$t][$s][0] = 1;
              $status[$t][$s][1][0] = $bizprod[$t][$s];
              $status[$t][$s][2][0] = time();
              $status[$t][$s][2][1] = time() + (($trade_goods[$job_goods[$t][$bizprod[$t][$s]]][0]*3600)*((100-$timebonus)/100));
              $sstatus = serialize($status[$t]);
              $producing++;
            }
          }
        }
        if ($producing)
        {
          $message = $job_biz_names[$t][3];        
          mysql_query("UPDATE Profs SET status='".$sstatus."' WHERE id='".$biz[id]."'");        
        }
      }
      elseif ($bizact == 5) // Sell sub product
      {
        $gtotal = 0;
        for ($s=0; $s<$upgrades[$t][0]; $s++)
        {
          if ($status[$t][$s][0] == 2) // is it done?
          {
            $bizprod = $status[$t][$s][1][0];
            $gname = $job_goods[$t][$bizprod];
            $valbonus = 0;
            $valbonus += $upgrades[$t][1][1]*5 + $upgrades[$t][1][2]*3;
            if ($t==7 || $t==8) $valbonus += $hires[$t][2]*2;
            $gvalue=$trade_goods[$job_goods[$t][$bizprod]][1]*((100+$valbonus)/100);
            if ($t<7)
            {
              $qualbonus=$valbonus+$town_bonuses[cQ];
              $quality = $status[$t][$s][5];
              $valbonus = ((100*$quality)/2);
              if ($t == 4) $inum=20;
              elseif ($t == 5) $inum=21;
              elseif ($t == 6) $inum=19;
              $gname=getQualityWord($inum, $quality)." ".$gname;                      
              $gvalue=$item_base[strtolower($gname)][2]*$status[$t][$s][4]*0.75; 
            
              if ($shopg[$inum][$quality][$bizprod-1]>=0)
                $shopg[$inum][$quality][$bizprod-1] += 5;
              $location[shopg]=serialize($shopg);
              mysql_query("UPDATE Locations SET shopg='".$location[shopg]."' WHERE name='$loc_query'");
            }
            $gtotal += $gvalue;
            $status[$t][$s][0] = 0;
            $status[$t][$s][1][0] = 0;
            $status[$t][$s][2][0] = 0;
            $status[$t][$s][2][1] = 0;
            $status[$t][$s][3] = 0;
            $status[$t][$s][4] = 0;
            $status[$t][$s][5] = 0;            
            $sstatus = serialize($status[$t]);
          }
        }
        if ($gtotal)
        {
          $char[gold] += $gtotal;
          $ustats[prof_earn] += $gtotal;
          $biz['value'] += intval($gtotal/2);        
          $message = "Goods sold for ".displayGold($gtotal)."!";
          if ($society[id]) updateSocRep($society, $location[id], $gtotal);
          mysql_query("UPDATE Users SET gold='".$char[gold]."' WHERE id='".$char[id]."'");
          mysql_query("UPDATE Users_stats SET prof_earn='".$ustats[prof_earn]."' WHERE id='".$char[id]."'");
          mysql_query("UPDATE Profs SET status='".$sstatus."', value='".$biz['value']."' WHERE id='".$biz[id]."'");
        }
        else { $message = "You didn't select anything to sell!"; }
      }
      elseif ($bizact == 6) // take consumbable
      {
        if ($status[$t][$bizsub][0] == 2) // is it done?
        {
          $takenum = mysql_real_escape_string($_POST['cquant'.$bizsub.'b'.$t]);
          if ($takenum <= $status[$t][$bizsub][4])
          {
            $bizprod = $status[$t][$bizsub][1][0];
            $gname = $job_goods[$t][$bizprod];
            $valbonus = $upgrades[$t][1][1]*5 + $upgrades[$t][1][2]*3;
            $qualbonus=$valbonus+$town_bonuses[cQ];
            $valbonus = 0;
            $quality = $status[$t][$bizsub][5];
            if ($t == 4) $inum=20;
            elseif ($t == 5) $inum=21;
            elseif ($t == 6) $inum=19;
            $gname = ucwords(getQualityWord($inum, $quality)." ".$gname);
          
            $itmsize = mysql_num_rows(mysql_query("SELECT * FROM Items WHERE owner='$id' AND type>=19 AND istatus=0"));
            if ($itmsize+$takenum <= $pouch_max)
            {
              for ($k=0; $k < $takenum; $k++)
              {
                $base= strtolower($gname);
                $itype=$item_base[$base][1];
                $istats= itp($item_base[$base][0],$item_base[$base][1]);
                $ipts= lvl_req($istats,100);
                $itime=time();
                $result = mysql_query("INSERT INTO Items (owner,type,    cond, istatus,points, society,last_moved,base,   prefix,suffix,stats) 
                                                   VALUES ('$id','$itype','100','0',    '$ipts','',    '$itime',  '$base','',    '',    '$istats')");
              }         

              $message = $takenum." ".$gname." taken!";
              $status[$t][$bizsub][4] -= $takenum;
              
              if ($status[$t][$bizsub][4] <= 0)
              {
                $status[$t][$bizsub][0] = 0;
                $status[$t][$bizsub][1][0] = 0;
                $status[$t][$bizsub][2][0] = 0;
                $status[$t][$bizsub][2][1] = 0;
                $status[$t][$bizsub][3] = 0;
                $status[$t][$bizsub][4] = 0;
                $status[$t][$bizsub][5] = 0;
              }
              $sstatus = serialize($status[$t]);
              mysql_query("UPDATE Profs SET status='".$sstatus."' WHERE id='".$biz[id]."'");
            }
            else { $message = "You don't have room to carry that!"; }
          }
          else { $message = "There aren't that many left to take!"; }
        }
        else { $message = "It's not done yet!";}
      }      
      elseif ($bizact == 7) // start trade plan
      {
        $sgood = 0;
        for ($s=0; $s<$upgrades[$t][0]; $s++)
        {
          if ($status[$t][$s][0] == 0) // is it idle?
          {
            if ($bizdest[$s] >= 0 && $bizprod[$t][$s] >=0 && $bizprod2[$t][$s]>=0)
            {
              $timebonus = 0;
              $subship = $upgrades[$t][2][$s];
              $timebonus += $upgrades[$t][1][0]*5 + $upgrades[$t][1][2]*3 - $bx - $town_bonuses[$job_time_bonus[$t]];
              $tspeed = $ship_type[$ship_order[$subship]][1];
              $tdist = $ship_travel[$ship_ports[$location['id']-1]][$bizdest[$s]];
              $status[$t][$s][0] = 1;
              $status[$t][$s][1][0] = $bizprod[$t][$s];
              $status[$t][$s][1][1] = $bizprod2[$t][$s];
              $status[$t][$s][1][2] = $bizdest[$s];
              $status[$t][$s][2][0] = time();
              $status[$t][$s][2][1] = time() + ((($tdist/$tspeed)*3600)*((100-$timebonus)/100));
              if ( $shipg[floor($bizprod[$t][$s]/3)][$bizprod[$t][$s]%3] >0)
              {
                $shipg[floor($bizprod[$t][$s]/3)][$bizprod[$t][$s]%3] -= 1;
              }
              $sgood++;
            }
            else { $message = "You must select a destination, local good, and target good before setting sail!"; }
          }
          else { $message = "Wait until it's finished it's last task!";}
        }
        if ($sgood)
        {
          $shipgs = serialize($shipg);
          mysql_query("UPDATE Locations SET shipg='".$shipgs."' WHERE id='".$location[id]."'");        
          $sstatus = serialize($status[$t]);
          mysql_query("UPDATE Profs SET status='".$sstatus."' WHERE id='".$biz[id]."'");
          $message = $job_biz_names[$t][3];        
        }
      }
      elseif ($bizact == 8) // finish trade plan
      {
        $totvalue=0;
        for ($s=0; $s<$upgrades[$t][0]; $s++)
        {
          if ($status[$t][$s][0] == 2) // is it done?
          {
            $destloc = mysql_fetch_array(mysql_query("SELECT * FROM Locations WHERE id='".($status[$t][$s][1][2]+1)."'"));
            $destgoods = unserialize($destloc[shipg]);

            $p1_loc = floor($status[$t][$s][1][0]/3);
            $p1_num = $status[$t][$s][1][0]%3;
            $p1_lval = ($ship_travel[$ship_ports[$location[id]-1]][$p1_loc])/50*$base_good_val;
            $p1_dval = ($ship_travel[$ship_ports[$status[$t][$s][1][2]]][$p1_loc])/50*$base_good_val;
            $p1_ddem = (100-($destgoods[$p1_loc][$p1_num]/5))/100;
    
            $p2_loc = floor($status[$t][$s][1][1]/3);
            $p2_num = $status[$t][$s][1][1]%3;    
            $p2_lval = ($ship_travel[$ship_ports[$location[id]-1]][$p2_loc])/50*$base_good_val;
            $p2_dval = ($ship_travel[$ship_ports[$status[$t][$s][1][2]]][$p2_loc])/50*$base_good_val;
            $p2_ldem = (100-($shipg[$p2_loc][$p2_num]/5))/100;
            
            if ($p1_ddem<.5) $p1_ddem=.5;
            if ($p2_ldem<.5) $p2_ldem=.5;
            $ship_hold = $ship_type[$ship_order[$upgrades[$t][2][$s]]][2];
            $valbonus = 0;
            $valbonus += $upgrades[$t][1][1]*5;
            $tvalue = ((($p1_dval-floor($p1_lval/2))*$ship_hold*$p1_ddem)/2 + (($p2_lval-floor($p2_dval/2))*$ship_hold*$p2_ldem)/2)*((100+$valbonus)/100);
            
            $bizprod = $status[$t][$s][1][0];
            $totvalue += $tvalue;

            $status[$t][$s][0] = 0;
            $status[$t][$s][1][0] = 0;
            $status[$t][$s][2][0] = 0;
            $status[$t][$s][2][1] = 0;          
            if ($shipg[$p2_loc][$p2_num] >= 0)     $shipg[$p2_loc][$p2_num]+= $ship_hold;
            if ($destgoods[$p1_loc][$p1_num] >= 0) $destgoods[$p1_loc][$p1_num] += $ship_hold;
            $dshipgs = serialize($destgoods);
            mysql_query("UPDATE Locations SET shipg='".$dshipgs."' WHERE id='".($status[$t][$s][1][2]+1)."'");         
          }
          else { $message = "It's not done yet!";}
        }
        $char[gold] += $totvalue;
        $ustats[prof_earn] += $totvalue;   
        $biz['value'] += intval($totvalue/2);
        $shipgs = serialize($shipg);
        mysql_query("UPDATE Locations SET shipg='".$shipgs."' WHERE id='".$location[id]."'");
        mysql_query("UPDATE Users SET gold='".$char[gold]."' WHERE id='".$char[id]."'");
        mysql_query("UPDATE Users_stats SET prof_earn='".$ustats[prof_earn]."' WHERE id='".$char[id]."'");
        $sstatus = serialize($status[$t]);
        mysql_query("UPDATE Profs SET status='".$sstatus."', value='".$biz['value']."' WHERE id='".$biz[id]."'");
        
        $message = "Trade complete! Earned ".displayGold($totvalue)."!";        
      }
      elseif ($bizact == 9) // Trade In Ship
      {
        $tmpgold = 0;
        $oldups = $upgrades;
        echo $biz['value'].":";
        for ($s=0; $s<$upgrades[$t][0]; $s++)
        {
          if ($status[$t][$s][0] == 0) // is it inactive?
          {
            if ($bizship[$s]>=0)
            {
              $tmpgold += intval(($ship_type[$ship_order[$bizship[$s]]][0]-floor($ship_type[$ship_order[$upgrades[$t][2][$s]]][0]/2))*$bu);
              $biz['value'] += intval(($ship_type[$ship_order[$bizship[$s]]][0])*$bu/2)-intval(($ship_type[$ship_order[$upgrades[$t][2][$s]]][0])*$bu/2);
              $upgrades[$t][2][$s] = $bizship[$s];
              echo $biz['value'].":";
            }
          }
        }
        if ($char[gold] >= $tmpgold)
        {
          $char[gold] -= $tmpgold;
          $ustats[prof_earn] -= $tmpgold;
          $supgrades = serialize($upgrades[$t]);
          mysql_query("UPDATE Users SET gold='".$char[gold]."' WHERE id='".$char[id]."'");
          mysql_query("UPDATE Users_stats SET prof_earn='".$ustats[prof_earn]."' WHERE id='".$char[id]."'");
          mysql_query("UPDATE Profs SET upgrades='".$supgrades."', value='".$biz['value']."' WHERE id='".$biz[id]."'");
          $message = "Ships traded for ".displayGold($tmpgold)."!";
        }
        else 
        {
          $upgrades = $oldups;
          $message = "You can't afford that!";
        }
      }
      elseif ($bizact == 10) // add offer to market
      {
        for ($s=0; $s<$upgrades[$t][0]; $s++)
        {
          if ($status[$t][$s][0] == 0) // is it idle?
          {
            if ($biziname[$s] > 0 && $bizprod[$t][$s] >=0 && $bizag[$s]>=0 && $bizas[$s]>=0 && $bizac[$s]>=0 )
            {
              $oprice = ($bizag[$s]*10000)+($bizas[$s]*100)+$bizac[$s];
              if ($bizag[$s] <= 25)
              {
                if ($oprice > 0)
                {
                  if ($itmlist[$bizprod[$t][$s]][id] == $biziname[$s])
                  {
                    $status[$t][$s][0] = 1;
                    $status[$t][$s][1][0] = $itmlist[$bizprod[$t][$s]];
                    $status[$t][$s][1][1] = $oprice;
                    $status[$t][$s][1][2] = $char[id];
                    $status[$t][$s][2] = time();
                
                    $mid=$char[id]+30000;
                    $result = mysql_query("UPDATE Items SET owner='".$mid."', istatus='0', last_moved='".time()."' WHERE id='".$itmlist[$bizprod[$t][$s]][id]."'");

                    $sstatus = serialize($status[$t]);
                    $message = "Market Offers Updated!";
                    mysql_query("UPDATE Profs SET status='".$sstatus."' WHERE id='".$biz[id]."'");
                  }
                  else { $message = "There was some confusion about what you're offering. Please try again!"; }
                }
                else { $message = "You must set a price for the item!";}
              }
              else { $message = "You cannot ask for more than 25 gold for an item!";}
            }
            else { $message = "You must select an item to offer!";}
          }
          else { $message = "Wait until it's finished it's last task!";}
        }
      }
      elseif ($bizact == 11) // Take back offered item
      {
        if ($status[$t][$bizsub][0] == 1) // is it active?
        {
          $takecost = floor($status[$t][$bizsub][1][1]/2);
          if ($char[gold] >= $takecost)
          {
            if ($listsize < $inv_max)
            {
              $char[gold] = $char[gold]-$takecost; 
              $result = mysql_query("UPDATE Items SET owner='".$char[id]."', last_moved='".time()."' WHERE id='".$status[$t][$bizsub][1][0][id]."'");
              mysql_query("UPDATE Users SET gold='$char[gold]' WHERE id=$id");
              $status[$t][$bizsub][0] = 0;
              $status[$t][$bizsub][1][0] = 0;
              $status[$t][$bizsub][1][1] = 0;
              $status[$t][$bizsub][1][2] = 0;
              $status[$t][$bizsub][2] = 0;
              $message = "Item taken for ".displayGold($takecost)."!";
              $sstatus = serialize($status[$t]);
              mysql_query("UPDATE Profs SET status='".$sstatus."' WHERE id='".$biz[id]."'"); 
            }
            else
            { $message = "You don't have room in your inventory for any more items!"; }           
          }
          else
          { $message = "You don't have enough gold!"; } 
        }
        else
        { $message = "There's nothing to take!"; }
      }  
      elseif ($bizact == 12) // Sell marketed item
      {
        if ($status[$t][$bizsub][0] == 1) // is it active?
        {
          $sellval = floor(item_val(iparse($status[$t][$bizsub][1][0],$item_base, $item_ix,$ter_bonuses))/4);
          $char[gold] = $char[gold]+$sellval;
          mysql_query("UPDATE Users SET gold='$char[gold]' WHERE id=$id");
          $status[$t][$bizsub][0] = 0;
          $status[$t][$bizsub][1][0] = 0;
          $status[$t][$bizsub][1][1] = 0;
          $status[$t][$bizsub][1][2] = 0;
          $status[$t][$bizsub][2] = 0;
          $message = "Item sold for ".displayGold($sellval)."!";
          $sstatus = serialize($status[$t]);
          mysql_query("UPDATE Profs SET status='".$sstatus."' WHERE id='".$biz[id]."'"); 
        }
        else
        { $message = "There's nothing to sell!"; }
      }
      elseif ($bizact == 13) // Clear market log
      {
        $cleared=0;
        for ($s=0; $s<$upgrades[$t][0]; $s++)
        {
          if ($status[$t][$s][0] == 2) // is it active?
          {
            $status[$t][$s][0] = 0;
            $status[$t][$s][1][0] = 0;
            $status[$t][$s][1][1] = 0;
            $status[$t][$s][1][2] = 0;
            $status[$t][$s][2] = 0;
            $cleared++;
          }
        }
        if ($cleared)
        {
          $sstatus = serialize($status[$t]);
          mysql_query("UPDATE Profs SET status='".$sstatus."' WHERE id='".$biz[id]."'"); 
          $message = "Displays ready for new offers!";
        }
        else { $message = "Something odd happened. Please try again!"; } 
      }
      elseif ($bizact == 14) // office actions
      {
        $orderTotal = 0;
        $chaosTotal = 0;
        $fundsTotal = 0;
        $defenseTotal = 0;
        $rulerTotal = 0;
        
        for ($s=0; $s<$upgrades[$t][0]; $s++)
        {
          if ($status[$t][$s][0] == 2) // is it done?
          {
            $bizprod = $status[$t][$s][1][0];
            $gname = $job_goods[$t][$bizprod];
            $valbonus = 0;
            $valbonus += $upgrades[$t][1][1]*5 + $upgrades[$t][1][2]*3;
            $gvalue=round($ustats['loc_ji'.$location[id]]*$trade_goods[$gname][1]*((100+$valbonus)/100));

            if ($bizprod == 1) $orderTotal += $gvalue;
            else if ($bizprod == 2) $chaosTotal += $gvalue;
            else if ($bizprod == 3) $fundsTotal += $gvalue;
            else if ($bizprod == 4) $fundsTotal -= $gvalue;
            else if ($bizprod == 5) $defenseTotal += $gvalue;
            else if ($bizprod == 6) $defenseTotal -= $gvalue;
            else if ($bizprod == 7) $rulerTotal += $gvalue;
            else if ($bizprod == 8) $rulerTotal -= $gvalue;
            $gtotal++;
            $status[$t][$s][0] = 0;
            $status[$t][$s][1][0] = 0;
            $status[$t][$s][2][0] = 0;
            $status[$t][$s][2][1] = 0;
            $status[$t][$s][3] = 0;
            $status[$t][$s][4] = 0;
            $status[$t][$s][5] = 0;            
            $sstatus = serialize($status[$t]);
          }
        }
        if ($gtotal)
        { 
          $alignTotal = 0;
          if ($orderTotal)
          {
            $location[order] += $orderTotal;
            $alignTotal += $orderTotal/10;
          }
          if ($chaosTotal) 
          {
            $location[chaos] += $chaosTotal;
            $alignTotal -= $chaosTotal/10;
          }
          if ($fundsTotal) 
          {
            $location[bank] += $fundsTotal;
            if ($location[bank] < 0) $location[bank] = 0;
            $alignTotal += $fundsTotal/1000;
          }
          if ($defenseTotal) 
          { 
            $location[army] += $defenseTotal;
            if ($location[army] < 1000 ) $location[army] = 1000;
            $alignTotal += $defenseTotal/333;
          }
          if ($rulerTotal)
          {
            $ruler = mysql_fetch_array(mysql_query("SELECT id, align, area_score FROM Soc WHERE name='$location[ruler]'"));
            $area_score = unserialize($ruler['area_score']);    
            $area_score[$location[id]] = $area_score[$location[id]]+$rulerTotal;
            $area_score[$location[id]] = number_format($area_score[$location[id]],2,'.','');
            $a_s_str = serialize($area_score);
            mysql_query("UPDATE Soc SET area_score='$a_s_str' WHERE id='$ruler[id]' ");
            
            if ($ruler[align] > 0) $alignTotal += $rulerTotal/3;
            else $alignTotal -= $rulerTotal/3;
          }       
          $char[align] += $alignTotal;
          
          $soc_name = $char[society];
          $society = mysql_fetch_array(mysql_query("SELECT id, name, align FROM Soc WHERE name='$soc_name' "));
          if ($society[id]) 
          {
            $society[align] += $alignTotal;
            $result = mysql_query("UPDATE Soc SET align = align + ".$alignTotal." WHERE id='".$society[id]."'");
          }
          $s = "";
          if ($gtotal > 1) $s = "s";
          $message = "Assignment".$s." completed!";
          mysql_query("UPDATE Locations SET myOrder=myOrder+$orderTotal, chaos=chaos+$chaosTotal, bank='".$location[bank]."', army='".$location[army]."' WHERE id='".$location[id]."'");
          mysql_query("UPDATE Profs SET status='".$sstatus."' WHERE id='".$biz[id]."'");
          mysql_query("UPDATE Users SET align='$char[align]' WHERE id=$id");
        }
        else { $message = "You didn't select any assignments to complete!"; }
      } 
      elseif ($bizact == 15) // sell and restart product
      {
        $gtotal = 0;
        for ($s=0; $s<$upgrades[$t][0]; $s++)
        {
          if ($status[$t][$s][0] == 2) // is it done?
          {
            $bizprod = $status[$t][$s][1][0];
            $gname = $job_goods[$t][$bizprod];
            $valbonus = 0;
            $valbonus += $upgrades[$t][1][1]*5 + $upgrades[$t][1][2]*3;
            if ($t==7 || $t==8) $valbonus += $hires[$t][2]*2;
            $gvalue=$trade_goods[$job_goods[$t][$bizprod]][1]*((100+$valbonus)/100);
            if ($t<7)
            {
              $qualbonus=$valbonus+$town_bonuses[cQ];
              $quality = $status[$t][$s][5];
              $valbonus = ((100*$quality)/2);
              if ($t == 4) $inum=20;
              elseif ($t == 5) $inum=21;
              elseif ($t == 6) $inum=19;
              $gname=getQualityWord($inum, $quality)." ".$gname;                      
              $gvalue=$item_base[strtolower($gname)][2]*$status[$t][$s][4]*0.75; 
            
              if ($shopg[$inum][$quality][$bizprod-1]>=0)
                $shopg[$inum][$quality][$bizprod-1] += 5;
              $location[shopg]=serialize($shopg);
              mysql_query("UPDATE Locations SET shopg='".$location[shopg]."' WHERE name='$loc_query'");
            }
            $gtotal += $gvalue;
            $timebonus = 0;
            $timebonus += $upgrades[$t][1][0]*5 + $upgrades[$t][1][2]*3 - $bx - $town_bonuses[$job_time_bonus[$t]];
            if ($t==7 || $t==8) $timebonus += $hires[$t][1]*2;            
            $status[$t][$s][0] = 1;
            // $status[$t][$s][1][0] = 0;
            $status[$t][$s][2][0] = time();
            $status[$t][$s][2][1] = time() + (($trade_goods[$job_goods[$t][$status[$t][$s][1][0]]][0]*3600)*((100-$timebonus)/100));
            $status[$t][$s][3] = 0;
            $status[$t][$s][4] = 0;
            $status[$t][$s][5] = 0;            
            $sstatus = serialize($status[$t]);
          }
          
        }
     
        if ($gtotal)
        {
          $char[gold] += $gtotal;
          $ustats[prof_earn] += $gtotal;
          $biz['value'] += intval($gtotal/2);        
          $message = "Goods sold for ".displayGold($gtotal)."!";
          if ($society[id]) updateSocRep($society, $location[id], $gtotal);
          mysql_query("UPDATE Users SET gold='".$char[gold]."' WHERE id='".$char[id]."'");
          mysql_query("UPDATE Users_stats SET prof_earn='".$ustats[prof_earn]."' WHERE id='".$char[id]."'");
          mysql_query("UPDATE Profs SET status='".$sstatus."', value='".$biz['value']."' WHERE id='".$biz[id]."'");
        }
        else { $message = "You didn't select anything to sell!"; }
      }  
      elseif ($bizact == 16) // restart trade plan
      {
        $totvalue=0;
        for ($s=0; $s<$upgrades[$t][0]; $s++)
        {
          if ($status[$t][$s][0] == 2) // is it done?
          {
            $destloc = mysql_fetch_array(mysql_query("SELECT * FROM Locations WHERE id='".($status[$t][$s][1][2]+1)."'"));
            $destgoods = unserialize($destloc[shipg]);

            $p1_loc = floor($status[$t][$s][1][0]/3);
            $p1_num = $status[$t][$s][1][0]%3;
            $p1_lval = ($ship_travel[$ship_ports[$location[id]-1]][$p1_loc])/50*$base_good_val;
            $p1_dval = ($ship_travel[$ship_ports[$status[$t][$s][1][2]]][$p1_loc])/50*$base_good_val;
            $p1_ddem = (100-($destgoods[$p1_loc][$p1_num]/5))/100;
    
            $p2_loc = floor($status[$t][$s][1][1]/3);
            $p2_num = $status[$t][$s][1][1]%3;    
            $p2_lval = ($ship_travel[$ship_ports[$location[id]-1]][$p2_loc])/50*$base_good_val;
            $p2_dval = ($ship_travel[$ship_ports[$status[$t][$s][1][2]]][$p2_loc])/50*$base_good_val;
            $p2_ldem = (100-($shipg[$p2_loc][$p2_num]/5))/100;
            
            if ($p1_ddem<.5) $p1_ddem=.5;
            if ($p2_ldem<.5) $p2_ldem=.5;
            $ship_hold = $ship_type[$ship_order[$upgrades[$t][2][$s]]][2];
            $valbonus = 0;
            $valbonus += $upgrades[$t][1][1]*5;
            $tvalue = ((($p1_dval-floor($p1_lval/2))*$ship_hold*$p1_ddem)/2 + (($p2_lval-floor($p2_dval/2))*$ship_hold*$p2_ldem)/2)*((100+$valbonus)/100);
            
            $bizprod = $status[$t][$s][1][0];
            $totvalue += $tvalue;

            $timebonus = 0;
            $subship = $upgrades[$t][2][$s];
            $timebonus += $upgrades[$t][1][0]*5 + $upgrades[$t][1][2]*3 - $bx - $town_bonuses[$job_time_bonus[$t]];
            $tspeed = $ship_type[$ship_order[$subship]][1];
            $tdist = $ship_travel[$ship_ports[$location['id']-1]][$status[$t][$s][1][2]];

            $status[$t][$s][0] = 1;
            //$status[$t][$s][1][0] = 0;
            $status[$t][$s][2][0] = time();
            $status[$t][$s][2][1] = time() + ((($tdist/$tspeed)*3600)*((100-$timebonus)/100));         
            if ($shipg[$p2_loc][$p2_num] >= 0)     $shipg[$p2_loc][$p2_num]+= $ship_hold-1;
            if ($destgoods[$p1_loc][$p1_num] >= 0) $destgoods[$p1_loc][$p1_num] += $ship_hold;
            $dshipgs = serialize($destgoods);
            mysql_query("UPDATE Locations SET shipg='".$dshipgs."' WHERE id='".($status[$t][$s][1][2]+1)."'");         
          }
          else { $message = "It's not done yet!";}
        }

        $char[gold] += $totvalue;
        $ustats[prof_earn] += $totvalue;   
        $biz['value'] += intval($totvalue/2);
        $shipgs = serialize($shipg);
        mysql_query("UPDATE Locations SET shipg='".$shipgs."' WHERE id='".$location[id]."'");
        mysql_query("UPDATE Users SET gold='".$char[gold]."' WHERE id='".$char[id]."'");
        mysql_query("UPDATE Users_stats SET prof_earn='".$ustats[prof_earn]."' WHERE id='".$char[id]."'");
        $sstatus = serialize($status[$t]);
        mysql_query("UPDATE Profs SET status='".$sstatus."', value='".$biz['value']."' WHERE id='".$biz[id]."'");
        
        $message = "Trade complete! Earned ".displayGold($totvalue)."!";        
      }
      elseif ($bizact == 17) // restart office actions
      {
        $orderTotal = 0;
        $chaosTotal = 0;
        $fundsTotal = 0;
        $defenseTotal = 0;
        $rulerTotal = 0;
        
        for ($s=0; $s<$upgrades[$t][0]; $s++)
        {
          if ($status[$t][$s][0] == 2) // is it done?
          {
            $bizprod = $status[$t][$s][1][0];
            $gname = $job_goods[$t][$bizprod];
            $valbonus = 0;
            $valbonus += $upgrades[$t][1][1]*5 + $upgrades[$t][1][2]*3;
            $gvalue=round($ustats['loc_ji'.$location[id]]*$trade_goods[$gname][1]*((100+$valbonus)/100));

            if ($bizprod == 1) $orderTotal += $gvalue;
            else if ($bizprod == 2) $chaosTotal += $gvalue;
            else if ($bizprod == 3) $fundsTotal += $gvalue;
            else if ($bizprod == 4) $fundsTotal -= $gvalue;
            else if ($bizprod == 5) $defenseTotal += $gvalue;
            else if ($bizprod == 6) $defenseTotal -= $gvalue;
            else if ($bizprod == 7) $rulerTotal += $gvalue;
            else if ($bizprod == 8) $rulerTotal -= $gvalue;
            $gtotal++;
            $timebonus = 0;
            $timebonus += $upgrades[$t][1][0]*5 + $upgrades[$t][1][2]*3 - $bx - $town_bonuses[$job_time_bonus[$t]];
            $status[$t][$s][0] = 1;
            $status[$t][$s][2][0] = time();
            $status[$t][$s][2][1] = time() + (($trade_goods[$job_goods[$t][$status[$t][$s][1][0]]][0]*3600)*((100-$timebonus)/100));          
            $sstatus = serialize($status[$t]);
          }
        }
        if ($gtotal)
        { 
          $alignTotal = 0;
          if ($orderTotal)
          {
            $location[order] += $orderTotal;
            $alignTotal += $orderTotal/10;
          }
          if ($chaosTotal) 
          {
            $location[chaos] += $chaosTotal;
            $alignTotal -= $chaosTotal/10;
          }
          if ($fundsTotal) 
          {
            $location[bank] += $fundsTotal;
            if ($location[bank] < 0) $location[bank] = 0;
            $alignTotal += $fundsTotal/1000;
          }
          if ($defenseTotal) 
          { 
            $location[army] += $defenseTotal;
            if ($location[army] < 1000 ) $location[army] = 1000;
            $alignTotal += $defenseTotal/333;
          }
          if ($rulerTotal)
          {
            $ruler = mysql_fetch_array(mysql_query("SELECT id, align, area_score FROM Soc WHERE name='$location[ruler]'"));
            $area_score = unserialize($ruler['area_score']);    
            $area_score[$location[id]] = $area_score[$location[id]]+$rulerTotal;
            $area_score[$location[id]] = number_format($area_score[$location[id]],2,'.','');
            $a_s_str = serialize($area_score);
            mysql_query("UPDATE Soc SET area_score='$a_s_str' WHERE id='$ruler[id]' ");
            
            if ($ruler[align] > 0) $alignTotal += $rulerTotal/3;
            else $alignTotal -= $rulerTotal/3;
          }       
          $char[align] += $alignTotal;
          
          $soc_name = $char[society];
          $society = mysql_fetch_array(mysql_query("SELECT id, name, align FROM Soc WHERE name='$soc_name' "));
          if ($society[id]) 
          {
            $society[align] += $alignTotal;
            $result = mysql_query("UPDATE Soc SET align = align + ".$alignTotal." WHERE id='".$society[id]."'");
          }
          $s = "";
          if ($gtotal > 1) $s = "s";
          $message = "Assignment".$s." completed!";
          mysql_query("UPDATE Locations SET myOrder=myOrder+$orderTotal, chaos=chaos+$chaosTotal, bank='".$location[bank]."', army='".$location[army]."' WHERE id='".$location[id]."'");
          mysql_query("UPDATE Profs SET status='".$sstatus."' WHERE id='".$biz[id]."'");
          mysql_query("UPDATE Users SET align='$char[align]' WHERE id=$id");
        }
        else { $message = "You didn't select any assignments to complete!"; }
      }                   
    }
    // done actions
    $bizinfo[$t] = "<div class='tab-pane' id='".$job_biz_names[$t][2]."_upgrades_tab'>";
    $bizinfo[$t] .= "<table class='table table-responsive table-clear small'>";
    $bizinfo[$t].= "<tr><th>Upgrade</th><th>Effect</th><th>Cost</th><th>&nbsp;</th></tr><tr>";
    for ($i=0; $i<3; $i++)
    {
      $bizinfo[$t].="<tr><td class='foottext'>".$biz_tools[$t][$i]."</td><td class='foottext' align='center'>".itm_info(cparse($tool_stats[$biz_tools[$t][$i]][0]))."</td>";
      if (!$upgrades[$t][1][$i])
      {
        $bizinfo[$t].="<td>".displayGold(intval($tool_stats[$biz_tools[$t][$i]][1]*$bu))."</td><td><input type='button' class='btn btn-xs btn-success' name='".$job_biz_names[$t][0]."Tool".$i."' value='Buy' onClick='javascript:bizAction(".$t.",1,".$i.");'></td></tr>";
      }
      else
      {
        $bizinfo[$t].="<td class='foottext'>Purchased</td><td>&nbsp;</td></tr>";
      }
    }
    if (($t==7 || $t==8))
    {           
      for ($i=0; $i<2; $i++)
      {
        $bizinfo[$t].= "<tr><td class='foottext'>Hire ".$biz_hires[$t][$i]." (".$hires[$t][($i+1)].")</td><td class='foottext' align='center'>".itm_info(cparse($hire_stats[$biz_hires[$t][$i]][0]))." Per Hire</td>";
        if ($hires[$t][0] < $upgrades[$t][0])
        {
          $bizinfo[$t].= "<td>".displayGold(intval(($hires[$t][0]+1)*$hirebase*$bu))."</td><td><input type='button' class='btn btn-xs btn-success' name='".$job_biz_names[$t][0]."Hire".$i."' value='Hire' onClick='javascript:bizAction(".$t.",2,".($i+1).");'></td></tr>";
        }
        else
        {
          $bizinfo[$t].= "<td class='foottext'>Max Hired</td><td>&nbsp;</td></tr>";
        }
      }
    }
    $bizinfo[$t].= "</td></tr>";
    if ($upgrades[$t][0] < $jobs[$t])
    {
      $bizinfo[$t].= "<tr><td class='foottext'>Purchase new ".$job_biz_names[$t][1]."?</td><td>&nbsp;</td><td>".displayGold(intval(($upgrades[$t][0]+1)*$stationbase*$bu))."</td><td><input type='button' class='btn btn-xs btn-success' name='buy".$job_biz_names[$t][1]."' value='Buy' onClick='javascript:bizAction(".$t.",3,1);'></td></tr>";
    }     
    $bizinfo[$t].= "</table></div>";
    

    $bizstats[$t][0] = "";
    $bizstats[$t][1] = "";
    $bizstats[$t][2] = "";
    $bizstats[$t][3] = "";
    $bizstats[$t][4] = "";
    $addShips=0;
    $bizcount[0]=0;
    $bizcount[1]=0;
    $bizcount[2]=0;
    $isIActive='';
    $isPActive='';
    $isCActive='';
    for ($i=0; $i< $upgrades[$t][0]; $i++)
    {  
      // inactive
      if ($status[$t][$i][0]==0)
      {
        $bizcount[0]++;
      }
      elseif ($status[$t][$i][0]==1) // In Progress
      {
        $bizcount[1]++;
      }
      elseif ($status[$t][$i][0]==2) // Complete
      {
        $bizcount[2]++;
      }
    }
    if ($bizcount[0]) $isIActive = "active";
    else if ($bizcount[1]) $isPActive = "active";
    else if ($bizcount[2]) $isCActive = "active";

    if ($bizcount[0] > 0)
    {
      $bizstats[$t][4] .= "<li class='".$isIActive."'><a href='#".$job_biz_names[$t][2]."_inactive_tab' data-toggle='tab'>Inactive</a></li>";
      if ($t == 9)
      {
        $addShips=1;
      }
    }
    if ($bizcount[1] > 0)
    {
      $bizstats[$t][4] .= "<li class='".$isPActive."'><a href='#".$job_biz_names[$t][2]."_progress_tab' data-toggle='tab'>In Progress</a></li>";
    }
    if ($bizcount[2] > 0)
    {
      $bizstats[$t][4] .= "<li class='".$isCActive."'><a href='#".$job_biz_names[$t][2]."_complete_tab' data-toggle='tab'>Complete</a></li>";
    }
    $bizstats[$t][4] .= "<li><a href='#".$job_biz_names[$t][2]."_upgrades_tab' data-toggle='tab'>Upgrades</a></li>";    
    if ($addShips)
    {
      $bizstats[$t][4] .= "<li><a href='#".$job_biz_names[$t][2]."_ships_tab' data-toggle='tab'>Ships</a></li>";
    }
    
    for ($i=0; $i< $upgrades[$t][0]; $i++)
    {   
      // inactive
      if ($status[$t][$i][0]==0)
      {
        if($t != 9 && $t != 0) // not shipping or market
        {
          if (!($bizstats[$t][0]))
          {
            $bizstats[$t][0] .= "<div class='tab-pane ".$isIActive."' id='".$job_biz_names[$t][2]."_inactive_tab'>";
            $bizstats[$t][0] .= "<table class='table table-responsive table-clear small'><tr><th>&nbsp;</th><th>".$job_biz_names[$t][2]."</th><th>Time</th><th>Value</th><th>&nbsp;</th></tr>";
          }
          $bizstats[$t][0] .= "<tr><td align='right'>".$job_biz_names[$t][1]." ".($i+1).": </td><td align='center'><select id='product".$i."b".$t."' name='product".$i."b".$t."' class='form-control gos-form input-sm' onChange='javascript:showProductInfo(".$t.",".$i.")'><option value='0'>-Select-</option>";
          for ($p=1; $p<=$biz_max_produce[$t][$jobs[$t]]; $p++)
          {
            $bizstats[$t][0] .= "<option value='".$p."'>".$job_goods[$t][$p]."</option>";
          }
          $bizstats[$t][0] .= "</select></td><td><div id='protime".$i."b".$t."'>&nbsp;</div></td><td><div id='proval".$i."b".$t."'>&nbsp;</div></td>";         
          $bizstats[$t][0] .= "</tr>";
        }
        else if ($t == 0) // stall
        {
          if (!($bizstats[$t][0]))
          {
            $bizstats[$t][0] .= "<div class='tab-pane ".$isIActive."' id='".$job_biz_names[$t][2]."_inactive_tab'>";
            $bizstats[$t][0] .= "<table class='table table-responsive table-clear small'><tr><th>&nbsp;</th><th>".$job_biz_names[$t][2]."</th><th>Value</th><th>Offer For</th></tr>";  
          }
          $bizstats[$t][0] .= "<tr><td align='right'>".$job_biz_names[$t][1]." ".($i+1).": </td><td align='center'><select id='product".$i."b".$t."' name='product".$i."b".$t."' class='form-control gos-form input-sm' onChange='javascript:showItemInfo(".$t.",".$i.")'><option value='-1'>-Select-</option>";
          for ($p=0; $p<$listsize; $p++)
          {
            if ($itmlist[$p][society]== "0" && $itmlist[$p][type] != 8 && $itmlist[$p][type] > 0)
            {
              $bizstats[$t][0] .= "<option value='".$p."'>".iname($itmlist[$p])."</option>";
              $so++;
            }
          }
          $bizstats[$t][0] .= "</select></td><td><div id='proval".$i."b".$t."'>&nbsp;</div></td>";         
          $bizstats[$t][0] .= "<td>";
          $bizstats[$t][0] .= "<img src='images/gold.gif' width='15' style='vertical-align:middle' alt='g:'/>";
          $bizstats[$t][0] .= "<input type='text' name='askgold".$i."' value='0' class='form-control gos-form input-sm' size='5' maxlength='2'/>";
          $bizstats[$t][0] .= "<img src='images/silver.gif' width='15' style='vertical-align:middle' alt='s:'/>";
          $bizstats[$t][0] .= "<input type='text' name='asksilver".$i."' value='0' class='form-control gos-form input-sm' size='2' maxlength='2'/>";
          $bizstats[$t][0] .= "<img src='images/copper.gif' width='15' style='vertical-align:middle' alt='c:'/>";
          $bizstats[$t][0] .= "<input type='text' name='askcopper".$i."' value='0' class='form-control gos-form input-sm' size='2' maxlength='2'/>";
          $bizstats[$t][0] .= "<input type='hidden' name='itmname".$i."' id='itmname' value=''/></td></tr>";
        }
        else // shipping
        {  
          if (!($bizstats[$t][3]))
          {        
            $bizstats[$t][3] .= "<div class='tab-pane' id='".$job_biz_names[$t][2]."_ships_tab'>";
            $bizstats[$t][3] .= "<table class='table table-responsive table-clear small'><tr><th>&nbsp;</th><th>Current Type</th><th>Speed</th><th>Cargo</th><th>Range</th>";
            $bizstats[$t][3] .= "<th>Upgrade To</th><th>Speed</th><th>Cargo</th><th>Range</th><th>Cost</th></tr>";
          }
          $bizstats[$t][3] .= "<tr><td align='right'>".$job_biz_names[$t][1]." ".($i+1).": </td><td align='center' class='foottext'>".$ship_order[$upgrades[$t][2][$i]]."</td><td align='center' class='foottext'>".$ship_type[$ship_order[$upgrades[$t][2][$i]]][1]."</td><td align='center' class='foottext'>".$ship_type[$ship_order[$upgrades[$t][2][$i]]][2]."</td><td align='center' class='foottext'>".$ship_type[$ship_order[$upgrades[$t][2][$i]]][3]."</td>";
          $bizstats[$t][3] .= "<td align='center'><select id='shipt".$i."' name='shipt".$i."' class='form-control gos-form input-sm' onChange='javascript:showShipInfo(".$upgrades[$t][2][$i].",".$i.")'><option value='-1'>-Select-</option>";
          for ($p=0; $p<6; $p++)
          {
            if ($p != $upgrades[$t][2][$i])
            {
              $bizstats[$t][3] .= "<option value='".$p."'>".$ship_order[$p]."</option>";
            }
          }
          $bizstats[$t][3] .= "</select></td>"; 
          $bizstats[$t][3] .= "<td class='foottext'><div id='shipsp".$i."'>&nbsp;</div></td><td class='foottext'><div id='shipca".$i."'>&nbsp;</div></td><td class='foottext'><div id='shipra".$i."'>&nbsp;</div></td><td class='foottext'><div id='shipco".$i."'>&nbsp;</div></td>";
          $bizstats[$t][3] .= "</tr>";
          
          if (!($bizstats[$t][0]))
          {
            $bizstats[$t][0] .= "<div class='tab-pane ".$isIActive."' id='".$job_biz_names[$t][2]."_inactive_tab'>";
            $bizstats[$t][0] .= "<table class='table table-responsive table-clear small'><tr><th>&nbsp;</th><th>Sail To (Distance)</th><th>Take (Target Supply)</th><th>Get (Local Supply)</th><th>Time</th><th>Value</th></tr>";
          }
          $bizstats[$t][0] .= "<tr><td align='right'>".$job_biz_names[$t][1]." ".($i+1).": </td><td align='center'><select id='dest".$i."' name='dest".$i."' class='form-control gos-form input-sm' onChange='javascript:setProds(".$location['id'].",".$i.")'><option value='-1'>-Select-</option>";
          for ($p=0; $p<8; $p++)
          {
            if ($ship_ports[$p] != $loc_query && ($ship_travel[$loc_query][$p] <= $ship_type[$ship_order[$upgrades[$t][2][$i]]][3]+($upgrades[$t][1][2]*50)))
            {
              $bizstats[$t][0] .= "<option value='".$p."'>".$ship_ports[$p]."</option>";
            }
          }
          $bizstats[$t][0] .= "</select><div id='portdist".$i."'>&nbsp;</div></td>";
          $bizstats[$t][0] .= "<td align='center'><select id='product".$i."b".$t."' name='product".$i."b".$t."' class='form-control gos-form input-sm' onChange='javascript:showTradeInfo(".$location['id'].",".$upgrades[$t][2][$i].",".$i.")'><option value='-1'>-Select-</option>";
          for ($p=0; $p<8; $p++)
          {
            for ($g=0; $g<3; $g++)
            {
              if ($shipg[$p][$g] !=0)
              {
                $bizstats[$t][0] .= "<option value='".($p*3+$g)."'>".$job_goods[$t][$p][$g]."</option>";
              }
            }
          }
          $bizstats[$t][0] .= "</select><div id='prod1num".$i."'>&nbsp;</div></td>";
          $bizstats[$t][0] .= "<td align='center'><select id='producto".$i."b".$t."' name='producto".$i."b".$t."' class='form-control gos-form input-sm' onChange='javascript:showTradeInfo(".$location['id'].",".$upgrades[$t][2][$i].",".$i.")'><option value='-1'>-Select-</option>";
          $bizstats[$t][0] .= "</select><div id='prod2num".$i."'>&nbsp;</div></td>";
          $bizstats[$t][0] .= "<td class='foottext'><div id='protime".$i."b".$t."'>&nbsp;</div></td><td class='foottext'><div id='proval".$i."b".$t."'>&nbsp;</div></td>";         
          $bizstats[$t][0] .= "<td>&nbsp;</td></tr>";
        }
      }
      elseif ($status[$t][$i][0]==1) // In Progress
      {
        if ($t == 0) // stall
        {
          $timepast = (time()-$status[$t][$i][2]);
          $timemsg = "Seconds ago";
          $minago = floor($timepast/60);
          if ($minago > 1 && $minago < 60) $timemsg = "$minago minutes ago";
          elseif ($minago >= 60 && $minago < 120) $timemsg = "A hour ago";
          elseif ($minago >= 120 && $minago < 1440) $timemsg = intval($minago/60)." hours ago";
          elseif ($minago >= 1440 && $minago < 2880) $timemsg = "A day ago";
          elseif ($minago >= 2880) $timemsg = intval($minago/1440)." days ago";
          $takecost = floor($status[$t][$i][1][1]/2);
          $sellval = floor(item_val(iparse($status[$t][$i][1][0],$item_base, $item_ix,$ter_bonuses))/4);
          if (!($bizstats[$t][1]))
          {
            $bizstats[$t][1] .= "<div class='tab-pane ".$isPActive."' id='".$job_biz_names[$t][2]."_progress_tab'>";
            $bizstats[$t][1] .= "<table class='table table-responsive table-clear small'><tr><th>&nbsp;</th><th>".$job_biz_names[$t][2]."ed</th><th>Price</th><th>Posted</th><th>&nbsp;</th></tr>";
          }
          $bizstats[$t][1] .=  "<tr><td align='right'>".$job_biz_names[$t][1]." ".($i+1).": </td><td align='center'><a href=javascript:popUp('itemstat.php?base=".str_replace(" ","_",$status[$t][$i][1][0][base])."&prefix=".str_replace(" ","_",$status[$t][$i][1][0][prefix])."&suffix=".str_replace(" ","_",$status[$t][$i][1][0][suffix])."&lvl=".$char[level]."&gender=".$char[sex]."&cond=".$status[$t][$i][1][0][cond]."')>";
          $bizstats[$t][1] .=  iname($status[$t][$i][1][0])."</a></td><td align='center' class='foottext'>".displayGold($status[$t][$i][1][1])."</td>";
          $bizstats[$t][1] .=  "<td align='center' class='foottext'>".$timemsg."</td>";
          $bizstats[$t][1] .=  "<td><input type='button' value='Take' class='btn btn-sm btn-warning' onClick='javascript:subAction(".$t.",11,0,".$i.");'><input type='button' value='Sell' class='btn btn-sm btn-danger' onClick='javascript:subAction(".$t.",12,0,".$i.");'></td>";
        }
        else
        {
          $timeleft = ($status[$t][$i][2][1] - time());
          if ($timeleft <0)
          {
            $status[$t][$i][0]=2;
            if ($t<7)
            {
              $status[$t][$i][4]=5;
              $qualbonus = $upgrades[$t][1][1]*5 + $upgrades[$t][1][2]*3+$town_bonuses[cQ];;
              $qualnum=$jobs[$t]*5+$qualbonus+rand(1,50);
              $status[$t][$i][5]=floor($qualnum/25);
              if ($status[$t][$i][5] < 0) $status[$t][$i][5] = 0;
              elseif ($status[$t][$i][5] > 3) $status[$t][$i][5] = 3;              
            }
            $sstatus = serialize($status[$t]);
            mysql_query("UPDATE Profs SET status='".$sstatus."' WHERE id='".$biz[id]."'");          
            $i--;
          }
          else 
          {         
            $timedone = (time()- $status[$t][$i][2][0]);
            $timemsg = "Seconds left";
            $minleft = floor($timeleft/60);
            if ($minleft > 1 && $minleft < 60) $timemsg = "$minleft minutes left";
            elseif ($minleft >= 60 && $minleft < 120) $timemsg = "A hour left";
            elseif ($minleft >= 120 && $minleft < 1440) $timemsg = intval($minleft/60)." hours left";
            elseif ($minleft >= 1440 && $minleft < 2880) $timemsg = "A day left";
            elseif ($minleft >= 2880) $timemsg = intval($minleft/1440)." days left";      
            if($t != 9) // not shipping
            { 
              $mult = 1;
              $valbonus = 0;
              $valbonus += $upgrades[$t][1][1]*5 + $upgrades[$t][1][2]*3;
              if ($t==7 || $t==8) $valbonus += $hires[$t][2]*2;
              if ($t<7)
              {
                $mult=5*0.75;
                $qualbonus=$valbonus+$town_bonuses[cQ];
                $valbonus = 0;
                $qualnum1=$jobs[$t]*5+$qualbonus+1;
                $qualnum2=$jobs[$t]*5+$qualbonus+50;
                $quals="";
                if (25-$qualnum1>0)
                {
                  $quals[0]=25-$qualnum1; 
                  $quals[1]=25;
                }
                elseif (50-$qualnum1>0)
                {
                  $quals[1]=50-$qualnum1; 
                  $quals[2]=25;
                }
                elseif (75-$qualnum1>0)
                {
                  $quals[2]=75-$qualnum1; 
                  $quals[3]=25;
                }
                if ($qualnum2>100) $quals[3]+=$qualnum2-100;
                elseif ($qualnum2>75) $quals[3]+=$qualnum2-75;
                elseif ($qualnum2>50) $quals[2]+=$qualnum2-50;
                $quality = ($quals[0]*1+$quals[1]*2+$quals[2]*3+$quals[3]*4)/50;
                $valbonus = ((100*$quality)/2);
              }
              $percomp= floor($timedone/($status[$t][$i][2][1] - $status[$t][$i][2][0])*100);
              if (!($bizstats[$t][1]))
              {
                $bizstats[$t][1] .= "<div class='tab-pane ".$isPActive."' id='".$job_biz_names[$t][2]."_progress_tab'>";
                $bizstats[$t][1] .= "<table class='table table-responsive table-clear small'><tr><th>&nbsp;</th><th>Good</th><th>Value</th><th>% Done</th><th>Time Left</th></tr>";
              }
              $bizstats[$t][1] .= "<tr><td align='right'>".$job_biz_names[$t][1]." ".($i+1).": </td><td align='center' class='foottext'>".$job_goods[$t][$status[$t][$i][1][0]]."</td>";
              if ($t != 99) $bizstats[$t][1] .= "<td align='center' class='foottext'>".displayGold($trade_goods[$job_goods[$t][$status[$t][$i][1][0]]][1]*((100+$valbonus)/100)*$mult)."</td>";
              else 
              {
                $assignAmount = round($ustats['loc_ji'.$location[id]]*$trade_goods[$job_goods[$t][$status[$t][$i][1][0]]][1]*((100+$valbonus)/100)*$mult);
                if ($status[$t][$i][1][0] == 3 || $status[$t][$i][1][0] == 4) $assignAmount = displayGold($assignAmount);
                $bizstats[$t][1] .= "<td align='center' class='foottext'>".$assignAmount."</td>";
              }
              $bizstats[$t][1] .= "<td align='center' class='foottext'>".$percomp."%</td><td align='center' class='foottext'>".$timemsg."</td></tr>";
            }
            else // shipping
            {           
              $tspeed = $ship_type[$ship_order[$upgrades[$t][2][$i]]][1];
              $tdist = $ship_travel[$ship_ports[$location['id']-1]][$status[$t][$i][1][2]];
            
              $percomp= floor($timedone/(($tdist/$tspeed)*3600)*100);
              $destloc = mysql_fetch_array(mysql_query("SELECT * FROM Locations WHERE id='".($status[$t][$i][1][2]+1)."'"));
              $destgoods = unserialize($destloc[shipg]);
              $p1_loc = floor($status[$t][$i][1][0]/3);
              $p1_num = $status[$t][$i][1][0]%3;
              $p1_lval = ($ship_travel[$ship_ports[$location[id]-1]][$p1_loc])/50*$base_good_val;
              $p1_dval = ($ship_travel[$ship_ports[$status[$t][$i][1][2]]][$p1_loc])/50*$base_good_val;
              $p1_ddem = (100-($destgoods[$p1_loc][$p1_num]/5))/100;
     
              $p2_loc = floor($status[$t][$i][1][1]/3);
              $p2_num = $status[$t][$i][1][1]%3;    
              $p2_lval = ($ship_travel[$ship_ports[$location[id]-1]][$p2_loc])/50*$base_good_val;
              $p2_dval = ($ship_travel[$ship_ports[$status[$t][$i][1][2]]][$p2_loc])/50*$base_good_val;
              $p2_ldem = (100-($shipg[$p2_loc][$p2_num]/5))/100;
             
              if ($p1_ddem<.5) $p1_ddem=.5;
              if ($p2_ldem<.5) $p2_ldem=.5;
              
              $ship_hold = $ship_type[$ship_order[$upgrades[$t][2][$i]]][2];
              $valbonus = 0;
              $valbonus += $upgrades[$t][1][1]*5;
              $tvalue = ((($p1_dval-floor($p1_lval/2))*$ship_hold*$p1_ddem)/2 + (($p2_lval-floor($p2_dval/2))*$ship_hold*$p2_ldem)/2)*((100+$valbonus)/100);
              if (!($bizstats[$t][1]))
              {
                $bizstats[$t][1] .= "<div class='tab-pane ".$isPActive."' id='".$job_biz_names[$t][2]."_progress_tab'>";
                $bizstats[$t][1] .= "<table class='table table-responsive table-clear small'><tr><th>&nbsp;</th><th>Destination</th>";
                $bizstats[$t][1] .= "<th class='hidden-xs'>Selling</th><th class='hidden-xs'>Buying</th><th class='visible-xs'>Selling / Buying</th>";
                $bizstats[$t][1] .= "<th>Value</th><th>% Done</th><th>Time Left</th></tr>";
              }
              $bizstats[$t][1] .= "<tr><td align='right'>".$job_biz_names[$t][1]." ".($i+1).": </td><td class='littletext' align='center'>".$ship_ports[$status[$t][$i][1][2]]."</td>";
              $bizstats[$t][1] .= "<td class='hidden-xs' align='center'>".$job_goods[$t][$p1_loc][$p1_num]."</td><td class='hidden-xs' align='center'>".$job_goods[$t][$p2_loc][$p2_num]."</td>";
              $bizstats[$t][1] .= "<td class='visible-xs' align='center'>".$job_goods[$t][$p1_loc][$p1_num]." / ".$job_goods[$t][$p2_loc][$p2_num]."</td>";
              $bizstats[$t][1] .= "<td class='littletext' align='center'>".displayGold($tvalue)."</td><td class='littletext' align='center'>".$percomp."%</td><td class='littletext' align='center'>".$timemsg."</td></tr>";
            }
          }
        }
      }
      elseif ($status[$t][$i][0]==2) // Complete
      {
        if ($t == 0) // stall
        {
          $timepast = (time()-$status[$t][$i][2]);
          $timemsg = "Seconds ago";
          $minago = floor($timepast/60);
          if ($minago > 1 && $minago < 60) $timemsg = "$minago minutes ago";
          elseif ($minago >= 60 && $minago < 120) $timemsg = "A hour ago";
          elseif ($minago >= 120 && $minago < 1440) $timemsg = intval($minago/60)." hours ago";
          elseif ($minago >= 1440 && $minago < 2880) $timemsg = "A day ago";
          elseif ($minago >= 2880) $timemsg = intval($minago/1440)." days ago";
          if (!($bizstats[$t][2]))
          {
            $bizstats[$t][2] .= "<div class='tab-pane ".$isCActive."' id='".$job_biz_names[$t][2]."_complete_tab'>";
            $bizstats[$t][2] .= "<table class='table table-responsive table-clear small'><tr><th>&nbsp;</th><th>".$job_biz_names[$t][2]."ed</th><th>Sold For</th><th>Sold</th><th>Bought by</th></tr>";
          }
          $bizstats[$t][2] .=  "<tr><td align='right'>".$job_biz_names[$t][1]." ".($i+1).": </td><td align='center' class='foottext'><a href=javascript:popUp('itemstat.php?base=".str_replace(" ","_",$status[$t][$i][1][0][base])."&prefix=".str_replace(" ","_",$status[$t][$i][1][0][prefix])."&suffix=".str_replace(" ","_",$status[$t][$i][1][0][suffix])."&lvl=".$char[level]."&gender=".$char[sex]."&cond=".$status[$t][$i][1][0][cond]."')>";
          $bizstats[$t][2] .=  iname($status[$t][$i][1][0])."</a></td><td align='center' class='foottext'>".displayGold($status[$t][$i][1][1])."</td>"; 
          $bizstats[$t][2] .=  "<td align='center' class='foottext'>".$timemsg."</td>";
          
          if ($status[$t][$i][3])
          {
            $stancename= array("","Ally","Enemy");
            $bizstats[$t][2] .=  "<td width='100' class='foottext' align='center'>".$stancename[$status[$t][$i][3]]."</td>";
          }
          else
          {
            $bizstats[$t][2] .=  "<td width='100' class='foottext' align='center'>Neutral</td>";          
          }
        }      
        elseif($t != 9) // not shipping
        {  
          $valbonus = 0;
          $valbonus += $upgrades[$t][1][1]*5 + $upgrades[$t][1][2]*3;
          if ($t==7 || $t==8) $valbonus += $hires[$t][2]*2;
          if ($t<7)
          { 
            $qualbonus=$valbonus+$town_bonuses[cQ];
            $quality = $status[$t][$i][5];
            $valbonus = ((100*$quality)/2);
            if ($t == 4) $inum=20;
            elseif ($t == 5) $inum=21;
            elseif ($t == 6) $inum=19;
            $gname=getQualityWord($inum, $quality)." ".$job_goods[$t][$status[$t][$i][1][0]];                      
            $gvalue=$item_base[strtolower($gname)][2]*$status[$t][$i][4]*0.75;
          }
          else 
          {
            $gname=$job_goods[$t][$status[$t][$i][1][0]];
            $gvalue=$trade_goods[$job_goods[$t][$status[$t][$i][1][0]]][1]*((100+$valbonus)/100);
          }
          
          if (!($bizstats[$t][2]))
          {
            $bizstats[$t][2] .= "<div class='tab-pane ".$isCActive."' id='".$job_biz_names[$t][2]."_complete_tab'>";
            $bizstats[$t][2] .= "<table class='table table-responsive table-clear small'><tr><th>&nbsp;</th><th>Good</th><th>Value</th>";
            if ($t<7) $bizstats[$t][2] .= "<th>Quantity</th><th>&nbsp;</th>";
            else $bizstats[$t][2] .= "<th>&nbsp;</th><th>&nbsp;</th>";
            $bizstats[$t][2] .= "</tr>";
          }
          $bizstats[$t][2] .= "<tr><td align='right'>".$job_biz_names[$t][1]." ".($i+1).": </td><td align='center' class='foottext'>".ucwords($gname)."</td>";
          if ($t != 99 ) $bizstats[$t][2] .= "<td align='center' class='foottext'>".displayGold($gvalue)."</td>";
          else 
          {
            $assignAmount = round($ustats['loc_ji'.$location[id]]*$trade_goods[$job_goods[$t][$status[$t][$i][1][0]]][1]*((100+$valbonus)/100));
            if ($status[$t][$i][1][0] == 3 || $status[$t][$i][1][0] == 4) $assignAmount = displayGold($assignAmount);
            $bizstats[$t][2] .= "<td align='center' class='foottext'>".$assignAmount."</td>";
          }
          if ($t<7)
          {
            $bizstats[$t][2] .= "<td align='center' class='foottext'><select id='cquant".$i."b".$t."' name='cquant".$i."b".$t."' class='form-control gos-form input-sm'>";
            for ($p=$status[$t][$i][4]; $p>0; $p--)
            {
              $bizstats[$t][2] .= "<option value='".$p."'>".$p."</option>";
            }
            $bizstats[$t][2] .= "</select></td>";
            $bizstats[$t][2] .= "<td align='center' class='foottext'><input type='button' value='Take' class='btn btn-warning btn-sm' onClick='javascript:subAction(".$t.",6,0,".$i.");'></td>";
          }
          else
          {
            $bizstats[$t][2] .= "<td>&nbsp;</td><td>&nbsp;</td>";
          }
          $bizstats[$t][2] .= "</tr>";    
        }
        else // shipping
        {
          $destloc = mysql_fetch_array(mysql_query("SELECT * FROM Locations WHERE id='".($status[$t][$i][1][2]+1)."'"));
          $destgoods = unserialize($destloc[shipg]);
          $p1_loc = floor($status[$t][$i][1][0]/3);
          $p1_num = $status[$t][$i][1][0]%3;
          $p1_lval = ($ship_travel[$ship_ports[$location[id]-1]][$p1_loc])/50*$base_good_val;
          $p1_dval = ($ship_travel[$ship_ports[$status[$t][$i][1][2]]][$p1_loc])/50*$base_good_val;
          $p1_ddem = (100-($destgoods[$p1_loc][$p1_num]/5))/100;
    
          $p2_loc = floor($status[$t][$i][1][1]/3);
          $p2_num = $status[$t][$i][1][1]%3;    
          $p2_lval = ($ship_travel[$ship_ports[$location[id]-1]][$p2_loc])/50*$base_good_val;
          $p2_dval = ($ship_travel[$ship_ports[$status[$t][$i][1][2]]][$p2_loc])/50*$base_good_val;
          $p2_ldem = (100-($shipg[$p2_loc][$p2_num]/5))/100;
          
          if ($p1_ddem<.5) $p1_ddem=.5;
          if ($p2_ldem<.5) $p2_ldem=.5;
            
          $ship_hold = $ship_type[$ship_order[$upgrades[$t][2][$i]]][2];
          $valbonus = 0;
          $valbonus += $upgrades[$t][1][1]*5;
          $tvalue = ((($p1_dval-floor($p1_lval/2))*$ship_hold*$p1_ddem)/2 + (($p2_lval-floor($p2_dval/2))*$ship_hold*$p2_ldem)/2)*((100+$valbonus)/100);
          if (!($bizstats[$t][2]))
          {
            $bizstats[$t][2] .= "<div class='tab-pane ".$isCActive."' id='".$job_biz_names[$t][2]."_complete_tab'>";          
            $bizstats[$t][2] .= "<table class='table table-responsive table-clear small'><tr><th>&nbsp;</th><th>Destination</th><th>Selling</th><th>Buying</th><th>Value</th><th>&nbsp;</th></tr>";
          }
          $bizstats[$t][2] .= "<tr><td align='right'>".$job_biz_names[$t][1]." ".($i+1).": </td><td class='littletext' align='center'>".$ship_ports[$status[$t][$i][1][2]]."</td><td class='littletext' align='center'>".$job_goods[$t][$p1_loc][$p1_num]."</td><td class='littletext' align='center'>".$job_goods[$t][$p2_loc][$p2_num]."</td><td class='littletext' align='center'>".displayGold($tvalue)."</td>";
          $bizstats[$t][2] .= "<td class='littletext'></td></tr>";
        }
      }
    }
    if ($bizstats[$t][0])
    {
      if ($t == 0) $bizstats[$t][0] .= "<tr><td colspan='4' align='right'><input type='button' value=".$job_biz_names[$t][2]." class='btn btn-sm btn-success' onClick='javascript:subAction(".$t.",10,0,".$i.");'></td></tr></table></div>";
      else if ($t == 9 ) $bizstats[$t][0] .= "<tr><td colspan='9' align='right'><input type='button' value=".$job_biz_names[$t][2]." class='btn btn-sm btn-success' onClick='javascript:subAction(".$t.",7,0,0);'></td></tr></table></div>";
      else $bizstats[$t][0] .= "<tr><td colspan='4' align='right'><input type='button' value=".$job_biz_names[$t][2]." class='btn btn-sm btn-success' onClick='javascript:subAction(".$t.",4,0,0);'></td></tr></table></div>";
    }
    if ($bizstats[$t][1]) $bizstats[$t][1] .= "</table></div>";
    if ($bizstats[$t][2])
    {
      if ($t == 0) $bizstats[$t][2] .= "<tr><td colspan='4' align='right' class='foottext'><input type='button' value='Clear' class='btn btn-sm btn-success' onClick='javascript:subAction(".$t.",13,0,0);'></td></tr>";
      else if ($t == 9) {
        $bizstats[$t][2] .= "<tr><td colspan='5' align='right' class='foottext'><input type='button' value='Dock' class='btn btn-sm btn-success' onClick='javascript:subAction(".$t.",8,0,0);'></td>";
        $bizstats[$t][2] .= "<td align='center' class='foottext'><input type='button' value='Again' class='btn btn-sm btn-success' onClick='javascript:subAction(".$t.",16,0,0);'></td></tr>";        
      }
      else if ($t == 99) {
        $bizstats[$t][2] .= "<tr><td colspan='4' align='right' class='foottext'><input type='button' value='Complete' class='btn btn-sm btn-success' onClick='javascript:subAction(".$t.",14,0,0);'></td>";
        $bizstats[$t][2] .= "<td align='center' class='foottext'><input type='button' value='Again' class='btn btn-sm btn-success' onClick='javascript:subAction(".$t.",17,0,0);'></td></tr>";
      }
      else {
        $bizstats[$t][2] .= "<tr><td colspan='4' align='right' class='foottext'><input type='button' value='Sell' class='btn btn-sm btn-success' onClick='javascript:subAction(".$t.",5,0,0);'></td>";
        $bizstats[$t][2] .= "<td align='center' class='foottext'><input type='button' value='Again' class='btn btn-sm btn-success' onClick='javascript:subAction(".$t.",15,0,0);'></td></tr>";
      }
      $bizstats[$t][2] .= "</table></div>";
    }
    if ($bizstats[$t][3])
    {
      if ($t==9) $bizstats[$t][3] .= "<tr><td colspan='10' align='right'><input type='button' value='Trade In' class='btn btn-sm btn-success' onClick='javascript:subAction(".$t.",9,0,0);'></td></tr></table></div>";
      else $bizstats[$t][3] .= "</table></div>";
    }

  }
}
?>              
<script type="text/javascript">
  var sgoods = new Array(6);
<?php
  for ($i = 0; $i<8; $i++)
  {
    echo "sgoods[$i]= new Array(3);\n";
    echo "sgoods[$i][0] = '-Select-';\n";
    for ($j=1; $j<4; $j++)
    {
      $tmp_good = $job_goods[9][$i][$j-1];
      echo "sgoods[$i][$j] = '".$tmp_good."';\n";      
    }
  }
  echo "var base_good_val = ".$base_good_val.";\n";
  echo "var bu = ".$bu.";\n";
  echo "var bx = ".$bx.";\n";
?> 
  var distance = new Array(8);
  var sdata = {};
  var ports = new Array(8);
  var ships = new Array(6);
  var ups = new Array(14);
  ups[0]='';
<?php

  for ($i = 0; $i<8; $i++)
  {
    echo "distance[$i]= new Array(8);\n"; // distance
    for ($j=0; $j<8; $j++)
    {
      echo "distance[$i][$j] = ".$ship_travel[$ship_ports[$i]][$j].";\n";
    }
    for ($j=1; $j<4; $j++) // prod port
    {
      $tmp_good = $job_goods[9][$i][$j-1];
      echo "sdata.".str_replace(" ","_",$tmp_good)." = {};\n";
      echo "sdata.".str_replace(" ","_",$tmp_good).".port = ".$trade_goods[$tmp_good][0].";\n";     
    }
    $tmploc = mysql_fetch_array(mysql_query("SELECT * FROM Locations WHERE id='".($i+1)."'"));
    $tmpsg = unserialize($tmploc[shipg]);
    echo "ports[$i] = new Array (6);\n";
    for ($j=0; $j < 8; $j++) // port prod num
    {
      echo "ports[$i][$j] = new Array (3);\n";
      for ($k=0; $k<3; $k++)
      {
        echo "ports[$i][$j][$k] = ".$tmpsg[$j][$k].";\n";
      }
    }
  }  
  for ($i = 0; $i<6; $i++)
  {
    // ship data
    echo "ships[$i] = new Array(4);\n";
    echo "ships[$i][0] = ".$ship_type[$ship_order[$i]][1].";\n"; // speed
    echo "ships[$i][1] = ".$ship_type[$ship_order[$i]][2].";\n"; // cargo
    echo "ships[$i][2] = ".$ship_type[$ship_order[$i]][3].";\n"; // range
    echo "ships[$i][3] = ".intval($ship_type[$ship_order[$i]][0]*$bu).";\n"; // cost
  }
  

  for ($i = 1; $i<14; $i++)
  {
    $j=$i;
    if ($i==13) $j=99;
    if ($j<4 || ($j>9 && $j!=99)) echo "ups[$i]='';\n";
    else
    {
      echo "ups[$i]=new Array(2);\n";
      $timebonus = 0;
      $timebonus += $upgrades[$j][1][0]*5 + $upgrades[$j][1][2]*3 - $bx - $town_bonuses[$job_time_bonus[$j]];
      $valbonus = 0;
      $valbonus += $upgrades[$j][1][1]*5;
      if ($i!=9) $valbonus += $upgrades[$j][1][2]*3;
      if ($i==7 || $i==8)
      {
        $valbonus += $hires[$j][2]*2;
        $timebonus += $hires[$j][1]*2;
      }
      if ($i<7)
      {
        $qualbonus=$valbonus+$town_bonuses[cQ];
        $valbonus = 0;
        $qualnum1=$jobs[$i]*5+$qualbonus+1;
        $qualnum2=$jobs[$i]*5+$qualbonus+50;
        $quals="";
        if (25-$qualnum1>0)
        {
          $quals[0]=25-$qualnum1; 
          $quals[1]=25;
        }
        elseif (50-$qualnum1>0)
        {
          $quals[1]=50-$qualnum1; 
          $quals[2]=25;
        }
        elseif (75-$qualnum1>0)
        {
          $quals[2]=75-$qualnum1; 
          $quals[3]=25;
        }
        if ($qualnum2>100) $quals[3]+=$qualnum2-100;
        elseif ($qualnum2>75) $quals[3]+=$qualnum2-75;
        elseif ($qualnum2>50) $quals[2]+=$qualnum2-50;
        $quality = ($quals[0]*1+$quals[1]*2+$quals[2]*3+$quals[3]*4)/50;
        $valbonus = ((100*$quality)/2);          
      }
      echo "ups[$i][0] = ".((100-$timebonus)/100).";\n";
      echo "ups[$i][1] = ".((100+$valbonus)/100).";\n";
    }
  }
?>

function setProds(loc,sub)
{
  loc= loc-1;
  var index = document.getElementById('dest'+sub).value;
  var newElem = document.getElementById('producto'+sub+'b9');
  var tmp = '';
  newElem.options.length = 0;
  var arr = sgoods[index];
  for (var i=0; i<arr.length; i++) 
  {
    tmp = arr[i];
    if (i != 0)
    {
      newElem.options[newElem.options.length] = new Option(tmp,(index*3)+i-1);
    }
    else
    {
      newElem.options[newElem.options.length] = new Option(tmp,-1);
    }
  }
  
  document.getElementById('protime'+sub+'b9').innerHTML="";
  document.getElementById('proval'+sub+'b9').innerHTML= ""; 
  document.getElementById('portdist'+sub).innerHTML="<center><font class='foottext'>("+distance[loc][index]+")</font></center>";  
}

function showTradeInfo(loc,ship,sub)
{
  var dest = document.getElementById('dest'+sub).value;
  var prodElem = document.getElementById('product'+sub+'b9');
  var prod2Elem = document.getElementById('producto'+sub+'b9');
  var prod1 = document.getElementById('product'+sub+'b9').value;
  var prod2 = document.getElementById('producto'+sub+'b9').value;
  loc = loc-1;
  var p1_loc=Math.floor(prod1/3);
  var p1_num=prod1%3;
  var p2_loc  = Math.floor(prod2/3);
  var p2_num  = prod2%3;
  if (prod1>=0)
  {
    if (ports[dest][p1_loc][p1_num]>=0)
    {
      document.getElementById('prod1num'+sub).innerHTML="<center><font class='foottext'>("+ports[dest][p1_loc][p1_num]+")</font></center>";
    }
    else
    {
      document.getElementById('prod1num'+sub).innerHTML="<center><font class='foottext'>FULL</font></center>";
    }
  }
  else
  {
    document.getElementById('prod1num'+sub).innerHTML="&nbsp;";
  }
  if (prod2>=0)
  {
    document.getElementById('prod2num'+sub).innerHTML="<center><font class='foottext'>("+ports[loc][p2_loc][p2_num]+")</font></center>";
  }
  else
  {
    document.getElementById('prod2num'+sub).innerHTML="&nbsp;";
  }  
  if (prod1 >= 0 && prod2 >= 0)
  {    
    var p1_lval = (distance[loc][p1_loc])/50*base_good_val;
    var p1_dval = (distance[parseInt(dest)][p1_loc])/50*base_good_val;
    var p1_ddem = (100-(ports[dest][p1_loc][p1_num]/5))/100;
    if (p1_ddem <0.50) p1_ddem=0.50;
    if (ports[dest][p1_loc][p1_num]<0) p1_ddem = 0;
    
    var p2_lval = (distance[loc][p2_loc])/50*base_good_val;
    var p2_dval = (distance[dest][p2_loc])/50*base_good_val;
    var p2_ldem = (100-(ports[loc][p2_loc][p2_num]/5))/100;  
    if (p2_ldem <0.50) p2_ldem=0.50; 

    var tvalue = (((p1_dval-Math.floor(p1_lval/2))*ships[ship][1]*p1_ddem)/2 + ((p2_lval-Math.floor(p2_dval/2))*ships[ship][1]*p2_ldem)/2)*ups[9][1];
    var ptime = distance[loc][dest]/ships[ship][0]*ups[9][0];    
    
    document.getElementById('protime'+sub+'b9').innerHTML="<center><font class='foottext'>"+ ptime.toFixed(2)+" hours</font></center>";
    document.getElementById('proval'+sub+'b9').innerHTML= "<center><font class='foottext'>"+displayGold(Math.floor(tvalue),0)+"</font></center>";    
  }
  else
  {
    document.getElementById('protime'+sub+'b9').innerHTML="";
    document.getElementById('proval'+sub+'b9').innerHTML= "";
  }
}

function showShipInfo(ship, sub)
{
  var shipup = document.getElementById('shipt'+sub).value;
  var cost = ships[shipup][3]-Math.floor(ships[ship][3]/2);
  document.getElementById('shipsp'+sub).innerHTML="<center><font class='foottext'>"+ ships[shipup][0]+"</font></center>";
  document.getElementById('shipca'+sub).innerHTML="<center><font class='foottext'>"+ ships[shipup][1]+"</font></center>";
  document.getElementById('shipra'+sub).innerHTML="<center><font class='foottext'>"+ ships[shipup][2]+"</font></center>";
  document.getElementById('shipco'+sub).innerHTML="<center><font class='foottext'>"+ displayGold(cost,0)+"</font></center>";
}

function bizAction (num,act,val)
{
  document.bizform.biznum.value=num;
  document.bizform.bizact.value=act;  
  document.bizform.bizval.value=val;
  document.bizform.submit();
}

function subAction (num,act,val,sub)
{
  document.bizform.biznum.value=num;
  document.bizform.bizact.value=act;  
  document.bizform.bizval.value=val;  
  document.bizform.bizsub.value=sub;
  document.bizform.submit();
}

function showProductInfo(pro, sub)
{ 
  var basepro= pro;
  if (pro==99) pro=13;
  var goods = new Array(14);
  var data = {};
  goods[0] = '';
<?php
for ($i=1; $i<14; $i++)
{
  $k=$i;
  if ($i==13) $k=99;
  $maxgoods = 12;
  if ($i==7) $maxgoods=18;
  elseif ($k==99) $maxgoods=8;
  echo "goods[$i] = new Array(".($maxgoods+1).");";
  for ($j=0; $j<=$maxgoods; $j++)
  {
    echo "goods[$i][$j] = \"".str_replace(" ","_",$job_goods[$k][$j])."\";";
    if (($k>3 || $k==0) && ($k<9 || $k==99) && $j>0)
    {
      echo "data.".str_replace(" ","_",$job_goods[$k][$j])." = {};";
      echo "data.".str_replace(" ","_",$job_goods[$k][$j]).".ptime = ".$trade_goods[$job_goods[$k][$j]][0].";";
      echo "data.".str_replace(" ","_",$job_goods[$k][$j]).".value = ".$trade_goods[$job_goods[$k][$j]][1].";";      
    }
  }
}
echo "var locji=".$ustats['loc_ji'.$location[id]].";";
?>
  var prod = document.getElementById('product'+sub+'b'+basepro).value;

  var mult = 1;
  if (pro < 7) mult = 5*0.75;
  document.getElementById('protime'+sub+'b'+basepro).innerHTML= "<center><font class='littletext'>"+(data[goods[pro][prod]].ptime*ups[pro][0]).toFixed(2)+" hours</font></center>";
  if (basepro!=99)
  {
    document.getElementById('proval'+sub+'b'+basepro).innerHTML= "<center><font class='littletext'>"+displayGold(Math.floor(data[goods[pro][prod]].value*ups[pro][1]*mult),0)+"</font></center>"; 
  }
  else
  {
    var val = Math.round((data[goods[pro][prod]].value*ups[pro][1]*mult)*locji);
    if (prod==3 || prod==4)
    {
      val = displayGold(Math.floor(val),0);
    }
    document.getElementById('proval'+sub+'b'+basepro).innerHTML= "<center><font class='littletext'>"+val+"</font></center>"; 
  }
}

  var inv = new Array();
<?php
  for ($i=0; $i<$listsize; ++$i)
  {
    echo "  inv[".$i."] = new Array('".str_replace(" ","_",$itmlist[$i][base])."','".str_replace(" ","_",$itmlist[$i][prefix])."','".str_replace(" ","_",$itmlist[$i][suffix])."','".$itmlist[$i][cond]."','".$itmlist[$i][id]."');";
  }
?>

function showItemInfo(pro,sub)
{ 
  var prod = document.getElementById('product'+sub+'b'+pro).value;
  if (sub == 0)  document.bizform.itmname0.value = inv[prod][4];  
  else if (sub == 1)  document.bizform.itmname1.value = inv[prod][4];
  else if (sub == 2)  document.bizform.itmname2.value = inv[prod][4];
  else if (sub == 3)  document.bizform.itmname3.value = inv[prod][4];
  else if (sub == 4)  document.bizform.itmname4.value = inv[prod][4];
  else if (sub == 5)  document.bizform.itmname5.value = inv[prod][4];
  else if (sub == 6)  document.bizform.itmname6.value = inv[prod][4];
  else if (sub == 7)  document.bizform.itmname7.value = inv[prod][4];
  else if (sub == 8)  document.bizform.itmname8.value = inv[prod][4];
  else if (sub == 9)  document.bizform.itmname9.value = inv[prod][4];
  
  document.getElementById('proval'+sub+'b'+pro).innerHTML= "<center><font class='littletext'>"+displayGold(item_val(iparse(inv[prod][0],inv[prod][1],inv[prod][2],inv[prod][3])),0)+"</font></center>"; 
}

</script>
<?php
if ($mode != 1) { $bg = "background-image:url('images/townback/".str_replace(' ','_',strtolower($char['location'])).".jpg'); "; }

include('header.htm');
?>
<!-- TABS -->
  <div class="row solid-back">
    <div class="col-sm-12">
      <div id="content">
        <ul id="biztabs" class="nav nav-tabs" data-tabs="tabs">       
      <?php
        for ($t=0; $t<11; $t++)
        { 
          if ($t==10) $t=99;
          if ($myLocBiz[$t])
          {
            $isActive="";
            if ($biznum==$t || ($biznum == '' && $firstbiz==$t)) $isActive="class='active'";
            echo "<li ".$isActive."><a href='#".$job_biz_names[$t][0]."_tab' data-toggle='tab'>".ucwords($job_biz_names[$t][0])."</a></li>";
          }
        }
      ?>
        </ul>

        <form name='bizform' class='form-inline' action='mybusinesses.php' method='POST'>
          <input type='hidden' name='biznum' value='-1'/>
          <input type='hidden' name='bizact' value='-1'/>
          <input type='hidden' name='bizval' value='-1'/>
          <input type='hidden' name='bizsub' value='-1'/> 

        <div class="tab-content">        
      <?php
        for ($t=0; $t<11; $t++)
        { 
          if ($t==10) $t=99;
          if ($myLocBiz[$t])
          {
            $isActive="";
            if ($biznum==$t || ($biznum == '' && $firstbiz==$t)) $isActive="active";          
            echo "<div class=\"tab-pane ".$isActive."\" id='".$job_biz_names[$t][0]."_tab'>";
      ?>
            <div class='col-md-5 col-lg-4 hidden-xs hidden-sm'>      
              <table class='table table-clear small table-bordered'>
                <tr>
                <?php
                  for ($i=0; $i<9; $i++)
                  {
                    if ($i < $upgrades[$t][0])
                    {
                      $goodName = $job_goods[$t][$i];
                      if ($t==9) $goodName=$job_goods[$t][floor($i/3)][$i%3];
                      if ($t>0 || $status[$t][$i][0] != 1) 
                      {
                        echo "<td width='33%' align='center'><img class='img img-optional' src='images/profs/".$job_biz_names[$t][0].($status[$t][$i][0]+1).".jpg'/></td>";
                      }
                      else
                      {
                        $imgname= "items/".str_replace(' ','',$status[$t][$i][1][0][base]).".gif";
                        echo "<td width='33%' align='center'><img class='img img-optional' src='".$imgname."'/></td>";
                      }
                    }
                    else
                    {
                      $goodName = $job_goods[$t][$i];
                      if ($t==9) $goodName=$job_goods[$t][floor($i/3)][$i%3];
                      echo "<td width='33%' align='center'><img class='img img-optional' src='images/profs/univprof.jpg'/></td>";
                    }              
                    if (($i+1)%3==0) echo "</tr><tr>";
                  }
                ?>
                </tr>
              </table>
            </div>
            <div class='col-sm-12 col-md-7 col-lg-8'>
              <div class='row'>
                  <div id="content">
                    <ul id="bstatstabs" class="nav nav-tabs" data-tabs="tabs"><?php echo $bizstats[$t][4];?></ul>
                    <div class="tab-content">
                      <?php echo $bizstats[$t][0].$bizstats[$t][1].$bizstats[$t][2].$bizstats[$t][3].$bizinfo[$t]."<br/>"; ?>
                    </div>
                  </div>
              </div>
            </div>
          </div>
      <?php
          }
        }
      ?>
        </div>              
        </form>
      </div>
    </div>
  </div>
<?php
$no_show_footer = 0;

include('footer.htm');
?>