<?php
include_once("connect.php");
include_once('locFuncs.php');
include_once('displayFuncs.php');
include_once("questFuncs.php");

mysql_query("LOCK TABLES Users WRITE, messages WRITE, Items WRITE, Locations WRITE, Users_stats WRITE, Soc WRITE, Contests WRITE, Hordes WRITE, Quests WRITE, Routes WRITE;");

// Determine if unique bonuses that affect other cities are purchased.
$defRuler='';
$orderRuler='';
$chaosRuler='';

// Ruled Defense bonus
$defcity = mysql_fetch_array(mysql_query("SELECT ruler, upgrades FROM Locations WHERE name='Tar Valon'"));
$defups = unserialize($defcity[upgrades]);
if ($defups[7]>0) $defRuler = $defcity[ruler];

// Ruled Order bonus
$ordcity = mysql_fetch_array(mysql_query("SELECT ruler, upgrades FROM Locations WHERE name='Salidar'"));
$ordups = unserialize($ordcity[upgrades]);
if ($ordups[7]>0) $orderRuler = $ordcity[ruler];

// Ruled Chaos bonus
$chaoscity = mysql_fetch_array(mysql_query("SELECT ruler, upgrades FROM Locations WHERE name='Thakan&#39;dar'"));
$chaosups = unserialize($chaoscity[upgrades]);
if ($chaosups[7]>0) $chaosRuler = $chaoscity[ruler];

$save_location = $location;
$result_loc = mysql_query("SELECT * FROM Locations WHERE 1");
while ( $listloc = mysql_fetch_array( $result_loc ) )
{
  echo "Updating ".$listloc[name]."...";
  
  // Only do this once an hour
  if ($listloc[id] == 1)
  {
    // CHECK IF SEALS ARE BREAKIN
    $cityRumors = mysql_fetch_array(mysql_query("SELECT * FROM messages WHERE id='50000'"));
    $topchar = mysql_fetch_array(mysql_query("SELECT id, level FROM Users WHERE 1 ORDER BY level DESC, exp DESC LIMIT 1"));
    $numSeals = mysql_num_rows(mysql_query("SELECT id FROM Items WHERE type=0"));
    $numBroken = mysql_num_rows(mysql_query("SELECT id FROM Items WHERE type=0 AND owner='99999'"));
    $breakSeal = 0;
    // check if all are broken
    if ($numBroken < 7)
    {
      if ($cityRumors[checktime] > 0)
      {
        // At least one seal has broken. Check to see if 5 days have past since the last one broken.
        $daysSince = intval(((time()/3600)-$cityRumors[checktime])/24);
        if ($numBroken < intval($daysSince/5)+1)
        {
          $breakSeal = 1;
        }
      }
      else if ($topchar[level] >= 80)
      {
        // If no seals have broken yet and a player is level 80 or greater, break a seal. Save the time.
        mysql_query("UPDATE messages SET checktime='".intval(time()/3600)."' WHERE id='$cityRumors[id]'");
        $breakSeal = 1;
      }
  
      if ($breakSeal == 1)
      {
        $sealsLeft = 7-$numBroken;
        $sealToBreak = rand(1, $sealsLeft);

        // If the seal we're breaking is one that's being held, delete it. Else, just inserted a broken one.
        if ($sealToBreak <= $numSeals-$numBroken)
        {
          $sealNum=1;
          $result = mysql_query("SELECT * FROM Items WHERE type=0 ORDER BY last_moved");  
          while ($tmpSeal = mysql_fetch_array( $result ) )
          {
            if ($tmpSeal[base] != "broken seal")
            {
              $sealList[$sealNum++] = $tmpSeal;
            }
          }
          $result5 = mysql_query("DELETE FROM Items WHERE type=0 AND owner='".$sealList[$sealToBreak][owner]."'");
        }
        // Insert a broken seal. Set owner to 99999, so no one has it.
        // Rumors page has special code to handle owner of 99999.
        $itime = time();
        $result = mysql_query("INSERT INTO Items (owner,  type,cond,istatus,points,society,last_moved,base,         prefix,suffix,stats) 
                                          VALUES ('99999','0', '0', '0',    '1',   '',     '$itime',  'broken seal','',    '',    '')");
      }
      $numBroken = mysql_num_rows(mysql_query("SELECT id FROM Items WHERE type=0 AND owner='99999'"));
    }
  }
  
  
   
  $hourspast = intval(time()/3600) - $listloc[last_update];
  if ($hourspast > 100) $hourspast = 100;
  if ($hourspast > 0)
  {
    echo "Hours past = ".$hourspast." | ";
    $listloc[last_update] = intval(time()/3600);
    $upgrades = unserialize($listloc[upgrades]);
    
    // Check for stamina bonus
    if (!$listloc[isDestroyed])
    {
      $town_bonuses = cparse(getTownBonuses($upgrades, $unique_buildings[$listloc[name]], $unique_build_bonuses));
      if ($town_bonuses[rM] > 0)
      {
        // Find all users in the city and give them extra stamina!
        $locUsers= mysql_query("SELECT id, stamina FROM Users WHERE location = '$listloc[name]'");
        while ($locUser = mysql_fetch_array($locUsers))
        {
          mysql_query("UPDATE Users SET stamina=stamina+".$town_bonuses[rM]." WHERE id='$locUser[id]'");
        }
      }
    }
    
    // update shipping
    $tmpsg = unserialize($listloc[shipg]);
    for ($i=0; $i<6; $i++)
    {
      for ($j=0; $j<3; $j++)
      {
        if ($tmpsg[$i][$j]>0) 
        {
          $sgsub = $hourspast;
          if ($sgsub > $tmpsg[$i][$j]) $sgsub = $tmpsg[$i][$j];
          $tmpsg[$i][$j] = $tmpsg[$i][$j]-$sgsub;
        }
      }
    }
     
    // update resources
    if (!$listloc[isDestroyed])
    {

      // tournament/war update
      $endtime = intval(time()/3600);
      $clanscores = unserialize($listloc[clan_scores]);

      $WarWinner = "";
      $result2 = mysql_query("SELECT id, contestants, rules, reward, type, distro FROM Contests WHERE location='$listloc[name]' AND done='0' AND ends<='$endtime'");
      while ($contest = mysql_fetch_array( $result2 ) )
      {
        echo "Updating contest ".$contest[id]." | ";
        $contestants = unserialize($contest[contestants]);
        $rules = unserialize($contest[rules]);
        $reward = unserialize($contest[reward]);
        $results = unserialize($contest[results]);
        $ranks = array();
        
        foreach ($contestants as $cid => $cdata)
        {
          if ($cdata[0]) 
          { 
            $ranks[$cdata[0]]=$cdata[1];
            $rid[$cdata[0]]=$cid;
          }
        }
        arsort($ranks);
        $place=1;
        $pdone=0;
        $numc= count($contestants);
        $gr=$reward[0];
        $jr=$reward[1];
        $first='';
        $winner='';
        foreach ($ranks as $rname => $rscore) 
        {
          if (!$pdone)
          {
            // tourney, not a war
            if ($contest[type]!= 10)
            {
              $cname = explode('_', $rname);
              $winner= mysql_fetch_array(mysql_query("SELECT * FROM Users LEFT JOIN Users_stats ON Users.id=Users_stats.id WHERE Users.name='$cname[0]' AND Users.lastname='$cname[1]'"));
              if ($place==1)
              {
                $first = $winner[name]." ".$winner[lastname];
                if ($contest[distro]==1) // winner take all
                {            
                  $bg= floor($gr*.8);
                  $pg= $gr-$bg;
                  $gr=0;
                  $winner[bankgold]+= $bg;
                  $winner[gold]+= $pg;
                  $winner[ji] += $jr;
                  $jr=0;
                  $pdone=1;
                  mysql_query("UPDATE Users_stats SET win_tourney=win_tourney+1 WHERE id='$winner[id]'"); 
                }
                else if ($contest[distro]==2) // top 3
                {
                  $wshare=0.6;
                  if ($numc==1) $wshare+=.4; else if ($numc==2) $wshare+=.1;
                  $gshare = round($reward[0]*$wshare);
                  $bg= floor($gshare*.8);
                  $pg= $gshare-$bg;
                  $gr-= $gshare;
                  $winner[bankgold]+= $bg;
                  $winner[gold]+= $pg;
                  $jshare = round($reward[1]*$wshare);
                  $winner[ji] += $jshare;
                  $jr-= $jshare;
                  mysql_query("UPDATE Users_stats SET win_tourney=win_tourney+1 WHERE id='$winner[id]'"); 
                }
                $society = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$winner[soc_name]' "));
                $area_rep = unserialize($society[area_rep]);
                $area_rep[$listloc[id]] += 100;
                if ($area_rep[$listloc[id]] > 500) $area_rep[$listloc[id]] = 500;
                $area_reps = serialize($area_rep);
                mysql_query("UPDATE Soc SET area_rep='".$area_reps."' WHERE id='".$society[id]."'");
              }
              else if ($place==2)
              {
                if ($contest[distro]==2) // top 3
                {
                  $gshare = round($reward[0]*.3);
                  $bg= floor($gshare*.8);
                  $pg= $gshare-$bg;
                  $gr-= $gshare;
                  $winner[bankgold]+= $bg;
                  $winner[gold]+= $pg;
                  $jshare = round($reward[1]*.3);
                  $winner[ji] += $jshare;
                  $jr-= $jshare;
                }
              }
              else if ($place==3)
              {
                if ($contest[distro]==2) // top 3
                {
                  $gshare = $gr;
                  $bg= floor($gshare*.8);
                  $pg= $gshare-$bg;
                  $gr-= $gshare;
                  $winner[bankgold]+= $bg;
                  $winner[gold]+= $pg;
                  $jshare = $jr;
                  $winner[ji] += $jshare;
                  $jr-= $jshare;
                  $pdone=1;
                }
              }
              mysql_query("UPDATE Users SET bankgold='$winner[bankgold]', gold='$winner[gold]' WHERE id='$winner[id]'");
              mysql_query("UPDATE Users_stats SET ji='$winner[ji]' WHERE id='$winner[id]'"); 
              if ($gr==0 && $jr==0)
              {
                $pdone=1;
              }
            }
            else
            {
              // this is a war!!
              if ($place==1)
              {
                $cname = explode('_', $rname);
                $winner= mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$rname'"));
                $first = $winner[name];
                
                // Update Chaos
                // If winner is not ruler, add extra chaos.
                $listloc[chaos]+=(25+floor($jr/100));
                $WarWinner = $first;
                if ($first != $listloc[ruler]) 
                {
                  $listloc[chaos]+=100;
                }
                
                $listloc[bank]+=$gr;
                $gr=0;
                mysql_query("UPDATE Locations SET bank='$listloc[bank]', chaos='$listloc[chaos]' WHERE id='$listloc[id]'");
                $stance = unserialize($winner['stance']);
                $numSupport=0;
                $supporters='';
                foreach ($stance as $c_n => $c_s)
                {
                  if ($c_n && $c_s==1)
                  {
                    $ally= mysql_fetch_array(mysql_query("SELECT id, support FROM Soc WHERE name='$c_n'"));
                    $asupport=unserialize($ally['support']);
                    if ($asupport[$listloc[id]] == $winner[id])
                    {
                      $supporters[$numSupport]=$ally[id];
                      $numSupport++;
                    }
                  }
                }
                $wj=$jr/2;
                $jr=$wj;
                $sj=$jr/($numSupport+1);
                $wj+=$sj;
                for ($s=0; $s<$numSupport; $s++)
                {
                  $sally= mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE id='$supporters[$s]'"));
                  $sascore = unserialize($sally['area_score']);
                  $sascore[$listloc[id]]=$sascore[$listloc[id]]+$sj;
                  $ssas = serialize($sascore);
                  mysql_query("UPDATE Soc SET area_score='$ssas' WHERE id='$sally[id]'");
                }
                $wascore = unserialize($winner['area_score']);
                $wascore[$listloc[id]]=$wascore[$listloc[id]]+$wj;
                $swas = serialize($wascore);
                $winner['area_score'] = $swas;
                $jr=0; 
                mysql_query("UPDATE Soc SET area_score='$swas' WHERE id='$winner[id]'");
              }
              else
              {
                // everyone else loses a percentage of their Ji which goes directly to the winner (not spread to supporters)
                $wascore = unserialize($winner['area_score']);
                $loser= mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$rname'"));
                $lascore = unserialize($loser['area_score']);
                $jpercent = 0.10;
                if ($rules[0] == $loser[id]) $jpercent = 0.05;
                $jiRisk = floor($lascore[$listloc[id]]*$jpercent);
                $wascore[$listloc[id]] += $jiRisk;
                $lascore[$listloc[id]] -= $jiRisk;
                $clanscores[$winner[id]] = $wascore[$listloc[id]];
                $clanscores[$loser[id]] = $lascore[$listloc[id]];
                $rules[2] = $jiRisk;
                $swas = serialize($wascore);
                $lwas = serialize($lascore);
                mysql_query("UPDATE Soc SET area_score='$swas' WHERE id='$winner[id]'");
                mysql_query("UPDATE Soc SET area_score='$lwas' WHERE id='$loser[id]'");

                $area_rep = unserialize($loser[area_rep]);
                $area_rep[$listloc[id]] -= 250;
                if ($area_rep[$listloc[id]] < -500) $area_rep[$listloc[id]] = -500;
                $area_reps = serialize($area_rep);
                mysql_query("UPDATE Soc SET area_rep='".$area_reps."' WHERE id='".$loser[id]."'");
              }
            }
          }
          $place++;
        }
        $srules = serialize($rules);
        mysql_query("UPDATE Contests SET done=1, rules = '$srules', winner='$first' WHERE id='$contest[id]'");    
      }
      // end tournaments/war
    
      // Determine rulers
      echo "Determine rulers. | ";
      $halfhour = time()-1800;    
      $resultf = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM Users WHERE location='$listloc[name]' AND depart<='$halfhour' "));
      $numchar = $resultf[0];
      $listloc[pop]=getTownPop($upgrades,$numchar,$build_pop);
      $newruler= "No One";
      $topscore= "100";
      $locid = $listloc['id'];
      $supporting=array();

      $result2 = mysql_query("SELECT id, area_score, stance FROM Soc WHERE 1 ORDER BY score DESC");
      $armyup = 0;
      if ($listloc[ruler]==$defRuler) $armyup+=10;
      while ($soc = mysql_fetch_array( $result2 ) )
      {
        $clan_id = $soc[id];
        if (!$clanscores[$clan_id]) $clanscores[$clan_id]=0;
        $area_score = unserialize($soc['area_score']);
        {
          $tempas = ($area_score[$locid]);
          for ($h=0; $h < $hourspast; $h++)
          {
            $tempas = ($tempas*(.9996));
          }
          $tempas = (round($tempas*100))/100;
         
          // Adjust city army health
          $stance = unserialize($soc['stance']);
          $rs=0;
          foreach ($stance as $c_n => $c_s)
          {
            if ($listloc[ruler]==str_replace("_"," ",$c_n))
            {
              $rs = $c_s;
            }
          }
          if ($rs == 1) // allied with ruler
          {
            $armyup += $tempas/33;
          }
          elseif ($rs == 2) // enemy with ruler
          {
            $armyup -= $tempas/75;
          }
          else // neutral with ruler
          {
            $armyup += $tempas/50;
          }
          
          $area_score[$locid] = number_format($tempas,4,'.','');
          $sas= serialize($area_score);
          mysql_query("UPDATE Soc SET area_score='$sas' WHERE id='$clan_id'");
        }
      }
      $armyup = round($armyup);
      $listloc[army] += $armyup;
      if ($listloc[army] < 10000) $listloc[army] = 10000;
      elseif ($listloc[army] > $hhealth*1.5) $listloc[army] = $hhealth*1.5;
      $topfound=0;
      $rival = 0;
      $rulerAlign = 0;
      if ($clanscores)
      {
        foreach ($clanscores as $key => $value)
        {
          if (!$topfound)
          {
            $soc = mysql_fetch_array(mysql_query("SELECT id, name, members, align FROM `Soc` WHERE id = '$key'"));
            if ($value >= $topscore) 
            {
              $newruler = $soc['name'];
              $rulerAlign = getClanAlignment($soc[align], $soc[members]);
              $topscore = $value;
            }
            else
            {
              $rival = $value;
            }
            $topfound = 1;
          }
          else if ($rival == 0)
          {
            $rival = $value;
          }
        }
      }    
     
      $numhorde = mysql_num_rows(mysql_query("SELECT id FROM Hordes WHERE done='0' AND target='$listloc[name]'"));    
  
      for ($h=0; $h < $hourspast; $h++)
      {
        if (!$numhorde) $listloc[bank] += $listloc[pop]*10;
        
        if ($listloc[last_war])
        {
          $myWar = mysql_fetch_array(mysql_query("SELECT id, starts, ends, contestants, results FROM Contests WHERE id='$listloc[last_war]' "));
        }
        else
        {
          $myWar = 0;
        }
        if ($myWar != 0) // Previous clan battle
        {
          if ($myWar[starts] >= intval(time()/3600) || $myWar[ends] < intval(time()/3600))
          {
            $myWar=0;
          }
        }
      
        // Update Order & Chaos 
        $chaosEffect = ceil($listloc[chaos]*(5*(rand(1,5))/100));     
        $listloc[chaos] -= $chaosEffect;
        $listloc[myOrder] -= $chaosEffect;
        $orderUp = floor(($topscore-$rival)/500);
        if ($orderUp > 10) $orderUp = 10;
        $orderUp2 = floor($numchar/2);
        if ($orderUp2 > 10) $orderUp2 = 10;
        if ($listloc[ruler] == $orderRuler) $listloc[myOrder] += 5;
        $listloc[myOrder] += $orderUp + $orderUp2 + $rulerAlign;
  
        // Add Seal effects
        $sealid = $listloc[id]+50000;
        $hasSeal = mysql_num_rows(mysql_query("SELECT id FROM Items WHERE type=0 && owner='$sealid'"));
        if ($hasSeal)
        {
          $listloc[chaos] += 5;
          $listloc[army] += 50;
          $listloc[bank] += 500;
        }
      
        if ($myWar == 0 && $numhorde==0)
        {
          $rulercut = round($listloc[bank]/1000);
          $listloc[bank] -= $rulercut;
          if ($listloc[ruler]!=$newruler)
          {
            // Update City Rumors
            $cityRumors = mysql_fetch_array(mysql_query("SELECT * FROM messages WHERE id='50000'"));
  
            $rumorMessages = unserialize($cityRumors[message]);
            $numRumors = count($rumorMessages);
          
            $myMsg = "<".$listloc[name]."_".time().">` ".$newruler." has taken control of ".$listloc[name]." from ".$listloc[ruler]."!|";
            $rumorMessages[$numRumors] = $myMsg;
    
            $rumorMessages = pruneMsgs($rumorMessages, 50);
            $newRumors = serialize($rumorMessages);
            mysql_query("UPDATE messages SET message='$newRumors' WHERE id='50000'");         
          }
          $listloc[ruler]=$newruler;
          if ($WarWinner != "" && $WarWinner != $listloc[ruler])
          {
            if ($hasSeal)
            {
              $result5 = mysql_query("DELETE FROM Items WHERE type=0 && owner='$sealid'");
            }
          }
          $ruler =  mysql_fetch_array(mysql_query("SELECT id, bank, stance FROM Soc WHERE name='$listloc[ruler]'"));
          $stance = unserialize($ruler['stance']);
          if ($chaosRuler != '')
          {
            foreach ($stance as $c_n => $c_s)
            {
              if ($c_s ==2)
              {
                if ($chaosRuler==str_replace("_"," ",$c_n))
                {
                  $listloc[chaos]+=2;
                }
              }
            }
          }
          $ruler[bank] += $rulercut;
        }
        else // at war
        {
          $result = mysql_query("SELECT id, name, lastname, society FROM Users WHERE location='".$listloc['name']."'");
          while ( $wlistchar = mysql_fetch_array( $result ) )
          {
            $wsoc = mysql_fetch_array(mysql_query("SELECT id, support FROM Soc WHERE name='$wlistchar[society]'")); 
            $wid= $wsoc[id];
            $issup = 1;
            $wsupport = unserialize($wsoc['support']);
            if ($wsupport[$listloc[id]] != 0)
            {
              $wid = $wsupport[$listloc[id]];
              $issup=2;
            }
            $wcon = unserialize($myWar[contestants]);
            if ($wcon[$wid]) 
            {
              $wresults = unserialize($myWar[results]);
              if (!$wresults[$wid]) $wresults[$wid] = array(0); 
              $rnum=0;
              if ($issup==2) $rnum=2;
              $wresults[$wid][$rnum] += 1;
              $wcon[$wid][1] += 1;
              $swr=serialize($wresults);
              $swc=serialize($wcon);
              mysql_query("UPDATE Contests SET contestants='$swc', results='$swr' WHERE id='$myWar[id]'");       
            }
          }
        }
      }
      
      // Update quests
      $location = $listloc;
      include("locBonuses.php");
      $qalign = "";
      if ($town_bonuses[uQ]) $qalign=0;
      elseif ($town_bonuses[lQ]) $qalign=1;
      elseif ($town_bonuses[sQ]) $qalign=2;      
      // check for expired quests
      $result = mysql_query("SELECT * FROM Quests WHERE location='".$location[name]."' && done='0' && cat != 1");
      while ($quest = mysql_fetch_array( $result ) )
      {        
        if ($quest[expire] != -1 && $quest[expire]*3600 < time())
        {
          $resultt = mysql_query("UPDATE Quests SET done='1' WHERE id='".$quest[id]."'");
        }
      }
      // check for quest generation
      $result2 = mysql_query("SELECT * FROM Quests WHERE location='".$listloc[name]."' && done='0' && num_avail='-1' && cat != 1 && type != '".$quest_type_num['Support']."'");
      $num_quests = mysql_num_rows($result2);
      $maxquests = 4+$town_bonuses[eQ];
      for ($i=0; $i+$num_quests < $maxquests; $i++)
      {
        $qs = unserialize(generate_random_quest($listloc[name],$qalign));
        $sql = "INSERT INTO Quests (name,       type,       offerer,       num_avail,       num_done,started,       expire,       align,       location,       reqs,       goals,       special,       reward,       done) 
                            VALUES ('$qs[name]','$qs[type]','$qs[offerer]','$qs[num_avail]',0,       '$qs[started]','$qs[expire]','$qs[align]','$qs[location]','$qs[reqs]','$qs[goals]','$qs[special]','$qs[reward]',0)";
        $resultt = mysql_query($sql, $db);
      }

      $tmpsgs = serialize($tmpsg);
      mysql_query("UPDATE Locations SET shipg='".$tmpsgs."',last_update='$listloc[last_update]', ruler='$listloc[ruler]', bank='$listloc[bank]', myOrder='$listloc[myOrder]', chaos='$listloc[chaos]', pop='$listloc[pop]', army='$listloc[army]' WHERE id='".$listloc[id]."'");
      mysql_query("UPDATE Soc SET bank='$ruler[bank]' WHERE id='$ruler[id]'");
    } // if !isDestroyed
    else
    {
      mysql_query("UPDATE Locations SET last_update='$listloc[last_update]' WHERE id='".$listloc[id]."'");
    }
  } // if hourspast
} // while city

mysql_query("UNLOCK TABLES;"); 

mysql_query("LOCK TABLES Estates WRITE;");
      $result2 = mysql_query("SELECT id, location, upgrades, owner, value, good FROM Estates WHERE level >= '2'");
      while ($testate = mysql_fetch_array( $result2 ) )
      {
        $tups = unserialize($testate[upgrades]);
        $testate[good] += $tups[9];
        if ($testate[good] > $tups[9] * 100) $testate[good] = $tups[9]*100;
        mysql_query("UPDATE Estates SET good='".$testate[good]."' WHERE id='$testate[id]'");
      }
mysql_query("UNLOCK TABLES;");
?>