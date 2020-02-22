<?php

/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/locFuncs.php");
include_once('map/mapdata/coordinates.inc');
$loc=$char['location'];
$no_query=1;
$message=mysql_real_escape_string($_REQUEST['message']);
$fromLoc=mysql_real_escape_string($_REQUEST['fromLoc']);
$toLoc=mysql_real_escape_string($_REQUEST['toLoc']);
$escortId=mysql_real_escape_string($_REQUEST['escortId']);
$waysId=mysql_real_escape_string($_REQUEST['waysId']);

$surrounding_area = $map_data[$loc]; 
$clean_loc = str_replace("&#39;", "", $char[location]);
if ($fromLoc == $clean_loc && (($toLoc >= 0 && $toLoc < 4) || $escortId > 0 || $waysId > 0)) 
{
  $result3 = mysql_query("SELECT * FROM Hordes WHERE done='0' AND location='$char[location]'");
  $numhorde = mysql_num_rows($result3);
  // SET TRAVELING
  if ($travel_mode[$char['travelmode']][1]<=$char['feedneed']) $char['travelmode']=0; // WALK IF HORSE IS TOO HUNGRY
  $newstamina = $char['stamina'];
  if ($char['travelmode']) $char['feedneed']++;
  else $newstamina--;
  if ($numhorde) $newstamina = $newstamina-2;
  if ($newstamina < 0) $newstamina = 0;

  if ($toLoc > -1)
  {
    $loc = $surrounding_area[$toLoc];
  }
  else if ($escortId > 0)
  { 
    $myquests= unserialize($char[quests]);
    $quest = mysql_fetch_array(mysql_query("SELECT * FROM Quests WHERE id='$escortId'"));
    $goals = unserialize($quest[goals]);
    $route = mysql_fetch_array(mysql_query("SELECT * FROM Routes WHERE id='".$goals[1]."'"));
    $rpath = unserialize($route[path]);
    $loc = $rpath[$myquests[$escortId][1]+1];
    $myquests[$escortId][1] += 1;
    $myquests[$escortId][2] = 0;
    $myquests2 = serialize($myquests);
    $char[quests] = $myquests2;
    mysql_query("UPDATE Users_data SET quests='".$myquests2."' WHERE id='$id'");
  }
  else if ($waysId > 0 && $waysId == $char[route])
  {
    $route = mysql_fetch_array(mysql_query("SELECT * FROM Routes WHERE id='".$waysId."'"));
    $rpath = unserialize($route[path]);
    $loc = $rpath[$char[routepoint]+1];
    $char[routepoint] = $char[routepoint]+1;
    if ($char[routepoint] >= $route[length]-1)
    {
      mysql_query("UPDATE Users SET route='0', routepoint='0' WHERE id='$id'");
      mysql_query("UPDATE Users_stats SET ways_use= ways_use + 1 WHERE id='$id'");
    }
    else
    {
      mysql_query("UPDATE Users SET routepoint='".$char[routepoint]."' WHERE id='$id'");
    }
  }
  
  if ($loc != $char[location])
  {
    $char[location] = $loc;
    mysql_query("UPDATE Users SET stamina='".$newstamina."', feedneed='".$char['feedneed']."', location='$loc' WHERE id='$char[id]'");
  }
}

$loc_name = $char['location'];
if ($location_array[$char['location']][2]) $wikilink = "Cities";
else $wikilink = "Wilderness+Areas";

include("map/places/banker.php");

if (!$message) $message = $loc_name;

include('header.htm');
?>

<?php
  $gop=0;
  $bg = "";
  if ($char['arrival']<=time() && $location_array[$char['location']][2]) 
  {
    $link1 = "town.php";
    $town_img_name = str_replace(' ','_',strtolower($char['location']));
    $town_img_name = str_replace('&#39;','',strtolower($town_img_name));
    if ($mode != 1) $bg = "background-image:url('images/townback/".$town_img_name.".jpg'); ";
  } 
  elseif ($char['arrival']<=time()) 
  {
    $link1 = "wild.php";
  }  
  else
  { 
    $gop =1;
    $link1 = "";

  }  
?>            
  <div class="row solid-back">
    <div class="col-sm-12">
      <div id="TownMap" name="TownMap" height="1000" marginwidth="0" marginheight="0" scrolling="no" frameborder="0" onload='adjustMyFrameHeight();'>
              <?php 
                if (!$gop) include ($link1); ?>
      </div>
    </div>
  </div>

<noscript>
Your browser will not currently run Javascript, which is required for this site.<br>I would strongly advise you turn it on and download the <a href="http://www.getfirefox.com">Firefox</a> web browser.
</noscript>

<?php
include('footer.htm');
?>