<?php
mysql_query("LOCK TABLES Users WRITE, Soc WRITE, Contests WRITE");
$cbs = (mysql_query("SELECT id, starts, contestants, results, participation FROM Contests WHERE type > 9 AND type != 99 AND starts <= $check AND done = 0"));
$check=intval(time()/3600);
while ($cb = mysql_fetch_array($cbs))
{
  echo "Updating hourly participation for CB $cb[id]...";
  $bhour = $check-$cb[starts];
  if ($bhour > 0)
  {
    $cb_clans = unserialize($cb[contestants]);
    $cb_results = unserialize($cb[results]);
    $cpart = unserialize($cb[participation]);
    // Loop over all the clans in the battles
    foreach ($cb_clans as $cid => $cdata)
    {
      if ($cid > 0)
      {
        // Add last hours participation points to the totals for the battle.
        if (!$cb_results[$cid]) $cb_results[$cid] = array(0);
        if ($cpart[$bhour][$cid] > 200) $cpart[$bhour][$cid]= 200; // Clans can only earn up to 200 points from participation an hour.
        $cb_results[$cid][3] += $cpart[$bhour][$cid];
        $cb_clans[$cid][1] = $cb_results[$cid][0]+$cb_results[$cid][1]+$cb_results[$cid][2]+$cb_results[$cid][3];
        if ($cb_results[$cid][2] > $cb_results[$cid][0]+$cb_results[$cid][1]+$cb_results[$cid][3]) { $cb_clans[$cid][1] = ($cb_results[$cid][0]+$cb_results[$cid][1]+$cb_results[$cid][3])*2; }
      }
    }
    
    $ccons = serialize($cb_clans);
    $cparts = serialize($cpart);
    $cresults = serialize($cb_results);
    mysql_query("UPDATE Contests SET contestants='".$ccons."', participation='".$cparts."', results='".$cresults."' WHERE id='$cb[id]'");
  }
}
mysql_query("UNLOCK TABLES;");

?>