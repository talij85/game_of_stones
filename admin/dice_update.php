<?php
mysql_query("LOCK TABLES Users WRITE, Locations WRITE, Estates WRITE, Users_stats WRITE;");
$result = mysql_query("SELECT * FROM Locations WHERE 1");
while ( $listloc = mysql_fetch_array( $result ) )
{
  if (!$listloc[isDestroyed])
  {
    // new dice game every 1 minutes
    if (time() - ($listloc[lastdice]*60) >= 60)
    {
      $listloc[lastdice] = intval(time()/60);
      if ($listloc[curr_dice]) $curr_dice = unserialize($listloc[curr_dice]);
      if (count($curr_dice) > 1)
      {
        updateDice($curr_dice, $listloc[wager], $listloc[gtype]);
        $listloc[prev_dice] = serialize($curr_dice);
        $listloc[old_wager]=$listloc[wager];
      }
      $newdice = '';
      for ($x=0; $x<5; $x++) 
      {
        $newdice[$x] = rand(1,6);
      }
      $next_dice['NPC'] = $newdice;
      $curr_dice = serialize($next_dice);
      $listloc[wager] = pow(10, rand($listloc[minw],$listloc[maxw]));
  
      mysql_query("UPDATE Locations SET curr_dice='$curr_dice', prev_dice='$listloc[prev_dice]', wager='$listloc[wager]', old_wager='$listloc[old_wager]', lastdice='$listloc[lastdice]' WHERE id='$listloc[id]'");
    }
  }
}
mysql_query("UNLOCK TABLES;");

?>