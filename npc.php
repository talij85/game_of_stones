<?php
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/charFuncs.php");
include_once("admin/locFuncs.php");
include_once("admin/duelFuncs.php");
include_once('map/mapdata/coordinates.inc');
include_once("admin/displayFuncs.php");

// Link for the wiki page
$wikilink = "Battle_Tips";

// Initialize variables for redirection
$redirect=0;
$rmsg='';

// Check if char has battles to use.
if ($char[battlestoday] >= $battlelimit)  
{
  $redirect=1;
  $rmsg="You have reached your battle limit. Please wait for more turns.";
}
// Check if char has staminat to battles.
if ($char['stamina'] <= 0)  
{
  $redirect=1;
  $rmsg="You just do not have the energy to do that!";
}
// Check if in wilderness area.
if ($location_array[$char['location']][2]) 
{
  $redirect=1;
  $rmsg="You cannot do that from here!";
}

// Get Char data
$stats = mysql_fetch_array(mysql_query("SELECT * FROM Users_stats WHERE id='$char[id]'"));
$myquests= unserialize($char[quests]);
$qcomp = 0;

// determine wilderness grid cell
$row = mysql_real_escape_string($_GET[row]);
$col = mysql_real_escape_string($_GET[col]);
if ($row == "" || $col == "")
{
  $row = rand(0,4);
  $col = rand(0,4);
}

// Get char equipment
include('admin/equipped.php');

// Check if battling a horde
$horde = mysql_real_escape_string($_REQUEST['horde']);
$army = mysql_real_escape_string($_REQUEST['army']);
$myHorde=0;
if ($horde)
{
  $result = mysql_query("SELECT * FROM Hordes WHERE done='0' AND location='$char[location]'");
  while ($tHorde = mysql_fetch_array( $result ) )
  {
    if ($tHorde[id]== $horde)
    {
      $myHorde=$tHorde;
    }
  }
}
// Check if horde found
$extraTurns = 0;
if ($myHorde)
{
  $extraTurns = 2;
  if ($myHorde[type] == 3) $extraTurns = 1;
  if ($char[battlestoday]+$extraTurns >= $battlelimit)  
  {
    $redirect=1;
    $rmsg="You don't have enought turns to battle the horde. Please wait for more turns.";
  }
}

// Initialize war Ji.
$wpts1 = 0;
$warJi = 0;

// Check for quest.
$qeid_count = 0;
$qeid = '';
$qid = 0;
$found = 0;
$specific = "a";
$plural = 0;

// Check quests if hunting and not already on a find quest.
$hunt = mysql_real_escape_string($_REQUEST['hunt']);
$escortId = mysql_real_escape_string($_REQUEST['escort']);
$rehunt = 0;
$escorting = 0;
if ($hunt) $rehunt = 1;

if ($myquests)
{
  if ($escortId && $myquests[$escortId][0] == 1)
  {
    $quest = mysql_fetch_array(mysql_query("SELECT * FROM Quests WHERE id='$escortId'"));
    $goals = unserialize($quest[goals]);
    if ($quest[expire] > (time()/3600)|| $quest[expire] == -1)
    {
      $escorting = $escortId;
    }
  }
  foreach ($myquests as $c_n => $c_s)
  {
    if ($c_s[0] == 1)
    {
      $quest = mysql_fetch_array(mysql_query("SELECT * FROM Quests WHERE id='$c_n'")); 
      $goals = unserialize($quest[goals]);
      if (($quest[expire] > (time()/3600) || $quest[expire] == -1)&& strtolower($goals[2]) == strtolower($char[location]))
      {
        if ($quest[type] == $quest_type_num["NPC"] && $hunt)
        {
          if ($myquests[$c_n][1]< $goals[0])
          {
            $qeid[$qeid_count++] = $c_n;
          }
        }
        else if ($quest[type] == $quest_type_num["Find"])
        {
          if ($myquests[$c_n][1]< $goals[0] 
           && $myquests[$c_n][2] == $row 
           && $myquests[$c_n][3] == $col)
          {
            $qid = $c_n;
            break;
          }
        } 
        else if ($quest[type] == $quest_type_num["Horde"] && $hunt)
        {
          if ($myquests[$c_n][1] > 0)
          {
            $qeid[$qeid_count++] = $c_n;
          }
        }
      }
    }
  }
}
// if quest, override with correct NPC.
if ($qeid_count>0 && $qid == 0)
{
  $qid = $qeid[rand(0,$qeid_count-1)];
}
$teid = 0;
$qv = -1;
$doquest = 0;
$hbattle = 0;
if ($qid > 0)
{
  $doquest = mysql_fetch_array(mysql_query("SELECT * FROM Quests WHERE id='$qid'"));
  $dqgoals = unserialize($doquest[goals]);
  $teid = $dqgoals[1];
  if ($doquest[type] == $quest_type_num["Find"])
  {
    $char2['name'] = $quest[offerer]."'s";
    $specific = $char2['name'];
    $found = 1;
  }
  else if ($doquest[type] == $quest_type_num["Horde"])
  {
    $qv = $myquests[$qid][1];
    $char2['name'] = "The ".$horde_types[$enemy_list[$char['location']][$teid]]." of ";
    $specific = $char2['name'];
    $plural = 1;
    $hbattle = 1;
  }
}

// FIND BATTLE
$enum = intval(rand(0,100));

// if quest, override with correct NPC.
if ($qid > 0)
{
  if ($teid ==0) $enum = 0;
  elseif ($teid ==1) $enum = 35;
  elseif ($teid ==2) $enum = 60;
  elseif ($teid ==3) $enum = 75;  
  elseif ($teid ==4) $enum = 90;  
}

// Determine which NPC.
if ($enum < 35)
{
  $eid = 0;
  $elvl = intval(rand(-2,0));
}
elseif ($enum < 60)
{
  $eid = 1;
  $elvl = intval(rand(-1,1));
}
elseif ($enum < 75)
{
  $eid = 2;
  $elvl = intval(rand(-1,1));
}
elseif ($enum < 90)
{
  $eid = 3;
  $elvl = intval(rand(-1,1));
}
else
{
  $eid = 4;
  $elvl = intval(rand(0,2));
}
if ($found) $elvl += 2;
$elvl += $char[level];

// Check for estates with upgrades to raise level of NPCs.
$result= (mysql_query("SELECT id, upgrades FROM Estates WHERE row='$row' AND col='$col' AND location='$char[location]'"));
$npcAlign=0;
$bubble = 0;
while ($testate = mysql_fetch_array( $result ) )
{
  $teups = unserialize($testate[upgrades]);
  if ($teups[4]> $eid)
  {
    $elvl += 1;
  }
}
if (!$myHorde[id]) // non-horde battle
{
  $bubbleNum = rand(1,1000);
  if ((floor($char[level]/10)) >= $bubbleNum) $bubble = 1;
  if (!$bubble)
  {
    $ename = $enemy_list[$char['location']][$eid];

    $evit = $elvl*10 + 40;
    if ($evit < 25) $evit = 25;
    if ($qv > 0) $evit = $qv;
    if ($elvl <1) $elvl = 1;
  }
  else 
  {
    $doquest = 0;
    $ename = "Bubble of Evil";
    $evit = $char[vitality]+20;
    $elvl = $char[level]+2;
  }
}
else // horde battle
{
  $hbattle = 1;
  $npc_info = unserialize($myHorde[npcs]);
  $target=0;
  if ($army) $target=1;
  $ename = $npc_info[$target][0];
  $elvl = $char[level];
  $char2['name'] = "The ".$horde_types[$npc_info[$target][0]]." of ";
  $specific = $char2['name'];
  $plural = 1;
  $evit=$npc_info[$target][1];
  if ($evit <= 0)
  {
    $redirect=1;
    $rmsg="Your target has already been defeated!";
  }
  if ($target) $npcAlign = 2;
  else if ($myHorde[type] == 3) $npcAlign = -2;
  else $npcAlign = -1;
}

// prof bonueses
$pro_bonus = getProfBonuses ($wild_types[$char['location']], $proStats);

// nation bonuses
$nb = $nation_bonus[$char['nation']][0];
$nat_bonus = getNatBonuses($nb,$wt_bonuses[$wild_types[$char['location']]],$nt_bonuses[$npc_types[$ename]],$town_bonuses);

// Estate bonuses
$est_bonus = "";
$myEstate= mysql_fetch_array(mysql_query("SELECT * FROM Estates WHERE owner='$id' AND location='$char[location]'"));
$estJiBonus = 1;
if ($myEstate)
{
  $eups = unserialize($myEstate[upgrades]);
  $est_stats = cparse(getUpgradeBonuses($eups, $estate_ups, 9));
  $est_bonus .= "O".$est_stats[zD]." ";
  $est_bonus .= "N".$est_stats[zE]." ";
  $est_bonus .= "L".$est_stats[zL]." ";
  if ($est_stats[nJ]) $estJiBonus = (100+$est_stats[nJ])/100;
}

// Initializze Battle data.
if (!$char2['name']) $char2['name'] = "The";
$char2['lastname'] = $ename;
$char2['level'] = $elvl;
$char2['vitality'] = $evit;
$char2['skills'] = "";
$char2['active'] = "";
$char2['itmlist'] = "";
$char2['stomach'] = "";

$char_attack = array(
'level' => array($char[level],$char2[level]),
'max_health' => array($char[vitality],$char2[vitality]),
'health_limit' => array($char[vitality],$char2[vitality]),
'health' => array($char[vitality],$char2[vitality]),
'def_mult' => array(0,0),
'def' => array(0,0),
'speed' => array(20,20), // temp values. Replace based on class
'poison' => array(0,0),
'taint' => array(0,0),
'accuracy' => array(0,0),
'dodge' => array(0,0),
'move' => array(0,0),
);

// Combine all Battle stats.
$bonuses1 = $stomach_bonuses1." ".$stamina_effect1." ".$skill_bonuses1." ".$nat_bonus." ".$pro_bonus." ".$est_bonus;
$weapon_a = $bonuses1." ".$estats[0];

if (!$bubble) 
{
  $genlvl = $char2['level'];
  if ($hbattle) $genlvl = floor($genlvl*.75);
  $weapon_b = itp(generateNPC($char2['lastname'], $genlvl),0);
}
else
{
  $weapon_b = $weapon_a;
}
//echo itm_info(cparse($weapon_b));
$awesomeness=lvl_req(clbc($weapon_a),100);
$awesomeness_b=lvl_req(clbc($weapon_b),100);

// Do this AFTER weapons, so plural doesn't break stats.
if ($plural)
{
  $char2['lastname'] = $npc_plural[$ename];
}
if ($char[name] == $char2[name] || ucwords($char[name]) == "The" || ucwords($char2[name]) == "The" || $myHorde[id])
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

// Do the battle.
$bresult = doDuel($char_attack, $player_word, $weapon_a, $weapon_b,$hbattle);

$score1 = $bresult[0][score1];
$score2 = $bresult[0][score2];
$winner = $bresult[0][winner];

$bturns = count($bresult) - 1;

$char_attack[health][0] = $bresult[0][ahpf];
$char_attack[health][1] = $bresult[0][dhpf];

////////////////////////////////////////////////////////////////////////////////////////////////////////////

// No Cheating
if (!$_GET[data]) $battle_view = 600; else $battle_view = intval($_GET[data]+10);
$time_dif = time()-$char[nextbattle];
$update_battles='';
if ($time_dif >= 0 && $char[battlestoday] < $battlelimit) 
{
  $char[battlestoday]++;
  if ($myHorde[type]==1) $char[battlestoday]+= 2;
  if ($myHorde[type]==3) $char[battlestoday]+= 1;
  $update_battles="Users.nextbattle='".intval(time()+(($battle_view*0.001)*$bturns))."', Users.battlestoday='".$char[battlestoday]."'";
}
elseif ($time_dif < 0) 
{
$redirect=1;
$rmsg="You must wait until your last battle finished before starting a new one!";
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////

if (!$redirect)
{
// ATTACKER'S PASSIVE EFFECTS
$passive = $weapon_a;
$char_stats = cparse($passive,0);
$g_steal = intval($char_stats['pG']);
if ($g_steal>95) $g_steal=95;
//$x_gain = intval($char_stats['pX']);

// UPDATE SCORES FOR CLOSE LEVELS
  $soc_name = $char[society];
  $society = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$soc_name' "));
  $area_score = unserialize($society['area_score']);
  $username = $char2[name]."_".$char2[lastname];
  $score_up = number_format(1-.1*($char[level]-$elvl),2,'.','');
  if ($score1)
  {
    $surrounding_area = $map_data[$char[location]]; 
    $area_rep = unserialize($society[area_rep]);

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
      
      if ($myHorde[id])
      {
        if ($myHorde[target]!=$loc)
        {
          $score_up2 = $score_up*2.5/$atWar;
        }
        else 
        {
          $score_up2 = $score_up*4.5/$atWar;
          if ($army) { $area_rep[$loc_query[id]] -= 1; }
          else { $area_rep[$loc_query[id]] += 1;}
          if ($area_rep[$loc_query[id]] > 500) $area_rep[$loc_query[id]] = 500;
          else if ($area_rep[$loc_query[id]]< -500) $area_rep[$loc_query[id]] = -500;
        }
      }
      else
      {
        $score_up2 = $score_up/$atWar;
      }
      $arep = $area_rep[$loc_query[id]];
      $score_up3=(1+$arep/1000)*$score_up2;
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
              if ($atWar==2) $warJi=$score_up3;    
              $area_score_sup[$loc_query[id]] += $score_up3;
              $area_score_sup[$loc_query[id]] = number_format($area_score_sup[$loc_query[id]],2,'.','');
              $a_ss_str = serialize($area_score_sup);
              mysql_query("UPDATE Soc SET score=score+($score_up3), area_score='$a_ss_str' WHERE id='".$societySup[id]."' ");
              
              $greward = $score_up3 * $qreward[1];
              $stats[quest_earn] += $greward;
              $stats[support_quest_ji] += $score_up3;
              mysql_query("UPDATE Users SET gold=gold+($greward) WHERE id='".$char[id]."'");
              mysql_query("UPDATE Users_stats SET quest_earn='".$stats[quest_earn]."', support_quest_ji='".$stats[support_quest_ji]."' WHERE id='".$char[id]."'");

              $goals[0] += $score_up3;
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
        if ($atWar==2) $warJi=$score_up3;    
        $area_score[$loc_query[id]] += $score_up3;
        $area_score[$loc_query[id]] = number_format($area_score[$loc_query[id]],2,'.','');
        $a_s_str = serialize($area_score);
        $area_reps = serialize($area_rep);
        mysql_query("UPDATE Soc SET score=score+($score_up3), area_score='$a_s_str', area_rep='".$area_reps."' WHERE id='".$society[id]."' ");
      }
      $stats['loc_ji'.$loc_query[id]]+=$score_up3;
      $stats['loc_ji'.$loc_query[id]]=number_format($stats['loc_ji'.$loc_query[id]],2,'.','');
      $result = mysql_query("UPDATE Users_stats SET loc_ji".$loc_query[id]."='".$stats['loc_ji'.$loc_query[id]]."' WHERE id='$id'");
    }
  }
  
// Update Stamina
  $stamina1 = 0; 

  // -1 for a loss
  if (!$score1) {$stamina1++;}

  // -1 per 100 damage taken
  $stamina1 += floor(($char_attack[health_limit][0]-$char_attack[health][0])/100);

  // -1 if health == 0
  if ($char_attack[health][0] == 0) $stamina1++;

// CHECK QUESTS
if ($myquests)
{
  foreach ($myquests as $c_n => $c_s)
  {
    if ($c_s[0] == 1)
    {
      $quest = mysql_fetch_array(mysql_query("SELECT * FROM Quests WHERE id='$c_n'")); 
      if ($quest[expire] > (time()/3600)|| $quest[expire] == -1)
      {
        if ($quest[type] == $quest_type_num["NPC"])
        {
          $goals = unserialize($quest[goals]);
          if (strtolower($goals[2]) == strtolower($char[location]) && $goals[1] == $eid)
          {
            $myquests[$c_n][1] += $score1;
            if ($myquests[$c_n][1]== $goals[0]) $qcomp = 1;
          }
        }
        else if ($quest[type] == $quest_type_num["Find"])
        {
          if ($quest[id] == $doquest[id])
          {
            $myquests[$c_n][1] += $score1;
            if ($myquests[$c_n][1] >= $goals[0]) $qcomp = 1;
          }
        }
        else if ($quest[type] == $quest_type_num["Horde"])
        {
          if ($quest[id] == $doquest[id])
          {
            $myquests[$c_n][1] = $char_attack[health][1];
            if ($myquests[$c_n][1] <= 0) $qcomp = 1;
          }
        }
        else if ($quest[type] == $quest_type_num["Escort"])
        {
          if ($quest[id] == $escorting)
          {
            if ($score1) $myquests[$c_n][2] += 1;
            else $myquests[$c_n][2] -= 1;
          }
        }  
      }
    }
  }
}
$myquests2 = serialize($myquests);  

// CHECK STOMACH
for ($i = 0; $i < $fullness; $i++)
{
  $stomach[$i][istatus] = $stomach[$i][istatus]-1;
  if ($stomach[$i][istatus] <= 0)
  {
    mysql_query("DELETE FROM Items WHERE id='".$stomach[$i][id]."'");
  }
  else 
  {
    mysql_query("UPDATE Items SET istatus='".$stomach[$i][istatus]."' WHERE id='".$stomach[$i][id]."'");
  }
}
$hmult=1;
if ($myHorde[id]) 
{
  $hmult=2;
  if ($army) $stats['army_wins'] += $score1;
  else $stats['horde_wins'] += $score1;
}
$score1=$score1*$hmult;
$stats['wins'] += $score1; 
$stats['battles']+= $hmult;
$stats['npc_wins']+= $score1; 
$stats['tot_npcs']+= $hmult;
  
if ($npc_types[$ename]==1)
{
  $stats['shadow_npcs']+= $hmult;
  $stats['shadow_wins'] += $score1;
  if ($stats[most_npc_wins]< $stats['shadow_wins']) $stats[most_npc_wins] = $stats['shadow_wins'];
} 
elseif ($npc_types[$ename]==2)
{
  $stats['military_npcs']+= $hmult;
  $stats['military_wins'] += $score1;
  if ($stats[most_npc_wins]< $stats['military_wins']) $stats[most_npc_wins] = $stats['military_wins'];
} 
elseif ($npc_types[$ename]==3)
{
  $stats['ruffian_npcs']+= $hmult;
  $stats['ruffian_wins'] += $score1;
  if ($stats[most_npc_wins]< $stats['ruffian_wins']) $stats[most_npc_wins] = $stats['ruffian_wins'];
} 
elseif ($npc_types[$ename]==4)
{
  $stats['channeler_npcs']+= $hmult;
  $stats['channeler_wins'] += $score1;
  if ($stats[most_npc_wins]< $stats['channeler_wins']) $stats[most_npc_wins] = $stats['channeler_wins'];
} 
elseif ($npc_types[$ename]==5)
{
  $stats['animal_npcs']+= $hmult;
  $stats['animal_wins'] += $score1;
  if ($stats[most_npc_wins]< $stats['animal_wins']) $stats[most_npc_wins] = $stats['animal_wins'];
} 
elseif ($npc_types[$ename]==6)
{
  $stats['exotic_npcs']+= $hmult;
  $stats['exotic_wins'] += $score1;
  if ($stats[most_npc_wins]< $stats['exotic_wins']) $stats[most_npc_wins] = $stats['exotic_wins'];
} 
 
  $char[stamina]-=$stamina1;
  if ($char[stamina]<0) $char[stamina]=0;
    
  // calculate bonuses
  $strength = getStrength($char[used_pts], $char[equip_pts], $char[stamina], $char[stamaxa]);
  $equipbonus = floor((90-$strength)/15);
  $lvlbonus = $char2[level]- $char[level];
  if ($lvlbonus > 5) $lvlbonus =5;
  if ($lvlbonus < -15) $lvlbonus =-15;
  $query = "SELECT id, name, lastname FROM Users WHERE (name!='The' OR lastname!='Creator') ORDER BY level DESC, exp DESC LIMIT 0,10 ";
  $result = mysql_query($query);
  $altpenalty = 1;
  
  $expup = 0;
  if (!$myHorde)
  {
    if ($winner == $player_word[0]) 
    {  
      $expup +=round((15.0+$lvlbonus)/$altpenalty);
    }
    else 
    {
      $expup +=round(((15+$lvlbonus)/2)/$altpenalty);
    }
  }
  else
  {
    if ($winner == $player_word[0]) 
    {  
      $expup +=round(((17*($extraTurns+1))-1+$lvlbonus)/$altpenalty);
    }
    else 
    {
      $expup +=round((((17*($extraTurns+1))-1+$lvlbonus)/2)/$altpenalty);
    }
    
    $husers = unserialize($myHorde[users]);
    if (!$husers[$char[id]])
    {
      $husers[$char[id]] = array(0,0,0,0);
    }

    if ($score1) $husers[$char[id]][($target*2)+0] += 1;
    $husers[$char[id]][($target*2)+1] += ($npc_info[$target][1] - $char_attack[health][1]);
    $shu = serialize($husers);   
  }
  $hordeJiBonus=1;
  if ($myHorde[id])
  {
    $hordeJiBonus=3;
    $lastBattleMult = 1; 
    if ($myHorde[type] == 3)  $lastBattleMult = 2;
    $alignUp = $expup/20*($duelAlignment[getAlignment($char[align])][$npcAlign+2])*$lastBattleMult;
    $char[align] += $alignUp;
    if ($society[id]) 
    {
      $society[align] += $alignUp;
      $result = mysql_query("UPDATE Soc SET align = align + ".$alignUp." WHERE id= '$society[id]' ");
    }
  }
  $stats[ji] += ($expup*$estJiBonus*$hordeJiBonus)/40;
  $char[exp]+=$expup;
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

// Degrade Equipped Items
$bturns= array(0,0);
for ($t=1; $t < count($bresult); $t++)
{
  $turn = $bresult[$t][turn];
  $bturns[$turn] += 1;
}
$iresult=mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus>0 AND type<15");
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

// FIND ITEM
$des_message = '';
$pic_name = 'images/BattleBox/OP.gif';
$rand_val = rand(0,100)+($char_stats['L']*2);
if ($bubble) $rand_val = 100;

$finditem="";
$prefix="";
$suffix="";
$wear=100;
$invsize=mysql_num_rows(mysql_query("SELECT id FROM Items WHERE owner='$char[id]' AND type<15"));
if (($rand_val >= 50 && $winner == $player_word[0])) 
{
  // find item
  if ($invsize < $inv_max)  
  {
    // seal find requirements check
    $topchar = mysql_fetch_array(mysql_query("SELECT id, level FROM Users WHERE 1 ORDER BY level DESC, exp DESC LIMIT 1"));
    $findseal=0;
    $availSeals = getMaxSeals($topchar[level]);
    
    if ($availSeals > 0)
    {
      $numSeals=mysql_num_rows(mysql_query("SELECT id FROM Items WHERE type=0"));
      $sealChance = rand(1,20000);
      if ($numSeals == 0 && $char[level] >= 50) 
      {
        $findseal = 1; // First Seal
      }
      else if (($numSeals < $availSeals) && ($sealChance < $char[level])) // Seals 2-6
      {
        $mySeals = mysql_num_rows(mysql_query("SELECT id FROM Items WHERE type<=0 && owner='$char[id]'"));
        if ($mySeals == 0)
        {
          $findseal = 1;
        }
        else
        {
          //echo "You already got one!";
        }
      }
    }
    $findHorn = 0;
    $availHorn = getMaxHorn($topchar[level]);
    $numHorn=mysql_num_rows(mysql_query("SELECT id FROM Items WHERE type='-1'"));
    if ($findseal == 0 && $char[level] >= 20 && $availHorn > $numHorn)
    {
      $hornChance = rand(1,20000);
      $bestLevel = $topchar[level] - 10;
      $myChance = $char[level];
      if ($myChance > $bestLevel) $myChance = ($bestLevel*2)-$char[level];
      
      if ($hornChance < $myChance)
      {
        $findHorn = 1;
        echo "You found the Horn of Valere!";
      }
    }
    
    $find_array = array();
    $l1 = 0;
    if ($findseal)
    {
      $finditem = "prison seal";
      $ftype = $item_base[$finditem][1];
    }
    else if ($findHorn)
    {
      $finditem = "horn of valere";
      $ftype = $item_base[$finditem][1];
    }
    else if (rand(1,100) > 60 && !$bubble) {
      foreach ($item_base as $name => $data) { // check item level requirements
        if (lvl_req($data[0],100) <= $char['equip_pts']/2 && $data[1] < 12 && $data[1] != 8 && $data[1] != 0 && $data[1] != -1) $find_array[$l1++] = $name;
      }
      $finditem = $find_array[rand(0,count($find_array)-1)];
      $ftype = $item_base[$finditem][1];
    }
    else { // find ter'angreal
      $ftype = rand(12,13);
      $findex = rand(0,$ftype-10);
      $finditem = $item_list[$ftype][$findex];
    }
    $rand_tali = rand(0,3);
    if ($rand_tali < 1 && $ftype > 11) $rand_tali += rand(1,2);

    $max_lvl=intval($char[level]/4+3); 
    if ($bubble) $max_level += 2; 
    if ($rand_tali == 1 || $rand_tali == 3)
    {  
      $prefix = $tali_list[bell_rand($max_lvl,21)][rand(0,3)];
    }
    if ($rand_tali == 2 || $rand_tali == 3)
    {
      $suffix = $tali_list[bell_rand($max_lvl,21)][rand(4,7)];
    } 
    if ($ftype <= 0)
    {
      $prefix='';
      $suffix='';
    }
    
    // calc weapon degradation
    if ($ftype < 12 && $ftype != 0 && $ftype != 8)
    {
      $wear = bell_rand(100,100);
    }
    if ($pro_stats[eF]) $wear += $pro_stats[eF];
    if ($wear >100) $wear = 100;
    
    $bresult[0][iimg] = "items/".str_replace(" ","",$finditem).".gif";
    
    // add item and display message
    $findnum = count($itmlist);
    if ($finditem) 
    {
      $itype=$ftype;
      $istats= itp($item_base[$finditem][0]." ".$item_ix[$prefix]." ".$item_ix[$suffix],$ftype);
      $istats .= getTerMod($ter_bonuses,$itype,$prefix,$suffix,$item_base[$finditem][2]);
      $ipts= lvl_req($istats,100);
      $itime=time();
      $result = mysql_query("INSERT INTO Items (owner,type,    cond,   istatus,points, society,last_moved,base,       prefix,   suffix,   stats) 
                                        VALUES ('$id','$itype','$wear','0',    '$ipts','',     '$itime',  '$finditem','$prefix','$suffix','$istats')");
      $invsize++;

      $bname = str_replace(" ","_",$finditem);
      $pname = 0;
      $sname = 0;
      $popuptext = "itemstat.php?base=".$bname."&prefix=";
      $linktext = "";
      if ($prefix !="")
      {
        $linktext .= ucwords($prefix)." ";
        $pname = str_replace(" ","_",$prefix);
        $popuptext .= $pname;
      }
      $linktext .= ucwords($finditem);
      $popuptext .= "&suffix=";
      if ($suffix != "")
      {
        $linktext .= " ".str_replace("Of","of",ucwords($suffix));
        $sname = str_replace(" ","_",$suffix);
        $popuptext .= $sname;
        
      }
      $popuptext .= "&lvl=".$char[level]."&gender=".$char[sex]."&cond=".$wear;
      
      $bresult[0][item] = "You found: <a href=javascript:popUp('".$popuptext."')>$linktext</a>! (".$invsize."/".$inv_max.")";

      $stats[items_found]++;
    }
  }
  else $bresult[0][item] = "You were unable to pick up an item<br>because your inventory is full";
}
if ($qcomp) $bresult[0][quest] = 1;

$array_gen = generate_duel_text($bresult);

// Estate taxes
$charip = unserialize($char[ip]); 
$alts = getAlts($charip);
$result10= mysql_query("SELECT id, owner, upgrades, level, inv FROM Estates WHERE row='$row' AND col='$col' AND location='$char[location]'");
while ($testate = mysql_fetch_array( $result10 ) )
{
  $eowner = mysql_fetch_array(mysql_query("SELECT id, name, lastname, bankgold, society FROM Users WHERE id='$testate[owner]'"));
  $username = $eowner[name]."_".$eowner[lastname];
  $ally=0;
  if ($society[id])
  {
    $stance = unserialize($society['stance']);
    foreach ($stance as $c_n => $c_s)
    {
      if ($c_s ==1 && $eowner['society']==str_replace("_"," ",$c_n))
      {
        $ally = 1;
      }
    }
  }
  
  if (!($ally) && !($alts[$username]))
  {
    $teups = unserialize($testate[upgrades]);
    $test_stats = cparse(getUpgradeBonuses($teups, $estate_ups, 9));
    if ($test_stats[wT])
    {
      $eowner[bankgold] += ($test_stats[wT]*($testate[level]+1))*25;
      mysql_query("UPDATE Users SET bankgold='".$eowner[bankgold]."' WHERE id='".$eowner[id]."'");
    }
    $eid = (20000+$testate[id]);
    $ecount = mysql_num_rows(mysql_query("SELECT * FROM Items WHERE owner='".$eid."' AND type<15"));
    $chance=rand(1,100);

    if ($finditem && ($item_base[$base][1] > 0) && ($test_stats[eT]>=$chance) && ($ecount<($test_stats[zS]+3)))
    {
      $base= $finditem;
      $itype=$item_base[$base][1];
      $istats= itp($item_base[$finditem][0]." ".$item_ix[$prefix]." ".$item_ix[$suffix],$itype);
      $ipts= lvl_req($istats,100);
      $itime=time();

      $result = mysql_query("INSERT INTO Items (owner, type,    cond,   istatus,points, society,last_moved,base,   prefix,   suffix,   stats) 
                                        VALUES ('$eid','$itype','$wear','0',    '$ipts','',     '$itime',  '$base','$prefix','$suffix','$istats')");  
    }
  }
}

// UPDATE WARS
if (!$myHorde[id])
{
  $tlog = "";
  $lnum=4;
  $dwpts=0;

  $lastBattle = 0;
  $lastBattle = mysql_fetch_array(mysql_query("SELECT * FROM Contests WHERE type='99' "));
  if ($lastBattle == 0)
  {
    for ($x=0; $x<$lnum; $x++)
    {
      $loc = $surrounding_area[$x];
      $loc_query = mysql_fetch_array(mysql_query("SELECT * FROM Locations WHERE name = '$loc'"));
      if ($loc_query[last_war])
      {
        $wpts1 = 0;
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
        
          $wresults = unserialize($myWar[results]);       
          $wcon = unserialize($myWar[contestants]);
          if ($wcon[$wid]) 
          {
            if (!$wresults[$wid]) $wresults[$wid] = array(0);
            
            $isWinner = 0;
            if ($winner == $player_word[0]) $isWinner = 1;
            $lvlDiff = ($char2[level]-$char[level]);
            $str1 = getStrength($char[used_pts], $char[equip_pts], $char[stamina], $char[stamaxa]);
            $str2 = 90;
            $wpts1 = getCBPoints($char, $myWar, $isWinner, $lvlDiff, $char_attack, $str1, $str2, 0);
            
            // cut points in half if supporting and round to nearest point.
            $wpts1 = round($wpts1/$wsupport);           
            //echo $wpts1.":";
          
            // Add points to contest
            $rnum=1;
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
            $dwpts=$wpts1;
          }
          if ($dwpts) $tlog .= "<br>".$loc_query[name]." Clan Battle! ".$name1.": ".show_sign1($dwpts);
        }
      }
    }
  }
  else
  {
    if (!$lastBattle[done] && ($lastBattle[starts] <= intval(time()/3600)))
    {
      $wpts1 = 0;
      $wresults = unserialize($lastBattle[results]);       
      $wcon = unserialize($lastBattle[contestants]);       
  
      $isWinner = 0;
      if ($winner == $player_word[0]) $isWinner = 1;
      $lvlDiff = ($char2[level]-$char[level]);
      $str1 = getStrength($char[used_pts], $char[equip_pts], $char[stamina], $char[stamaxa]);
      $str2 = 90;
      $wpts1 = getCBPoints($char, $myWar, $isWinner, $lvlDiff, $char_attack, $str1, $str2, 0);
      
      // Determine which side you're on
      $alignnum = getAlignment($char[align]);
      $side= 0;
      $nPenalty= 1;
      if ($alignnum > 0)
      { 
        $side = 1;
      }
      else if ($alignnum < 0)
      {
        $side = -1;
      }
      else
      {
        $nPenalty = 2;
        if ($char[align] > 0)
        {
          $side = 1;
        }
        else
        {
          $side = -1;
        }
      }
      
      // Netural penalty
      $wpts1 = round($wpts1/$nPenalty);
            
      // Add points to contest
      $wresults[$side][$nPenalty] += $wpts1;
      $wcon[$side][1] += $wpts1;

      $swr=serialize($wresults);
      $swc=serialize($wcon);
      $swp=serialize($wreward);
      mysql_query("UPDATE Contests SET contestants='$swc', results='$swr' WHERE id='$lastBattle[id]'");
      $dwpts=$wpts1;      
    }
    if ($dwpts) $tlog .= "<br>Last Battle Results! ".$name1.": ".show_sign1($dwpts); 
  }
} 
  
// UPDATE DATABASE
$result = mysql_query("UPDATE Users LEFT JOIN Users_data ON Users.id=Users_data.id SET ".$update_battles.", Users.awesomeness='".$awesomeness."', Users.stamina='".$char[stamina]."', Users.stamaxa='".$char[stamaxa]."', Users.newskills='".$char[newskills]."', Users.newprof='".$char[newprof]."', Users.exp='".$char[exp]."', Users.level='".$char[level]."', Users.exp_up='".$char[exp_up]."', Users.exp_up_s='".$char[exp_up_s]."', Users.points='".$char[points]."', Users.propoints='".$char[propoints]."', Users_data.quests='".$myquests2."', Users.align='".$char[align]."', Users.vitality='".$char[vitality]."', Users.equip_pts='".$char[equip_pts]."' WHERE Users.id='$id' ");
$result = mysql_query("UPDATE Users_stats SET wins='".$stats['wins']."', ji='".$stats[ji]."', items_found='".$stats[items_found]."', battles='".$stats['battles']."', most_npc_wins='".$stats[most_npc_wins]."', npc_wins='".$stats['npc_wins']."', horde_wins='".$stats['horde_wins']."', army_wins='".$stats['army_wins']."', tot_npcs='".$stats['tot_npcs']."', shadow_npcs='".$stats['shadow_npcs']."', shadow_wins='".$stats['shadow_wins']."', military_npcs='".$stats['military_npcs']."', military_wins='".$stats['military_wins']."', ruffian_npcs='".$stats['ruffian_npcs']."', ruffian_wins='".$stats['ruffian_wins']."', channeler_npcs='".$stats['channeler_npcs']."', channeler_wins='".$stats['channeler_wins']."', animal_npcs='".$stats['animal_npcs']."', animal_wins='".$stats['animal_wins']."', exotic_npcs='".$stats['exotic_npcs']."', exotic_wins='".$stats['exotic_wins']."' WHERE id='$id'");

if ($myHorde)
{
  $npc_info[$target][1] = $char_attack[health][1];
  $hdone=0;
  $finisher=0;
  $afinisher=0;
  $defeated=0;
  $armydone=$myHorde[army_done];
  
  // Horde defeated!
  if ($npc_info[0][1] <= 0) 
  {
    $hdone=1;
    $finisher=$char[id];
    $defeated=time();
    
    $stats[horde_finish] += 1;
    mysql_query("UPDATE Users_stats SET horde_finish='".$stats[horde_finish]."' WHERE id='".$char[id]."'");
    
    // All clans gain 3% of their Ji in the city.
    $attacked = mysql_fetch_array(mysql_query("SELECT id, name, myOrder, chaos, ruler, army FROM Locations WHERE name='$myHorde[target]'"));
    $sresult = mysql_query("SELECT id, area_score, name FROM Soc WHERE 1");
    while ($lsoc = mysql_fetch_array($sresult))
    {
      $tas = unserialize($lsoc[area_score]);
      $tas[$attacked[id]] = $tas[$attacked[id]]*1.03;
      $sas= serialize($tas);
      mysql_query("UPDATE Soc SET area_score='$sas' WHERE id='$lsoc[id]'");      
    }
    
    // Apply damages to city defenses
    $armyDmg = $attacked[army]-$npc_info[1][1];
    $attacked[army] -= round($armyDmg/2);
    if ($attacked[army] < 1000) $attacked[army] = 1000;
    
    // Update Order when horde is defeated.
    mysql_query("UPDATE Locations SET myOrder=myOrder+100, army='$attacked[army]' WHERE id='$attacked[id]'");
  }
  else if ($target==1 && ($npc_info[$target][1] <= 0)) // army defeated
  {
    $npc_info[0][1] = $npc_info[0][1] *2;
    $armydone=1;
    $afinisher=$char[id];

    $stats[army_finish] += 1;
    mysql_query("UPDATE Users_stats SET army_finish='".$stats[army_finish]."' WHERE id='".$char[id]."'");
    
    // ruling clan's Ji decreased by 3%
    $attacked = mysql_fetch_array(mysql_query("SELECT id, name, myOrder, ruler FROM Locations WHERE name='$myHorde[target]'"));
    $ruler = mysql_fetch_array(mysql_query("SELECT id, area_score, name FROM Soc WHERE name='$attacked[ruler]'"));
    $tas = unserialize($ruler[area_score]);
    $tas[$attacked[id]] = $tas[$attacked[id]]*0.97;
    $sas= serialize($tas);
    mysql_query("UPDATE Soc SET area_score='$sas' WHERE id='$ruler[id]'"); 
    
    // Update Chaos when horde is defeated.
    mysql_query("UPDATE Locations SET chaos=chaos+50 WHERE id='$attacked[id]'"); 
  }
  $snpci= serialize($npc_info);
  $result = mysql_query("UPDATE Hordes SET npcs='$snpci', done='$hdone', users='$shu', finisher='$finisher', afinisher='$afinisher', defeated='$defeated', army_done='$armydone' WHERE id='$myHorde[id]'"); 
}

// THE ACTUAL PAGE DRAWING
$message = "<b>".$char[name]." ".$char[lastname]."</b> vs <b>".$specific." Level ".$char2[level]." ".$char2[lastname]."</b> - ".($battlelimit-$char[battlestoday])." / $battlelimit battles remaining";
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

echo "var maxHealth0= ".$char[vitality].";\n";
echo "var maxHealth1= ".$char2[vitality].";\n";

if ($redirect==0)
{
?>
  window.onLoad = startBattle();
<?php
}
?>
function startBattle() {
  window.self.setInterval("updateBattle()",<?php echo $battle_view;?>);
}

function redirectMessage(msg)
{
  document.getElementById('message').value=msg;
  alert(msg);
  document.redirectForm.submit();
}

function updateBattle() {
  if (myBattle.length > myTurn) {
    if (myTurn > 0) document.getElementById("battleBox").innerHTML = document.getElementById("battleBox2").innerHTML.replace(/battletext/g, 'battledtext')+document.getElementById("battleBox").innerHTML;
    document.getElementById("battleBox2").innerHTML = myBattle[myTurn][0];
    if (myBattle[myTurn][1] != "")
    {
      document.getElementById("battleImg").innerHTML = "<img class='img-optional' src='"+myBattle[myTurn][1]+"'>";
    }
    else
    {
      document.getElementById("battleImg").innerHTML = "<img class='img-optional' src='images/BattleBox/OP.gif' class='img img-responsive hidden-xs'/>";
    }

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
redirectMessage('<?php echo $rmsg;?>');
// End -->
</SCRIPT>
<?php
}
?>
  <div class="row solid-back">
    <div class='col-sm-12'>
      <table border=0 cellpadding=0 cellspacing=0 width='100%'>
        <tr>
          <td class='hidden-xs img-optional' rowspan=3 width='64'><img class='img-optional' width='64' src='images/BattleBox/Left2.jpg' class='img hidden-xs'/></td>
          <td class='visible-md img-optional' width='110' style='background-image:url(images/BattleBox/TopLeftmdspcr.jpg); background-repeat: repeat-x;'></td>
          <td class='visible-lg img-optional' width='210' style='background-image:url(images/BattleBox/TopLeftwidespcr.jpg); background-repeat: repeat-x;'></td>
          <td class='hidden-xs' align='center' height='38' width='592'>
            <img class='img-optional' src='images/BattleBox/topmid2.jpg' class='img hidden-xs' width='592' />
          </td>
          <td class='visible-md img-optional' width='110' style='background-image:url(images/BattleBox/TopRtmdspcr.jpg); background-repeat: repeat-x;'></td>
          <td class='visible-lg img-optional' width='210' style='background-image:url(images/BattleBox/TopRtwidespcr.jpg); background-repeat: repeat-x;'></td>
          <td class='hidden-xs' rowspan=3 width='64'><img class='img-optional' width='64' src='images/BattleBox/Right2.jpg' class='img hidden-xs'/></td>
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
                <img class='img-optional' src='images/BattleBox/OP.gif' class='img img-responsive'/>
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
          <td align='center' height='37' style='background-image:url(images/BattleBox/botspacerleft.jpg); background-repeat: repeat-x;'><img width='256' src='images/BattleBox/botleft.jpg' class='img hidden-xs'/><a href="npc.php?hunt=<?php echo $rehunt;?>&horde=<?php echo $horde;?>&army=<?php echo $army;?>"><img width='80' src="images/BattleBox/botmid1.jpg" border="0" alt="Again" onMouseover="this.src='images/BattleBox/botmid2.jpg'" onMouseout="this.src='images/BattleBox/botmid1.jpg'"/></a><img width='256' src='images/BattleBox/botright.jpg' class='img hidden-xs'/></td>
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