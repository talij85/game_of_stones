<?php

// DISPLAY FUNCTIONS
$build_pop = array(0,10,25,45,70,100,200);

// TOURNEY STUFF
$tourneyType= array('None','Normal','No Equipment','No Skills','No Consumables');
$tourneyDistro= array('None','Winner Take All','Top 3');

$estate_unq_prod= array("","fish","lumber","stone","luxury","livestock","grain");

// Track specific indices to use for checks
$quest_type_num = array(
'Items' => "0",
'NPC' => "1",
'Find' => "2",
'Horde' => "3",
'Escort' => "4",
'Bounty' => "10",
'Request' => "11",
'Clan' => "20",
'Support' => "21",
'Tutorial' => "30",
);

$rank_data = array(
// Heroes
array( "xp","Experience","Hero of the Horn", ),
array( "ji", "Ji","Hero of Honor",),
array( "achieved","Achievements","Hero of Legends", ),
array( "align_high","Positive Alignment","Warrior of the Light"),
array( "align_low","Negative Alignment","Agent of the Shadow"),
array( "win_tourney","Tournament Wins","Tournament Champion"),
// Wins
array( "wins", "Wins","Great Captain",),
array( "duel_wins", "Duel Wins","Master Duelist", ),
array( "enemy_wins","Wins vs. Enemies","Master Soldier", ),
array( "ally_wins", "Wins vs. Allies","Master Traitor",),
array( "off_wins","Offensive Wins","Master Aggressor", ),
array( "npc_wins","Wins vs. NPCs","Master Explorer", ),
array( "horde_wins","Wins vs. Hordes","Horde Breaker", ),
array( "army_wins","Wins vs. Armies","Army Breaker", ),

// NPCs
array( "shadow_wins","Wins vs. Shadow NPCs","Slayer of the Shadow", ),
array( "military_wins","Wins vs. Military NPCs","Slayer of Soldiers", ),
array( "ruffian_wins","Wins vs. Ruffian NPCs","Slayer of Ruffians", ),
array( "channeler_wins","Wins vs. Channeler NPCs","Slayer of Channelers", ),
array( "animal_wins","Wins vs. Animal NPCs","Slayer of Animals", ),
array( "exotic_wins","Wins vs. Exotic NPCs","Slayer of Exotic Animals", ),
// Quests
array( "quests_done","Total Quests Completed","Legendary Adventurer", ),
array( "npc_quests_done","NPC Quests Completed","Legendary Protector", ),
array( "find_quests_done","Find Quests Completed","Legendary Tracker", ),
array( "horde_quests_done","Horde Quests Completed","Legendary Slayer", ),
array( "escort_quests_done","Escort Quests Completed","Legendary Guardian", ),
array( "item_quests_done","Item Quests Completed","Legendary Trader", ),
array( "play_quests_done","Player Quests Completed","Legendary Samaritan", ),
array( "my_quests_done","Own Quests Completed","Legendary Recruiter", ),
// Coin
array( "coin","Coin on Hand","Heaviest Purse", ),
array( "bankcoin","Coin in Bank","Most Thrifty", ),
array( "coin_donated","Coin Donated","Biggest Donor", ),
array( "net_worth","Net Worth","Most Prosperous",),
array( "tot_business","Total Business Value","Master Entrepreneur"),
array( "tot_estate","Total Estate Value","Skilled Governor"),
// Earnings
array( "duel_earn","Duel Earnings","Top Mercenary", ),
array( "dice_earn","Gambling Earnings","High Roller", ),
array( "item_earn","Merchant Earnings","Master Merchant", ),
array( "quest_earn","Quest Earnings","Professional Adventurer", ),
array( "prof_earn","Profession Earnings","Most Professional", ),
); 

$soc_rank_data = array(
// Clan
array( "score","Ji","Clan of Honor", ),
array( "members", "Members","Clan of the Masses",),
array( "ruled","Ruled Cities","Hero of Power", ),
array( "align","Positive Alignment","Defenders of the Light",),
array( "-align","Negative Alignment","Servents of the Great Lord",),
array( "bank","Coin","Clan of Wealth", ),
);

$soc_rank_map = array(
"score" => "mostJi",
"members" => "mostMembers",
"bank" => "mostCoin",
"ruled" => "mostRuled",
"align" => "highAlign",
"-align" => "lowAlign",
);

$city_rank_data = array(
// City
array( "myOrder","Highest Order","Bastion of the Light", ),
array( "-myOrder","Lowest Order","Cauldron of Chaos", ),
array( "pop", "Highest Population","Booming Metropolis",),
array( "-pop", "Lowest Population","Ghost Town",),
);

function getUpgradeBonuses($upgrades, $bonuses, $num)
{
  $upbonus = "";
  for ($i=0; $i<$num; ++$i)
  {
    $upbonus .= getUpgradeBonus($upgrades, $bonuses, $i);
  }  
  
  return $upbonus;
}

function getUpgradeBonus($upgrades, $bonuses, $i, $up=0)
{
  $upbonus = "";
  if ($upgrades[$i]+$up) $upbonus.= $bonuses[$i][1].($bonuses[$i][2]*($upgrades[$i]+$up))." "; 
 
  return $upbonus;
}

// ITEM SORTER
function sortItems($sortBy)
{
  $sort_str = "";
  // time
  if ($sortBy == 0 || $sortBy == 8) $sort_str= "ORDER BY last_moved";
// type
  elseif ($sortBy==1) $sort_str= "ORDER BY type, points, last_moved";
// points
  elseif ($sortBy==2) $sort_str= "ORDER BY points, last_moved";
// istatus
  elseif ($sortBy==3) $sort_str= "ORDER BY istatus DESC, society ASC, points ASC, last_moved ASC";
// owner (vaults only)
  elseif ($sortBy==4) $sort_str= "ORDER BY owner, last_moved";
// worth
  elseif ($sortBy==5) $sort_str= "ORDER BY points, last_moved";
// name
  elseif ($sortBy==6) $sort_str= "ORDER BY prefix, base, suffix, last_moved"; 
// cond
  elseif ($sortBy==7) $sort_str= "ORDER BY cond, points, last_moved"; 

  return $sort_str;
}

function displayGold ($money, $text=0)
{
  $neg = 0;
  $rval="";
  if ($money < 0)
  {
    $neg = 1;
    $rval = "<font class='text-danger'>";    
    $money = abs($money);
  }
  $crown = floor($money/10000);
  $mark = floor(($money-$crown*10000)/100);
  $penny = floor (($money-$crown*10000-$mark*100));
  if ($text==0)
    $rval .= "<img src='images/gold.gif' width='15' title='Gold Crowns' style='vertical-align:middle' alt='g:'/>".$crown."<img src='images/silver.gif' title='Silver Marks' width='15' style='vertical-align:middle' alt='s:'/>".$mark."<img src='images/copper.gif' title='Copper Pennies' width='15' style='vertical-align:middle' alt='c:'/>".$penny;
  else
  {
    if ($crown != 1)$c = "crowns"; else $c = "crown";
    if ($mark != 1)$m = "marks"; else $m = "mark";
    if ($penny != 1)$p = "pennies"; else $p = "penny";
    $rval .= $crown." gold ".$c.", ".$mark." silver ".$m.", and ".$penny." copper ".$p;
  }
  if ($money < 0) $rval .= "</font>";
  
  return $rval;
}

function displayTime ($minpast, $ago=1, $detail=0)
{
  $pasttime = "Seconds";
  $remainder = 0;
  if ($minpast > 1 && $minpast < 60) $pasttime = "$minpast minutes";
  elseif ($minpast >= 60 && $minpast < 120)
  { 
    $pasttime = "A hour"; 
    $remainder = $minpast-60;
  }
  elseif ($minpast >= 120 && $minpast < 1440) 
  {
    $pasttime = intval($minpast/60)." hours";
    $remainder = $minpast - intval($minpast/60)*60;
  }
  elseif ($minpast >= 1440 && $minpast < 2880) 
  { 
    $pasttime = "A day";
    $remainder = $minpast - 1440;
  }
  elseif ($minpast >= 2880) 
  { 
    $pasttime = intval($minpast/1440)." days";
    $remainder = $minpast - intval($minpast/1440)*1440;
  }
  
  if ($detail && $remainder)
  {
    $pasttime .= " ".displayTime($remainder,0,1);
  }
  if ($ago) $pasttime .= " ago";
  
  return $pasttime;
}

function ComputeScore($list,$gtype)
{
  $score = 0;
  $temp_pairs = array('0','0');
  for ($x=0; $x<6; $x++)
  {
    $temp_score = 0;
    if ($list[$x] == 5 && !$temp_score) $temp_score = intval("9".$x."0"); // five of a kind
    if ($list[$x] == 4 && !$temp_score) $temp_score = intval("8".$x."0"); // four of a kind
    if ($list[$x] == 3 && !$temp_score) 
    {
      $temp_score = intval("3".$x."0"); // three of a kind
      for ($y=0; $y<6; $y++) 
      {
        if ($list[$y] == 2) $temp_score = intval("6".$x.$y); // full house
      }
    }
    if ($list[$x] == 2 && !$temp_score) // add to pair list
    {
      $temp_pairs[1]=$temp_pairs[0];
      $temp_pairs[0]=$x+1;
    }
    if ($temp_score > $score) $score = $temp_score;
  }
  if ($score < 500 && !$gtype) // straight, if Tops
  {
    if (($list[0] && $list[1] && $list[2] && $list[3] && $list[4]) || ($list[1] && $list[2] && $list[3] && $list[4] && $list[5])) $temp_score = 400;
  }
  if ($score < 400) // use pairs
  {
    if ($temp_pairs[1]) $score = intval("2".$temp_pairs[0].$temp_pairs[1]);
    elseif ($temp_pairs[0]) $score = intval("1".$temp_pairs[0]."0");
  }
  
  $count=0;
  $hand = 0;
  $score= $score*100000;
  for ($x=0; $x<6; $x++)
  {
    for ($y=0; $y<$list[$x]; $y++)
    {
      $hand += $x*pow(10,$count++);
    }
  }
  
  $score += $hand;
  
  return $score;
}

function updateDice ($curr_dice, $wager, $gtype)
{
  $list = '';
  $p_info = '';
  $winners = 0;
  $winning_score = 0;
  
// COMPUTE WINNER
  foreach ($curr_dice as $p => $a)
  {
    for ($x=0; $x<5; $x++)
    {
      $list[$p][$a[$x]-1] += 1;
    }
    $p_info[$p] = ComputeScore($list[$p],$gtype);
    if ($p_info[$p] > $winning_score) $winning_score=$p_info[$p];
  }
  foreach ($curr_dice as $p => $a)
  {
    if ($p_info[$p] == $winning_score) {$winners++;}
  }
  $winnings = intval(($wager*count($curr_dice))/$winners);
// UPDATE DATABASE
  foreach ($curr_dice as $p => $a)
  {
    $username = explode(' ',$p);

    if (intval($p_info[$p]) == intval($winning_score) && $username[0] != "NPC")
    {
      $tresult = mysql_query("SELECT id, gold FROM Users WHERE name='".$username[0]."' AND lastname='".$username[1]."'");
      $user = mysql_fetch_array($tresult);
      $user[gold] = $user[gold] + $winnings;
      mysql_query("UPDATE Users SET gold='$user[gold]' WHERE id='$user[id]'");
      $ustats = mysql_fetch_array(mysql_query("SELECT * FROM Users_stats WHERE id='".$user[id]."'"));
      $ustats['dice_earn'] += $winnings;
      $ustats['dice_wins']++;
      $result = mysql_query("UPDATE Users_stats SET dice_earn='".$ustats[dice_earn]."', dice_wins='".$ustats[dice_wins]."' WHERE id='".$user[id]."'");
    }
  }
}

function wotDate ()
{
  $wot_months = array( 
    1=>"Taisham",
    2=>"Jumara",
    3=>"Saban",
    4=>"Aine",
    5=>"Adar",
    6=>"Saven",
    7=>"Amadaine",
    8=>"Tammaz",
    9=>"Maigdhal",
    10=>"Choren",
    11=>"Shaldine",
    12=>"Nesan",
    13=>"Danu",
  );

  date_default_timezone_set('America/Los_Angeles');
  $day = date(j);
  $month = date(n);
  $year = date(Y);
  $leap = date(L);
  $souls = 0;
  if ($year%10 ==0)
  { $souls = 1; }

  switch ($month)
  {
    case 1:
      $daynum= $day;
      break;
    case 2:
      $daynum= $day+31;
      break;
    case 3:
      $daynum= $day+59+$leap;
      break;
    case 4:
      $daynum= $day+90+$leap;
      break;
    case 5:
      $daynum= $day+120+$leap;
      break;
    case 6:
      $daynum= $day+151+$leap;
      break;
    case 7:
      $daynum= $day+181+$leap;
      break;
    case 8:
      $daynum= $day+212+$leap;
      break;
    case 9:
      $daynum= $day+243+$leap;
      break;
    case 10:
      $daynum= $day+273+$leap;
      break;
    case 11:
      $daynum= $day+304+$leap;
      break;
    case 12:
      $daynum= $day+334+$leap;
      break;
    default:
      $daynum= $day;
      break;
  }

  if ($daynum < 19)
  {
    $fmonth = 1;
    $fday = $daynum +10;
  }
  else if ($daynum < 47)
  {
    $fmonth = 2;
    $fday = $daynum-18;
  }
  else if ($daynum < 75)
  {
    $fmonth = 3;
    $fday = $daynum-46;
  }
  else if ($daynum < 80)
  {
    $fmonth = 4;
    $fday = $daynum-74;
  }
  else if ($daynum < 103+$leap)
  {
    if ($leap && $daynum == 80)
    {
      $fmonth = 0;
      $fday = 2;
    }
    else
    {
      $fmonth = 4;
      $fday = $daynum-74-$leap;
    }
  }
  else if ($daynum < 131+$leap)
  {
    $fmonth = 5;
    $fday = $daynum-102-$leap;
  }
  else if ($daynum < 159+$leap)
  {
    $fmonth = 6;
    $fday = $daynum-130-$leap;
  }
  else if ($daynum < 172+$leap)
  {
    $fmonth = 7;
    $fday = $daynum-158-$leap;
  }
  else if ($daynum == 172+$leap)
  {
    $fmonth = 0;
    $fday = 1;
  }
  else if ($daynum < 188+$leap)
  {
    $fmonth = 7;
    $fday = $daynum-159-$leap;
  }
  else if ($daynum < 216+$leap)
  {
    $fmonth = 8;
    $fday = $daynum-187-$leap;
  }
  else if ($daynum < 244+$leap)
  {
    $fmonth = 9;
    $fday = $daynum-215-$leap;
  }
  else if ($daynum < 266+$leap)
  {
    $fmonth = 10;
    $fday = $daynum-244-$leap;
  }
  else if ($daynum < 272 + $leap+$souls)
  {
    if ($souls && $daynum ==266)
    {
      $fmonth = 0;
      $fday = 3;
    }
    else
    {
      $fmonth = 10;
      $fday = $daynum-244-$leap-$souls;
    }
  }
  else if ($daynum < 300+$leap+$souls)
  {
    $fmonth = 11;
    $fday = $daynum-271-$leap-$souls;
  }
  else if ($daynum < 328+$leap+$souls)
  {
    $fmonth = 12;
    $fday = $daynum-299-$leap-$souls;
  }
  else if ($daynum < 356+$leap)
  {
    $fmonth = 13;
    $fday = $daynum-327-$leap-$souls;
  }
  else if ($daynum < 366+$leap)
  {
    $fmonth = 1;
    $fday = $daynum-355-$leap;
  }
  else
  {
    return "Day ".$daynum;
  }
  
  return $wot_months[$fmonth-1]." ".$fday;
}

function getAllJobBonuses($prolvls)
{
  $jobon = "A0";
  for ($i=0; $i<13; $i++)
  {
    $jobon .= " ".getJobBonuses($i,$prolvls[$i]);
  }
  
  return $jobon;
}

function getTownPop($upgrades,$town_pop,$build_pop)
{
  $pop=10+$town_pop;
  for ($i=0; $i < 6; $i++)
  {
    $pop += $build_pop[$upgrades[$i]];
  }
  $pop += $build_pop[6]*($upgrades[6]+$upgrades[7]);
  
  return $pop;
}

function getJobBonuses($pro, $plvl)
{
$stats = '';
$halfUp = round($plvl/2);
$halfDown= floor($plvl/2);
$mult2=2*$plvl;
$mult3=3*$plvl;
$mult40=40*$plvl;

switch($pro)
{
case '0':
$stats = "pV-".$plvl." lB".$plvl;
break;
case '1':
$stats = "rV-".$mult2." eF".$mult2;
break;
case '2':
$stats = "iV-".$mult2." fV-".$mult2." eV".$plvl;
break;
case '3':
$stats = "dL".$plvl." cV".$plvl;
break;
case '4':
$stats = "hV-".$mult2." hU".$halfDown." gB".$plvl;
break;
case '5':
$stats = "dV-".$mult2." dU".$halfDown." bB".$plvl;
break;
case '6':
$stats = "fS".$halfUp." fU-".$halfDown." kB".$plvl;
break;
case '7':
$stats = "pL".$mult2." fB".$plvl;
break;
case '8':
$stats = "mL".$mult2." mB".$plvl;
break;
case '9':
$stats = "oL".$mult2." sB".$plvl;
break;
case '10':
$stats = "wL".$mult2." nQ".$mult40." tQ".$mult40;
break;
case '11':
$stats = "hL".$mult2." eS".$plvl." cS".$halfUp;
break;
case '12':
$stats = "fL".$mult2." fQ".$mult40." hQ".$mult40;
break;
}
if ($plvl == 0) $stats = "";
return $stats;
}

// PARSER FUNCTION
function cparse($string,$resolve=0)
{
  // RESOLVE all common terms and place in array
  $resolve = explode(" ",$string);
  for ($x=0; $x<count($resolve); $x++)
  {
    if (!preg_match('/^[a-z]*$/i',$resolve[$x][0].$resolve[$x][1])) // one letter code
    {
      $array[$resolve[$x][0]] += intval($resolve[$x][1].$resolve[$x][2].$resolve[$x][3].$resolve[$x][4].$resolve[$x][5]);
    }
    else // two letter code
    {
      $array[$resolve[$x][0].$resolve[$x][1]] += intval($resolve[$x][2].$resolve[$x][3].$resolve[$x][4].$resolve[$x][5].$resolve[$x][6]);
    }
  }
  if ($array['G'] > 75)
  {
    $array['N'] += ($array['G']-75)*2;
    $array['G'] = 75;
  }

  return $array;
}

// ITEM INFO
function show_sign1($thing) {
  if ($thing < 0) return $thing; else return "+".$thing;
}

$stat_msg = array(
  // Straight Item Stats
  'A' => array(""," Damage",'B'),
  'B' => array("","",3),
  'D' => array(""," Block",'E'),
  'E' => array("","",3),
  'O' => array("","% Damage",1),
  'N' => array("","% Defense",1),
  'H' => array("","% Damage to Self",1),
  'Y' => array(""," Accuracy",1),
  'V' => array(""," Dodge",1),
  'G' => array("","% Health Gain",1),
  'L' => array(""," Luck",1),
  'P' => array(""," Poison",1),
  'T' => array(""," Taint",1),
  'F' => array(""," First Strike",1),
  'S' => array(""," Stun",1),
  'X' => array(""," Speed",1),
  'W' => array(""," Wound",1),
  
  'M' => array(""," Stamina",1),
  'J' => array(""," Ji in Duels",1),
  'Q' => array(""," Experience in Duels",1),
   
  // Item Type specific stats
  'sA' => array(""," Sword Damage",2),
  'xA' => array(""," Axe Damage",2),
  'pA' => array(""," Spear Damage",2),
  'oA' => array(""," Bow Damage",2),
  'bA' => array(""," Bludgeon Damage",2),
  'kA' => array(""," Knife Damage",2),
  'wA' => array(""," Weave Damage",2),
  'hA' => array(""," Shield Block",2),
  'aA' => array(""," Body Armor Block",2),
  'lA' => array(""," Leg Armor Block",2),

  'sO' => array("","% Sword Damage",1),
  'xO' => array("","% Axe Damage",1),
  'pO' => array("","% Spear Damage",1),
  'oO' => array("","% Bow Damage",1),
  'bO' => array("","% Bludgeon Damage",1),
  'kO' => array("","% Knife Damage",1),
  'wO' => array("","% Weave Damage",1),
  'hO' => array("","% Shield Defense",1),
  'aO' => array("","% Body Armor Defense",1),
  'lO' => array("","% Leg Armor Defense",1),

  'sC' => array("","% Sword Usage Cost",1),
  'xC' => array("","% Axe Usage Cost",1),
  'pC' => array("","% Spear Usage Cost",1),
  'oC' => array("","% Bow Usage Cost",1),
  'bC' => array("","% Bludgeon Usage Cost",1),
  'kC' => array("","% Knife Usage Cost",1),
  'wC' => array("","% Weave Usage Cost",1),
  'hC' => array("","% Shield Usage Cost",1),
  'aC' => array("","% Body Armor Usage Cost",1),
  'lC' => array("","% Leg Armor Usage Cost",1),   

  // OP strength
  'fP' => array(""," Fire Strength",1),
  'wP' => array(""," Water Strength",1),
  'sP' => array(""," Spirit Strength",1),
  'aP' => array(""," Air Strength",1),
  'eP' => array(""," Earth Strength",1),
  
  // Price reduction
  'rV' => array("","% price of Repairs",1),
  'iV' => array("","% price of Inn rooms",1),
  'oV' => array("","% price of Outfitter purchaces",1),
  'sV' => array("","% price of Item Storage",1),
  'uV' => array("","% price of Horses & Feed",1),
  'hV' => array("","% price of Herbs",1),
  'dV' => array("","% price of Drinks",1),
  'fV' => array("","% price of Food",1),
  'lV' => array("","% price of Consumbable Items",1),
  'pV' => array("","% price of Equipment Items",1),
  'dP' => array("","% price of Swords",1),
  'xP' => array("","% price of Axes",1),
  'pP' => array("","% price of Spears",1),
  'oP' => array("","% price of Bows",1),
  'bP' => array("","% price of Bludgeons",1),
  'kP' => array("","% price of Knives",1),
  'hP' => array("","% price of Shields",1),
  'rP' => array("","% price of Body Armor",1),
  'lP' => array("","% price of Leg Armor",1),
  
  // Consumable turns
  'hU' => array("Herbs take "," turns to digest",1),
  'dU' => array("Drinks take "," turns to digest",1),
  'fU' => array("Food takes "," turns to digest",1),
  
  // Storage capacity
  'eS' => array(""," Equipment Storage capacity",1),
  'cS' => array(""," Consumable Storage capacity",1),

  // Misc profession bonuses
  'fS' => array(""," Stamina recovered from Food",1),
  'eF' => array("Found items ","% less damaged",0),
  
  // Luck bonuses
  'pL' => array(""," Luck vs NPCs in Plains",1),
  'hL' => array(""," Luck vs NPCs in Hills",1),
  'fL' => array(""," Luck vs NPCs in Forests",1),
  'mL' => array(""," Luck vs NPCs in Mountains",1),
  'oL' => array(""," Luck vs NPCs in Oceans",1),
  'wL' => array(""," Luck vs NPCs in Wastelands",1),
  'dL' => array(""," Luck in Duels",1),
  
  // Coin bonuses
  'eV' => array("Entertainment bonus coin level ","",0),
  'cV' => array("Purse Cutting bonus coin level ","",0),
  
  // Quests bonuses
  'fQ' => array(""," bonus coin per level for Find quests",1),
  'nQ' => array(""," bonus coin per level for NPC quests",1),
  'hQ' => array(""," bonus coin per level for Horde quests",1),
  'tQ' => array(""," bonus coin per level for Escort quests",1),
  'iQ' => array(""," bonus coin per level for Item quests",1),
  'pQ' => array(""," bonus coin per level for Player quests",1),
  'fJ' => array(""," bonus Ji per level for Find quests",1),
  'eJ' => array(""," bonus Ji per level for NPC quests",1),
  'iJ' => array(""," bonus Ji per level for Item quests",1),
  'pJ' => array(""," bonus Ji per level for Player quests",1),
  'uQ' => array("All Local Quests are Neutral Quests","",3),
  'lQ' => array("All Local Quests are Light Quests","",3),
  'sQ' => array("All Local Quests are Shadow Quests","",3),
  'eQ' => array(""," Local Quests Available",1),
  
  // Business Levels
  'fB' => array("Can own a level "," Farm",0),
  'mB' => array("Can own a level "," Mine",0),
  'sB' => array("Can own a level "," Shipping Office",0),
  'kB' => array("Can own a level "," Kitchen",0),
  'gB' => array("Can own a level "," Herb Garden",0),
  'bB' => array("Can own a level "," Brewery",0),
  'lB' => array("Can own a level "," Market Stall",0),
  
  // Business Stats
  'fT' => array("","% Crop Grow Time",1),
  'mT' => array("","% Mining Time",1),
  'sT' => array("","% Sailing Time",1),
  'kT' => array("","% Cook Time",1),
  'gT' => array("","% Herb Grow Time",1),
  'bT' => array("","% Brewing Time",1),
  'oT' => array("","% Assignment Time",1),
  'cT' => array("","% Consumable Time",1),

  'xV' => array("","% Crop Value",1),
  'mV' => array("","% Mineral Value",1),
  'gV' => array("","% Cargo Value",1),
  'oQ' => array("","% Assignment Effect",1),
  
  'kQ' => array(""," Food Quality",1),
  'gQ' => array(""," Herb Quality",1),
  'bQ' => array(""," Drink Quality",1),
  'cQ' => array(""," Consumable Quality",1),

  'sR' => array(""," Ship Range",1),
  'eM' => array("","% Price for Enemies",1),
  'aM' => array("","% Price for Allies",1),
  
  'bG' => array("","% price of Businesses",1),
  'bU' => array("","% price of Business Upgrages",1),

  // Town Bonuses
  'dW' => array("","% Wait between Duels",1),
  'bD' => array("","% Max Bank Deposit",1),
  'iS' => array(""," Slots in Clan Shops",1),
  
  // Misc Unique building bonuses
  'rM' => array(""," Stamina recovered per hour",1),
  'sS' => array("","% price of stocking Clan Shops ",1),
  'rS' => array(""," Ji required for items in Clan Shops",1),
  'dR' => array(""," City Defense gain in Ruled Cities",1),
  'oR' => array(""," Order gain in Ruled Cities",1),
  'cW' => array(""," Chaos gain in Enemy Cities",1),
  
  
  // NPC bonuses
  'sD' => array("","% damage vs Shadow NPCs",1),
  'mD' => array("","% damage vs Military NPCs",1),
  'rD' => array("","% damage vs Ruffian NPCs",1),
  'cD' => array("","% damage vs Channeler NPCs",1),
  'aD' => array("","% damage vs Animal NPCs",1),
  'eD' => array("","% damage vs Exotic Animal NPCs",1),
  'sE' => array("","% defense vs Shadow NPCs",1),
  'mE' => array("","% defense vs Military NPCs",1),
  'rE' => array("","% defense vs Ruffian NPCs",1),
  'cE' => array("","% defense vs Channeler NPCs",1),
  'aE' => array("","% defense vs Animal NPCs",1),
  'eE' => array("","% defense vs Exotic Animal NPCs",1),  
  
  // Nationality bonuses
  'aN' => array("","% damage if Aiel",1),
  'lN' => array("","% damage if Altaran",1),
  'nN' => array("","% damage if Amadician",1),
  'yN' => array("","% damage if Andoran",1),
  'qN' => array("","% damage if Arafellin",1),
  'fN' => array("","% damage if Atha&#39;an Miere",1),
  'hN' => array("","% damage if Cairhienin",1),
  'dN' => array("","% damage if Domani",1),
  'gN' => array("","% damage if Ghealdanin",1),
  'iN' => array("","% damage if Illianier",1),
  'kN' => array("","% damage if Kandori",1),
  'mN' => array("","% damage if Murandian",1),
  'oN' => array("","% damage if Ogier",1),
  'eN' => array("","% damage if Saldaean",1),
  'xN' => array("","% damage if Seanchan",1),
  'sN' => array("","% damage if Shienaran",1),
  'bN' => array("","% damage if Taraboner",1),
  'tN' => array("","% damage if Tarien",1),
  
  'rN' => array("","% damage if Armsman",1),
  'wN' => array("","% damage if Wanderer",1),
  'uN' => array("","% damage if Outdoorsman",1),
  'cN' => array("","% damage if Channeler",1),
  'zN' => array("","% damage if Darkfriend",1),
  
  // Wilderness bonuses
  'oE' => array("","% defense in Ocean Areas",1),
  'fE' => array("","% defense in Forest Areas",1),
  'uE' => array("","% defense in Mountain Areas",1),
  'wE' => array("","% defense in Wasteland Areas",1),
  'hE' => array("","% defense in Hill Areas",1),
  'pE' => array("","% defense in Plain Areas",1),

  // Estate Bonuses
  'zD' => array("","% damage in Estate&#39;s Wilderness Area",1),
  'zE' => array("","% defense in Estate&#39;s Wilderness Area",1),
  'nJ' => array("","% Ji vs NPCs in Estate&#39;s Wilderness Area",1),
  'zL' => array(""," Luck in Estate&#39;s Wilderness Area",1),
  'zQ' => array("Local Quest bonus coin level ","",0),
  'zS' => array(""," Estate Storage",1),
  'eT' => array("","% chance for bonus Ter&#39;angreal in Estates",1),
  'wT' => array("Estate Tax level ","",0),
  'tV' => array("","% discount in Surrounding Cities",1),
  
  // Estate Production
  'fG' => array("Lumber Production level ","",0),
  'hG' => array("Livestock Production level ","",0),
  'mG' => array("Ore Production level ","",0),
  'oG' => array("Fish Production level ","",0),
  'pG' => array("Grain Production level ","",0),
  'wG' => array("Luxury Production level ","",0),
  
  // Clan Bonuses
  'lS' => array(""," Vault Storage",1),
  'eL' => array(""," Luck vs. Clan Enemies",1),
  'nR' => array("","% defense in Ruled Cities",1),
  'gR' => array("","% health gain in Ruled Cities",1),
  'vR' => array(""," dodge in Ruled Cities",1),
  'oW' => array("","% damage in Enemy Cities",1),
  'pW' => array(""," poison in Enemy Cities",1),
  'yW' => array(""," accuracy in Enemy Cities",1), 

  // Obsolete...?
  'kS' => array(""," Food Storage",1),
  'gS' => array(""," Herb Storage",1),
  'bS' => array(""," Drink Storage",1),
  'bX' => array("","% speed of Businesses",1),
  'wW' => array(""," hours notice before Wars",1),
  'rW' => array(""," hours wait between Wars",1),
  'tW' => array(""," hours wait between Tournaments",1),
);

function display_stat($sym, $char_stats, $old_stats)
{
  global $stat_msg;
  $msg = $stat_msg[$sym];
  $bonus='';
  if ($char_stats[$sym] != 0 || ($old_stats[$sym] != 0  && $old_stats[$sym] != 0))
  {
    // Create info string
    if ($msg[2]=='0')
    {
      $bonus = $msg[0].($char_stats[$sym]).$msg[1];
    }
    else if ($msg[2]=='1')
    {
      $bonus = $msg[0].show_sign1($char_stats[$sym]).$msg[1];
    }
    else if ($msg[2]==2)
    {
      $bonus = $msg[0].show_sign1($char_stats[$sym])."-".($char_stats[$sym]*2).$msg[1];
    }
    else if ($msg[2]==3)
    {
      $bonus = $msg[0].$msg[1];
    }
    else
    {
      $bonus = $msg[0].$char_stats[$sym]."-".$char_stats[$msg[2]].$msg[1];
    }
    
    // Color stat changes
    if ($old_stats != '' && ($msg[2]!=3))
    {
      $font = "";
      if ($char_stats[$sym] > $old_stats[$sym])
      {
        $font = "<font class='text-success'>";
      }
      else if ($char_stats[$sym] < $old_stats[$sym])
      {
        $font = "<font class='text-danger'>";
      }
      
      if ($font != "")
      {
        $bonus = $font.$bonus."</font>";
      }
    }
    if ($bonus != "") $bonus .= "<br/>";
  }

  //return str_replace(" ", "&nbsp;", $bonus);
  return $bonus;
}

function itm_info($char_stats, $old_stats=0)
{
  global $stat_msg;
  $bonuses = "";
  foreach ($stat_msg as $sym => $msg)
  {
    $bonuses .= display_stat($sym, $char_stats, $old_stats);
  }
  
  return $bonuses;
}

function yesno ($mybool)
{
  $rval='';
  if ($mybool) $rval = "Yes"; 
  else $rval = "No";
  
  return $rval;
}

function getMaxSeals($toplevel)
{
  $rval = 0;
  if ($toplevel >= 70) $rval = 7;
  elseif ($toplevel >= 60) $rval = 5;
  elseif ($toplevel >= 50) $rval = 3;

  return $rval;
}

function getMaxHorn($toplevel)
{
  $rval = 0;
  if ($toplevel >= 20) $rval = 1;
  return $rval;
}

function pruneMsgs($msgs, $max)
{
  $newMsg = "";
  $numMsgs = count($msgs);
  if ($numMsgs > $max)
  {
    for ($i=0; $i < $max; $i++)
    {
      $newMsg[$i] = $msgs[$numMsgs-$max+$i];
    }
  }
  else
  {
    $newMsg = $msgs;
  }

  return $newMsg;
}


function getAlignment($align)
{
  $rval = 0;
  
  if ($align <= -1000) $rval = -2;
  else if ($align <= -300) $rval = -1;
  else if ($align >= 1000) $rval = 2;
  else if ($align >= 300) $rval = 1;

  
  return $rval;
}

function getClanAlignment($align, $members)
{
  $rval = 0;
  
  if ($align <= -1250-(250*$members)) $rval = -2;
  else if ($align <= -500-(100*$members)) $rval = -1;
  else if ($align >= 1250+(250*$members)) $rval = 2;
  else if ($align >= 500+(100*$members)) $rval = 1;  
  return $rval;
}

function getAlignmentText ($alignnum)
{
  $lalign = "Neutral";
  if ($alignnum == 2) $lalign = "Light";
  elseif ($alignnum == 1) $lalign = "Lean Light";
  elseif ($alignnum == -1) $lalign = "Lean Shadow";
  elseif ($alignnum == -2) $lalign = "Shadow";
  
  return $lalign;
}

function inClanBattle($clan_id)
{
  $inBattle = 0;
  $hour = time()/3600;
  
  $cresult=mysql_query("SELECT id, contestants FROM Contests WHERE type = 10 AND starts < '".$hour."' AND ends > '".$hour."'");
  while ($cb = mysql_fetch_array($cresult))
  {
    $contestants = unserialize($cb[contestants]);
    foreach ($contestants as $cid => $cdata)
    {
      if ($cid == $clan_id) $inBattle = 1;
    }
  }
  
  return $inBattle;
}

function isClanLeader($achar, $soc_name, $officer, $loc_id)
{
  $society = mysql_fetch_array(mysql_query("SELECT id, subleaders, subs, offices, area_score, leader, leaderlast FROM Soc WHERE name='$soc_name' "));
  $subleaders = unserialize($society['subleaders']);
  $subs = $society['subs'];
  $offices = unserialize($society['offices']);
  $b = 0; 
  
  if (strtolower($achar[name]) == strtolower($society[leader]) && strtolower($achar[lastname]) == strtolower($society[leaderlast]) ) 
  {
    $b=1;
  }
  if ($subs > 0)
  {
    foreach ($subleaders as $c_n => $c_s)
    {
      if (strtolower($achar[name]) == strtolower($c_s[0]) && strtolower($achar[lastname]) == strtolower($c_s[1]) )
      {
        $b=1;
      }
    }
  }    
  if ($officer && $offices && $offices[$loc_id])
  {
    if ($offices[$loc_id][0] != '1')
    {
      if (strtolower($achar[name]) == strtolower($offices[$loc_id][0]) && strtolower($achar[lastname]) == strtolower($offices[$loc_id][1]) ) 
      {
        $b=1;  
      }
    }
  }
  
  return $b;
}

?>