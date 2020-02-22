<?php
$lastBattleDone = mysql_num_rows(mysql_query("SELECT id FROM Contests WHERE type='99' AND done='1'"));

mysql_query("LOCK TABLES Users WRITE, Users_stats WRITE, messages WRITE, Profs WRITE, Estate WRITE, Soc WRITE, Soc_stats WRITE;");
if (!$lastBattleDone)
{
  $heroes = "";
  $herids = "";
  
  // Update stats of all characters
  $result = mysql_query("SELECT * FROM Users LEFT JOIN Users_stats ON Users.id=Users_stats.id WHERE (Users.nation!='0')");
  while ( $listchar = mysql_fetch_array( $result ) )
  {
    $listchar[coin]=$listchar[gold];
    $listchar[xp]=$listchar[exp];
    $enum = 0;
    $elvl = 0;
    $tot_estate = 0;
    $estquery = mysql_query("SELECT id, owner, value, level FROM Estates WHERE owner='".$listchar[id]."' ORDER BY value DESC");

      while ($tmpEstate = mysql_fetch_array($estquery))
      {
        if (!$enum)
        {
          $listchar[top_estate]=$tmpEstate[value];
        }
        if ($tmpEstate[level] > $elvl) $elvl = $tmpEstate[level]; 
        $tot_estate += $tmpEstate[value];
        $enum++;
      }

    if ($enum) $elvl++;
    $listchar[num_estates]=$enum;
    $listchar[highest_estate]=$elvl;
    $listchar[tot_estate]=$tot_estate;
    $bnum = 0;
    $tot_business = 0;
    $bquery = mysql_query("SELECT id, owner, value FROM Profs WHERE owner='".$listchar[id]."' ORDER BY value DESC");
    while ($tmpBiz = mysql_fetch_array($bquery))
    {
      if (!$bnum)
      {
        $listchar[top_business]=$tmpBiz[value];
      }
      $tot_business += $tmpBiz[value];
      $bnum++;
    }
    $listchar[num_biz]=$bnum;
    $listchar[tot_business]=$tot_business;
    $listchar[net_worth] = $listchar[tot_business] + $listchar[tot_estate] + $listchar[coin] + $listchar[bankcoin];
    mysql_query("UPDATE Users_stats SET coin='".$listchar[coin]."', xp='".$listchar[xp]."', top_estate='".$listchar[top_estate]."', tot_estate='".$listchar[tot_estate]."', top_business='".$listchar[top_business]."', tot_business='".$listchar[tot_business]."', net_worth='".$listchar[net_worth]."', num_estates='".$listchar[num_estates]."', highest_estate='".$listchar[highest_estate]."' WHERE id='".$listchar[id]."'");
  }
    
  // loop over all ranks and deterime top 10. If LB is has started, don't update coin ranks.
  $cityRumors = mysql_fetch_array(mysql_query("SELECT * FROM messages WHERE id='50000'"));
  $hoursFromBreak = intval(((time()/3600)-$cityRumors[checktime]));
  $loops =count($rank_data);
  if ($cityRumors[checktime] != 0 && $hoursFromBreak >= 720) 
  { 
    $loops = $loops;
  }
  for ($y = 0; $y < $loops; $y++)
  {
    $rank_by=$rank_data[$y][0];
    $x=0;
    $result = mysql_query("SELECT * FROM Users LEFT JOIN Users_stats ON Users.id=Users_stats.id WHERE (Users.nation!='0') ORDER BY ".$rank_by." DESC, exp DESC LIMIT 0,10 ");
    $numchar = mysql_num_rows($result);
    while ( $listchar = mysql_fetch_array( $result ) )
    {
      $heroes[$x+1][$rank_by]=$listchar[$rank_by];
      $heroes[$x+11][$rank_by]=$listchar[id];
      $x++;
    }
  }
  // updated the database for the top 10 for each rank (10 for number and 10 for the id)
  if ($numchar > 0 )
  {
    for ($z=1; $z<=20; $z++)
    {
      mysql_query("UPDATE Users_stats SET xp='".$heroes[$z][xp]."', ji='".$heroes[$z][ji]."', achieved='".$heroes[$z][achieved]."', wins='".$heroes[$z][wins]."', duel_wins='".$heroes[$z][duel_wins]."', enemy_wins='".$heroes[$z][enemy_wins]."', ally_wins='".$heroes[$z][ally_wins]."', off_wins='".$heroes[$z][off_wins]."', npc_wins='".$heroes[$z][npc_wins]."', shadow_wins='".$heroes[$z][shadow_wins]."', military_wins='".$heroes[$z][military_wins]."', ruffian_wins='".$heroes[$z][ruffian_wins]."', channeler_wins='".$heroes[$z][channeler_wins]."', animal_wins='".$heroes[$z][animal_wins]."', exotic_wins='".$heroes[$z][exotic_wins]."', quests_done='".$heroes[$z][quests_done]."', play_quests_done='".$heroes[$z][play_quests_done]."', find_quests_done='".$heroes[$z][find_quests_done]."', npc_quests_done='".$heroes[$z][npc_quests_done]."', item_quests_done='".$heroes[$z][item_quests_done]."', horde_quests_done='".$heroes[$z][horde_quests_done]."', escort_quests_done='".$heroes[$z][escort_quests_done]."', my_quests_done='".$heroes[$z][my_quests_done]."', coin='".$heroes[$z][coin]."', bankcoin='".$heroes[$z][bankcoin]."', coin_donated='".$heroes[$z][coin_donated]."', duel_earn='".$heroes[$z][duel_earn]."', dice_earn='".$heroes[$z][dice_earn]."', item_earn='".$heroes[$z][item_earn]."', quest_earn='".$heroes[$z][quest_earn]."', prof_earn='".$heroes[$z][prof_earn]."', top_estate='".$heroes[$z][top_estate]."', tot_estate='".$heroes[$z][tot_estate]."', top_business='".$heroes[$z][top_business]."', tot_business='".$heroes[$z][tot_business]."', net_worth='".$heroes[$z][net_worth]."', align_high='".$heroes[$z][align_high]."', align_low='".$heroes[$z][align_low]."', win_tourney='".$heroes[$z][win_tourney]."', horde_wins='".$heroes[$z][horde_wins]."', army_wins='".$heroes[$z][army_wins]."' WHERE id='".($z+10000)."'");
    }
  }
}
mysql_query("UNLOCK TABLES;");

// Update Soc Stats
$msgs = mysql_fetch_array(mysql_query("SELECT * FROM messages WHERE id='0'"));
if ($msgs[checktime] < floor(time()/600) && !$lastBattleDone)
{
  for ($y = 0; $y < count($soc_rank_data); $y++)
  {

    $rank_by=$soc_rank_data[$y][0];
    $field = $rank_by;
    $orderBy = $rank_by." DESC";
    if (substr($rank_by, 0, 1) === "-") 
    { 
      $field = substr($rank_by, 1, strlen($rank_by));
      $orderBy =$field." ASC";
    }
    $x=0;
    $result = mysql_query("SELECT * FROM Soc WHERE 1 ORDER BY ".$orderBy.", id ASC LIMIT 0,10 ");
    $numsoc = mysql_num_rows($result);
    while ( $listsoc = mysql_fetch_array( $result ) )
    {
      $heroes[$x+1][$rank_by]=$listsoc[$field];
      $heroes[$x+11][$rank_by]=$listsoc[id];
      $x++;
    }
  }
  // updated the database for the top 10 for each rank
  if ($numsoc > 0 )
  {
    for ($z=1; $z<=10; $z++)
    {
      mysql_query("UPDATE Soc_stats SET mostJiNum='".$heroes[$z][score]."', mostJiId='".$heroes[$z+10][score]."', mostMembersNum='".$heroes[$z][members]."', mostMembersId='".$heroes[$z+10][members]."', mostRuledNum='".$heroes[$z][ruled]."', mostRuledId='".$heroes[$z+10][ruled]."', mostCoinNum='".$heroes[$z][bank]."', mostCoinId='".$heroes[$z+10][bank]."', highAlignNum='".$heroes[$z][align]."', highAlignId='".$heroes[$z+10][align]."', lowAlignNum='".(0-$heroes[$z]['-align'])."', lowAlignId='".$heroes[$z+10]['-align']."' WHERE id='".($z+10000)."'");
    }
  }
}

?>