<?php
include_once('mapData.php');
include_once('itemFuncs.php');
include_once('charFuncs.php');

  date_default_timezone_set('America/Los_Angeles');
  
// QUEST FUNCTIONS
// Keep in sync with $quest_type_num in displayFuncs
$quest_type = array(
0 => "Items", // Find x items of a type
1 => "NPC",   // Defeat x NPCs of a type
2 => "Find",  // Find a specific NPC
3 => "Horde", // Defeat a small horde
4 => "Escort", // Escort someone on a specific path
10 => "Bounty",  // Defeat a player X times
11 => "Request", // Request a specific item
20 => "Clan",    // Defeat members of a clan X times
21 => "Support", // Earn X Ji in a given city
30 => "Tutorial", // Complete specific achievements
);

function show_quest_reqs($reqs_stats)
{
  $reqs="";
  if ($reqs_stats['L']) $reqs .= "Level ".$reqs_stats['L']."<br>";
  if ($reqs_stats['S']==1) $reqs .= "Male<br>"; elseif ($reqs_stats['S']==2) $reqs .= "Female<br>";

  return str_replace(" ", "&nbsp;", $reqs);
}

function check_quest_reqs($reqs_stats, $char)
{
  $reqs_met = 1;
  $myquests= unserialize($char[quests]);
  if ($reqs_stats['L'] && $reqs_stats['L'] > $char[level])
  { $reqs_met = 0; }
  if ($reqs_stats['C'])
  {
    if ($reqs_stats['C'] == 1 || $reqs_stats['C']%10 == 0) // generic class
    {
      if (floor($reqs_stats['C']/10) != floor($char[type]/10))
      { $reqs_met = 0; }
    }
    else if (floor($reqs_stats['C']/2) != floor($char[type]/2)) // specific class
    { $reqs_met = 0; }
  }
  if (($reqs_stats['S']==1 && $char[sex] != 0) || ($reqs_stats['S']==2 && $char[sex] != 1))
  { $reqs_met = 0; }
  if ($reqs_stats['Q'] && $myquests[$reqs_stats['Q']][0] != 2)
  { $reqs_met = 0; }

  return $reqs_met;
}

function show_quest_goals($align, $goals, $type, $offerer, $specials)
{
  global $item_base, $item_type_pl, $enemy_list, $horde_types, $npc_plural, $npc_nation_data, $quest_type_num, $achievements;
  
  $rval = "";
  $special=unserialize($specials);

  $quest_wording= array(
    '0' => array(array(" is looking for ",". "),array(" is in need of "," for the defense of the city. "),array(" requires "," for the Great Lord's plans. ")),
    '1' => array(array(" are threatening ","Defeat "," and you will be rewarded."),array(" have overrun ","Repel "," for a small token of gratitude."),array(" are disrupting plans involving ","Eliminate "," to claim a fair reward.")),
    '2' => array(array(" has been causing "," trouble lately in the ","Find and defeat the "),array(" has been terrorizing "," lately in the ","Seek out and run off the "),array(" is standing in ","'s way in the ","Hunt down and exterminate this ")),
    '3' => array(array(" is wandering ","Remove the "," from the area."),array(" is rampaging across ","Stop the "," from menacing the area."),array(" is protecting ","Destroy the "," or face the consequences!")),
    '4' => array(array(" is traveling along a "," route to "," and is in need of protection. Escort "," and receive your reward."),array(" must journey the "," way to "," for business that must not be disrupted. Protect "," to help the cause."),array(" has orders to spread chaos along a "," route to "," that must be carried out. Take "," to the destination without fail.")),
  );

  $horde_size = array(
    '10' => "Tiny ",
    '15' => "Small ",
    '20' => "",
    '25' => "Large ",
    '30' => "Huge ",
  );
  $escort_length = array(
    '6' => "Quick",
    '12' => "Short",
    '18' => "Standard",
    '24' => "Winding",
    '30' => "Scenic",
   );

  $gloc = str_replace("-ap-", "'", str_replace("_", " ", $goals[2]));
  if ($type == $quest_type_num["Items"])
  {
    $itm = $goals[1];
    $itm_name= "";
    if (count($goals[1][0]==2))
    {
      $itm_name=$item_type_pl[$goals[1][0][1]]." ";
      $itype = $goals[1][0][1];
    }
    else
    {
      if ($itm[1] != "" && $itm[1] != "*")
      {
        $itm_name .= $itm[1]." ";
      }
      if ($itm[0] != "" && $itm[0] != "*")
      {
        $itm_name .= $itm[0]." ";
      }
      else $itm_name .= "Item ";
      if ($itm[2] != "" && $itm[2] != "*")
      {
        $itm_name .= $itm[2]." ";
      }
    }
    $itm_name = ucwords($itm_name);

    if ($itype <9 || $itype == 12)
    {
      if ($itm[1] == "*" && $itm[2] == "*") $itm_name .= "(Any prefix or suffix) ";
      elseif ($itm[1] == "*") $itm_name .= "(Any prefix) ";
      elseif ($itm[2] == "*") $itm_name .= "(Any suffix) ";
    }
    $rval = $offerer.$quest_wording[0][$align][0].$item_type_pl[$goals[1][0][1]].$quest_wording[0][$align][1];
    $rval .= "Bring ".$goals[0]." ".$itm_name." to ".$gloc;
    if ($special[9]){
      $rval .= "<a class='btn btn-success btn-xs btn-block' href='http://talij.com/goswiki/index.php?title=".$special[9]."' target='_blank'>Details</a>";
    }
  }
  elseif ($type == $quest_type_num["NPC"])
  {  
    $ename = $enemy_list[$goals[2]][$goals[1]];
    $rval = $npc_plural[$ename].$quest_wording[1][$align][0].$offerer."'s ".$special[2]." in the ".$gloc.". ";
    $rval .= $quest_wording[1][$align][1].$goals[0]." ".$ename."s in the ".$gloc.$quest_wording[1][$align][2];
    if ($special[9]){
      $rval .= "<a class='btn btn-success btn-xs btn-block' href='http://talij.com/goswiki/index.php?title=".$special[9]."' target='_blank'>Details</a>";
    }
  }
  elseif ($type == $quest_type_num["Find"])
  {
    $ename = $enemy_list[$goals[2]][$goals[1]];
    $rval = "A ".$ename.$quest_wording[2][$align][0].$offerer.$quest_wording[2][$align][1].$gloc.". ";
    $rval .= $quest_wording[2][$align][2].$ename.".";
    if ($special[9]){
      $rval .= "<a class='btn btn-success btn-xs btn-block' href='http://talij.com/goswiki/index.php?title=".$special[9]."' target='_blank'>Details</a>";
    }
  }
  elseif ($type == $quest_type_num["Horde"])
  {
    $ename = $enemy_list[$goals[2]][$goals[1]];
    $rval = "A ".$horde_size[$goals[0]].$horde_types[$ename]." of ".$npc_plural[$ename].$quest_wording[3][$align][0].$gloc.". ";
    $rval .= $quest_wording[3][$align][1].$horde_types[$ename].$quest_wording[3][$align][2];
    if ($special[9]){
      $rval .= "<a class='btn btn-success btn-xs btn-block' href='http://talij.com/goswiki/index.php?title=".$special[9]."' target='_blank'>Details</a>";
    }    
  }
  elseif ($type == $quest_type_num["Escort"])
  {
    $rval = $offerer.$quest_wording[4][$align][0].$escort_length[$goals[0]].$quest_wording[4][$align][1].$gloc.$quest_wording[4][$align][2].$offerer.$quest_wording[4][$align][3];
    if ($special[9]){
      $rval .= "<a class='btn btn-success btn-xs btn-block' href='http://talij.com/goswiki/index.php?title=".$special[9]."' target='_blank'>Details</a>";
    }    
  }
  elseif ($type == $quest_type_num["Bounty"])
  {
    $rval = $offerer." has put a bounty on ".str_replace("_", " ", $goals[1])."'s head. ";
    $rval .= "Defeat ".str_replace("_", " ", $goals[1])." ".$goals[0]." times to collect the bounty.";
    if ($special[9]){
      $rval .= "<a class='btn btn-success btn-xs btn-block' href='http://talij.com/goswiki/index.php?title=".$special[9]."' target='_blank'>Details</a>";
    }
  }
  elseif ($type == $quest_type_num["Request"])
  {
    $itm = $goals[1];
    $itm_name= "";
    if ($itm[1] != "" && $itm[1] != "*")
    {
      $itm_name .= $itm[1]." ";
    }
    if ($itm[0] != "" && $itm[0] != "*")
    {
      $itm_name .= $itm[0]." ";
    }
    else $itm_name .= "Item ";
    if ($itm[2] != "" && $itm[2] != "*")
    {
      $itm_name .= $itm[2]." ";
    }
    $itm_name = ucwords($itm_name);

    if ($item_base[$itm[0]][1] <9 || $item_base[$itm[0]][1] == 12)
    {
      if ($itm[1] == "*" && $itm[2] == "*") $itm_name .= "(Any prefix or suffix) ";
      elseif ($itm[1] == "*") $itm_name .= "(Any prefix) ";
      elseif ($itm[2] == "*") $itm_name .= "(Any suffix) ";
    }
    $rval = $offerer." needs ".$goals[0]." ".$itm_name."for their grand plans. ";
    $rval .= "Bring ".$goals[0]." to ".str_replace("-ap-", "'", str_replace("_", " ", $goals[2]))." to collect your reward.";
  }
  elseif ($type == $quest_type_num["Clan"])
  {
    $rval = $offerer." has put a bounty on all members of ".$goals[1].". ";
    $rval .= "Defeat members of ".$goals[1]." ".$goals[0]." times to collect the bounty.";
  }
  elseif ($type == $quest_type_num["Support"])
  {
    $rval = $offerer." has asked for support in ".$gloc.". ";
    $rval .= "The first ".$goals[1]." Ji earned in  ".$gloc." will receive payment for their help.";
  }
  elseif ($type == $quest_type_num["Tutorial"])
  {
    $rval = "The ".$offerer." has given you the following tasks:<br/>";
    foreach ($goals as $achieve => $alvl)
    {
      $ainfo = $achievements[$achieve];
      $x = $ainfo[3][$alvl-1];
      $atask = $ainfo[2][0];
      if (count($ainfo[2]) > 1)
      {
        if ($ainfo[2][2])
        {
          $atask.= displayGold($x,1);
        }
        else
        {
          $atask.= $x;
        }
        $atask.= $ainfo[2][1];
      }
      $rval .= $atask."<br/>";
    }
    $rval .= "<a class='btn btn-success btn-xs btn-block' href='http://talij.com/goswiki/index.php?title=".$specials."' target='_blank'>Details</a>";
  }

  return $rval;
}

function check_inventory_items ($item, $inventory, $type=-1, $item_base='')
{
  $count = 0;
  $itm = $item;
  $inv = $inventory;

  if ($inv[0])
  {
  for ($i=0; $i < count($inv); ++$i)
  {
    if ($inv[$i]['society']==0)
    {
      $found = 1;
      if ($inv[$i]['istatus']<=0)
      {
        if ($itm[0] != '*' && $itm[0] != 'Terangreal')
        {
          if (strtolower($inv[$i][base]) != strtolower($itm[0])) $found=0;
        }
        if ($found && $itm[1] != '*')
        {
          if (strtolower($inv[$i][prefix]) != strtolower($itm[1])) $found=0;
        }
        if ($found && $itm[2] != '*')
        {
          if (strtolower($inv[$i][suffix]) != strtolower($itm[2])) $found=0;
        }
      }
      else $found=0;
      if ($type >=0 && $type < 12)
      {
        if ($inv[$i][type] != $type) $found=0;
      }
      elseif ($type == 12)
      {
        if ($inv[$i][type] != $type && $inv[$i][type] != ($type+1)) $found=0;
      }    
      if ($found) $count++;
    }
  }
  }
  return $count;
}

function take_quest_items ($goal, $inventory)
{
  $goals = unserialize($goal);
  $itm = $goals[1];
  $inv = unserialize($inventory);
  $count= 0;
  $listsize = count($inv);
  for ($i=0; $i < $listsize; ++$i)
  {
    $found = 1;
    if ($inv[$i][istatus] <= 0)
    {
      if ($itm[0] != '*')
      {
        if (strtolower($inv[$i][base]) != strtolower($itm[0])) $found=0;
      }
      if ($found && $itm[1] != '*')
      {
        if (strtolower($inv[$i][prefix]) != strtolower($itm[1])) $found=0;
      }
      if ($found && $itm[2] != '*')
      {
        if (strtolower($inv[$i][suffix]) != strtolower($itm[2])) $found=0;
      }
    }
    else $found = 0;
    if ($found && $count < $goals[0])
    {
      $result = mysql_query("DELETE FROM Items WHERE id='".$inv[$i][id]."'");
      $count++;
    }
  }

  $rval = serialize($inv);
  return $rval;
}

// $users - array of all characters currently in quest town
// $loc = location
function generate_random_quest($loc,$qalign="")
{
  global $map_data, $item_type_pl, $horde_types, $npc_plural, $loc_npc_nations, $npc_nation_names, $npc_nation_data, $enemy_list, $quest_type_num;
  
  $type = intval(rand(0,4));
  $started = time()/3600;
  $expire = $started + intval(rand(48,72));
  $num_avail = -1;
  $num_done = 0;

  $tnamelist='';
  $ncount=0;
  for ($i=0; $i < count($loc_npc_nations[$loc]); $i++)
  {
    for ($j=0; $j < count($npc_nation_names[$loc_npc_nations[$loc][$i]]); $j++)
    {
      $tnamelist[$ncount++]= $npc_nation_names[$loc_npc_nations[$loc][$i]][$j];
    }
  }
  $tnid=intval(rand(0,$ncount-1));
  $range =0;
  $nat = '';
  for ($i=0; $i < count($loc_npc_nations[$loc]); $i++)
  {
    $range+=count($npc_nation_names[$loc_npc_nations[$loc][$i]]);
    if ($nat == '' && $tnid < $range)
    {
      $nat=$loc_npc_nations[$loc][$i];
    }
  }
  $special[0] = $tnamelist[$tnid];
  $special[1] = $nat;
  $special[2] = $npc_nation_data[$nat][0][intval(rand(0,2))];
  $special[3] = $npc_nation_data[$nat][1][intval(rand(0,(count($npc_nation_data[$nat][1])-1)))];
  $special[4] = $npc_nation_data[$nat][2][intval(rand(0,(count($npc_nation_data[$nat][2])-1)))];
  
  if ($type == $quest_type_num["Items"])
  {
    $goals[0] = intval(rand(1,5));
    
    $ritem = intval(rand(1,10));
    if ($ritem == 8) $ritem = 11;
    $itype = array("T",$ritem);
    $prefix = "*";
    $suffix = "*";

    $goals[1]= array($itype,$prefix,$suffix);
    $goals[2] = $loc;
    $reward[0] = "GP";
    $reward[1] = 110;
    
    $qname = $tnamelist[$tnid]." in ".$loc." Wants ".ucwords($item_type_pl[$itype[1]]);    
  }
  else if ($type == $quest_type_num["NPC"])
  {
    $mult = intval(rand(1,5));
    $goals[0] = $mult*5;
    $enum = intval(rand(0,100));
    if ($enum < 35) { $eid = 0; }
    elseif ($enum < 60) { $eid = 1; }
    elseif ($enum < 75) { $eid = 2; }
    elseif ($enum < 90) { $eid = 3; }
    else { $eid = 4; }

    $goals[1] = $eid;
    $surrounding_area = $map_data[$loc];
    $goals[2] = $surrounding_area[intval(rand(0,3))];
    
    $reward[0] = "LI";
    $rtype = rand(1,11);
    if ($rtype == 8) $rtype = 12;
    if ($rtype == 11) $rtype = 13;
    $reward[1]= $rtype;
    $reward[2] = intval((($eid+$mult)*5)+25);
    $qname = "Attacks on ".$tnamelist[$tnid]."-ap-s ".$special[2];    
  }
  else if ($type == $quest_type_num["Find"])
  {
    $goals[0] = 1;
    $enum = intval(rand(0,100));
    if ($enum < 35) { $eid = 0; }
    elseif ($enum < 60) { $eid = 1; }
    elseif ($enum < 75) { $eid = 2; }
    elseif ($enum < 90) { $eid = 3; }
    else { $eid = 4; }

    $goals[1] = $eid;
    $surrounding_area = $map_data[$loc];
    $goals[2] = $surrounding_area[intval(rand(0,3))];
    
    $reward[0] = "LI";
    $rtype = rand(1,11);
    if ($rtype == 8) $rtype = 12;
    if ($rtype == 11) $rtype = 13;
    $reward[1]= $rtype;
    $reward[2] = intval((($eid+5)*5)+25);
    $qname = "Find ".$special[0]."-ap-s ".$enemy_list[$goals[2]][$goals[1]];  
  }
  else if ($type == $quest_type_num["Horde"])
  {
    $goals[0] = rand(2,6)*5;
    $enum = intval(rand(0,100));
    if ($enum < 35) { $eid = 0; }
    elseif ($enum < 60) { $eid = 1; }
    elseif ($enum < 75) { $eid = 2; }
    elseif ($enum < 90) { $eid = 3; }
    else { $eid = 4; }

    $goals[1] = $eid;
    $surrounding_area = $map_data[$loc];
    $goals[2] = $surrounding_area[intval(rand(0,3))]; 

    $reward[0] = "LI";
    $rtype = rand(1,11);
    if ($rtype == 8) $rtype = 12;
    if ($rtype == 11) $rtype = 13;
    $reward[1]= $rtype;
    $reward[2] = intval((($eid+5)*5)+25);
    $ename = $enemy_list[$goals[2]][$goals[1]];
    $gloc = str_replace("-ap-", "'", str_replace("_", " ", $goals[2]));
    $qname = "A ".$horde_types[$ename]." of ".$npc_plural[$ename]." in ".$gloc;  
  }
  else if ($type == $quest_type_num["Escort"])
  {
    $goals[0] = rand(1,5)*6;
    $route = mysql_fetch_array(mysql_query("SELECT * FROM Routes WHERE start='".$loc."' AND length >'".$goals[0]."' ORDER BY rand()"));
    $goals[1] = $route[id];
    $path = unserialize($route[path]);
    $goals[2] = $path[$goals[0]];

    $reward[0] = "LG";
    $reward[1] = 20*$goals[0];
    $gloc = str_replace("-ap-", "'", str_replace("_", " ", $goals[2]));
    $qname = "Escort ".$special[0]." to ".$gloc;
  }

  $align=rand(0,3);
  if ($qalign != "") $align = $qalign;
  if ($align==3) $align=0;
  
  $quest[name]=$qname;
  $quest[type]=$type;
  $quest[location]= $loc;
  $quest[offerer] = $special[3]." ".$special[0]." ".$special[4];
  $quest[num_avail]= $num_avail;
  $quest[started]= $started;
  $quest[expire]=$expire;
  $quest[align]=$align;
  $quest[goals]=serialize($goals);
  $quest[reward]=serialize($reward);
  $quest[special]=serialize($special);
  $quest[reqs]=0;

  return serialize($quest);
}


function getQuestInfo ($quest, $goodevil=0)
{
  global $quest_type, $item_base, $item_type, $item_type_pl, $enemy_list, $npc_nation_data;
  
  $qtype = $quest_type[$quest[type]];
  if ($quest[expire] != -1)
  {
    $expire = ($quest[expire]*3600) - time();
    if ($expire < 3600) $extime= number_format($expire/60)." minutes";
    elseif ($expire < 86400) $extime = number_format($expire/3600)." hours";
    else $extime = number_format($expire/86400)." days";
  }
  if ($quest[reqs] != "0")
  {
    $reqs=cparse($quest[reqs],0);
    $show_reqs=  show_quest_reqs($reqs);
  }
  else 
  {
    $show_reqs = "None";
  }
  $goals = unserialize($quest['goals']);
  $show_goals=  show_quest_goals($quest[align],$goals,$quest['type'], $quest['offerer'], $quest['special']);
  $qreward= unserialize($quest[reward]);
  if ($qreward[0]=="G")
  { $reward = displayGold($qreward[1]); }
  elseif ($qreward[0] == "LI")
  { $reward = ucfirst($item_type[$qreward[1]]); }
  elseif ($qreward[0] == "LG")
  { $reward = "Leveled Coin"; }
  elseif ($qreward[0] == "I")
  {
    $item = explode("|",$qreward[1]);
    $reward = ucwords($item[1])." ".ucwords($item[0])." ".ucwords($item[2]);
  }
  elseif ($qreward[0] == "GP")
  {
    $reward = $qreward[1]."% Value";
  }
  
  $questInfo = "";

  $questInfo .= "Type: ".$qtype."<br/>";
  if ($quest['offerer'] != '') 
  {
    $questInfo .= "Offered By: ".str_replace("_", " ", $quest['offerer'])."<br/>";
  }
  $questInfo .= "From: ".$quest['location']."<br/>";
  if ($quest['num_avail'] > 0) 
  {
    $questInfo .= "Available: ".($quest['num_avail']-$quest['num_done'])."<br/>";
  }
  if ($quest[expire] != -1)
  {
    $questInfo .= "Expires: ".$extime."<br/>";
  }

  $questInfo .= "Reward: ".$reward."<br/>";
  $questInfo .= "<br/>";
  $questInfo .= "<p><i>".$show_goals."</i></p>";
  
  return $questInfo;
}
?>