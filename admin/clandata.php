<?php
include_once("connect.php");
include_once('displayFuncs.php');
mysql_query("LOCK TABLES Users WRITE, Soc WRITE, Soc_stats WRITE; Locations WRITE, messages WRITE, Estates WRITE;");

// build base rep levels from estates for all clans
$result= mysql_query("SELECT * FROM Estates WHERE 1");
$esup = array();
while ($estate = mysql_fetch_array( $result ) )
{
  if ($estate[supporting] != "")
  {
    $supporting = unserialize($estate[supporting]);
    if (count($supporting))
    {
      foreach ($supporting as $loc => $clan) 
      {
        if (!$esup[$loc][$clan]) 
        {$esup[$loc][$clan]=0;}
        $esup[$loc][$clan]+= $estate[level];
      }
    }
  }
}

$result = mysql_query("SELECT * FROM Soc WHERE 1");
while ( $listsoc = mysql_fetch_array( $result ) )
{
  if (time()-($listsoc[lastupkeep]*3600) >= 3600 || 1)
  {
    $listsoc[lastupkeep] = intval(time()/3600);
    
    // Remove Inactive members based on clan settings
    if ($listsoc[inactivity] > 0)
    {
      $inactive_days = $listsoc[inactivity]*5;
      $delete_before = (time() - (86400*$inactive_days));
      $toRemove = mysql_query("SELECT * FROM Users WHERE society='".$listsoc[name]."' AND lastonline<'".$delete_before."' ");
      while ($rchar = mysql_fetch_array($toRemove))
      {
        //removeFromClan($rchar);
      }
    }
    
    // Update number of members
    $resultf = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM Users WHERE society='".$listsoc[name]."'"));
    $numchar = $resultf[0];
    if ($numchar!=$listsoc[members]) 
    {
      $listsoc[members] = $numchar;
      $cresult = mysql_query("UPDATE Soc SET members='$numchar' WHERE id='".$listsoc[id]."'");
    }
    $numpeople = $listsoc[members];    
    $upgrades = unserialize($listsoc['upgrades']);
    
    $upkeep=0;
    $max = 0;
    for ($i=0; $i<5; ++$i)
    {
      if ($upgrades[$i]>$upgrades[$max]) $max = $i;
      $upkeep += $upgrades[$i];
    }
    $upkeep = $upkeep*$numpeople;   
    
    if ($upkeep && ($listsoc[bank] < $upkeep)) 
    {
      $upgrades[$max]--;
      $listsoc[bank] += intval((pow(10,$upgrades[$max])*10000)/3);
    }
    $listsoc[bank] = $listsoc[bank]-$upkeep;
    $upgrades_str= serialize($upgrades);
    $num_ruled = mysql_num_rows(mysql_query("SELECT id FROM Locations WHERE ruler='".$listsoc[name]."' AND isDestroyed='0'"));
        
    mysql_query("UPDATE Soc SET upgrades='$upgrades_str', bank='$listsoc[bank]', lastupkeep='$check', ruled='$num_ruled' WHERE name='$listsoc[name]'");
    
    // Update clan rep
    echo $listsoc[name].":";
    $offices = unserialize($listsoc['offices']);
    $area_rep = unserialize($listsoc[area_rep]);
    $lresult = mysql_query("SELECT id, name FROM Locations WHERE 1");  
    while ($loc = mysql_fetch_array( $lresult ) )
    {
      $base = 0;
      $base += $esup[$loc[name]][$listsoc[id]]/2;
      if ($offices[$loc[id]]) $base += 100;
      
      $diff = $area_rep[$loc[id]]-$base;
      echo $loc[id].":".$area_rep[$loc[id]]."-".$base."=".$diff.">";
      $area_rep[$loc[id]] -= $diff/100;
      echo $area_rep[$loc[id]]."|";
    }
    $area_reps = serialize($area_rep);
    mysql_query("UPDATE Soc SET area_rep='".$area_reps."' WHERE id='".$listsoc[id]."'");
  }
}
mysql_query("UNLOCK TABLES;");

?>