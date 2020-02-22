<?php
  // Connect
  include_once("connect.php");
  include_once("mapData.php");
  include_once('locFuncs.php');
  
  //createRouteTable ();
  
  // Find shortest paths
  foreach ($loc_npc_nations as $loc => $value)
  {
    $found = "";
    $found[$loc] = 1;
    //generateShortestPaths($loc, 1, $found);
  }
  
  // Generate Random routes
  foreach ($unique_buildings as $loc => $value)
  {
    $count = 0;
    $basePath[$count] = $loc;
    $done = addConnection($basePath, $count);
  }

function generateShortestPaths($loc, $level, $found)
{
  global $map_data;
  
  if ($level == 1)
  {
    $path[0] = $loc;
  
    $found = genShortPaths($path, $found);
  }
  else
  {
    $lpaths = mysql_query("SELECT * FROM Routes WHERE type='1' AND start='".$loc."' AND length='".$level."'");
    while ($lpath = mysql_fetch_array($lpaths))
    {
      $found = genShortPaths(unserialize($lpath[path]), $found);
    }    
  }
  
  if (count($found) < 48 && $level < 10)
  {
    generateShortestPaths($loc, $level+1, $found);
  }
}
  
function genShortPaths($path, $found)
{
  global $map_data;
  $count = count($path);
  $surrounding_area = $map_data[$path[$count-1]];
  for ($i = 0; $i < 4; $i++)
  {
    $next = $surrounding_area[$i];
    $prevPaths = mysql_num_rows(mysql_query("SELECT * FROM Routes WHERE type='1' AND start='".$path[0]."' AND end='".$next."' AND length < ".$count));
    if ($prevPaths == 0)
    {
      $found[$surrounding_area[$i]] = 1;
      $path[$count] = $surrounding_area[$i];
      insertRoute($path, 1);        
    }
  }
  return $found;
}  
  
function addConnection ($curPath, $myCount)
{
  global $map_data;
  
  $added = 0;
  $found = 0;
  $surrounding_area = $map_data[$curPath[$myCount]];
  $r = rand(0,3);  
  for ($i = 0; $i < 4; $i++)
  {
    $l = ($r + $i)%4;
    
    if (($myCount < 6 || $found == 0) && inPath($curPath, $surrounding_area[$l]) == 0)
    {
      $newPath = $curPath;
      $newCount = $myCount + 1;
      $newPath[$newCount] = $surrounding_area[$l];
      
      $subAdded = addConnection($newPath, $newCount);
      if ($subAdded == 0)
      {
        printPath($newPath);
        insertRoute($newPath, 0);
        $found = 1;
        $added = 2;
      }
      else if ($subAdded == 1)
      {
        $added = 1;      
      }
      else if ($subAdded == 2)
      {
        $found =1;
        $added = 2;
      } 
    }
  } 
 
  return $added;
}

function inPath ($myPath, $newLoc)
{
  $matched = 0;
  for ($i=0; $i < count($myPath); $i++)
  {
    if ($myPath[$i] == $newLoc)
    {
      $matched = 1;
      $i = 100;
    }
  }
  
  return $matched;
}

function printPath ($myPath)
{
  echo count($myPath).": ";
  for ($i=0; $i < count($myPath); $i++)
  {
    echo $myPath[$i]."->";
  } 
  echo "<br/>"; 
}

function createRouteTable ()
{
  echo "<br><br>::ROUTE TABLE::<br><br>";
  // Drop Old Table
  $query  = 'DROP TABLE IF EXISTS Routes';
  $result = mysql_query($query);
  echo "Drop Old Table: $result";

  // Create New Table
  $query = "CREATE TABLE IF NOT EXISTS `Routes` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `start` char(30) DEFAULT NULL, 
    `end` char(30) DEFAULT NULL,
    `next` char(30) DEFAULT NULL,
    `type` int(11) NOT NULL,
    `length` int(11) NOT NULL, 
    `path` text,
    PRIMARY KEY (`id`)
  ) ENGINE=MyISAM  DEFAULT CHARSET=latin1";

  $result = mysql_query($query);
  echo "<br>Create New Table: $result";
}

function insertRoute ($path, $type)
{
  $mystart = $path[0];
  $mynext = $path[1];
  $mylength = count($path);
  $myend = $path[$mylength -1];
  $spath = serialize($path);

  mysql_query("INSERT INTO Routes (start,     end,     next,     type,   length,    path) 
                           VALUES ('$mystart','$myend','$mynext','$type','$mylength','$spath')");
}

?>  