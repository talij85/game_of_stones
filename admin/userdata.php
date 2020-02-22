<?php
// load basic data
include_once("config.php");
include_once('displayFuncs.php');
include_once('busiFuncs.php');
include_once('charFuncs.php');
$char = mysql_fetch_array(mysql_query("SELECT * FROM Users LEFT JOIN Users_data ON Users.id=Users_data.id WHERE Users.id='$id'"));
$curtime=time();
$jobs= unserialize($char[jobs]);
$pro_stats=cparse(getAllJobBonuses($jobs));
$check=intval(time()/3600);

// MAX INVENTORY SIZE (ANY CHANGES ALSO NEED TO BE CHANGED IN THE 'map/places/blacksmith.php' SCRIPT)
$inv_max=$base_inv_max+5*$char['travelmode2']+$pro_stats[eS];
$pouch_max = 4+2*$char['pouch_type']+$pro_stats[cS];

$char[lastonline]= time();
mysql_query("UPDATE Users SET lastonline='$char[lastonline]' WHERE id='$id'");

// Darkfriends cannot have a positive alignment.
$types = unserialize($char['type']);

if ($types[0] == 5 && $char[align] > 0) 
{
  $char[align] = 0;
  mysql_query("UPDATE Users SET align='$char[align]' WHERE id='$id'");
}

$lastBattleDone = mysql_num_rows(mysql_query("SELECT id FROM Contests WHERE type='99' AND done='1'"));

$funtime = 1;
if ($char[donor]) 
{
  $battlelimit = $battlelimit*2*$funtime;
  $maxquests = $maxquests*2;
  $maxalts = $maxalts*2;
}
if ($funtime == 2) // Ironman rules
{
  $battlelimit = 400;
  $maxquests = $maxquests*2;
  $maxalts = 1;
  $maxsocalts += 1;
}

mysql_query("LOCK TABLES Users WRITE;");
if ($char[gold] < 0 || $char[gold] > $max_gold) // KEEP GOLD BELOW MAX
{
  if ($char[gold] < 0) mysql_query("UPDATE Users SET gold='0' WHERE id='$id'");
  else  mysql_query("UPDATE Users SET gold='$max_gold' WHERE id=$id");
}

if ($char['arrival']<=time() && $char['arrival']!=0) // ARRIVE AT TRAVEL TO PLACE
{
  include($_SERVER['DOCUMENT_ROOT']."/".$subfile."/map/mapdata/coordinates.inc");
  $char['location']=$char['travelto'];
  mysql_query("UPDATE Users SET location='".$char['travelto']."', arrival='0' WHERE id='$id'");
}
mysql_query("UNLOCK TABLES;");

// UPDATE USER ACHIEVEMENTS
mysql_query("LOCK TABLES Users WRITE, Users_data WRITE, Users_stats WRITE, Notes WRITE;");
$myAchieve= unserialize($char[achieve]);
$stats = mysql_fetch_array(mysql_query("SELECT * FROM Users_stats WHERE id='$char[id]'"));
if ($char[align] > $stats[align_high]) 
{
  mysql_query("UPDATE Users_stats SET align_high='".$char[align]."' WHERE id='$id'");
}
if ((0-$char[align]) > $stats[align_low]) 
{
  mysql_query("UPDATE Users_stats SET align_low='".(0-$char[align])."' WHERE id='$id'");
}
$achieved=0;
$maGold=0;
$maJi=0;
$maSp=0;
$maPp=0;
$maAchieved=0;
$amsg = "You have completed an Achievement!<br/>";
$creator = mysql_fetch_array(mysql_query("SELECT id, name, lastname FROM Users WHERE name = 'The' AND lastname = 'Creator' ", $db));
$cid = $creator[id];
foreach ($achievements as $branch => $ainfo)
{
  $a= $myAchieve[$branch];
  if (!$a) $a=0;
  if ($a > count($ainfo[3])) 
  {
    $myAchieve[$branch] = count($ainfo[3]);
    $a=$myAchieve[$branch];
  }
  $maAchieved+=$a;
  if ($a < count($ainfo[3]))
  {
    $x = $ainfo[3][$a];
    if ($stats[$ainfo[0]] >= $x) 
    {
      $achieved++;
      $myAchieve[$branch]++;
      if ($ainfo[5] == 'cp')
      {
        $maGold += $ainfo[4][$a];
      }
      elseif ($ainfo[5] == 'ji') $maJi += $ainfo[4][$a];
      elseif ($ainfo[5] == 'sp') $maSp += $ainfo[4][$a];
      elseif ($ainfo[5] == 'pp') $maPp += $ainfo[4][$a];

      $notesub= "Completed: ".$ainfo[2][0];
      if (count($ainfo[2]) > 1)
      {
        if ($ainfo[2][2])
        {
          $notesub.= displayGold($x,1);
        }
        else
        {
          $notesub.= $x;
        }
        $notesub.= $ainfo[2][1];
      }      
      $amsg .= "<br/>You recieved: ";
      if ($maGold) $amsg.= displayGold($maGold,1).", ";
      if ($maJi) $amsg.= $maJi." Ji, ";
      if ($maSp) $amsg.= $maSp." Skill Points, ";
      if ($maPp) $amsg.= $maPp." Profession Points, ";
      $amsg.= "<br/><br/>Keep up the good work!";   

      $note=$amsg;
      $note_extra="";

      $result = mysql_query("INSERT INTO Notes (from_id,   to_id,    del_from,del_to,type,root,sent,        cc,subject,       body,       special) 
                                        VALUES ('".$cid."','".$id."','0',     '0',   '5', '0', '".time()."','','".$notesub."','".$note."','".$note_extra."')");
    }
  }
}
if ($maAchieved != $stats[achieved])
{
  $stats[achieved] = $maAchieved;
  mysql_query("UPDATE Users_stats SET achieved='".$stats[achieved]."' WHERE id='$id'");
}
if ($achieved)
{ 
  $char[gold]+=$maGold;
  $stats[ji]+=$maJi;
  $char[points] += $maSp;
  $char[propoints] += $maPp;
  $sma=serialize($myAchieve);

  mysql_query("UPDATE Users SET newachieve=newachieve+1, gold='".$char[gold]."', points='".$char[points]."', propoints='".$char[propoints]."' WHERE id='$id'");
  mysql_query("UPDATE Users_data SET achieve='".$sma."' WHERE id='$id'");
  mysql_query("UPDATE Users_stats SET ji='".$stats[ji]."' WHERE id='$id'");
}
mysql_query("UNLOCK TABLES;");

// UPDATE DICE GAMES
include_once('dice_update.php');

?>