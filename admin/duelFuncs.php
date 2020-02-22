<?php
// duelFuncs

// Shadow -> Light
$duelAlignment = array (
'-2' => array (3,2,1,0,-1),
'-1'=> array (2,1,0,-1,-2),
'0'=> array (2,1,0,-1,-2),
'1'=> array (2,1,0,-1,-2), 
'2'=> array (1,0,-1,-2,-3),
);

// neutral, ally, enemy
$stanceMod = array (0,-1,1);

function bell_rand($mymax, $maxall)
{
  $weight = 0;
  if ($mymax > $maxall)
  {
    $weight= $mymax-$maxall;
    $mymax = $maxall;
  }
  $per = rand(1,100)+$weight;
  if ($per <=5) $out = intval(rand(1,10));
  elseif ($per <=15) $out = intval(rand(11,25));
  elseif ($per <=35) $out = intval(rand(26,40));
  elseif ($per <=65) $out = intval(rand(41,60));
  elseif ($per <=85) $out = intval(rand(61,75));
  elseif ($per <=95) $out = intval(rand(76,90));
  else $out = intval(rand(91,100));
  
  $retval= intval($out*$mymax/100+1);
  if ($retval > $maxall) $retval = $maxall;
  return $retval;
}

function new_bline($int, $string, $img="", $h1="", $h2="") 
{
  static $a = 0;
  $rval="";
  if (!$int) 
  {
    $a++;
    $rval .= "myBattle[".($a-1)."] = new Array(4);\n";
    $rval .= "myBattle[".($a-1)."][0] = \"$string\";\n";
    $rval .= "myBattle[".($a-1)."][1] = \"$img\";\n";
    $rval .= "myBattle[".($a-1)."][2] = \"$h1\";\n";
    $rval .= "myBattle[".($a-1)."][3] = \"$h2\";\n";
    return $rval;
  }
  else return $a;
}

function getStamEffects($stam)
{
  $effect = "A0 B0 ";
  if ($stam < .6) $effect = $effect."N-10 ";
  if ($stam < .4) $effect = $effect."O-10 ";
  if ($stam < .2) $effect = $effect."Y-1 V-1 ";
  if ($stam <= 0) $effect = $effect."X-1 ";

  return $effect;
}

$duel_text = array (
  '0' => array (" misses horribly!",
                " delivers an easily dodged attack!",
                " flails about wildly!"),
  '1' => array ("'s attack barely makes a scrape.",
                " nearly misses completely.",
                " comes forward with a predictable attack."),
  '2' => array (" connects with a glancing blow.",
                "'s blow has little force behind it.",
                "'s attack is partially deflected."),
  '3' => array (" attacks.",
                " deals a respectable blow.",
                " connects with a solid hit."),
  '4' => array (" surges forward.",
                " gets a lucky hit in.",
                " sends a well placed strike."),
  '5' => array (" takes advantage of an opening.",
                " launches into a surprising maneuver.",
                " unleashes the depths of their skill."),
  '6' => array (" delivers a staggering flurry of hits!",
                " avoids a clumsy block for a devastating attack!",
                " unleashes a furious assault!"),
);

$hlvl_mult = array(0,0.50,0.75,1.00,1.20,1.40,1.60);

function doDuel($char_attack, $player_word, $weapon_a, $weapon_b, $horde=0)
{
  global $hlvl_mult;

  // Player 2 Setup
  $char_stats = cparse($weapon_b,0);
  if ($char_stats['N']) $char_attack[def_mult][1] = $char_stats['N'];
  if ($char_stats['D']) $char_attack[def][1] = rand($char_stats['D'],$char_stats['E']);
  if ($char_stats['X'])
  {
    $char_attack[speed][1] += $char_stats['X'];
  }
  $char_attack[move][1] += $char_attack[speed][1];
  $char_attack[luck][1] = $char_stats['L'];
  if ($char_stats['F'])
  {
    $char_attack[move][1] += $char_stats['F'];
  }  
  if ($char_stats['Y']) $char_attack[accuracy][1] = $char_stats['Y'];
  if ($char_stats['V']) $char_attack[dodge][1] = $char_stats['V'];
  
  // Player 1 Setup
  $char_stats = cparse($weapon_a,0);
  if ($char_stats['N']) $char_attack[def_mult][0] = $char_stats['N'];
  if ($char_stats['D']) $char_attack[def][0] = rand($char_stats['D'],$char_stats['E']);
  if ($char_stats['X'])
  {
    $char_attack[speed][0] += $char_stats['X'];
  }
  $char_attack[move][0] += $char_attack[speed][0];
  $char_attack[luck][0] = $char_stats['L'];
  if ($char_stats['F'])
  {
    $char_attack[move][0] += $char_stats['F'];
  }  
  if ($char_stats['Y']) $char_attack[accuracy][0] = $char_stats['Y'];
  if ($char_stats['V']) $char_attack[dodge][0] = $char_stats['V'];
  
  // Determine First turn
  $z = 0;
  $array_gen = "";
  $turns_row=1;
  //echo "0: A=".$char_attack[move][0]." B=".$char_attack[move][1]." | ";
  if ($char_attack[move][0] >= $char_attack[move][1])
  {
    $turn = 0;
    $turn_n = 1;
  }
  else
  {
    $turn = 1;
    $turn_n = 0;
  }
  
  // Cut movement in half to nerf first strike, then add half one players speed to stager the movements.
  $char_attack[move][$turn] = floor($char_attack[move][$turn]/2);
  $char_attack[move][$turn_n] = floor($char_attack[move][$turn_n]/2);
  $char_attack[move][$turn_n] -= floor($char_attack[speed][$turn_n]/2);
  //echo " T:".$turn." | 1: A=".$char_attack[move][0]." B=".$char_attack[move][1];
 
////////////////////////////////////////////////////////////////////////////////////////////////////////////

  $bresult[0][ahp] = $char_attack[health][0];
  $bresult[0][dhp] = $char_attack[health][1];

  while ($char_attack[health][$turn] > 0 && $char_attack[health][$turn_n] > 0 && $z < 12)
  {
    $z++;
    
    if ($z>1)
    {
      $char_attack[move][$turn_n] += $char_attack[speed][$turn_n]-$char_attack[wound][$turn_n];

      if ($char_attack[move][$turn] <= $char_attack[move][$turn_n] || $turns_row >=3) 
      {
        if ($turn) $turn = 0;
        else $turn = 1;
        $turn_n = 1-$turn;
        $turns_row=1;
      }
      else $turns_row++;
      //echo " T=".$turn." | ".$z.": A=".$char_attack[move][0]." B=".$char_attack[move][1];

    }

    // CLEAR all temporary arrays and get info from weapons and attacks
    $damage = 0;
    $char_stats = array();
    if ($turn == 0) $weapon = $weapon_a;
    else $weapon = $weapon_b;

    // RESOLVE all common terms and place in array
    $char_stats = cparse($weapon,0);

    // MELEE DAMAGE
    $damage = rand($char_stats['A'],$char_stats['B']);
    $opponent_defense = $char_attack[def_mult][$turn_n] - round($char_attack[poison][$turn_n]/3);
    $damage_mult = ((($char_stats['O'] - round($char_attack[poison][$turn]/3)) - $opponent_defense)/100);
    $char_attack[def][$turn] = 0;
    if ($char_stats['D']) $char_attack[def][$turn] = rand($char_stats['D'],$char_stats['E']); else $char_attack[def][$turn] = 0;
 
    // Cut opponent's poison by 25%.    
    $char_attack[poison][$turn_n] = intval($char_attack[poison][$turn_n]*0.75);
 
    // COMBINE DAMAGES
    if ($damage < 0) $damage = 0;
    $hit_level= 3;
    $hit_type = intval(rand(0,100)+$char_attack[accuracy][$turn]-$char_attack[dodge][$turn_n]);
    
    if ($hit_type <= 0) { $hit_level= 0; }
    else if ($hit_type <= 10) { $hit_level= 1;}
    else if ($hit_type <= 25) { $hit_level= 2;}
    else if ($hit_type >= 100) { $hit_level= 6;}
    else if ($hit_type >= 90) { $hit_level= 5;}
    else if ($hit_type >= 75) { $hit_level= 4;}
    $damage *= $hlvl_mult[$hit_level]+$damage_mult;
    
    $damage = round($damage)- $char_attack[def][$turn_n];    
    if ($damage < 0) $damage =0;
    $char_attack[health][$turn_n] -= $damage;
    
    $bresult[$z][turn] = $turn;
    $bresult[$z][att] = $player_word[$turn];
    $bresult[$z][def] = $player_word[$turn_n];
    $bresult[$z][hlvl] = $hit_level;
    $bresult[$z][dam] = $damage;

    // STUN
    $stunned = 0;
    $bresult[$z][stun] = 0;
    if ($char_stats['S'] && rand(0,4))
    {
      $stunned = 1;
      $stun_num = rand(0,$char_stats['S']);
      
      if ($stun_num > $char_attack[speed][$turn_n]) $stun_num = $char_attack[speed][$turn_n];
      $char_attack[move][$turn_n] = $char_attack[move][$turn_n] - $stun_num;
      $bresult[$z][stun] = $stun_num;
    }
    
    // WOUND
    $bresult[$z][wound] = 0;
    if ($char_stats['W'])
    {
      $wound_num = rand(0,$char_stats['W']);
      
      if ($wound_num > $char_attack[speed][$turn_n]/2) $wound_num = floor($char_attack[speed][$turn_n]/2);
      
      if ($wound_num > 0) 
      {
        if ($wound_num > $char_attack[wound][$turn_n])
        {
          $char_attack[wound][$turn_n] = $wound_num;
          $bresult[$z][wound] = $wound_num; 
        }
        $stunned = 1;
        $char_attack[move][$turn_n] = $char_attack[move][$turn_n] - $wound_num;
        $bresult[$z][stun] += $wound_num;
      }
    }

    // POISON/TAINT HIT CHANCE
    $p_chance = round(60 + $char_attack[luck][$turn]/2 - $char_attack[luck][$turn_n]);
    $t_chance = $p_chance + floor($char_attack[level][$turn]/4) - 13;
    //echo $z."(T%: 60+".($char_attack[luck][$turn]/2)."-".($char_attack[luck][$turn_n])."+".floor($char_attack[level][$turn]/4)."-13=".$t_chance.") ";
    if ($p_chance > 75) $p_chance=75;
    if ($p_chance < 40) $p_chance=40;
    if ($t_chance > 85) $t_chance=85;
    if ($t_chance < 35) $t_chance=35;
    $p_chance_mult = 1;
    $p_chance_bonus = 0;
    if ($hit_level == 0) $p_chance_mult = 0.5; // if critical miss, cut chance of doing poison damage in half
    else if ($hit_level == 6) $p_chance += 15; // if critical hit, raise chance of doing poison damage by 15%

    // POISON
    if ($char_stats['P'] && (rand(1,100)<=($p_chance*$p_chance_mult)))
    { $char_attack[poison][$turn_n] += $char_stats['P']; }

    $poison_damage = round($char_attack[poison][$turn_n]);
    $char_attack[health][$turn_n] -= $poison_damage;
    $bresult[$z][poison]=0;
    if ($poison_damage) { $bresult[$z][poison] = $poison_damage; }
    
    // Cut your own poison by 25%
    $char_attack[poison][$turn] = intval($char_attack[poison][$turn]*0.75);
    
    // ADD TAINT TO OPPONENT
    $bresult[$z][taint] = 0;
    if ($char_stats['T'] && (rand(1,100)<=$t_chance)) 
    {
      $char_attack[taint][$turn_n] += $char_stats['T'];
      $bresult[$z][taint] = $char_stats['T'];
    }
    
    // PLAYER TAKES TAINT DAMAGE
    $bresult[$z][tdmg] = 0;
    if ($char_attack[taint][$turn]) 
    {
      $taint_damage = $char_attack[taint][$turn];
      $char_attack[health][$turn] -= $taint_damage;
      $char_attack[max_health][$turn] -= $taint_damage;
      if ($taint_damage) { $bresult[$z][tdmg] = $taint_damage; } 
    }

    // GAIN LIFE
    $health_gain = 0;
    $bresult[$z][hgain] = 0;
    if ($char_stats['G'])
    {
      $tomax = ($char_attack[max_health][$turn]-$char_attack[health][$turn]);
      $health_gain = round($tomax * ($char_stats['G']/100)); 
      if ($health_gain > $tomax) $health_gain = $tomax;  
    }
    if ($health_gain)
    {
      $char_attack[health][$turn] += $health_gain;
      $bresult[$z][hgain] = $health_gain;
    }
    $char_attack[max_health][$turn] = $char_attack[health][$turn];
        
    // HURT SELF
    if ($char_stats['H']) $health_take = intval($damage * ($char_stats['H']/100) + 0.5); else $health_take = 0;
    $char_attack[health][$turn] -= $health_take;
    $bresult[$z][sdmg] = 0;
    if ($health_take) { $bresult[$z][sdmg] = $health_take; }
  
    // DISPLAY RESULTS
    if ($turn) $wide = 30; else $wide = 70;
    if ($char_attack[health][$turn] < 0) $char_attack[health][$turn] = 0;
    if ($char_attack[health][$turn_n] < 0) $char_attack[health][$turn_n] = 0;
    if ($char_attack[health][$turn] <= 0.33*$char_attack[health_limit][$turn]) $h1 = 5; else $h1 = 4;
    if ($char_attack[health][$turn_n] <= 0.33*$char_attack[health_limit][$turn_n]) $h2 = 5; else $h2 = 4;
    $bresult[$z][ahp] = $char_attack[health][$turn];
    $bresult[$z][dhp] = $char_attack[health][$turn_n];
  }

  if (!$horde)
  {
    if ($char_attack[health][0] == $char_attack[health][1]) {$winner = ""; $score1 = 0; $score2 = 0;} // tie
    elseif ($char_attack[health][1] > $char_attack[health][0]) {$winner = $player_word[1]; $score1 = 0; $score2 = 1;} // player 2 wins
    else {$winner = $player_word[0]; $score1 = 1; $score2 = 0;} // player 1 wins
  }
  else
  {
    $hdiff0=$char_attack[health_limit][0]-$char_attack[health][0];
    $hdiff1=$char_attack[health_limit][1]-$char_attack[health][1];
    
    if ($char_attack[health][1] <= 0) {$winner = $player_word[0]; $score1 = 1; $score2 = 0;} // player 1 wins
    elseif ($char_attack[health][0] <= 0 || $hdiff1 < $hdiff0) {$winner = $player_word[1]; $score1 = 0; $score2 = 1;} // horde win
    elseif ($hdiff1 > $hdiff0) {$winner = $player_word[0]; $score1 = 1; $score2 = 0;} // player 1 wins
    else {$winner = ""; $score1 = 0; $score2 = 0;} // tie
  }
  
  $bresult[0][ahpf] = $char_attack[health][0];
  $bresult[0][dhpf] = $char_attack[health][1];
  $bresult[0][score1] = $score1;
  $bresult[0][score2] = $score2;
  $bresult[0][winner] = $winner;

////////////////////////////////////////////////////////////////////////////////////////////////////////////

  return $bresult;
} 

function generate_duel_message($bresult)
{
  global $hlvl_mult;
  
  if ($bresult[1][turn] == 0)
  {
    $att = $bresult[1][att];
    $def = $bresult[1][def];
  }
  else
  {
    $def = $bresult[1][att];
    $att = $bresult[1][def];
  }
  for ($t=1; $t < count($bresult); $t++)
  {
    $turn = $bresult[$t][turn];
    $totals[$turn][turns] += 1;
    $totals[$turn][dam] += $bresult[$t][dam];
    $totals[$turn][taint] += $bresult[$t][tdmg];
    $totals[$turn][poison] += $bresult[$t][poison];
    $totals[$turn][crit] += $hlvl_mult[$bresult[$t][hlvl]]*100;
  }
   
  $lognote = "<table border=0 cellpadding=0 cellspacing=0 width=450>";
  $lognote .= "<tr><td><b>Stat</b></td><td><b>".$att."</b></td><td><b>".$def."</b></td></tr>";
  $lognote .= "<tr><td>Turns</td><td>".$totals[0][turns]."</td><td>".$totals[1][turns]."</td></tr>";
  $lognote .= "<tr><td>Stamina %</td><td>".$bresult[0][stam1]."%</td><td>".$bresult[0][stam2]."%</td></tr>";
  $lognote .= "<tr><td>Damage</td><td>".$totals[0][dam]."</td><td>".$totals[1][dam]."</td></tr>";
  $lognote .= "<tr><td>Taint</td><td>".$totals[1][taint]."</td><td>".$totals[0][taint]."</td></tr>";
  $lognote .= "<tr><td>Poison</td><td>".$totals[0][poison]."</td><td>".$totals[1][poison]."</td></tr>";
  $lognote .= "<tr><td>Avg. Accuracy</td><td>";
  if ($totals[0][turns] >0) $lognote .= number_format($totals[0][crit]/$totals[0][turns])."%</td><td>";
  else $lognote .= "-</td><td>";
  if ($totals[1][turns] >0) $lognote .= number_format($totals[1][crit]/$totals[1][turns])."%</td></tr>";
  else $lognote .= "-</td></tr>";
  $lognote .= "</table><br/><br/>";
  
  $lognote .= $bresult[0][winner]." took ".displayGold($bresult[0][gold],1)."<br/>";
  $lognote .= $bresult[0][tlog];
  
  return $lognote;
}

function generate_duel_text($bresult)
{
  global $duel_text;
  
  $align= array("left","right");
  $attach = "<br/>";
  
  $array_gen = "";
  
  for ($t=1; $t< count($bresult); $t++)
  {
    $turn = $bresult[$t][turn];
    $bimage = "attack".$turn;
    if ($bresult[$t][hlvl]>2)  $bimage = "miss".$turn;
    $text = "<p align='".$align[$turn]."'><font class='battletext'>".$bresult[$t][att].$duel_text[$bresult[$t][hlvl]][rand(0,2)]."</font>";
    if ($bresult[$t][dam] > 0) $text .= "<font class='battletext'> ".$bresult[$t][def]." takes <b>".$bresult[$t][dam]."</b> damage</font>";
    if ($bresult[$t][stun])
    {
      if (rand(0,1)) $bimage="stun";
      $text .= $attach."<font class='battletext'>".$bresult[$t][def]." is stunned for ".$bresult[$t][stun]."</font>";
    }
    if ($bresult[$t][wound])
    {
      if (rand(0,1)) $bimage="wound";  
      if ($bresult[$t][stun]) $text .= "<font class='battletext'> and wounded for ".$bresult[$t][wound].".</font>"; 
      else $text .= $attach."<font class='battletext'>".$bresult[$t][def]." is wounded for ".$bresult[$t][wound].".</font>";
    }
    if ($bresult[$t][poison])
    {
      if (rand(0,1)) $bimage="poison";
      $text .= $attach."<font class='battletext'>Poison wracks ".$bresult[$t][def]."'s body for ".$bresult[$t][poison]." damage</font>";
    }
    if ($bresult[$t][taint])
    {
      if (rand(0,1)) $bimage="taint";
      $text .= $attach."<font class='battletext'>".$bresult[$t][def]."'s taint increases by ".$bresult[$t][taint]."</font>";
    }
    if ($bresult[$t][tdmg])
    {
      $text .= $attach."<font class='battletext'>".$bresult[$t][att]." takes ".$bresult[$t][tdmg]." taint damage</font>";
    }
    if ($bresult[$t][hgain])
    {
      if (rand(0,1)) $bimage="healthgain";
      $text .= $attach."<font class='battletext'>".$bresult[$t][att]." gains ".$bresult[$t][hgain]." health</font>";
    }
    if ($bresult[$t][sdmg])
    {
      if ($bresult[$t][hgain]) $text .= "<font class='battletext'>, but is injured and takes ".$bresult[$t][sdmg]." damage.</font>";
      else $text .= $attach."<font class='battletext'>".$bresult[$t][att]." is injured in the attack and takes ".$bresult[$t][sdmg]." damage</font>";
    }
    if ($bresult[$t][ahp] > $bresult[0][ahp]*0.33) $hcolor1='text-primary'; else $hcolor1='text-danger';
    if ($bresult[$t][dhp] > $bresult[0][ahp]*0.33) $hcolor2='text-primary'; else $hcolor2='text-danger'; 
    
    $text .= $attach."<font class='battletext'>".$bresult[$t][att]." has </font><font class='".$hcolor1." battletext'><b>".$bresult[$t][ahp]."</b></font>";
    $text .= "<font class='battletext'> health and ".$bresult[$t][def]." has </font><font class='".$hcolor2." battletext'><b>".$bresult[$t][dhp]."</b></font>";
    $text .= "<font class='battletext'> health</font><br/><br/>";
    $bimage ="images/".$bimage.".gif";
    
    if ($turn ==0) $text = new_bline(0,$text,$bimage,$bresult[$t][ahp],$bresult[$t][dhp]);    
    else $text = new_bline(0,$text,$bimage,$bresult[$t][dhp],$bresult[$t][ahp]);

    $array_gen .= $text;
  }
      
  $pic_name = 'images/BattleBox/OP.gif';
  $win_thing = "<center>";
  if ($bresult[0][winner] != "")
    $win_thing .= "<b>".$bresult[0][winner]." wins</b><br/>";
  else
    $win_thing .= "<b>The Duel is a Tie!</b><br/>";
  if ($bresult[0][gold])
  {
    $win_thing .= "<b>and takes ".displayGold($bresult[0][gold])."</b><br>";
    $pic_name = 'images/marks.gif';    
  }
  if ($bresult[0][cgold]) $win_thing .= "<b>".displayGold($bresult[0][cgold])." was taken for ".$bresult[0][cwin]."</b><br/><br/>";
  if ($bresult[0][alt])$win_thing .= "<b>but was compelled to take no gold for winning.</b><br/><br/>";

  if ($bresult[0][item])
  {
    $win_thing .= $bresult[0][item];
    $pic_name = $bresult[0][iimg];
  }  
  if ($bresult[0][quest]) $win_thing .= "<br><br><b>Quest Completed!</b>";
  
  $win_thing .= "</center>";
  
  $array_gen .= new_bline(0,$win_thing,$pic_name,$bresult[0][ahpf],$bresult[0][dhpf]);
  
  return $array_gen;
}

function generateNPC ($name, $lvl)
{
  $tier = floor($lvl/15);
  $sum=0;
  for ($i=1; $i<=$tier; $i++) { $sum+=$i; }

  $pts = $sum*$tier*15 + ($lvl-($tier*15))*($tier+1)+2;
  $temp_pts = ($pts/3);
  $x=0;
  $done=0;
  $s1=0;
  $s2=0;
  $s3=0;
  while (!$done)
  {
    $y=(floor($x/2)+1);
    if (($y) <= $temp_pts) { $temp_pts= $temp_pts-$y; $x++;}
    else $done=1;
  }

  $s1=$x;
  $s2=$x;
  $s3=$x;
  $y=floor($x/2)+1;
  
  $temp_pts= $temp_pts*3;
  if ($temp_pts>=$y)
  {
    $s1++;
    $temp_pts-=$y;
    if ($temp_pts>=$y)
    {
      $s2++;
      $temp_pts-=$y;
    }
  }
  
 

  switch ($name)
  {
    // Shadow
    case 'Trolloc':
      $stats = "O".(2*$s1)." T".$s2." O".(2*$s3);
      $att=70;
      break;

    case 'Myrddraal':
      $stats = "S".$s1." V".$s2." T".$s3;
      $att = 60;
      break;

    case 'Darkhound':
      $stats= "P".$s1." T".$s2." N".(2*$s3);
      $att = 30;
      break;

    case 'Draghkar':
      $stats= "S".$s1." T".$s2." W".ceil($s3/3);
      $att = 50;
      break;
      
    case 'Grayman':
      $stats = "V".$s1." P".$s2." T".$s3;
      $att = 40;
      break;

    case 'Gholam':
      $stats = "V".$s1." W".ceil($s2/3)." V".$s3;
      $att = 60;
      break;

    case 'Jumara':
      $stats = "G".$s1." T".$s2." A".$s3." B".(2+$s3);
      $att = 50;
      break;
     
    // Military
    case 'Soldier':
      $stats = "O".(2*$s1)." N".(2*$s2)." Y".($s3);
      $att = 60;
      break;

    case 'Merchant Guard':
      $stats = "N".(2*$s1)." G".$s2." D".$s3." E".(2*$s3);
      $att = 30;
      break;

    case 'Whitecloak':
      $stats = "N".(2*$s1)." O".(2*$s2)." N".(2*$s3);
      $att = 50;
      break;

    case 'Seanchan':
      $stats = "O".(2*$s1)." N".(2*$s2)." O".(2*$s3);
      $att = 60;
      break;

    case 'Aiel':
      $stats = "V".$s1." Y".$s2." F".$s3;   
      $att = 60;
      break;

    case 'Warder':
      $stats = "Y".$s1." V".$s2." Y".$s3;
      $att = 60;
      break;

    case 'Deathwatch Guard':
      $stats = "A".$s1." B".(2*$s1)." N".(2*$s2)." S".$s3;
      $att = 70;
      break;

    // Ruffian
    case 'Bandit':
      $stats = "V".$s1." N".(2*$s2)." P".$s3;
      $att = 60;
      break;

    case 'Dragonsworn':
      $stats = "F".$s1." N".(2*$s2)." O".(2*$s3);
      $att = 75;
      break;

    case 'Darkfriend':
      $stats = "V".$s1." O".(2*$s2)." N".(2*$s3);
      $att = 60;
      break;

    case 'Brigand':
      $stats = "S".$s1." O".($s2*2)." Y".$s3;
      $att = 60;
      break;

    case 'Mercenary':
      $stats = "S".$s1." O".($s2*2)." S".$s3;
      $att = 60;
      break;

    case 'Drunken Soldier':
      $stats = "O".(2*$s1)." O".(3*$s2)." H".$s2." A".$s3." B".(2*$s3);
      $att = 80;
      break;

    case 'Wolfbrother':
      $stats = "Y".$s1." H".$s2." O".(3*$s2)." Y".$s3;
      $att = 70;
      break;

    // Channeler
    case 'Wilder':
      $stats = "F".$s1." N".(2*$s2)." F".$s3;
      $att = 60;
      break;

    case 'Mad Male Channeler':
      $stats = "T".($s1)." H".$s2." O".(3*$s2). " T".$s3;
      $att = 80;
      break;
      
    case 'Damane':
      $stats = "F".$s1." O".(2*$s2)." Y".$s3;
      $att = 65;
      break;

    case 'Asha&#39;man':
      $stats = "Y".($s1)." O".(2*$s2). " W".ceil($s3/3);
      $att = 65;
      break;
      
    case 'Black Ajah':
      $stats = "N".(2*$s1)." T".$s2." X".ceil($s3/3);
      $att = 40;
      break;

    case 'Aes Sedai':
      $stats = "N".(2*$s1)." V".$s2." X".ceil($s3/3);
      $att = 40;
      break;

    case 'Dreadlord':
      $stats = "T".($s1)." O".(2*$s2). " W".ceil($s3/3);
      $att = 65;
      break;

    // Animal
    case 'Guard Dog':
      $stats = "F".($s1)." V".($s2*2)." N".(2*$s3);
      $att = 50;
      break;

    case 'Wolf':
      $stats = "N".($s1*2). " Y".($s2)." F".($s3);
      $att = 65;
      break;

    case 'Lion':
      $stats = "F".($s1)." O".($s2*2). " V".($s3);
      $att = 60;
      break;

    case 'Capar':
      $stats = "V".($s1)." N".($s2*2). " A".($s3)." B".(2*$s3);
      $att = 50;
      break;

    case 'Bear':
      $stats = "O".(2*$s1)." Y".($s2). " D".($s3)." E".(2*$s3);
      $att = 70;
      break;

    case 'Gara':
      $stats = "P".($s1)." S".($s2). " O".(2*$s3);
      $att = 60;
      break;

    case 'Blacklance':
      $stats = "P".($s1)." X".ceil($s2/3). " P".($s3);
      $att = 50;
      break;
      
    // Exotic
    case 'Grolm':
      $stats = "G".($s1)." O".(2*$s2). " O".(2*$s3);
      $att = 65;
      break;

    case 'Torm':
      $stats = "V".($s1)." N".(2*$s2). " V".$s3;
      $att = 45;
      break;

    case 'Corlm':
      $stats = "Y".($s1)." O".(2*$s2). " Y".$s3;
      $att = 55;
      break;
      
    case 'Raken':
      $stats = "V".($s1)." X".ceil($s2/3). " V".($s3);
      $att = 65;
      break;

    case 'Lopar':
      $stats = "O".(2*$s1)." N".(2*$s2). " G".($s3);
      $att = 60;
      break;

    case 'To&#39;raken':
      $stats = "Y".($s1)." O".(2*$s2)." X".ceil($s3/3);
      $att = 65;
      break;

    case 'S&#39;redit':
      $stats = "S".($s1)." N".($s2*2). " S".($s3);
      $att = 60;
      break;

    default:
      $stats = "";
      $att = 60;
      $name = "GLITCH";
      break;
  }

  $points = ceil(($lvl*15+85)*.85);
  $base= floor($points/3);

  $ab=round($base*$att/100);
  $de=$base-$ab;
  $offset=ceil($lvl/10);
  $a=floor($ab/2)-$offset;
  $b=ceil($ab/2)+$offset;
  $d=floor($de/2)-$offset;
  $e=ceil($de/2)+$offset;
  
  $final = "A".$a." B".$b." D".$d." E".$e." ".$stats;
  
  return $final;
}  

function getProfBonuses ($wildType, $proStats)
{
  $pro_bonus = "";
  if ($wildType == 1 && $pro_stats[oL])
    $pro_bonus.= "L".$pro_stats[oL];
  if ($wildType == 2 && $pro_stats[fL])
    $pro_bonus.= "L".$pro_stats[fL];
  if ($wildType == 3 && $pro_stats[mL])
    $pro_bonus.= "L".$pro_stats[mL];
  if ($wildType == 4 && $pro_stats[wL])
    $pro_bonus.= "L".$pro_stats[wL];
  if ($wildType == 5 && $pro_stats[hL])
    $pro_bonus.= "L".$pro_stats[hL];
  if ($wildType == 6 && $pro_stats[pL])
    $pro_bonus.= "L".$pro_stats[pL];  
  
  return $pro_bonus;
}

function getCBPoints($char, $myWar, $isWinner, $lvlDiff, $char_attack, $str1, $str2, $isDuel)
{
  $wpts1 = 0;
  
  // location bonus
  $locmult = 1;
  if ($char[location]== $myWar[location])
  {
    $locmult=2;
  }
  
  $duelBonus = 0;
  if ($isDuel) $duelBonus = 4;
            
  // win/lose bonus
  $wmult = -1;
  if ($isWinner) 
  {
    $wmult = 1;
  }
  $wpts1 += ($duelBonus + (2 * $locmult)) * $wmult; 
  //echo "Base=".$wpts1.":"; 
            
  // level bonus
  $ldiff = ($lvlDiff)/2;
  if ($ldiff > 5) $ldiff = 5;
  else if ($ldiff < -5) $ldiff = -5;
  $wpts1 += $ldiff;
  //echo "PostLvl=".$wpts1.":";
            
  // health bonus 
  $hdiff = (($char_attack[health][0]/$char_attack[health_limit][0])-($char_attack[health][1]/$char_attack[health_limit][1]))*5;
  $wpts1 += $hdiff;
//  echo ($char_attack[health][0]/$char_attack[health_limit][0])."-".($char_attack[health][1]/$char_attack[health_limit][1]).":";
  //echo "PostHealth=".$wpts1.":";
  
  // strength bonus
  $wpts1 += ($str2-$str1)/20; 
  //echo "PostStr=".$wpts1.":";
  
  return $wpts1;
}

?>