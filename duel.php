<?php

$enemy_id = intval($_POST[enemyid]);

include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/charFuncs.php");
include_once("admin/duelFuncs.php");
include_once("admin/locFuncs.php");
include_once('map/mapdata/coordinates.inc');

if ($location_array[$char['location']][2])
{
  $is_town=1;
  $dw= $town_bonuses[dW];
}
else 
{
  $is_town=0;
  $dw=0;
}

$surrounding_area = $map_data[$char[location]];

$charip = unserialize($char[ip]); 
$alts = getAlts($charip);

$tlog = "";
$wikilink = "Battle_Tips";
$redirect=0;
$rmsg='';
$soc_name = $char[society];
$society = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$soc_name' "));
$stats = mysql_fetch_array(mysql_query("SELECT * FROM Users_stats WHERE id='$char[id]'"));
$location = mysql_fetch_array(mysql_query("SELECT * FROM Locations WHERE name='$char[location]'"));

$multiply = 1;
if ($char[donor] && $_POST[ddd] && ($battlelimit-$char[battlestoday]>1)) $multiply = 2;
if ($char[battlestoday] >= $battlelimit)  
{
  $redirect=1;
  $rmsg='You have reached your battle limit. Please wait for more turns.';
}
if ($enemy_id == $id)  
{
  $redirect=1;
  $rmsg='You punch yourself in the ear and fall over!';
}
if ($char['stamina'] <= 0)  
{
  $redirect=1;
  $rmsg='You just do not have the energy for that!';
}

// FIND BATTLE
$find_battle = unserialize($char[find_battle]);
$char2 = mysql_fetch_array(mysql_query("SELECT * FROM Users LEFT JOIN Users_data ON Users.id=Users_data.id WHERE Users.id='$enemy_id'"));
if ($find_battle[$enemy_id] > time() - intval(30*(100+$dw)/100)) // DUEL FIX: 0 -> 300 
{ 
  $redirect=1;
  $rmsg='You must wait before attacking that character again!';
}
else
  $find_battle[$enemy_id] = time();
 
$choose_battle = 1;
$find_battle = serialize($find_battle);
if (!$char2[id])
{
  $redirect=1;
  $rmsg="Unable to find the opponent $enemy_id";
}
if ($char['location'] != $char2['location']) 
{
  $redirect=1;
  $rmsg="It's impossible to duel from such distances! ".$enemy_id;
}
$lvlbonus = $char2[level]- $char[level];
if ($lvlbonus > 15 || $lvlbonus < -15) 
{
  $redirect=1;
  $rmsg="That character is outside your level range. Pick on someone your own size!";
}

$ally=0;
$myTourney=0;
$tresults=0;
$tpts1 = 0;
$tpts2 = 0;
$wpts1 = 0;
$wpts2 = 0;
$warJi = 0;
$slvl1=$char[level];
$slvl2=$char2[level];
$hasSeal1 = mysql_num_rows(mysql_query("SELECT id FROM Items WHERE type=0 && owner='$char[id]'"));
$hasSeal2 = mysql_num_rows(mysql_query("SELECT id FROM Items WHERE type=0 && owner='$char2[id]'"));
$hasHorn1 = mysql_num_rows(mysql_query("SELECT id FROM Items WHERE type='-1' && owner='$char[id]'"));
$hasHorn2 = mysql_num_rows(mysql_query("SELECT id FROM Items WHERE type='-1' && owner='$char2[id]'"));
$citySeal = 0;
if ($is_town)
{
  $sealid = $location[id]+50000;
  $citySeal = mysql_num_rows(mysql_query("SELECT id FROM Items WHERE type=0 && owner='$sealid'"));
}
// Check for tournaments
if ($char[lastcontest] && $char[lastcontest] == $char2[lastcontest]) // same tourney?
{
  $myTourney = mysql_fetch_array(mysql_query("SELECT * FROM Contests WHERE id='$char[lastcontest]' "));
  if ($myTourney[done] || ($myTourney[starts] > intval(time()/3600))) $myTourney=0; // tourney over or hasn't started yet
  else // both players in same active tourney 
  {
    $trules = unserialize($myTourney[rules]);
    $tresults = unserialize($myTourney[results]);
    if (!$tresults[$char[id]]) $tresults[$char[id]] = array(0);
    if (!$tresults[$char[id]][$char2[id]]) $tresults[$char[id]][$char2[id]] = array(0,0,0);
    if ($tresults[$char[id]][$char2[id]][0] >= $trules[2]) $myTourney = 0;
    else
    {
      $tresults[$char[id]][$char2[id]][0] = $tresults[$char[id]][$char2[id]][0]+1;
      if ($char[location]== $myTourney[location])
      {
        $tpts1+=5;
        $tpts2+=5;
      }
      $tlb = 3;
      if ($myTourney[type] == 2) $tlb=1;
      $tpts1 += ($slvl2-$slvl1)*$tlb;
      $tpts2 += ($slvl1-$slvl2)*$tlb;
    }
  }
}

$stats2 = mysql_fetch_array(mysql_query("SELECT * FROM Users_stats WHERE id='$char2[id]'"));

include('admin/equipped.php');

$duel_health= intval(($char[vitality]+$char2[vitality])/2);

$char_attack = array(
'level' => array($char[level],$char2[level]),
'max_health' => array($duel_health,$duel_health),
'health' => array($duel_health,$duel_health),
'health_limit' => array($duel_health,$duel_health),
'def_mult' => array(0,0),
'def' => array(0,0),
'speed' => array(20,20), // temp values. Replace based on class
'poison' => array(0,0),
'taint' => array(0,0),
'accuracy' => array(0,0),
'dodge' => array(0,0),
'move' => array(0,0),
'wound' => array(0,0),
);
// Prof Bonuses
$pro_bonus="";
if($pro_stats[dL])
{
  $pro_bonus .= "L".$pro_stats[dL];
}

// Estate bonuses
$est_bonus = "";
$myEstate= mysql_fetch_array(mysql_query("SELECT * FROM Estates WHERE owner='$id' AND location='$char[location]'"));
if ($myEstate)
{
  $eups = unserialize($myEstate[upgrades]);
  $est_stats = cparse(getUpgradeBonuses($eups, $estate_ups, 9));
  $est_bonus .= "O".$est_stats[zD]." ";
  $est_bonus .= "N".$est_stats[zE]." ";
  $est_bonus .= "L".$est_stats[zL]." ";
}

$est_bonus2 = "";
$myEstate= mysql_fetch_array(mysql_query("SELECT * FROM Estates WHERE owner='$char2[id]' AND location='$char[location]'"));
if ($myEstate)
{
  $eups = unserialize($myEstate[upgrades]);
  $est_stats = cparse(getUpgradeBonuses($eups, $estate_ups, 9));
  $est_bonus2 .= "O".$est_stats[zD]." ";
  $est_bonus2 .= "N".$est_stats[zE]." ";
  $est_bonus2 .= "L".$est_stats[zL]." ";
}

// Clan Bonuses
$clan_bonus1="A0 B0 ";
$clan_bonus2="A0 B0 ";
$loc = $char['location'];

$upgrades = unserialize($society['upgrades']);
$society2 = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$char2[society]' "));
$upgrades2 = unserialize($society2['upgrades']);
$enemy = 0;
$location = mysql_fetch_array(mysql_query("SELECT id, ruler FROM Locations WHERE name='$char[location]'"));
if ($location[id])
{
  if ($location[ruler] == $society[name])
  {
    $clan_bonus1 = $clan_bonus1."N".($upgrades[2]*2)." G".($upgrades[3]*2)." V".($upgrades[4])." "; 
  }
  if ($location[ruler] == $society2[name])
  {
    $clan_bonus2 = $clan_bonus2."N".($upgrades2[2]*2)." G".($upgrades2[3]*2)." V".($upgrades2[4])." "; 
  }  
}
if ($soc_name != "" && $char['society']!=$char2['society']) 
{
  $stance = unserialize($society['stance']);
  foreach ($stance as $c_n => $c_s)
  {
    if ($c_s ==2)
    {
      if ($char2['society']==str_replace("_"," ",$c_n))
      {
        $enemy = 1;
      }
      if ($location[id])
      {
        if ($location[ruler]==str_replace("_"," ",$c_n))
        {
          $clan_bonus1 = $clan_bonus1."O".($upgrades[5]*2)." P".($upgrades[6])." Y".($upgrades[7])." ";
        }
      }
    }
  }
  if ($enemy)
  {
    $clan_bonus1 = $clan_bonus1."L".($upgrades[1]*2)." ";
    $clan_bonus2 = $clan_bonus2."L".($upgrades2[1]*2)." ";
  }
}

$slead=0;
$slead2=0;
// Are we leaders?
if (strtolower($name) == strtolower($society[leader]) && strtolower($lastname) == strtolower($society[leaderlast]) ) 
{
  $slead=1;
}
if ($subs > 0)
{
  foreach ($subleaders as $c_n => $c_s)
  {
    if (strtolower($name) == strtolower($c_s[0]) && strtolower($lastname) == strtolower($c_s[1]) )
    {
      $slead=2;
    }
  }
}
if (strtolower($char2[name]) == strtolower($society2[leader]) && strtolower($char2[lastname]) == strtolower($society2[leaderlast]) ) 
{
  $slead2=1;
}
if ($subs > 0)
{
  foreach ($subleaders as $c_n => $c_s)
  {
    if (strtolower($char2[name]) == strtolower($c_s[0]) && strtolower($char2[lastname]) == strtolower($c_s[1]) )
    {
      $slead2=2;
    }
  }
}

if ($location[ruler] == $char[society]) $clan_bonus1 = $clan_bonus1."N".($upgrades[3]*2)." G".($upgrades[4]*2)." ";
else if ($location[ruler] == $char2[society]) $clan_bonus2 = $clan_bonus2."N".($upgrades2[3]*2)." G".($upgrades2[4]*2)." ";

if ($char[name] == $char2[name] || ucwords($char[name]) == "The" || ucwords($char2[name]) == "The")
{
  $name1 = $char[name]." ".$char[lastname];
  $name2 = $char2[name]." ".$char2[lastname];
} 
else 
{
  $name1 = $char[name]; 
  $name2 = $char2[name];
}
$player_word = array($name1,$name2);

// combine all bonuses
$bonuses1 = $pro_bonus." ".$est_bonus;
$bonuses2 = $est_bonus2;
if (!$myTourney)
{
  $bonuses1 .= $clan_bonus1." ".$stamina_effect1;
  $bonuses2 .= $clan_bonus2." ".$stamina_effect2;
}
if (!$myTourney || $myTourney[type] != 3)
{
  $bonuses1 .= " ".$skill_bonuses1;
  $bonuses2 .= " ".$skill_bonuses2;
}
if (!$myTourney || $myTourney[type] != 4)
{
  $bonuses1 .= " ".$stomach_bonuses1;
  $bonuses2 .= " ".$stomach_bonuses2;
}

$weapon_a = $bonuses1;
$weapon_b = $bonuses2;
if (!$myTourney || $myTourney[type] != 2) // No Equip Tourney
{
  $weapon_a .= " ".$estats[0];
  $weapon_b .= " ".$estats2[0];
}

$awesomeness=lvl_req(clbc($weapon_a),100);

$bresult = doDuel($char_attack, $player_word, $weapon_a, $weapon_b);

$score1 = $bresult[0][score1];
$score2 = $bresult[0][score2];
$winner = $bresult[0][winner];

$char_attack[health][0] = $bresult[0][ahpf];
$char_attack[health][1] = $bresult[0][dhpf];

$bresult[0][stam1] = number_format($stam_diff1*100);
$bresult[0][stam2] = number_format($stam_diff2*100);

// No Cheating
if (!$_GET[data]) $battle_view = 1000; else $battle_view = intval($_GET[data]+100);
$time_dif = time()-$char[nextbattle];
$update_battles='';
if ($time_dif >= 0 && $char[battlestoday] < $battlelimit) {
  $char[battlestoday] += $multiply;
  $update_battles=", nextbattle='".intval($time+($battle_view*0.001)*(1+new_bline(1,"","","","")))."', battlestoday='".$char[battlestoday]."'";
}
elseif ($time_dif < 0) 
{
  $redirect=1;
  $rmsg="Wait until the last battle finishes!";
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////

if (!$redirect)
{
// ATTACKER'S PASSIVE EFFECTS
$passive = $weapon_a;
$char_stats = cparse($passive,0);
$g_steal = intval($char_stats['L']);

if ($g_steal>95) $g_steal=95;
//$x_gain = intval($char_stats['pX']);

// UPDATE SCORES FOR CLOSE LEVELS
$strength = getStrength($char[used_pts], $char[equip_pts], $char[stamina], $char[stamaxa]);
$strength2 = getStrength($char2[used_pts], $char2[equip_pts], $char2[stamina], $char2[stamaxa]);
$equipbonus = floor(($strength2-$strength)/15);
$lvlbonus = $char2[level]- $char[level];
if ($lvlbonus > 5) $lvlbonus =5;
if ($lvlbonus < -15) $lvlbonus =-15;
  
$username = $char2[name]."_".$char2[lastname];
$myquests= unserialize($char[quests]);

if ($society[id])
{
  $stance = unserialize($society['stance']);
  foreach ($stance as $c_n => $c_s)
   {
    if ($c_s ==1 && $char2['society']==str_replace("_"," ",$c_n))
    {
      $ally = 1;
    }
  }
}
if ($score1 && !$alts[$username] && $ally != 1) 
{
  $area_rep = unserialize($society[area_rep]);
  if ($is_town)
  {
    $loc = $char['location'];
    $loc_query = mysql_fetch_array(mysql_query("SELECT * FROM `Locations` WHERE name = '$loc'"));

    $atWar=1;
    if ($soc_name != "" && $loc_query[last_war])
    {
      $myWar = mysql_fetch_array(mysql_query("SELECT * FROM Contests WHERE id='$loc_query[last_war]' "));
      if (!$myWar[done] && !($myWar[starts] > intval(time()/3600)))
      {
        // Get clan id's that might be involved in war.
        $wid= $society[id];
        $support = unserialize($society['support']);
        if ($support[$loc_query[id]] != 0)
        {
          $wid = $support[$loc_query[id]];
        }
        $wcon = unserialize($myWar[contestants]);
        if ($wcon[$wid]) 
        {
          $atWar=2;
        }
      }
    }
    $areascoreup = (4+.4*($lvlbonus+$equipbonus)+$citySeal)*$multiply/$atWar;
    $arep = $area_rep[$loc_query[id]];
    $areascoreup=(1+$arep/1000)*$areascoreup;

    if ($areascoreup < 0) $areascoreup=0;
    if ($atWar==2) $warJi=$areascoreup;

    $sQuest = 0;
    if ($myquests) 
    {
      foreach ($myquests as $c_n => $c_s)
      {
        if ($c_s[0] == 1)
        {
          $quest = mysql_fetch_array(mysql_query("SELECT * FROM Quests WHERE id='$c_n'")); 
          $goals = unserialize($quest[goals]);
          $qreward= unserialize($quest[reward]);
          
          if ($quest[type] == $quest_type_num["Support"] && $goals[2] == $loc && $soc_name != $quest[offerer] && $quest[done]==0) 
          {
            $sQuest = 1;
            $societySup = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='".$quest[offerer]."' "));
            $area_score_sup = unserialize($societySup['area_score']);
            $clan_id = $societySup[id];
            $area_score_sup[$loc_query[id]] += $areascoreup;
            $area_score_sup[$loc_query[id]] = number_format($area_score_sup[$loc_query[id]],2,'.','');
            $a_ss_str = serialize($area_score_sup);
            mysql_query("UPDATE Soc SET score=score+($areascoreup), area_score='$a_ss_str' WHERE id='$clan_id' ");
              
            $greward = $areascoreup * $qreward[1];
            $char[gold] += $greward;
            $stats[quest_earn] += $greward;
            $stats[support_quest_ji] += $areascoreup;
            mysql_query("UPDATE Users SET gold=gold+($greward) WHERE id='".$char[id]."'");
            mysql_query("UPDATE Users_stats SET quest_earn='".$stats[quest_earn]."', support_quest_ji='".$stats[support_quest_ji]."' WHERE id='".$char[id]."'");

            $goals[0] += $areascoreup;
            $qdone = 0;
            if ($goals[0] >= $goals[1]) { $qdone = 1;}
            $sgoals = serialize($goals);
            mysql_query("UPDATE Quests SET goals='$sgoals', done='$qdone' WHERE id='$quest[id]'");
            break;
          }
        }
      }
    }    
    if ($soc_name != "" && $sQuest == 0)
    {
      $clan_id = $society[id];
      $area_score = unserialize($society['area_score']);    
      $area_score[$loc_query[id]] = $area_score[$loc_query[id]]+$areascoreup;
      $area_score[$loc_query[id]] = number_format($area_score[$loc_query[id]],2,'.','');
      $a_s_str = serialize($area_score);
      mysql_query("UPDATE Soc SET area_score='$a_s_str', score=score+$areascoreup WHERE id='$clan_id' ");  
    }
    
    $stats['loc_ji'.$loc_query[id]]+=$areascoreup;
    $stats['loc_ji'.$loc_query[id]]=number_format($stats['loc_ji'.$loc_query[id]],2,'.','');
    $result = mysql_query("UPDATE Users_stats SET loc_ji".$loc_query[id]."='".$stats['loc_ji'.$loc_query[id]]."' WHERE id='$id'");
  }
  else
  {   
    // SET VARIABLES / LOAD MAP
    $score_up = (1+.1*($lvlbonus+$equipbonus))*$multiply;
    if ($score_up < 0) $score_up = 0;

    for ($x=0; $x<4; $x++)
    {
      $loc = $surrounding_area[$x];        
      $loc_query = mysql_fetch_array(mysql_query("SELECT * FROM `Locations` WHERE name = '$loc'"));

      $atWar=1;
      if ($soc_name != "" && $loc_query[last_war])
      { 
        $myWar = mysql_fetch_array(mysql_query("SELECT * FROM Contests WHERE id='$loc_query[last_war]' "));
        if (!$myWar[done] && !($myWar[starts] > intval(time()/3600)))
        {
          // Get clan id's that might be involved in war.
          $wid= $society[id];
          $support = unserialize($society['support']);
          if ($support[$loc_query[id]] != 0)
          {
            $wid = $support[$loc_query[id]];
          }
          $wcon = unserialize($myWar[contestants]);
          if ($wcon[$wid]) 
          {
            $atWar=2;
          }
        }
      }

      $score_up2 = $score_up/$atWar;
      $arep = $area_rep[$loc_query[id]];
      $score_up2=(1+$arep/1000)*$score_up2;
      if ($atWar==2) $warJi=$score_up2;        

      $sQuest = 0;
      if ($myquests) 
      {
        foreach ($myquests as $c_n => $c_s)
        {
          if ($c_s[0] == 1)
          {
            $quest = mysql_fetch_array(mysql_query("SELECT * FROM Quests WHERE id='$c_n'")); 
            $goals = unserialize($quest[goals]);
            $qreward= unserialize($quest[reward]);
            if ($quest[type] == $quest_type_num["Support"] && $goals[2] == $loc && $soc_name != $quest[offerer] && $quest[done]==0) 
            {
              $sQuest = 1;
              $societySup = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='".$quest[offerer]."' "));
              $area_score_sup = unserialize($societySup['area_score']);
              $clan_id = $societySup[id];

              $area_score_sup[$loc_query[id]] += $score_up2;
              $area_score_sup[$loc_query[id]] = number_format($area_score_sup[$loc_query[id]],2,'.','');
              $a_ss_str = serialize($area_score_sup);
              mysql_query("UPDATE Soc SET score=score+($score_up2), area_score='$a_ss_str' WHERE id='$clan_id' ");
              
              $greward = $score_up2 * $qreward[1];
              $char[gold] += $greward;
              $stats[quest_earn] += $greward;
              $stats[support_quest_ji] += $score_up2;
              mysql_query("UPDATE Users SET gold=gold+($greward) WHERE id='".$char[id]."'");
              mysql_query("UPDATE Users_stats SET quest_earn='".$stats[quest_earn]."', support_quest_ji='".$stats[support_quest_ji]."' WHERE id='".$char[id]."'");

              $goals[0] += $score_up2;
              $qdone = 0;
              if ($goals[0] >= $goals[1]) { $qdone = 1;}
              $sgoals = serialize($goals);
              mysql_query("UPDATE Quests SET goals='$sgoals', done='$qdone' WHERE id='$quest[id]'");
              break;
            }
          }
        }
      }
      if ($soc_name != "" && $sQuest==0)
      {
        $clan_id = $society[id];
        $area_score = unserialize($society['area_score']);
        $area_score[$loc_query[id]] = $area_score[$loc_query[id]]+$score_up2;
        $area_score[$loc_query[id]] = number_format($area_score[$loc_query[id]],2,'.','');
        $a_s_str = serialize($area_score);
        $society['area_score'] = $a_s_str;
        mysql_query("UPDATE Soc SET score=score+($score_up2), area_score='$a_s_str' WHERE id='$clan_id' "); 
      }     
      $stats['loc_ji'.$loc_query[id]]+=$score_up2;
      $stats['loc_ji'.$loc_query[id]]=number_format($stats['loc_ji'.$loc_query[id]],2,'.','');
      $result = mysql_query("UPDATE Users_stats SET loc_ji".$loc_query[id]."='".$stats['loc_ji'.$loc_query[id]]."' WHERE id='$id'");
    }
  }
}
  
// Update Stamina
$stamina1 = 0;
$stamina2 = 0;  

// -1 for a loss
if (!$score1) {$stamina1++;}
else {$stamina2++;}
// -1 per 100 damage taken
$stamina1 += floor(($char_attack[health_limit][0]-$char_attack[health][0])/100);
$stamina2 += floor(($char_attack[health_limit][1]-$char_attack[health][1])/100);
// -1 if health == 0
if ($char_attack[health][0] == 0) $stamina1++;
if ($char_attack[health][1] == 0) $stamina2++;

// CHECK QUESTS
if ($score1 && $myquests)
{
  foreach ($myquests as $c_n => $c_s)
  {
    if ($c_s[0] == 1)
    {
      $quest = mysql_fetch_array(mysql_query("SELECT * FROM Quests WHERE id='$c_n'")); 
      if ($quest[type] == $quest_type_num['Bounty'])
      {
        $goals = unserialize($quest[goals]);
        $namecomp = $char2[name]."_".$char2[lastname];
        if (strtolower($goals[1]) == strtolower($namecomp))
        {
          $myquests[$c_n][1]++;
        }
      }
      else if ($quest[type] == $quest_type_num['Clan'])
      {
        $goals = unserialize($quest[goals]);
      
        if (strtolower($goals[1]) == strtolower($char2[society]))
        {
          $myquests[$c_n][1]++;
        }
      }
    }
  }
}
$myquests2 = serialize($myquests);

// CHECK STOMACH
for ($i = 0; $i < $fullness; $i++)
{
  $stomach[$i][istatus] = $stomach[$i][istatus]-$multiply;
  if ($stomach[$i][istatus] <= 0)
  {
    mysql_query("DELETE FROM Items WHERE id='".$stomach[$i][id]."'");
  }
  else 
  {
    mysql_query("UPDATE Items SET istatus='".$stomach[$i][istatus]."' WHERE id='".$stomach[$i][id]."'");
  }
}

for ($i = 0; $i < $fullness2; $i++)
{
  $stomach2[$i][istatus] = $stomach2[$i][istatus]-$multiply;
  if ($stomach2[$i][istatus] <= 0)
  {
    mysql_query("DELETE FROM Items WHERE id='".$stomach2[$i][id]."'");
  }
  else 
  {
    mysql_query("UPDATE Items SET istatus='".$stomach2[$i][istatus]."' WHERE id='".$stomach2[$i][id]."'");
  }
}
  
// Update Stats
$stats['wins'] += $score1*$multiply; 
$stats['battles'] += $multiply;
$stats2['wins'] += $score2*$multiply; 
$stats2['battles'] += $multiply;
$stats['duel_wins'] += $score1*$multiply;
$stats['tot_duels'] += $multiply;
$stats2['duel_wins'] += $score2*$multiply;
$stats2['tot_duels'] += $multiply;
if ($enemy)
{
  $stats['enemy_wins'] += $score1*$multiply;
  $stats['enemy_duels'] += $multiply;
  $stats2['enemy_wins'] += $score2*$multiply;
  $stats2['enemy_duels'] += $multiply;
}
if ($ally && (!$myTourney))
{
  $stats['ally_wins'] += $score1*$multiply;
}

$stats['off_wins'] += $score1*$multiply;
$stats['off_bats'] += $multiply;
$stats2['def_wins'] += $score2*$multiply;
$stats2['def_bats'] += $multiply;

$char[stamina]-=$stamina1*$multiply;
if ($char[stamina]<0) $char[stamina]=0;
$char2[stamina]-=$stamina2*$multiply;  
if ($char2[stamina]<0) $char2[stamina]=0;  
 
// calculate bonuses
$sealbonus=0;
if ($hasSeal1+$hasSeal2 == 1)
{ $sealbonus=1; }
$hornbonus=0;
if ($hasHorn1+$hasHorn2 == 1)
{ $hornbonus=1; }
$altpenalty = 1;
if ($alts[$username]) $altpenalty = 2;
$expup = 0;
$stance = $ally;
if ($enemy) $stance = 2;
if ($winner == $player_word[0])
{
  $expup +=round((20.0+$lvlbonus+$equipbonus+$sealbonus)/$altpenalty)*$multiply;
  $stats[ji]+= $expup/20 + $hornbonus;
  $stats2[ji]-= $expup/40;
}
else
{
  $expup +=round(((20+$lvlbonus+$equipbonus+$sealbonus)/2)/$altpenalty)*$multiply;
  $stats2[ji]+= $expup/20 + $hornbonus;
  $stats[ji]-= $expup/40;
}
$alignUp = $expup/20*($duelAlignment[getAlignment($char[align])][getAlignment($char2[align])+2]);
$char[align] += $alignUp;
if ($society[id]) 
{
  $society[align] += $alignUp;
  $result = mysql_query("UPDATE Soc SET align = align + ".$alignUp." WHERE id= '$society[id]' ");
}
$char[exp] += $expup;
$update=level_at($char[exp],$char[exp_up],$char[exp_up_s],$char[level],$char['equip_pts'],$char[vitality],$char['stamaxa'],$char[points],$char[propoints],$char[newskills],$char[newprof]);

$char[level]=$update[0];
$char[exp_up]=$update[1];
$char[exp_up_s]=$update[2];
$char[equip_pts]=$update[3];
$char[vitality]=$update[4];
$char[stamaxa]=$update[5];
$char[points]=$update[6];
$char[propoints]=$update[7];
$char[newskills]=$update[8];
$char[newprof]=$update[9];

// LOSE GOLD
$gold1 = $char[gold];
$gold2 = $char2[gold];
if (!$alts[$username])
{
  $g_steal2 = 0;
  $g_lvl = 1;
  if ($lvlbonus <-5) $g_lvl =(15+$lvlbonus)/10;
  
  for ($i=0; $i < $multiply; $i++)
  {
    if ($score2) 
    {
      $gold2 += intval(0.1*$gold1+0.5); 
      $gold1 = intval($gold1*0.9+0.5);
    }
    elseif ($score1) 
    {
      $gold1 += intval(((10+$g_steal)/100)*$gold2*$g_lvl+0.5); 
      $gold2 -= intval(((10+$g_steal)/100)*$gold2*$g_lvl+0.5);
    }
  }
  if ($gold1<0) $gold1=0;
  if ($gold2<0) $gold2=0;

  if (abs($char[gold]-$gold1)) 
  {
    $bresult[0][gold] = abs($char[gold]-$gold1);
  }

  if ($soc_name != "" && $char['society']!=$char2['society'] && !$alts[$username]) 
  {
    if ($enemy)
    {
      $bank1 = $society[bank];
      $society2 = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$char2[society]' "));
      for ($i=0; $i < $multiply; $i++)
      {
        if ($winner == $player_word[1]) { $society2[bank] += ceil($society[bank]*.001); $society[bank] = ceil($society[bank]*.999); $socwin = $society2[name]; }
        else { $society[bank] += ceil($society2[bank]*.001*$g_lvl); $society2[bank] -= ceil($society2[bank]*.001*$g_lvl); $socwin = $society[name]; }
      }
      if (abs($society[bank]-$bank1)) $bresult[0][cgold] = abs($society[bank]-$bank1);
      $bresult[0][cwin] = $socwin;
    
      $result = mysql_query("UPDATE Soc SET bank = '$society[bank]' WHERE id= '$society[id]' ");
      $result = mysql_query("UPDATE Soc SET bank = '$society2[bank]' WHERE id= '$society2[id]' ");    
    }
  }
  // Steal seal or horn
  $invsize=mysql_num_rows(mysql_query("SELECT id FROM Items WHERE owner='$char[id]' AND type<15"));
  if ($invsize < $inv_max) 
  if ($score1 && ($hasSeal2 || $hasHorn2) && !$hasSeal1 && !$hasHorn1 && $invsize < $inv_max)
  {
    $steal_chance = 10+$char2[level]-$char[level];
    if ($steal_chance > 20) $steal_chance = 20;
    else if ($steal_chance < 5) $steal_chance = 5;
    
    $randNum = rand(1,100);
    if ($steal_chance >= $randNum)
    {
      // Take it!!
      $theSeal = mysql_fetch_array(mysql_query("SELECT id, owner, type FROM Items WHERE type<=0 && owner='$char2[id]'"));
      $myTime = time();
      mysql_query("UPDATE Items SET owner = '".$char[id]."', last_moved='".$myTime."' WHERE id='".$theSeal[id]."' ");
      if ($theSeal[type] == 0)
      {
        $tlog .= "<br/>You stole a Prison Seal!!!"; 
      }
      else
      {
        $tlog .= "<br/>You stole the Horn of Valere!!!"; 
      }
    }
  }
}
else 
{
  $bresult[0][alt] = 1;
}

$stats['duel_earn'] += $gold1-$char[gold];
$stats2['duel_earn'] += $gold2-$char2[gold];

// Degrade Equipped Items
$bturns= array(0,0);
for ($t=1; $t < count($bresult); $t++)
{
  $turn = $bresult[$t][turn];
  $bturns[$turn] += 1;
}
$iresult=mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus>0");
while ($qitem = mysql_fetch_array($iresult))
{
  $ichange=0;
  // armor or shield
  if ($qitem[type] > 6 && $qitem[type] < 12 && $qitem[type] != 8)
  {
    $qitem[cond] -= 0.1*$bturns[1] + 0.1*(intval(rand(0,$bturns[1])));
    if ( $qitem[cond] <0) $qitem[cond] = 0;
    $ichange=1;
  }
  elseif ($qitem[type] < 7) // non-weave weapon
  {
    $qitem[cond] -= 0.1*$bturns[0] + 0.1*(intval(rand(0,$bturns[0])));
    if ( $qitem[cond] <0) $qitem[cond] = 0;
    $ichange=1;
  }
  if ($ichange)
  {
    mysql_query("UPDATE Items SET cond='".$qitem[cond]."' WHERE id='".$qitem[id]."'");
  } 
}

$iresult=mysql_query("SELECT * FROM Items WHERE owner='$char2[id]' AND istatus>0");
while ($qitem = mysql_fetch_array($iresult))
{
  $ichange=0;
  // armor or shield
  if ($qitem[type] > 6 && $qitem[type] < 12 && $qitem[type] != 8)
  {
    $qitem[cond] -= 0.1*$bturns[0] + 0.1*(intval(rand(0,$bturns[0])));
    if ( $qitem[cond] <0) $qitem[cond] = 0;
    $ichange=1;
  }
  elseif ($qitem[type] < 7) // non-weave weapon
  {
    $qitem[cond] -= 0.1*$bturns[1] + 0.1*(intval(rand(0,$bturns[1])));
    if ( $qitem[cond] <0) $qitem[cond] = 0;
    $ichange=1;
  }
  if ($ichange)
  {
    mysql_query("UPDATE Items SET cond='".$qitem[cond]."' WHERE id='".$qitem[id]."'");
  } 
}

// UPDATE TOURNEY

if ($myTourney && !$alts[$username])
{
  $tpts1 += round(($char_attack[health][0]-$char_attack[health][1])/5);
  $tpts2 -= round(($char_attack[health][0]-$char_attack[health][1])/5);
  
  $tresults[$char[id]][$char2[id]][1] += $tpts1;
  $tresults[$char[id]][$char2[id]][2] += $tpts2;
  
  $tscores = unserialize($myTourney[contestants]);
  $tscores[$char[id]][1] += $tpts1;
  $tscores[$char2[id]][1] += $tpts2;
  
  $treward = unserialize($myTourney[reward]);
  $treward[1] += 1;
  
  $str = serialize($tresults);
  $sts = serialize($tscores);
  $strw = serialize($treward);
  mysql_query("UPDATE Contests SET contestants='$sts', results='$str', reward='$strw' WHERE id='$myTourney[id]'");
  $tlog .= "<br/>Tournament Results! ".$name1.": ".show_sign1($tpts1).". ".$name2.": ".show_sign1($tpts2);
}

// UPDATE WARS
  if ($is_town)
  {
    $lnum=1;
    $updateLocs[0]=$char[location];
  }
  else
  {
    // GET SURROUNDING AREAS
    $updateLocs = $surrounding_area;
    $lnum=4;
  }
  
  $lastBattle = 0;
  $lastBattle = mysql_fetch_array(mysql_query("SELECT * FROM Contests WHERE type='99' "));
  if ($lastBattle == 0)
  {  
    for ($x=0; $x<$lnum; $x++)
    {
      $wpts1 = 0;
      $wpts2 = 0;
      $loc = $updateLocs[$x];
      $loc_query = mysql_fetch_array(mysql_query("SELECT * FROM `Locations` WHERE name = '$loc'"));
      if ($loc_query[last_war] && $ally== 0 && !$alts[$username])
      {
        $myWar = mysql_fetch_array(mysql_query("SELECT * FROM Contests WHERE id='$loc_query[last_war]' "));
        if (!$myWar[done] && !($myWar[starts] > intval(time()/3600)))
        {
          // Get clan id's that might be involved in war.
          $wid= $society[id];
          $wsupport=1;
          $support = unserialize($society['support']);

          if ($support[$loc_query[id]] != 0 && $support[$loc_query[id]] != $society[id])
          {
            $wid = $support[$loc_query[id]];
            $wsupport= 2;
          }
          $wid2=0;
          $wsupport2=1;
          if ($society2[id])
          {
            $wid2= $society2[id];
            $support2 = unserialize($society2['support']);
   
            if ($support2[$loc_query[id]] != 0)
            {
              $wid2 = $support2[$loc_query[id]];
              $wsupport2= 2;
            }      
          }
          $wresults = unserialize($myWar[results]);       
          $wcon = unserialize($myWar[contestants]);
          if ($wcon[$wid]) 
          {
            if (!$wresults[$wid]) $wresults[$wid] = array(0);
            
            $isWinner = 0;
            if ($winner == $player_word[0]) $isWinner = 1;
            $lvlDiff = ($slvl2-$slvl1);
            $str1 = getStrength($char[used_pts], $char[equip_pts], $char[stamina], $char[stamaxa]);
            $str2 = getStrength($char2[used_pts], $char2[equip_pts], $char2[stamina], $char2[stamaxa]);

            $wpts1 = getCBPoints($char, $myWar, $isWinner, $lvlDiff, $char_attack, $str1, $str2, 1);
                       
            // enemy bonuses
            if ($wcon[$wid2])
            {
              $wpts1+=2/$wsupport2*$wmult;
              //echo $wpts1.":";
    
              // enemy leader bonus
              if ($wsupport2 == 1)
              {
                if ($slead2)
                {
                  $wpts1+=2/$slead2*$wmult;
                  //echo $wpts1.":";
                }
              }
            }
                 
            // cut points in half if supporting and round to nearest point.
            $wpts1 = round($wpts1/$wsupport);
            //echo $wpts1.":"; 
            
            // Add points to contest
            $rnum=0;
            if ($wsupport==2) $rnum=2;
            $wresults[$wid][$rnum] += $wpts1;
            $wcon[$wid][1] = $wresults[$wid][0]+$wresults[$wid][1]+$wresults[$wid][2]+$wresults[$wid][3];
            if ($wresults[$wid][2] > $wresults[$wid][0]+$wresults[$wid][1]+$wresults[$wid][3]) { $wcon[$wid][1] = ($wresults[$wid][0]+$wresults[$wid][1]+$wresults[$wid][3])*2;}

            $wreward = unserialize($myWar[reward]);
            $wreward[1] += $warJi;
            $swr=serialize($wresults);
            $swc=serialize($wcon);
            $swp=serialize($wreward);
            mysql_query("UPDATE Contests SET contestants='$swc', results='$swr', reward='$swp' WHERE id='$myWar[id]'");
            $tlog .= "<br/>".$loc_query[name]." Clan Battle! ".$name1.": ".show_sign1($wpts1);
          }
        }
      }
    } 
  }
  else
  {
    if (!$lastBattle[done] && ($lastBattle[starts] <= intval(time()/3600)))
    {
      $wpts1 = 0;
      $wpts2 = 0;
      $wresults = unserialize($lastBattle[results]);       
      $wcon = unserialize($lastBattle[contestants]);
     
      // Determine who's on what side
      $palign[1] = $char[align];
      $palign[2] = $char2[align];
      for ($p = 1; $p <= 2; $p++)
      {
        $alignnum[$p] = getAlignment($palign[$p]);
        $side[$p]= 0;
        $nPenalty[$p]= 1;
        $rnum[$p] = 0;
        if ($alignnum[$p] > 0)
        { 
          $side[$p] = 1;
        }
        else if ($alignnum[$p] < 0)
        {
          $side[$p] = -1;
        }
        else
        {
          $nPenalty[$p] = 2;
          $rnum[$p] = 2;
          if ($palign[$p] > 0)
          {
            $side[$p] = 1;
          }
          else
          {
            $side[$p] = -1;
          }
        }
      }
      
      // if on the same side, no points
      if ($side[1] != $side[2])
      {  
        if (!$wresults[$wid]) $wresults[$wid] = array(0);
        $isWinner = 0;
        if ($winner == $player_word[0]) $isWinner = 1;
        $lvlDiff = ($slvl2-$slvl1);
        $str1 = getStrength($char[used_pts], $char[equip_pts], $char[stamina], $char[stamaxa]);
        $str2 = getStrength($char2[used_pts], $char2[equip_pts], $char2[stamina], $char2[stamaxa]);
        $wpts1 = getCBPoints($char, $myWar, $isWinner, $lvlDiff, $char_attack, $str1, $str2, 1);

        // Offense only: bonus for dueling more pure opponents
        if ($side[1] == 1)
        {
          if ($alignnum[2] == -2)
          {
            $wpts1 += 4;
          }
          else if ($alignnum[2] == -1)
          {
            $wpts1 += 2;
          }
        }
        else
        {
          if ($alignnum[2] == 2)
          {
            $wpts1 += 4;
          }
          else if ($alignnum[2] == 1)
          {
            $wpts1 += 2;
          }
        }

        // Netural penalty
        $wpts1 = round($wpts1/$nPenalty[1]);   

        // Add points to contest
        $wresults[$side[1]][$rnum[1]] += $wpts1;
        $wcon[$side[1]][1] += $wpts1;  
        $wresults[$side[2]][$rnum[2]] += $wpts2;
        $wcon[$side[2]][1] += $wpts2;

        $swr=serialize($wresults);
        $swc=serialize($wcon);
        mysql_query("UPDATE Contests SET contestants='$swc', results='$swr', reward='$swp' WHERE id='$lastBattle[id]'");
        $tlog .= "<br/>Last Battle Results! ".$name1.": ".show_sign1($wpts1);
      }
    }
  }

$bresult[0][tlog] = $tlog;

// UPDATE DEFENSE LOG
$aid = $char[id];
$did = $char2[id];
$htext = $bresult[0][dhpf]."/".$bresult[0][dhp]." health to ".$bresult[0][ahpf]."/".$bresult[0][ahp]." health.";
$logsub = "";

if ($bresult[0][score2] || $bresult[0][score1]) 
{
  $logsub = $bresult[0][winner]." Wins! ".$htext;
}
else 
{
  $logsub = "You Tied! ".$htext;
}
$lognote = generate_duel_message($bresult);
$sbresult = serialize($bresult);
$result = mysql_query("INSERT INTO Notes (from_id,to_id, del_from,del_to,type,root,sent,        cc,subject,  body,      special) 
                                  VALUES ('$aid', '$did','0',     '0',   '9', '0', '".time()."','','$logsub','".$lognote."','$sbresult')");

// UPDATE DATABASE
$result = mysql_query("UPDATE Users SET gold='".abs($gold1)."' ".$update_battles.", awesomeness='".$awesomeness."', newskills='".$char[newskills]."', newprof='".$char[newprof]."', stamina='".$char[stamina]."', stamaxa='".$char[stamaxa]."', exp='".$char[exp]."', level='".$char[level]."', exp_up='".$char[exp_up]."', exp_up_s='".$char[exp_up_s]."', points='".$char[points]."', propoints='".$char[propoints]."', vitality='".$char[vitality]."', equip_pts='".$char[equip_pts]."', align='".$char[align]."' WHERE id='$id' ");
$result = mysql_query("UPDATE Users_data SET quests='".$myquests2."', find_battle='".$find_battle."' WHERE id='$id' ");
$result = mysql_query("UPDATE Users_stats SET wins='".$stats['wins']."', ji='".$stats[ji]."', battles='".$stats['battles']."', duel_wins='".$stats['duel_wins']."', tot_duels='".$stats['tot_duels']."', ally_wins='".$stats['ally_wins']."', enemy_wins='".$stats['enemy_wins']."', enemy_duels='".$stats['enemy_duels']."', off_wins='".$stats['off_wins']."', off_bats='".$stats['off_bats']."', duel_earn='".$stats['duel_earn']."' WHERE id='$id'");
$result2 = mysql_query("UPDATE Users SET newlog=newlog+1, gold='".abs($gold2)."', stamina='".$char2[stamina]."', stamaxa='".$char2[stamaxa]."' WHERE id='$enemy_id' ");
$result2 = mysql_query("UPDATE Users_stats SET wins='".$stats2['wins']."', ji='".$stats2[ji]."', battles='".$stats2['battles']."', duel_wins='".$stats2['duel_wins']."', tot_duels='".$stats2['tot_duels']."', enemy_wins='".$stats2['enemy_wins']."', enemy_duels='".$stats2['enemy_duels']."', def_wins='".$stats2['def_wins']."', def_bats='".$stats2['def_bats']."', duel_earn='".$stats2['duel_earn']."' WHERE id='$enemy_id'");

// THE ACTUAL PAGE DRAWING
$message = "<b>".$char[name]." ".$char[lastname]."</b> vs <b>".$char2[name]." ".$char2[lastname]."</b> - ".($battlelimit-$char[battlestoday])." / $battlelimit battles remaining";
$names_battle = "<b>".$char[name]." ".$char[lastname]."</b> vs <b>".$char2[name]." ".$char2[lastname]."</b> - ";
$array_gen = generate_duel_text($bresult);
}
// JAVASCRIPT
?>
<SCRIPT LANGUAGE="JavaScript">
<!-- Begin

var myTurn = 0;
var myBattle = new Array();
<?php
echo $array_gen;

echo "var looping = 1;\n";
echo "var tlog = '".$tlog."';\n";

echo "var maxHealth0= ".$bresult[0][ahp].";\n";
echo "var maxHealth1= ".$bresult[0][dhp].";\n";

if ($redirect==0)
{
?>
  window.onLoad = startBattle();
<?php
}
?>
function startBattle() 
{
  window.self.setInterval("updateBattle()",<?php echo $battle_view;?>);
}

function redirectMessage(msg)
{
  document.getElementById('message').value=msg;
  alert(msg);
  document.redirectForm.submit();
}

function updateBattle() 
{
  if (myBattle.length > myTurn) 
  {
    if (myTurn > 0) document.getElementById("battleBox").innerHTML = document.getElementById("battleBox2").innerHTML.replace(/battletext/g, 'battledtext')+document.getElementById("battleBox").innerHTML;
    document.getElementById("battleBox2").innerHTML = myBattle[myTurn][0];
    document.getElementById("battleImg").innerHTML = "<img class='img-optional' src='"+myBattle[myTurn][1]+"'>";
    document.getElementById("battleP1H").innerHTML = "<img src='images/health.gif' style='vertical-align:middle'>: "+myBattle[myTurn][2]+"/"+maxHealth0;
    document.getElementById("battleP2H").innerHTML = "<img src='images/health.gif' style='vertical-align:middle'>: "+myBattle[myTurn][3]+"/"+maxHealth1;
  }
  else if (looping)
  {
    document.getElementById("battleBox2").innerHTML = document.getElementById("battleBox2").innerHTML+tlog;
    looping = 0;
  }
  myTurn++;
}
// End -->
</SCRIPT>
<?php
include('header.htm');
?>
<form name="redirectForm" action="world.php" method="post">
  <input type='hidden' name='message' id='message' value=''>
</form>
<?php
if ($redirect)
{
?>
<SCRIPT LANGUAGE="JavaScript">
<!-- Begin
redirectMessage("<?php echo $rmsg;?>");
// End -->
</SCRIPT>
<?php
}
?>
  <div class="row solid-back">
    <div class='col-sm-12'>
      <table border=0 cellpadding=0 cellspacing=0 width='100%'>
        <tr>
          <td class='hidden-xs' rowspan=3 width='64'><img width='64' src='images/BattleBox/Left2.jpg' class='img hidden-xs img-optional'/></td>
          <td class='visible-md img-optional' width='110' style='background-image:url(images/BattleBox/TopLeftmdspcr.jpg); background-repeat: repeat-x;'></td>
          <td class='visible-lg img-optional' width='210' style='background-image:url(images/BattleBox/TopLeftwidespcr.jpg); background-repeat: repeat-x;'></td>
          <td class='hidden-xs' align='center' height='38' width='592'>
            <img src='images/BattleBox/topmid2.jpg' class='img hidden-xs img-optional' width='592' />
          </td>
          <td class='visible-md img-optional' width='110' style='background-image:url(images/BattleBox/TopRtmdspcr.jpg); background-repeat: repeat-x;'></td>
          <td class='visible-lg img-optional' width='210' style='background-image:url(images/BattleBox/TopRtwidespcr.jpg); background-repeat: repeat-x;'></td>
          <td class='hidden-xs' rowspan=3 width='64'><img width='64' src='images/BattleBox/Right2.jpg' class='img hidden-xs img-optional'/></td>
          <td class='visible-xs' rowspan=3></td>
          <td class='visible-xs' style='border-style: solid;' colspan=3></td>
          <td class='visible-xs' rowspan=3></td>
        </tr>
        <tr>
          <td class='solid-back' style='vertical-align: text-top;' height='155' colspan=3>
            <div class='row'>
              <div class='col-sm-3 col-md-2'>
                <div id="battleP1H" style="font-family: Verdana; font-size: 10px;"></div>
              </div>
              <div class='col-sm-6 col-md-8 hidden-xs'>
                <br/>
              </div>
              <div class='col-sm-3 col-md-2'>
                <div id="battleP2H" style="font-family: Verdana; font-size: 10px;"></div>
              </div>        
            </div>
            <div class='row'>
              <div class='col-md-2 hidden-sm hidden-xs'>
                <img src='images/BattleBox/OP.gif' class='img img-responsive img-optional'/>
              </div>
              <div class='col-sm-9 col-md-8'>
                <div id='battleBox2' style="font-family: Verdana; font-size: 11px;"></div>
              </div>
              <div class='col-sm-3 col-md-2'>
                <div id="battleImg" class='hidden-xs'></div>
              </div>        
            </div>
        </tr>
        <tr>
          <td class='visible-md img-optional' width='110' style='background-image:url(images/BattleBox/BLeftmdspcr.jpg); background-repeat: repeat-x;'></td>
          <td class='visible-lg img-optional' width='210' style='background-image:url(images/BattleBox/BLeftwidespcr.jpg); background-repeat: repeat-x;'></td>   
        <?php
          if ($mode != 1)
          {
        ?>
          <td align='center' height='37' style='background-image:url(images/BattleBox/botspacerleft.jpg); background-repeat: repeat-x;'><img width='256' src='images/BattleBox/botleft.jpg' class='img hidden-xs'/><a href="npc.php?hunt=<?php echo $rehunt;?>&horde=<?php echo $horde;?>&army=<?php echo $army;?>"><img width='80' src="images/BattleBox/botmid1.jpg" border="0" alt="Again" onMouseover="this.src='images/BattleBox/botmid2.jpg'" onMouseout="this.src='images/BattleBox/botmid1.jpg'"/></a><img width='256' src='images/BattleBox/botright.jpg' class='img hidden-xs'/>
          </td>
        <?php
          }
          else
          {
        ?>          
          <td align='center' height='37'>
            <a href="npc.php?hunt=<?php echo $rehunt;?>&horde=<?php echo $horde;?>&army=<?php echo $army;?>">
              <img width='80' src="images/BattleBox/botmid1.jpg" border="0" alt="Again" onMouseover="this.src='images/BattleBox/botmid2.jpg'" onMouseout="this.src='images/BattleBox/botmid1.jpg'"/>
            </a>
          </td>
        <?php
          }
        ?>               

          <td class='visible-md img-optional' width='110' style='background-image:url(images/BattleBox/BRtmdspcr.jpg); background-repeat: repeat-x;'></td>
          <td class='visible-lg img-optional' width='210' style='background-image:url(images/BattleBox/BRtwidespcr.jpg); background-repeat: repeat-x;'></td>          
        </tr>
      </table>
      <div class='panel-body battlebox'>
        <div class='row'>
          <div class='col-xs-10 col-xs-push-1'>
            <div id='battleBox' style="font-family: Verdana; font-size: 9px; color: #000000; "></div>
          </div>
        </div>
        <div class='row'>
          <div class='col-sm-12'>
          </div>
        </div>
      </div>      
    </div>    
  </div>
<?php
$no_show_footer = 1;

include('footer.htm');
?>