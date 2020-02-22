<?php

/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/itemFuncs.php");
include_once("admin/locFuncs.php");
include_once("admin/questFuncs.php");
include_once('map/mapdata/coordinates.inc');

$wikilink = "Quests";

$time=time();

// grab all non-equipped items
$id = $char[id];
$itmlist="";
$listsize=0;
$iresult=mysql_query("SELECT * FROM Items WHERE owner='$id' AND type<15 AND istatus <=0");
while ($qitem = mysql_fetch_array($iresult))
{
  $itmlist[$listsize++] = $qitem;
}

$myquests= unserialize($char[quests]);
$isCL = 1;
$isCO = 1;
$getitems = 0;
$ustats = mysql_fetch_array(mysql_query("SELECT * FROM Users_stats WHERE id='$id'"));

$submitted= mysql_real_escape_string($_POST['sub']);
$subtype= mysql_real_escape_string($_POST['subtype']);
$tab = mysql_real_escape_string($_POST['tab']);
$action = mysql_real_escape_string($_POST['action']);
$nq = mysql_real_escape_string($_POST['nq']);
$nqname=mysql_real_escape_string($_POST['nqname']);
$nqtype=mysql_real_escape_string($_POST['nqtype']);
$nqoffers=mysql_real_escape_string($_POST['nqoffers']);
$nqtotji=mysql_real_escape_string($_POST['nqtotji']);
$nqsupsm=mysql_real_escape_string($_POST['nqsupsm']);
$nqitem_pre=mysql_real_escape_string($_POST['nqitem_pre']);   
$nqitem_base=mysql_real_escape_string($_POST['nqitem_base']);   
$nqitem_suf=mysql_real_escape_string($_POST['nqitem_suf']);    
$nqtarget = mysql_real_escape_string($_POST['nqtarget']);
$nqtarget1 = mysql_real_escape_string($_POST['nqtarget1']);
$nqtarget2 = mysql_real_escape_string($_POST['nqtarget2']); 
$nqtarnum = mysql_real_escape_string($_POST['nqtarnum']);
$nqreward = mysql_real_escape_string($_POST['nqreward']);
$nqcopper = mysql_real_escape_string($_POST['nqcopper']);
$nqsilver = mysql_real_escape_string($_POST['nqsilver']);
$nqgold = mysql_real_escape_string($_POST['nqgold']);
$nqitem = mysql_real_escape_string($_POST['nqitem']);
$exq = mysql_real_escape_string($_POST['exq']);

$goodevil=$char['goodevil'];
if ($goodevil >1) $goodevil= 1;

$soc_name = $char[society];
$society = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$soc_name' "));
$subleaders = unserialize($society['subleaders']);
$subs = $society['subs'];
$offices = unserialize($society['offices']);
$b = 0; 

// leader
if (strtolower($name) == strtolower($society[leader]) && strtolower($lastname) == strtolower($society[leaderlast]) ) 
{
  $b=1;
}
// subleader
if ($subs > 0)
{
  foreach ($subleaders as $c_n => $c_s)
  {
    if (strtolower($name) == strtolower($c_s[0]) && strtolower($lastname) == strtolower($c_s[1]) )
    {
      $b=1;
    }
  }
}
// local officer
if ($offices[$location[id]][0] != '1')
{
  if (strtolower($name) == strtolower($offices[$location[id]][0]) && strtolower($lastname) == strtolower($offices[$location[id]][1]) ) 
  {
    $b=1;  
  }
}

// Is clan leader?
if ($b==0)
{
  $isCL = 0;
}
// Is clan office?
if (count($offices[$location[id]])==0)
{
  $isCO = 0;
}

// Set your quest to expire (OBE?)
if ($exq != '')
{
  $quest = mysql_fetch_array(mysql_query("SELECT * FROM Quests WHERE id='$exq'"));
  $qofferer = $char[name]." ".$char[lastname];
  
  if ($qofferer == $quest['offerer'])
  {
    if ($quest[expire] == -1)
    {
      $extime = (time()/3600)+12;
      mysql_query("UPDATE Quests SET expire='$extime' WHERE id='$exq'");
      $message = "Quest set to expire in 12 hours.";
    }
    else
    {
      $message = "That quest is already set to expire!";
    }
  }
  else
  {
    $message = "That's not your quest to mess with!";
  }
} 

// Create a new quest
if ($nq != '')
{
  $nqgood = 1;
  $nqofferer = $char[name]." ".$char[lastname];
  $itms = "";
  $query = "SELECT * FROM Quests WHERE offerer='$nqofferer' AND done='0'";
  $numquests = mysql_num_rows(mysql_query($query)); 
  
  if (!$nqname)
  {
    $nqgood = 0;
    $message = "You need to give your quest a name!";
  }
  elseif (!$nqreward)
  {
    $nqgood = 0;
    $message = "You need to choose a reward type!";
  }
  elseif ($numquests >= $maxquests)
  {
    $nqgood = 0;
    $message = "You already have the maximum number of quests active.";
  }
  else
  {
    $nqstart = time()/3600;
    $nqlocation = $char[location];
    $nqgoals[0]=$nqtarnum;

    // Prevent 'bad' inputs from user
    $nqname = htmlspecialchars(stripslashes($nqname),ENT_QUOTES);
    $nqtarget = htmlspecialchars(stripslashes($nqtarget),ENT_QUOTES);
    $nqtarget1 = htmlspecialchars(stripslashes($nqtarget1),ENT_QUOTES);
    $nqtarget2 = htmlspecialchars(stripslashes($nqtarget2),ENT_QUOTES);
    
    if ($nqtype == $quest_type_num["Request"]) // player item quest
    {
      if (($item_base[$nqitem_base][1]>11 || $nqitem_base== "Terangreal") && $nqitem_pre == '' && $nqitem_suf == '')
      {
        $nqgood=0;
        $message = "You must select a more specific Ter'angreal!";
      }
      else
      {
        $nqgoals[1]=array($nqitem_base,$nqitem_pre,$nqitem_suf);
      }
    }
    elseif ($nqtype == $quest_type_num["Bounty"]) // Player quest
    {
      $query1 = "SELECT * FROM Users WHERE name = '$nqtarget1' AND lastname = '$nqtarget2'";
      $result5 = mysql_query($query1);
      $target = mysql_fetch_array($result5); 
      if ($target)   
        $nqgoals[1]= $nqtarget1."_".$nqtarget2;  
      else
      {
        $nqgood = 0;
        $message = "How is anyone supposed to complete a quest to defeat someone who does not exist? ".$nqtarget1." ".$nqtarget2;
      }
    }
    elseif ($nqtype == $quest_type_num["Clan"]) // Clan quest
    {
      $query1 = "SELECT * FROM Soc WHERE name = '$nqtarget'";
      $result5 = mysql_query($query1);
      $target = mysql_fetch_array($result5); 
      if ($target)    
        $nqgoals[1]= $nqtarget;  
      else
      {
        $nqgood = 0;
        $message = "How is anyone supposed to complete a quest to defeat a clan that does not exist?";      
      }
    }
    elseif ($nqtype == $quest_type_num["Support"]) // Clan quest
    {
      $nqgoals[0]= 0;
      $nqgoals[1]= $nqtotji;
      $nqoffers = -1;
      $nqofferer = $char[society];
    }

    $nqgoals[2]=$nqlocation;
    $nqgoal = serialize($nqgoals);
    if ($nqtype == $quest_type_num["Support"]) // Clan quest
    {
      $sup_num = mysql_num_rows(mysql_query("SELECT id FROM `Quests` WHERE offerer = 'fafoiadsfojadfaf' and type = '21' and done = '0'"));
      if ($sup_num >=4)
      {
        $nqgood = 0;
        $message = "You must wait until your other support quests complete! $sup_num";
      }
      $totcoin = $nqtotji * $nqsupsm * 100;
      if ($society[bank] < $totcoin) 
      {
        $nqgood = 0;
        $message = "Your clan doesn't have that much money!";
      }
      $nqrewarded[0] = "GJ";
      $nqrewarded[1] = $nqsupsm * 100;
      $nqrewards = serialize($nqrewarded);
      $society[bank] -= $totcoin;
      
    }
    elseif ($nqreward == 1) // coin reward
    {
      $coin = ($nqgold*10000+$nqsilver*100+$nqcopper)*$nqoffers;
      if ($char[gold] < $coin)
      {
        $nqgood = 0;
        $message = "You don't have that much money!";
      }
      $nqrewarded[0]= "G";
      $nqrewarded[1]= $coin/$nqoffers;
      $nqrewards = serialize($nqrewarded);
      $char[gold] -= $coin;
      $ustats[quest_earn] -= $coin;
    }
    else // Item reward
    {
      $nqrewarded[0] = "I";
      $nqrewarded[1] = $nqitem;
      $nqrewards = serialize($nqrewarded);
      $itms[0] = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE id='$nqitem'"));      
    }
  }
  
  if ($nqgood)
  {
    $ustats[quests_created]++;
    $expire = time()/3600+168;
    if ($itms != "")
    {
      for ($x=0; $x<count($itms); $x++)
      {
        $result = mysql_query("UPDATE Items SET owner='50000', last_moved='".time()."' WHERE id='".$itms[$x][id]."'");
      }
    }

    mysql_query("UPDATE Users SET gold='$char[gold]' WHERE id=".$char[id]);
    mysql_query("UPDATE Users_stats SET quest_earn='".$ustats[quest_earn]."', quests_created='".$ustats[quests_created]."' WHERE id='".$char[id]."'");
    $sql = "INSERT INTO Quests (name,      type,      offerer,      num_avail,   num_done, started,    expire,    align, location,      reqs, goals,     special, reward,      done) 
                        VALUES ('$nqname', '$nqtype', '$nqofferer', '$nqoffers', 0,        '$nqstart', '$expire', 0,     '$nqlocation', 0,    '$nqgoal', 0,       '$nqrewards','0')";
    if ($society[id]) {mysql_query("UPDATE Soc SET bank='$society[bank]' WHERE id=".$society[id]);}
    $resultt = mysql_query($sql, $db);
    $message = "Quest Created!";
  }                        
}

if ($action) // Items quests Part 2
{
  $quest = mysql_fetch_array(mysql_query("SELECT * FROM Quests WHERE id='$action'")); 
  $goals = unserialize($quest[goals]);      
  $good = 1;
  $z=0;
  $x=0;
  $value=0;
  $q=0;
  $inv='';

  $itmlist="";
  $listsize=0;
  $typeq = "";
  if ($quest[type]==$quest_type_num["Items"]) $typeq = " AND type='".$goals[1][0][1]."'";

  $iresult=mysql_query("SELECT * FROM Items WHERE owner='$id'".$typeq." AND istatus <=0 AND society=0 ORDER BY id");
  while ($qitem = mysql_fetch_array($iresult))
  {
    $itmlist[$listsize++] = $qitem;
  } 

  while ($x < $listsize) // Get submitted items
  {
    $tmpItm = mysql_real_escape_string($_POST[$x]);
    if ($tmpItm)
    {
      $inv[$q]=mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE id='".$tmpItm."'"));
      if ($inv[$q][type] > 13) $value += intval($item_base[$inv[$q][base]][2]*1.1);
      else $value += intval(item_val(iparse($inv[$q],$item_base, $item_ix,$ter_bonuses))*1.1);
      $rlist[$q] = $inv[$q];
      $q++;
    }
    $x++;
  }

  $qreward= unserialize($quest[reward]);
  if ($qreward[0]=="G") // Set Coin Reward
  {
    $rvalue = $qreward[1]; 
  }
  elseif($qreward[0]=="GP") // Coin percent Reward (OBE?)
  {
    $rvalue = intval($value*$qreward[1]/100);
  }
  $ritem=0;
  if ($qreward[0]=="I") // Item Reward
  {
     $ritem = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE id='".$qreward[1]."'"));  
  }
  if ($quest[type]==$quest_type_num["Items"]) // Items quest
  {
    $coinup=$value;
    $itm = array('*','*','*');
    $nfound = check_inventory_items($itm, $inv, $goals[1][0][1],$item_base);
  }
  elseif ($quest[type]==$quest_type_num["Request"]) // Player Item quest
  {
    $coinup=$rvalue;
    $itm = $goals[1];
    $nfound = check_inventory_items($itm, $inv);  
  }
  if ($nfound != $q || $q != $goals[0])  // Check number of items submitted
  {
    $good = 0;
    $message = "You didn't submit the right number of valid items!";
  }
  if ($myquests[$action][0] == 2) // quest completed
  {
    $good = 0;
    $message = "You already submitted that quest!";
  }
  $scoreup=0;
  if ($quest[type] == $quest_type_num["Items"]) // Items quest
  {
    $scoreup = $goals[0]/2;  
  }
  if ($good && $q > 0) // All is good
  {
    if ($scoreup) // Did this quest add Ji?
    {
      if ($society[id]) 
      {
        $area_rep = unserialize($society[area_rep]);
        $area_rep[$qloc[id]] += $scoreup;
        $arep = $area_rep[$qloc[id]];
        $scoreup=(1+$arep/1000)*$scoreup;
        $area_reps = serialize($area_rep);
        mysql_query("UPDATE Soc SET area_rep='".$area_reps."' WHERE id='".$society[id]."'");
      }
      $qloc = mysql_fetch_array(mysql_query("SELECT * FROM `Locations` WHERE name = '$quest[location]'"));
      if ($myquests) 
      {
        foreach ($myquests as $c_n => $c_s)
        {
          if ($c_s[0] == 1)
          {
            $quest2 = mysql_fetch_array(mysql_query("SELECT * FROM Quests WHERE id='$c_n'")); 
            $goals = unserialize($quest2[goals]);
            $qreward= unserialize($quest2[reward]);
            if ($quest2[type] == $quest_type_num["Support"] && $goals[2] == $quest[location] && $soc_name != $quest2[offerer] && $quest2[done]==0) 
            {
              $society = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='".$quest2[offerer]."' "));
              
              $greward = $scoreup * $qreward[1];
              $stats[quest_earn] += $greward;
              $stats[support_quest_ji] += $scoreup;
              mysql_query("UPDATE Users SET gold=gold+($greward) WHERE id='".$char[id]."'");
              mysql_query("UPDATE Users_stats SET quest_earn='".$stats[quest_earn]."', support_quest_ji='".$stats[support_quest_ji]."' WHERE id='".$char[id]."'");

              $goals[0] += $scoreup;
              $qdone = 0;
              if ($goals[0] >= $goals[1]) { $qdone = 1;}
              $sgoals = serialize($goals);
              mysql_query("UPDATE Quests SET goals='$sgoals', done='$qdone' WHERE id='$quest2[id]'");
              break;
            }
          }
        }
      }       

      $area_score = unserialize($society['area_score']);
      $area_score[$qloc[id]] = $area_score[$qloc[id]]+$scoreup;
      $area_score[$qloc[id]] = number_format($area_score[$qloc[id]],2,'.','');
      $clan_id = $society[id];

      $a_s_str = serialize($area_score);
      mysql_query("UPDATE Soc SET score=score+".$scoreup." WHERE name='$char[society]' ");
      mysql_query("UPDATE Soc SET area_score='$a_s_str' WHERE id='$clan_id' ");
      // Reset society
      $society = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$soc_name' "));
      
      $ustats['loc_ji'.$qloc[id]]+=$scoreup;
      $ustats['loc_ji'.$qloc[id]]=number_format($ustats['loc_ji'.$qloc[id]],2,'.','');
      $result = mysql_query("UPDATE Users_stats SET loc_ji".$qloc[id]."='".$ustats['loc_ji'.$qloc[id]]."' WHERE id='$id'");
      if ($quest[align] !=0)
      {
        $alignUp = 0;
        if ($quest[align]==1) 
        {
          $char[align] += $scoreup;
          $alignUp += $scoreup;
          if ($society[id]) $society[align] += $scoreup;
        }
        else
        {
          $char[align] -= $scoreup;
          $alignUp -= $scoreup;
          if ($society[id]) $society[align] -= $scoreup;
        }
        
        mysql_query("UPDATE Users SET align='".$char[align]."' WHERE id='".$char[id]."' ");
        if ($society[id]) mysql_query("UPDATE Soc SET align= align + ".$alignUp." WHERE name='$char[society]' ");
      }
    }

    if ($quest[type] == $quest_type_num["Request"]) // Player Item quest
    {
      $offerername = explode(" ", $quest[offerer]);
      $charo = mysql_fetch_array(mysql_query("SELECT id FROM Users WHERE name='$offerername[0]' AND lastname='$offerername[1]'"));
      $ostats = mysql_fetch_array(mysql_query("SELECT my_quests_done FROM Users_stats WHERE id='$charo[id]'"));
      $ostats[my_quests_done]++;
      $ustats[play_quests_done]++;
      $ustats[quests_done]++;
      for ($i = 0; $i < count($inv); $i++)
      {
         $result = mysql_query("UPDATE Items SET owner='".$charo[id]."', last_moved='".time()."' WHERE id='".$inv[$i][id]."'");
      }
    } 
    else // Items quest
    {
      for ($i = 0; $i < count($inv); $i++)
      {
         $result = mysql_query("DELETE FROM Items WHERE id='".$inv[$i][id]."'");
      }
      $my_iQ = $town_bonuses[iQ];
      if ($my_iQ)
      {
        $bonusGold = $char[level]*$my_iQ;
        $char[gold] += $bonusGold;
        $ustats[quest_earn] += $bonusGold;
      }
      if ($town_bonuses[iJ])
      {
        $ustats[ji] += $town_bonuses[iJ];
      }
      
      $ustats[item_quests_done]++;
      $ustats[quests_done]++;
      $ustats[ji] += 5;
    } 
    $s = "";
    if ($q >1) $s ="s";
    $chargold = $coinup + $char[gold];
    if ($ritem)
    {
       $result = mysql_query("UPDATE Items SET owner='".$char[id]."', last_moved='".time()."' WHERE id='".$ritem[id]."'");
       $message="Item$s submitted. You earned a".iname($ritem);     
    }
    else 
    {
       $message="Item$s submitted. You earned ".displayGold($coinup);
    }
    $ustats[quest_earn] += $value;
    $ndone = $quest[num_done]+1;
    if ($quest[num_avail] > 0 && $ndone >= $quest[num_avail]) $qdone=1; else $qdone=0;
    $myquests[$action][0] = 2;
    $smyq= serialize($myquests);
    mysql_query("UPDATE Quests SET num_done='$ndone', done='$qdone' WHERE id='$action'"); 
    mysql_query("UPDATE Users LEFT JOIN Users_data ON Users.id=Users_data.id SET Users.gold='".$chargold."', Users_data.quests='$smyq' WHERE Users.id=".$char[id]);    
    mysql_query("UPDATE Users_stats SET quest_earn='".$ustats[quest_earn]."', ji='".$ustats[ji]."', quests_done='".$ustats[quests_done]."', play_quests_done='".$ustats[play_quests_done]."', item_quests_done='".$ustats[item_quests_done]."' WHERE id='".$char[id]."'");
    mysql_query("UPDATE Users_stats SET my_quests_done='".$ostats[my_quests_done]."' WHERE id='".$charo[id]."'");
  }
} // End of Item quest part 2 action

if ($subtype == $quest_type_num["Items"] || $subtype == $quest_type_num["Request"])  // Setup for Item Quest submitting
{
  $getitems = 1;
  $quest = mysql_fetch_array(mysql_query("SELECT * FROM Quests WHERE id='$submitted'")); 
  $goals = unserialize($quest[goals]);
  if ($subtype == $quest_type_num["Items"])
  {  
    $itm_name=$item_type_pl[$goals[1][0][1]];
  }
  else
  {
    $itm_name=$goals[1][0];
  }
  $message = "Select the ".$goals[0]." ".$itm_name." to submit";
}
elseif ($submitted != '') // Non-Item Quests
{
  $good = 1;
  $scoreup = 0;
  $charo = '';
  $quest = mysql_fetch_array(mysql_query("SELECT * FROM Quests WHERE id='$submitted'")); 
  $goals = unserialize($quest[goals]);      
                  
  if ($quest[type] == $quest_type_num["Horde"])
  {
    $percomp = 100 - floor(($myquests[$submitted][1]/$myquests[$submitted][2])*100);
  }
  else if ($quest[type] == $quest_type_num["Tutorial"])
  {
    $totalg = count($goals);
    $comped = 0;
    foreach ($goals as $achieve => $alvl)
    {
      if ($myAchieve[$achieve] >= $alvl)
      {
        $comped++;
      }
    }
    $percomp = floor(($comped/$totalg)*100);
  }
  else
  {
    $percomp = floor(($myquests[$submitted][1]/$goals[0])*100);
  }
  if ($percomp >= 100)  // Are you done
  {  
    if (($quest[type] == $quest_type_num["Items"] && $goals[2] == $char[location]) || $quest[type] != $quest_type_num["Items"]) // Right place to submit?
    {
      if ($myquests[$submitted][0] != 2) // Already done?
      {
        if ($quest[type] == $quest_type_num["NPC"]) // NPC quest
        {
          $scoreup = $goals[0]/5 + $goals[1];  
        }
        elseif ($quest[type] == $quest_type_num["Find"] || $quest[type] == $quest_type_num["Horde"]) // Find & Horde quest
        {
          $scoreup = 5 + $goals[1];  
        }
        elseif ($quest[type] == $quest_type_num["Escort"])
        {
          $scoreup = $goals[0]/8;
        }
        $qreward= unserialize($quest[reward]);
        if ($qreward[0]=="G") // Coin reward
        {
          $char[gold] += $qreward[1]; 
          $ustats[quest_earn] += $qreward[1];
        }
        elseif ($qreward[0]=="LG") // Leveled Coin reward
        {
          $char[gold] += 10*$goals[0]*$char[level]; 
          $ustats[quest_earn] += 10*$goals[0]*$char[level];
        }
        elseif ($qreward[0] == "I") // Item reward
        {
          $itmlist='';
          $listsize=0;
          $iresult=mysql_query("SELECT * FROM Items WHERE owner='$id' AND type<15");
          while ($qitem = mysql_fetch_array($iresult))
          {
            $itmlist[$listsize++] = $qitem;
          }
          if ($listsize < $inv_max) 
          {
            $ritem = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE id='".$qreward[1]."'"));  
            $result = mysql_query("UPDATE Items SET owner='".$char[id]."', last_moved='".time()."' WHERE id='".$ritem[id]."'");
            $message = "Quest submitted. You earned a ".iname($ritem).".";        
          } 
          else
          {   
            $good = 0;
            $message = "You have no room in your inventory for your reward.";
          }
        }
        elseif ($qreward[0] == "LI") // Leveled item reward
        {
          $type = $qreward[1];
          $percentage = $qreward[2];
          $prefix='';
          $suffix='';        
          
          if ($type < 12) // Non-ter'angreal
          {  
            $points = $char['equip_pts']*$percentage/100;
            $lvl = 0;
            for ($i=0; $i< count($item_list[$type]); ++$i)
            {
              if (lvl_req($item_base[$item_list[$type][$i]][0],100) <= $points)
              $lvl = $i;
            }
            $base= $item_list[$type][$lvl];      
          }
          else // Ter'angreal
          {
            $ttype = rand(12,13);
            $tnum = rand(0,count($item_list[$ttype])-1);
            $base = $item_list[$ttype][$tnum];
            $prefix = '';
            $suffix = ''; 
            $lvl=intval(($char[level]/3+2)*$percentage/100)+1;
            if ($lvl > 21) $lvl = 21;
            $ttype= rand(1,3);
            if ($ttype == 1 || $ttype == 3)
            {
              $prefix = $tali_list[$lvl][rand(0,3)];
            }
            if ($ttype == 2 || $ttype == 3)
            {
              $suffix = $tali_list[$lvl][rand(4,7)];
            }
          }
          
          // Add item to inventory
          $itmlist='';
          $listsize=0;
          $iresult=mysql_query("SELECT * FROM Items WHERE owner='$id' AND type<15");
          while ($qitem = mysql_fetch_array($iresult))
          {
            $itmlist[$listsize++] = $qitem;
          }        
          if ($listsize < $inv_max) 
          { 
            $itype=$item_base[$base][1];
            $istats= itp($item_base[$base][0]." ".$item_ix[$prefix]." ".$item_ix[$suffix],$item_base[$base][1]);
            $istats .= getTerMod($ter_bonuses,$itype,$prefix,$suffix,$item_base[$base][2]);
            $ipts= lvl_req($istats,100);
            $itime=time();
            $result = mysql_query("INSERT INTO Items (owner,          type,    cond, istatus,points, society,last_moved,base,   prefix,  suffix,   stats) 
                                              VALUES ('".$char[id]."','$itype','100','0',    '$ipts','0',    '$itime',  '$base','$prefix','$suffix','$istats')");
                                              
            $name = ucwords($prefix);
            if ($name) $name .= " ";
            $name .= ucwords($base);
            if ($suffix) $name .= " ".str_replace("Of","of",ucwords($suffix));
            $message = "Quest submitted. You earned a ".$name.".";
          } 
          else
          {  
            $good = 0;
            $message = "You have no room in your inventory for your reward.";
          }        
        }

        if ($good) // Are we good?
        {
          if ($scoreup) // Quest adds Ji
          {
            if ($society[id]) 
            {
              $area_rep = unserialize($society[area_rep]);
              $area_rep[$qloc[id]] += $scoreup;
              if ($area_rep[$qloc[id]] > 500) $area_rep[$qloc[id]] = 500;
              else if ($area_rep[$qloc[id]]< -500) $area_rep[$qloc[id]] = -500;
              $arep = $area_rep[$qloc[id]];
              $scoreup=(1+$arep/1000)*$scoreup;
              $area_reps = serialize($area_rep);
              mysql_query("UPDATE Soc SET area_rep='".$area_reps."' WHERE id='".$society[id]."'");
            }
            $clan_id = $society[id];
            $qloc_name = $quest[location];
            $qloc = mysql_fetch_array(mysql_query("SELECT * FROM `Locations` WHERE name = '$qloc_name'"));

            if ($myquests) 
            {
              foreach ($myquests as $c_n => $c_s)
              {
                if ($c_s[0] == 1)
                {
                  $quest2 = mysql_fetch_array(mysql_query("SELECT * FROM Quests WHERE id='$c_n'")); 
                  $goals = unserialize($quest2[goals]);
                  $qreward= unserialize($quest2[reward]);
                  if ($quest2[type] == $quest_type_num["Support"] && $goals[2] == $quest[location] && $soc_name != $quest2[offerer] && $quest2[done]==0) 
                  {
                    $society = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='".$quest2[offerer]."' "));
              
                    $greward = $scoreup * $qreward[1];
                    $stats[quest_earn] += $greward;
                    $stats[support_quest_ji] += $scoreup;
                    mysql_query("UPDATE Users SET gold=gold+($greward) WHERE id='".$char[id]."'");
                    mysql_query("UPDATE Users_stats SET quest_earn='".$stats[quest_earn]."', support_quest_ji='".$stats[support_quest_ji]."' WHERE id='".$char[id]."'");

                    $goals[0] += $scoreup;
                    $qdone = 0;
                    if ($goals[0] >= $goals[1]) { $qdone = 1;}
                    $sgoals = serialize($goals);
                    mysql_query("UPDATE Quests SET goals='$sgoals', done='$qdone' WHERE id='$quest2[id]'");
                    break;
                  }
                }
              }
            }
             
            $area_score = unserialize($society['area_score']);

            if ($quest[type] == $quest_type_num["Escort"])
            {
              $qloc_name2 = $goals[2];
              echo $qloc_name2;
              $qloc2 = mysql_fetch_array(mysql_query("SELECT * FROM `Locations` WHERE name = '$qloc_name2'"));
              $area_score[$qloc2[id]] = $area_score[$qloc2[id]]+$scoreup;
              $area_score[$qloc2[id]] = number_format($area_score[$qloc2[id]],2,'.','');
              if ($qloc2[next_wins] <= $area_score[$qloc2[id]])
              {
                while ($qloc2[next_wins] <= $area_score[$qloc2[id]])
                {
                  $qloc2[next_wins] += 100;
                }
                mysql_query("UPDATE Locations SET next_wins='".$qloc2[next_wins]."', ruler='".$society[name]."' WHERE id='$qloc2[id]'");
              }
            }
            $area_score[$qloc[id]] = $area_score[$qloc[id]]+$scoreup;
            $area_score[$qloc[id]] = number_format($area_score[$qloc[id]],2,'.','');
            if ($qloc[next_wins] <= $area_score[$qloc[id]])
            {
              while ($qloc[next_wins] <= $area_score[$qloc[id]])
              {
                $qloc[next_wins] += 100;
              }
              mysql_query("UPDATE Locations SET next_wins='".$qloc[next_wins]."', ruler='".$society[name]."' WHERE id='$qloc[id]'");
            }
            $a_s_str = serialize($area_score);
            mysql_query("UPDATE Soc SET score=score+1 WHERE name='$char[society]' ");
            mysql_query("UPDATE Soc SET area_score='$a_s_str' WHERE id='$society[id]' ");
            // Reset society
            $society = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$soc_name' "));
                  
            $locJiId= 'loc_ji'.$qloc[id];
            $ustats[$locJiId]+=$scoreup;
            $ustats[$locJiId]=number_format($ustats[$locJiId],2,'.','');
            $result = mysql_query("UPDATE Users_stats SET loc_ji".$qloc[id]."='".$ustats[$locJiId]."' WHERE id='$id'");
            
            if ($quest[type] == $quest_type_num["Escort"])
            {
              $locJiId= 'loc_ji'.$qloc2[id];
              $ustats[$locJiId]+=$scoreup;
              $ustats[$locJiId]=number_format($ustats[$locJiId],2,'.','');
              $result = mysql_query("UPDATE Users_stats SET loc_ji".$qloc2[id]."='".$ustats[$locJiId]."' WHERE id='$id'");
            }
            if ($quest[align] !=0)
            {
              $alignUp = 0;
              if ($quest[align]==1) 
              {
                $char[align] += $scoreup;
                $alignUp += $scoreup;
                if ($society[id]) $society[align] += $scoreup;
              }
              else
              {
                $char[align] -= $scoreup;
                $alignUp -= $scoreup;
                if ($society[id]) $society[align] -= $scoreup;
              }
        
              mysql_query("UPDATE Users SET align='".$char[align]."' WHERE id='".$char[id]."' ");
              if ($society[id]) mysql_query("UPDATE Soc SET align= align + ".$alignUp." WHERE id = '".$society[id]."' ");
            }            
          }

          // update quest stats
          $ndone = $quest[num_done]+1;
          if ($quest[num_avail] > 0 && $ndone >= $quest[num_avail]) $qdone=1; else $qdone=0;
          $myquests[$submitted][0] = 2;
          $tgoals = unserialize($quest[goals]);
          
          // check for estates
          $est_bonus=array();
          $myEstate= mysql_fetch_array(mysql_query("SELECT * FROM Estates WHERE owner='$id' AND location='$tgoals[2]'"));
          if ($myEstate[id])
          {
            $eups=unserialize($myEstate[upgrades]);
            $est_bonus = cparse(getUpgradeBonuses($eups, $estate_ups, 9));
          }
          
          if ($quest[type] == $quest_type_num["NPC"]) // NPC quest
          {
            $qdiff = $tgoals[0]/5 + $tgoals[1];
            $my_nQ = $pro_stats[nQ]+$town_bonuses[nQ];
            if ($my_nQ)
            {
              $bonusGold = $char[level]*$my_nQ;
              $char[gold] += $bonusGold;
              $ustats[quest_earn] += $bonusGold;
            }
            if ($est_bonus[zQ])
            {
              $estgold = 500*$qdiff*($myEstate[level]+1)*$est_bonus[zQ];
              $char[gold] += $estgold;
              $ustats[quest_earn] += $estgold;
            }
            if ($town_bonuses[eJ])
            {
              $ustats[ji] += $town_bonuses[eJ];
            }
           
            $ustats[npc_quests_done]++;
            $ustats[quests_done]++;
            $ustats[ji] += $qdiff;
          }
          elseif ($quest[type] == $quest_type_num["Find"]) // Find quest
          {
            $qdiff = $tgoals[1]+5;
            $my_fQ = $pro_stats[fQ]+$town_bonuses[fQ];
            if ($my_fQ)
            {
              $bonusGold = $char[level]*$my_fQ;
              $char[gold] += $bonusGold;
              $ustats[quest_earn] += $bonusGold;
            }
            if ($est_bonus[zQ])
            {
              $estgold = 500*$qdiff*($myEstate[level]+1)*$est_bonus[zQ];
              $char[gold] += $estgold;
              $ustats[quest_earn] += $estgold;
            }
            if ($town_bonuses[fJ])
            {
              $ustats[ji] += $town_bonuses[fJ];
            }
            
            $ustats[find_quests_done]++;
            $ustats[quests_done]++;
            $ustats[ji] += $qdiff;
          }
          elseif ($quest[type] == $quest_type_num["Horde"]) // Horde
          {
            $qdiff = $tgoals[1]+5;
            $my_hQ = $pro_stats[hQ]+$town_bonuses[hQ];
            if ($my_hQ)
            {
              $bonusGold = $char[level]*$my_hQ;
              $char[gold] += $bonusGold;
              $ustats[quest_earn] += $bonusGold;
            }
            if ($est_bonus[zQ])
            {
              $estgold = 500*$qdiff*($myEstate[level]+1)*$est_bonus[zQ];
              $char[gold] += $estgold;
              $ustats[quest_earn] += $estgold;
            }
            if ($town_bonuses[hJ])
            {
              $ustats[ji] += $town_bonuses[hJ];
            }
            
            $ustats[horde_quests_done]++;
            $ustats[quests_done]++;
            $ustats[ji] += $qdiff;
          }
          elseif ($quest[type] == $quest_type_num["Escort"]) // Escort
          {
            $qdiff = $tgoals[0]/3;
            $my_tQ = $pro_stats[tQ]+$town_bonuses[tQ];
            if ($my_tQ)
            {
              $bonusGold = $char[level]*$my_tQ;
              $char[gold] += $bonusGold;
              $ustats[quest_earn] += $bonusGold;
            }
            if ($est_bonus[zQ])
            {
              $estgold = 500*$qdiff*($myEstate[level]+1)*$est_bonus[zQ];
              $char[gold] += $estgold;
              $ustats[quest_earn] += $estgold;
            }
            if ($town_bonuses[tJ])
            {
              $ustats[ji] += $town_bonuses[tJ];
            }
            
            $ustats[escort_quests_done]++;
            $ustats[quests_done]++;
            $ustats[ji] += $qdiff;
          }
          elseif ($quest[type] == $quest_type_num["Tutorial"]) // Escort
          {
            $ustats[quests_done]++;
          }
          else // Player/Clan quest
          {
            $my_pQ = $town_bonuses[pQ];
            if ($my_pQ)
            {
              $bonusGold = $char[level]*$my_pQ;
              $char[gold] += $bonusGold;
              $ustats[quest_earn] += $bonusGold;
            }
            if ($town_bonuses[pJ])
            {
              $ustats[ji] += $town_bonuses[pJ];
            }
              
            $offerername = explode(" ", $quest[offerer]);
            $charo = mysql_fetch_array(mysql_query("SELECT * FROM Users WHERE name='$offerername[0]' AND lastname='$offerername[1]'"));
            $ostats = mysql_fetch_array(mysql_query("SELECT * FROM Users_stats WHERE id='$charo[id]'"));
            $ostats[my_quests_done]++;        
            $ustats[play_quests_done]++;
            $ustats[quests_done]++;
            mysql_query("UPDATE Users_stats SET my_quests_done='".$ostats[my_quests_done]."' WHERE id='".$charo[id]."'");
          }
          $smyq= serialize($myquests);
          mysql_query("UPDATE Quests SET num_done='$ndone', done='$qdone' WHERE id='$submitted'"); 
          mysql_query("UPDATE Users_stats SET quest_earn='".$ustats[quest_earn]."', ji='".$ustats[ji]."', quests_done='".$ustats[quests_done]."', play_quests_done='".$ustats[play_quests_done]."', npc_quests_done='".$ustats[npc_quests_done]."', find_quests_done='".$ustats[find_quests_done]."', horde_quests_done='".$ustats[horde_quests_done]."', escort_quests_done='".$ustats[escort_quests_done]."' WHERE id='".$char[id]."'");
          mysql_query("UPDATE Users LEFT JOIN Users_data ON Users.id=Users_data.id SET Users.gold='$char[gold]', Users_data.quests='$smyq' WHERE Users.id=".$char[id]);
        }
      } else $message = "You already submitted that quest!"; 
    } else $message = "You must be in the correct city to submit this quest!";
  } else $message = "You haven't met all the goals for that quest yet";
} 

?>
<script type="text/javascript">
  var qinfo = new Array();

<?php
  $result = mysql_query("SELECT * FROM Quests");
  $y=0;
  while ($quest = mysql_fetch_array( $result ) )
  {
    $qid = $quest[id];
    $nqofferer = $char[name]." ".$char[lastname];
    
    if ($myquests[$qid][0]==1 || $quest[offerer] == $nqofferer)
    {
      echo str_replace('-ap-','&#39;',"qinfo[$qid] = \"<FIELDSET class=abox><LEGEND><b>".$quest[name]."</b></LEGEND><center><br/><br/>\" + ");
      echo str_replace('-ap-','&#39;',"\"".getQuestInfo($quest, $goodevil)."\" + ");
      echo str_replace('-ap-','&#39;',"\"<br/><br/></center></FIELDSET>\";\n");
    }
  }
  
  
  $query = "SELECT * FROM Quests WHERE offerer='".$char[name]." ".$char[lastname]."' && done='0'";
  $result = mysql_query($query);
  $y=0;
  while ($quest = mysql_fetch_array( $result ) )
  { 
    $qid = $quest[id];
     
    {
      echo str_replace('-ap-','&#39;',"qinfo[$qid] = \"<FIELDSET class=abox><LEGEND><b>".$quest[name]."</b></LEGEND><center><br/><br/>\" + ");
      echo str_replace('-ap-','&#39;',"\"".getQuestInfo($quest, $goodevil)."\" + ");
      echo str_replace('-ap-','&#39;',"\"<br/><br/></center></FIELDSET>\";\n");
    }
  }  
?>
function swapinfo(myQuest)
{
  document.getElementById('queststats').innerHTML=qinfo[myQuest];
}

function swapinfo2(myQuest)
{
  document.getElementById('myqueststats').innerHTML=qinfo[myQuest];
}

</script>
<?php

if ($message == '') $message = "My Quests";

//HEADER
if (!$tab) $tab=1;
include('header.htm');

if (!$getitems) // Main Quest page
{
$itmlist="";
$listsize=0;

$iresult=mysql_query("SELECT * FROM Items WHERE owner='".$id."' AND type<15 AND istatus <=0");

while ($qitem = mysql_fetch_array($iresult))
{
  $itmlist[$listsize++] = $qitem;
}

?>
  <div class="row solid-back">
    <div class="col-sm-12">
      <div id="content">
        <ul id="myqtabs" class="nav nav-tabs" data-tabs="tabs">
          <li <?php if ($tab == 1) echo "class='active'";?>><a href="#active_tab" data-toggle="tab">Active Quests</a></li>
          <li <?php if ($tab == 2) echo "class='active'";?>><a href="#myq_tab" data-toggle="tab">My Quests</a></li>
        <?php 
          if ($location_array[$char['location']][2])
          {
        ?> 
          <li <?php if ($tab == 3) echo "class='active'";?>><a href="#create_tab" data-toggle="tab">Create a Quests</a></li>         
        <?php 
          }
        ?>
          <li <?php if ($tab == 4) echo "class='active'";?>><a href="#tut_tab" data-toggle="tab">Tutorial Quests</a></li>
        </ul>

        <div class="tab-content">
          <div class="tab-pane <?php if ($tab == 1) echo 'active';?>" id="active_tab">
            <form name="questForm" action="myquests.php" method="post">
              <input type="hidden" name="sub" value="0"/>
              <input type="hidden" name="subtype" value="0"/> 
              <table class='table table-responsive table-condensed table-clear table-hover table-striped small'>
                <tr>
                  <th>Quest</th>
                  <th>Type</th>
                  <th>Location</th>
                  <th>% Complete</th>   
                  <th>Expire</th> 
                  <th>&nbsp;</th>       
                </tr>
              <?php
                $curTime = intval(time()/3600);
                $query = "SELECT * FROM Quests WHERE expire >= '".$curTime."' ORDER BY expire ASC";
                $result = mysql_query($query);
                $y=0;
                while ($quest = mysql_fetch_array( $result ) )
                {
                  $qid = $quest[id];
                  $goals = unserialize($quest[goals]);      
       
                  if ($myquests[$qid][0]==1)
                  {
                    if (($quest[expire] != -1 && $quest[expire]*3600 < time()) || $quest[done]==1)
                    {
                      $myquests[$qid][0] = 0;
                      $smyq = serialize($myquests);
                      mysql_query("UPDATE Users LEFT JOIN Users_data ON Users.id=Users_data.id SET quests='$smyq' WHERE Users.id=".$char[id]);
                      $resultt = mysql_query("UPDATE Quests SET done='1' WHERE id='".$quest[id]."'");
                    }
                    else 
                    {
                      $iclass='warning';
                      if ($quest[align]==1) $iclass='primary';
                      else if ($quest[align]==2) $iclass='danger';
                      
                      $qinfo[$qid] = "<div class='panel panel-".$iclass."' style='width: 200px;'><div class='panel-heading'><h3 class='panel-title'>".str_replace('-ap-','&#39;',$quest[name])."</h3></div><div class='panel-body solid-back' align='center'>";
                      $qinfo[$qid] .= getQuestInfo($quest);
                      $qinfo[$qid] .= "</div></div>";
              ?>
                <tr>
                  <td class="popcenter">
                    <button type="button" class="btn btn-<?php echo $iclass; ?> btn-xs btn-block btn-wrap link-popover" data-toggle="popover" data-html="true" data-placement="bottom" data-content="<?php echo $qinfo[$qid];?>"><?php echo str_replace('-ap-','&#39;',$quest[name]); ?></button>
                  </td>
                <?php
                  $qtype = $quest_type[$quest[type]];
                ?>
                  <td align='center'><?php echo $qtype; ?></td> 
                  <td align='center'>
                <?php
                  if ($quest[type] == $quest_type_num["NPC"]
                   || $quest[type] == $quest_type_num["Find"]
                   || $quest[type] == $quest_type_num["Horde"])
                  {
                    echo $goals[2];
                  }
                  else if ($quest[type] == $quest_type_num["Escort"])
                  {
                    $route = mysql_fetch_array(mysql_query("SELECT * FROM Routes WHERE id='".$goals[1]."'"));
                    $rpath = unserialize($route[path]);
                    echo $rpath[$myquests[$qid][1]];
                  }
                  else
                  {
                    echo $quest[location]; 
                  }
                ?>
                  </td>
                <?php 
                  if ($quest[type] == $quest_type_num["Request"])
                  {
                    $itm = $goals[1];
                    $inv = $itmlist;
                    if ($goals[1][0] != "Terangreal")
                    {
                      $nfound = check_inventory_items($itm, $inv);
                    }
                    else
                    {
                      $nfound = check_inventory_items($itm, $inv, 12, $item_base);
                    }
                    $myquests[$qid][1] = $nfound;
                    $smyq = serialize($myquests);
                    mysql_query("UPDATE Users LEFT JOIN Users_data ON Users.id=Users_data.id SET quests='$smyq' WHERE Users.id=".$char[id]);
                  }
                  elseif ($quest[type] == $quest_type_num["Items"])
                  {
                    $itm = array('*','*','*');
                    $inv = $itmlist;
                    $nfound = check_inventory_items($itm, $inv,$goals[1][0][1],$item_base);
                    $myquests[$qid][1] = $nfound;
                    $smyq = serialize($myquests);
                    mysql_query("UPDATE Users LEFT JOIN Users_data ON Users.id=Users_data.id SET quests='$smyq' WHERE Users.id=".$char[id]);
                  }
                  if ($quest[type] == $quest_type_num["Horde"])
                  {
                    $percomp = 100 - floor(($myquests[$qid][1]/$myquests[$qid][2])*100);
                  }
                  else if ($quest[type] == $quest_type_num["Support"])
                  {
                    $percomp = floor(($goals[0]/$goals[1])*100);
                  }
                  else
                  {
                    $percomp = floor(($myquests[$qid][1]/$goals[0])*100);
                  }
                  if ($percomp > 100) $percomp = 100;
                ?>
                  <td align='center'><?php echo $percomp."%"; ?></td>
                  <td align='center'>
                <?php  
                  if ($quest[expire] != -1)
                  {
                    $expire = ($quest[expire]*3600) - time();
                    if ($expire < 3600) echo number_format($expire/60)." minutes";
                    elseif ($expire < 86400) echo number_format($expire/3600)." hours";
                    else echo number_format($expire/86400)." days";
                  }
                  else echo "&nbsp;";        
                ?>
                  </td>
                  <td align='center'>
                <?php
                  if ($percomp == 100)
                  {
                    if (($quest[type] == $quest_type_num["Items"] && $goals[2] == $char[location]) || $quest[type] != $quest_type_num["Items"])
                    {
                      $subLink = "javascript:submitQuestForm(".$quest[id].",".$quest[type].");";
                      echo "<input type='Button' name='submitter' value='Submit' onclick='".$subLink."' class='btn btn-success btn-xs'>";
                    }
                  }
                  else echo "&nbsp;";
                ?>
                  </td>
                </tr>
              <?php
                    }
                  }
                }
              ?>
              </table>
            </form>
          </div>
          <div class="tab-pane <?php if ($tab == 2) echo 'active';?>" id="myq_tab">
            <table class='table table-responsive table-condensed table-clear table-hover table-striped small'>
              <tr>
                <th>Quest</th>
                <th>Type</th>
                <th>Location</th>
                <th>Reward</th>
                <th>Expire</th> 
                <th>Avail</th>    
              </tr>
            <?php
              $y=0;
              $lMax= 1;
              if ($isCL) {$lMax = 2;}
              for ($l = 0; $l < $lMax; $l++) 
              {
                if ($l==0) { $result = mysql_query("SELECT * FROM Quests WHERE offerer='".$char[name]." ".$char[lastname]."' && done='0' ORDER BY expire ASC");}
                else { $result = mysql_query("SELECT * FROM Quests WHERE offerer='".$char[society]."' && done='0' ORDER BY expire ASC");}

                while ($quest = mysql_fetch_array( $result ) )
                {      
                  $qid = $quest[id]; 
                  $y++;        
                  $iclass='warning';
                  if ($quest[align]==1) $iclass='primary';
                  else if ($quest[align]==2) $iclass='danger';
                  
                  $qinfo[$qid] = "<div class='panel panel-".$iclass."' style='width: 200px;'><div class='panel-heading'><h3 class='panel-title'>".str_replace('-ap-','&#39;',$quest[name])."</h3></div><div class='panel-body solid-back' align='center'>";
                  $qinfo[$qid] .= getQuestInfo($quest);
                  $qinfo[$qid] .= "</div></div>";                
            ?>
              <tr>
                <td class="popcenter">
                  <button type="button" class="btn btn-<?php echo $iclass; ?> btn-xs btn-block btn-wrap link-popover" data-toggle="popover" data-html="true" data-placement="bottom" data-content="<?php echo $qinfo[$qid];?>"><?php echo str_replace('-ap-','&#39;',$quest[name]); ?></button>
                </td>
              <?php
                $qtype = $quest_type[$quest[type]];
              ?>
                <td align='center'><?php echo $qtype; ?></td>
                <td align='center'>
              <?php
                if ($quest[type] == $quest_type_num["Find"] || $quest[type] == $quest_type_num["NPC"]|| $quest[type] == $quest_type_num["Horde"])
                {
                  echo $goals[2];
                }
                else
                {
                  echo $quest[location]; 
                }
              ?>
                </td> 
              <?php
                if ($quest[num_avail] > 0)
                {
                  $qavail=$quest[num_avail]-$quest[num_done];
                }
                else $qavail="-";
              ?>   
                <td align='center'>
              <?php
                $qreward= unserialize($quest[reward]);
                if ($qreward[0]=="G")
                {  echo displayGold($qreward[1]); }
                elseif ($qreward[0] == "LI")
                { echo ucfirst($item_type[$qreward[1]]); }
                elseif ($qreward[0] == "I")
                {
                  $item = explode("|",$qreward[1]);
                  echo ucwords($item[1])." ".ucwords($item[0])." ".ucwords($item[2]);
                }
                elseif ($qreward[0]=="GP")
                { echo $qreward[1]."% Value"; }
                elseif ($qreward[0]=="GJ")
                { echo displayGold($qreward[1])." per Ji"; }
              ?>  
                </td>
                <td align='center'>
              <?php  
                if ($quest[expire] != -1)
                {
                  $expire = ($quest[expire]*3600) - time();
                  if ($expire < 3600) echo number_format($expire/60)." minutes";
                  elseif ($expire < 86400) echo number_format($expire/3600)." hours";
                  else echo number_format($expire/86400)." days";
                }
              ?>
                </td>
                <td align='center'><?php echo $qavail; ?></td>
              </tr>
            <?php
                }
              }
            ?>
            </table>
          </div>         
        <?php 
          if ($location_array[$char['location']][2])
          {
        ?> 
          <div class="tab-pane <?php if ($tab == 3) echo 'active';?>" id="create_tab">
            <form name='makequestform' method="post" action="myquests.php" class='form-horizontal' >
              <div class="form-group form-group-sm">
                <label for='nqname' class='control-label col-sm-2'>Quest Name: </label>
                <div class='col-sm-10'>
                  <input type='text' id='nqname' name='nqname' class="form-control gos-form"/>
                </div>
              </div>
              <div class="form-group form-group-sm">
                <label for='nqtype' class='control-label col-sm-2'>Quest Type: </label>
                <div class='col-sm-2'>
                  <select name='nqtype' id='nqtype' size='1' class="form-control gos-form" onchange="javascript:setType();"/>
                    <option value='-1'>-Select-</option>
                    <?php
                      echo "<option value='".$quest_type_num['Request']."'>Request</option>";
                      echo "<option value='".$quest_type_num['Bounty']."'>Bounty</option>";
                      if ($isCL || $isCO) 
                      { 
                        echo "<option value='".$quest_type_num['Clan']."'>Clan</option>"; 
                        echo "<option value='".$quest_type_num['Support']."'>Support</option>";
                      }
                    ?>
                  </select>
                </div>
                <div class="form-group form-group-sm col-sm-8"  id='nqoffersnum' name='nqoffersnum' style='display: none'>
                  <label for='nqoffers' class='control-label col-sm-3'>Offers: </label>
                  <div class='col-sm-3'>
                    <select name='nqoffers' id='nqoffers' size='1' class="form-control gos-form">
                    </select>
                  </div>
                  <label for='nqtarnum' class='control-label col-sm-3'>Target Number: </label>
                  <div class='col-sm-3'>
                    <select name='nqtarnum' id='nqtarnum' size='1' class="form-control gos-form">
                    </select>
                  </div>
                </div>
                <div class="form-group form-group-sm col-sm-8"  id='nqsupport' name='nqsupport' style='display: none'>
                  <label for='nqtotji' class='control-label col-sm-3'>Total Ji: </label>
                  <div class='col-sm-3'>
                    <select name='nqtotji' id='nqtotji' size='1' class="form-control gos-form">
                      <option value='50'>50</option>
                      <option value='100'>100</option>
                      <option value='150'>150</option>
                      <option value='200'>200</option>
                      <option value='250'>250</option>
                    </select>
                  </div>
                  <label for='supsm' class='control-label col-sm-3'>Silver per Ji: </label>
                  <div class='col-sm-3'>
                    <select name='nqsupsm' id='nqsupsm' size='1' class="form-control gos-form">
                      <option value='1'>1</option>
                      <option value='2'>2</option>
                      <option value='3'>3</option>
                      <option value='4'>4</option>
                      <option value='5'>5</option>
                      <option value='6'>6</option>
                      <option value='7'>7</option>
                      <option value='8'>8</option>
                      <option value='9'>9</option>
                      <option value='10'>10</option>
                    </select>
                  </div>
                </div>  
              </div>

              <div class="form-group form-group-sm"  id='nqitem_all' name='nqitem_all' style='display: none'>
                <label for='nqitem' class='control-label col-sm-2'>Target Item:</label>
                <div class='col-sm-3'>
                  <select class="form-control gos-form" id='nqitem_pre' name='nqitem_pre'>
                    <option value=''>None</option>
                    <?php 
                      foreach ($item_ix as $pname => $pdata)
                      {
                        $ptemp = explode(" ", $pname);
                        if (count($ptemp) == 1)
                          echo "<option value='".$pname."'>".ucwords($pname)."</option>";
                      }
                    ?>
                  </select>
                </div>
                <div class='col-sm-4'>  
                  <select class="form-control gos-form" id='nqitem_base' name='nqitem_base'>
                    <?php 
                      foreach ($item_base as $iname => $data)
                      {
                        if ($data[1] != 8 && $data[1] <14 && $data[1] > 0)
                          echo "<option value='".$iname."'>".ucwords($iname)."</option>";
                      }
                      echo "<option value='Terangreal'>".Terangreal."</option>";
                    ?>
                  </select>
                </div>
                <div class='col-sm-3'> 
                  <select class="form-control gos-form" id='nqitem_suf' name='nqitem_suf'>
                    <option value=''>None</option>
                    <?php 
                      foreach ($item_ix as $sname => $sdata)
                      {
                        $stemp = explode(" ", $sname);
                        if (count($stemp) > 1)
                          echo "<option value='".$sname."'>".ucwords($sname)."</option>";
                      }
                    ?>
                  </select>
                </div>
              </div>
              <div class="form-group form-group-sm" id='nqtarduel' name='nqtarduel' style='display: none'>                  
                <label for='nqtarduel' class='control-label col-sm-2'>Target Player:</label>
                <div class='col-sm-5'>
                  <input type='text' name='nqtarget1' class="form-control gos-form"/>
                </div>
                <div class='col-sm-5'>
                  <input type='text' name='nqtarget2' class="form-control gos-form"/>
                </div>
              </div>
              <div class="form-group form-group-sm" id='nqtarclan' name='nqtarclan' style='display: none'>
                <label for='nqtarclan' class='control-label col-sm-2'>Target Clan:</label>  
                <div class='col-sm-10'>                            
                  <input type='text' name='nqtarget' class="form-control gos-form"/>
                </div>
              </div>
              <div class="form-group form-group-sm" id='nqrewards' name='nqrewards' style='display: none'>
                <label for='nqtarnum' class='control-label col-sm-2'>Reward Type: </label>
                <div class='col-sm-2'>
                  <label class="radio-inline"><input type="radio" name="nqreward" id="nqreward" value="1" onchange="javascript:setRewards();" CHECKED/> Coin </label>
                </div>
                <div class='col-sm-1 hidden-xs hidden-sm hidden-md hidden-lg'>
                  <label class="radio-inline"><input type="radio" name="nqreward" id="nqreward" value="2" onchange="javascript:setRewards();"/> Item</label>
                </div>
                <label for='nqtarnum' class='control-label col-sm-2'>Reward: </label>
                <div class='col-sm-6'>
                  <div class='form-inline' align='left' id='coinreward' name='coinreward'> <!-- style='display: none'>-->
                    <img src='images/gold.gif'/>
                    <input type="text" name="nqgold" value="0" class="form-control gos-form" size="2" maxlength="2"/>
                    <img src='images/silver.gif'/>
                    <input type="text" name="nqsilver" value="0" class="form-control gos-form" size="2" maxlength="2"/>
                    <img src='images/copper.gif'/>
                    <input type="text" name="nqcopper" value="1" class="form-control gos-form" size="2" maxlength="2"/>
                  </div>
                  <div id='itemreward' name='itemreward' style='display: none'>              
                    <select name="nqitem" id="nqitem" size="1" class="form-control gos-form">
                    <?php
                      $have_item = 0;
                      for ($x=0; $x<$listsize; $x++) 
                      {
                        if (($itmlist[$x][istatus] == 0 || $itmlist[$x][istatus] == -2) && $itmlist[$x][society] == 0) 
                        {
                          echo "<option value='".$itmlist[$x][$id]."'>".iname($itmlist[$x])."</option>\n"; 
                          $have_item=1; 
                          if (!$f_item) $f_item=strtolower(str_replace(" ","",$itmlist[$x][0]));
                        }        
                      }
                      if (!$have_item) echo "<option value=''>No items</option>";
                    ?> 
                    </select>              
                  </div>
                </div>
              </div>
              <div class="form-group form-group-sm">
                <input type='hidden' name='nq' value='1'>
                <div class='col-sm-10 col-sm-push-2'>
                  <input type="Submit" name="submit" value="Create Quest" class="btn btn-sm btn-success">
                </div>
              </div>
            </form>
          </div>
        <?php
          }
        ?>
          <div class="tab-pane <?php if ($tab == 4) echo 'active';?>" id="tut_tab">
            <form name="tutorialForm" action="myquests.php" method="post">
              <input type="hidden" name="tutsub" value="0"/>
              <input type="hidden" name="tutsubtype" value="0"/> 
              <table class='table table-responsive table-condensed table-clear table-hover table-striped small'>
                <tr>
                  <th>Quest</th>
                  <th>Type</th>
                  <th>Location</th>
                  <th>% Complete</th>   
                  <th>&nbsp;</th>       
                </tr>
              <?php
                $myAchieve= unserialize($char[achieve]);
                $query = "SELECT * FROM Quests WHERE cat = '1' ORDER BY id ASC";
                $result = mysql_query($query);
                $y=0;
                while ($quest = mysql_fetch_array( $result ) )
                {
                  $qid = $quest[id];
                  $goals = unserialize($quest[goals]);
                  $reqs = unserialize($quest[reqs]);

                  if ($myquests[$qid][0]!=2 && check_quest_reqs($reqs, $char) == 1)
                  {
                    if ($myquests[$qid][0] != 1)
                    {
                      $myquests[$qid][0] = 1;
                      $myquests[$qid][1] = 0; 
                      if ($quest[type] == $quest_type_num["Find"])
                      {
                        $myquests[$qid][2] = rand(0,4);
                        $myquests[$qid][3] = rand(0,4);     
                      } else if ($quest[type] == $quest_type_num["Horde"])
                      {
                        $myquests[$qid][1] = $char[vitality]*$goals[0];
                        $myquests[$qid][2] = $char[vitality]*$goals[0];
                      } else if ($quest[type] == $quest_type_num["Escort"])
                      {
                        $myquests[$qid][2] = 0;
                      }
                      $myquests_ser = serialize($myquests);
                      mysql_query("UPDATE Users LEFT JOIN Users_data ON Users.id=Users_data.id SET quests='$myquests_ser' WHERE Users.id=".$char[id]);
                    }
                    $iclass='success';
                    
                    $qinfo[$qid] = "<div class='panel panel-".$iclass."' style='width: 200px;'><div class='panel-heading'><h3 class='panel-title'>".str_replace('-ap-','&#39;',$quest[name])."</h3></div><div class='panel-body solid-back' align='center'>";
                    $qinfo[$qid] .= getQuestInfo($quest);
                    $qinfo[$qid] .= "</div></div>";
              ?>
                <tr>
                  <td class="popcenter">
                    <button type="button" class="btn btn-<?php echo $iclass; ?> btn-xs btn-block btn-wrap link-popover" data-toggle="popover" data-html="true" data-placement="bottom" data-content="<?php echo $qinfo[$qid];?>"><?php echo str_replace('-ap-','&#39;',$quest[name]); ?></button>
                  </td>
                <?php
                  $qtype = $quest_type[$quest[type]];
                ?>
                  <td align='center'><?php echo $qtype; ?></td> 
                  <td align='center'>
                <?php
                  if ($quest[type] == $quest_type_num["NPC"]
                   || $quest[type] == $quest_type_num["Find"]
                   || $quest[type] == $quest_type_num["Horde"])
                  {
                    echo $goals[2];
                  }
                  else if ($quest[type] == $quest_type_num["Escort"])
                  {
                    $route = mysql_fetch_array(mysql_query("SELECT * FROM Routes WHERE id='".$goals[1]."'"));
                    $rpath = unserialize($route[path]);
                    echo $rpath[$myquests[$qid][1]];
                  }
                  else
                  {
                    echo $quest[location]; 
                  }
                ?>
                  </td>
                <?php 
                  if ($quest[type] == $quest_type_num["Items"])
                  {
                    $itm = array('*','*','*');
                    $inv = $itmlist;
                    $nfound = check_inventory_items($itm, $inv,$goals[1][0][1],$item_base);
                    $myquests[$qid][1] = $nfound;
                    $smyq = serialize($myquests);
                    mysql_query("UPDATE Users LEFT JOIN Users_data ON Users.id=Users_data.id SET quests='$smyq' WHERE Users.id=".$char[id]);
                  }
                  if ($quest[type] == $quest_type_num["Horde"])
                  {
                    $percomp = 100 - floor(($myquests[$qid][1]/$myquests[$qid][2])*100);
                  }
                  else if ($quest[type] == $quest_type_num["Tutorial"])
                  {
                    $totalg = count($goals);
                    $comped = 0;
                    foreach ($goals as $achieve => $alvl)
                    {
                      if ($myAchieve[$achieve] >= $alvl)
                      {
                        $comped++;
                      }
                    }
                    $percomp = floor(($comped/$totalg)*100);
                  }
                  else
                  {
                    $percomp = floor(($myquests[$qid][1]/$goals[0])*100);
                  }
                  if ($percomp > 100) $percomp = 100;
                ?>
                  <td align='center'><?php echo $percomp."%"; ?></td>
                  <td align='center'>
                <?php
                  if ($percomp == 100)
                  {
                    if (($quest[type] == $quest_type_num["Items"] && $goals[2] == $char[location]) || $quest[type] != $quest_type_num["Items"])
                    {
                      $subLink = "javascript:submitQuestForm(".$quest[id].",".$quest[type].");";
                      echo "<input type='Button' name='submitter' value='Submit' onclick='".$subLink."' class='btn btn-success btn-xs'>";
                    }
                  }
                  else echo "&nbsp;";
                ?>
                  </td>
                </tr>
              <?php
                  }
                }
              ?>
              </table>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script type='javascript'>
    window.onload=setType;
  </script>
<?php
}
else // Items Quests part 1
{
  include_once("admin/duelFuncs.php");
  include("admin/equipped.php");
?>
  <div class="row solid-back">
    <div class="col-sm-12">
      <form name="itemForm" action="myquests.php" method="post">
        <input type="hidden" name="sort" id="sort" value="" />
    <table class='table table-condensed table-hover table-clear small'>
      <tr>
        <th>&nbsp;</th>
	<th>Item</th>
	<th>Type</th>
	<th>Worth</th>	
	<th>Points</th>	
	<th>&nbsp;</th>
      </tr>  
<?php
      $draw_some = 0;

      $itmlist="";
      $listsize=0;
      $typeq = "";
      if ($subtype==$quest_type_num['Items']) $typeq = " AND type='".$goals[1][0][1]."'";

      $iresult=mysql_query("SELECT * FROM Items WHERE owner='".$id."'".$typeq." AND istatus <=0 AND society=0 ORDER BY id");
      while ($qitem = mysql_fetch_array($iresult))
      {
        $itmlist[$listsize++] = $qitem;
      }     
       
      for ($itemtoblit = 0; $itemtoblit < count($itmlist); $itemtoblit++)
      {
        if ($itmlist[$itemtoblit][istatus] == 0 || $itmlist[$itemtoblit][istatus] == -2)
        {
          // DRAW ITEM IN LIST
?>
          <tr>
<?php
          if ($itmlist[$itemtoblit][society] == 0)
          {
?>          
            <td width="20"><input type="checkbox" name="<?php echo $itemtoblit; ?>" value="<?php echo $itmlist[$itemtoblit][id];?>"></td>
<?php
          }
          else
            echo "<td>&nbsp;</td>";
          $iclass="btn-primary";
          if ($itmlist[$itemtoblit][istatus] == -2) $iclass = "btn-warning";                     
?>
            <td>
              <A class="btn btn-xs <?php echo $iclass;?> btn-block btn-wrap" HREF="javascript:popUp('itemstat.php?<?php echo "base=".$itmlist[$itemtoblit][base]."&prefix=".$itmlist[$itemtoblit][prefix]."&suffix=".$itmlist[$itemtoblit][suffix]."&lvl=".$char[level]."&gender=".$char[sex]."&cond=".$itmlist[$itemtoblit][cond]; ?>')"><?php echo iname($itmlist[$itemtoblit]); ?></A>
            </td>

<!-- DISPLAY ITEM TYPE -->
            <td align='center'>
<?php
              echo ucwords($item_type[$itmlist[$itemtoblit][type]]);
?>
            </td>

<!-- STATUS -->
            <td align='center'>
<?php
              if ($itmlist[$itemtoblit][type] > 13) $worth = $item_base[$itmlist[$itemtoblit][base]][2];
              else $worth = item_val(iparse($itmlist[$itemtoblit],$item_base, $item_ix,$ter_bonuses));
              echo displayGold($worth);
?>
            </td>
            <td align='center'>
<?php
              $weapon = iparse($itmlist[$itemtoblit],$item_base, $item_ix,$ter_bonuses);  
              $points = lvl_req($weapon, getTypeMod($skill_bonuses1,$itmlist[$itemtoblit][type]));
              echo $points."pts";
?>
            </td>            
            <td align='center'>&nbsp;</td>
          </tr>
<?php
          $draw_some = 1;
        }
      }

      if ($draw_some == 0) 
        echo "<tr><td colspan='7'><br><b>You are not carrying any items</b><br><br></td></tr>";
?>
      <tr>
        <td colspan='7' align=center>            
          <input type="hidden" name="action" value="0">
          <a data-href="javascript:submitFormItem(<?php echo $submitted;?>);" data-toggle="confirmation" data-placement="top" title="Submit selected items?" class="btn btn-success btn-sm btn-wrap">Submit for Quest</a>
        </td>
      </tr>

    </table>
      </form> 
    </div>   
  </div>

<?php
  }
?>
<script type='text/javascript'>
  var labels =new Array(3);
  labels[0]="Target Item";  
  labels[1]="Target Name";
  labels[2]="Target Clan";
  
  function submitQuestForm(sub,type)
  {
    document.questForm.sub.value= sub;
    document.questForm.subtype.value= type;    
    document.questForm.submit();
  }

  function expireQuestForm(ex)
  {
    document.myQuestForm.exq.value= ex;
    document.myQuestForm.submit();
  }
   
  function setTargetNumber()
  {
    var selElem = document.getElementById('nqtype');
    var index = selElem.selectedIndex; 
    var newElem = document.getElementById('nqtarnum');  
    var num = 5;
    if (index != 0) num = 20
    newElem.options.length = 0;
    for (var i=1; i<=num; i++) 
    {
      newElem.options[newElem.options.length] = new Option(i,i);
    } 
  }
  
  function setOffers()
  {
    var selElem = document.getElementById('nqtype');
    var index = selElem.selectedIndex; 
    var index2 = document.makequestform.nqreward[1].checked;     
    var newElem = document.getElementById('nqoffers');  
    var num = 5;
    if (index == 1) num = 10;
    else if (index == 2) num = 20;    
    if (index2) num = 1;  
    newElem.options.length = 0;  
    for (var i=1; i<=num; i++) 
    {
      newElem.options[newElem.options.length] = new Option(i,i);
    } 
  }
  
  function setType()
  {
    var selElem = document.getElementById('nqtype');
    var index = selElem.selectedIndex;   
    
    if (index == 1)
    {
      document.getElementById('nqitem_all').style.display = "block";
      document.getElementById('nqtarduel').style.display = "none";
      document.getElementById('nqtarclan').style.display = "none";  
      document.getElementById('nqoffersnum').style.display = "block";
      document.getElementById('nqsupport').style.display = "none";
      document.getElementById('nqrewards').style.display = "block"; 
    }
    else if (index == 2)
    {
      document.getElementById('nqitem_all').style.display = "none";
      document.getElementById('nqtarduel').style.display = "block";
      document.getElementById('nqtarclan').style.display = "none";
      document.getElementById('nqoffersnum').style.display = "block";
      document.getElementById('nqsupport').style.display = "none"; 
      document.getElementById('nqrewards').style.display = "block"; 
    }
    else if (index == 3)
    {
      document.getElementById('nqitem_all').style.display = "none";            
      document.getElementById('nqtarduel').style.display = "none";
      document.getElementById('nqtarclan').style.display = "block";
      document.getElementById('nqoffersnum').style.display = "block";
      document.getElementById('nqsupport').style.display = "none"; 
      document.getElementById('nqrewards').style.display = "block";   
    }
    else if (index == 4)
    {
      document.getElementById('nqitem_all').style.display = "none";            
      document.getElementById('nqtarduel').style.display = "none";
      document.getElementById('nqtarclan').style.display = "none";
      document.getElementById('nqoffersnum').style.display = "none";
      document.getElementById('nqsupport').style.display = "block"; 
      document.getElementById('nqrewards').style.display = "none";  
    }
    else
    {
      document.getElementById('nqitem_all').style.display = "none";            
      document.getElementById('nqtarduel').style.display = "none";
      document.getElementById('nqtarclan').style.display = "none";
      document.getElementById('nqoffersnum').style.display = "block";
      document.getElementById('nqsupport').style.display = "none"; 
      document.getElementById('nqrewards').style.display = "block"; 
      nqrewards
    }
    setOffers();
    setTargetNumber();
  }
  
  function setRewards()
  {
    var index = document.makequestform.nqreward[0].checked;  
    if (index)
    {
      document.getElementById('coinreward').style.display = "block";    
      document.getElementById('itemreward').style.display = "none";
    }
    else
    {
      document.getElementById('coinreward').style.display = "none";    
      document.getElementById('itemreward').style.display = "block";      
    }     
    setOffers(); 
  }
  
  function submitFormItem(act)
  {
    document.itemForm.action.value= act;
    document.itemForm.submit();
  }  
</script>
<?php
include('footer.htm');

?>