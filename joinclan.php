<?php

/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/charFuncs.php");
$doit=mysql_real_escape_string($_GET['doit']);
$noter=mysql_real_escape_string($_GET['note']);
$request=mysql_real_escape_string($_GET['request']);
$new_stance=intval(mysql_real_escape_string($_GET['stance']));
$id=$char[id];
$soc_name=str_replace("+"," ",mysql_real_escape_string($_GET[name]));


$society = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$soc_name' "));
$message = $soc_name;
$blocked = unserialize($society['blocked']);

$resultf = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM Users"));
$numchar = $resultf[0];
$soclimit = floor($numchar/10);
if ($soclimit < 10) $soclimit=10;

if ($char['goodevil']==1) $classn="good"; elseif ($char['goodevil']==2) $classn="evil"; else $classn="neutral";

$soc_name_char = $char[society];
$society_char = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$soc_name_char' "));
$stance = unserialize($society_char[stance]);

// SET CLAN'S STANCE
if ($name == $society_char[leader] && $lastname == $society_char[leaderlast] && $new_stance && $new_stance <= 2 && $new_stance >= -2 && $soc_name_char != $soc_name)
{
  $atWar=0;
  $query = "SELECT * FROM Locations WHERE 1";
  $result2 = mysql_query($query);  
  while ( $listchar = mysql_fetch_array( $result2 ) )
  {
    if ($listchar[last_war])
    {
      $myWar = mysql_fetch_array(mysql_query("SELECT * FROM Contests WHERE id='$listchar[last_war]' "));
      if (!$myWar[done] && !($myWar[starts] > intval(time()/3600)))
      {
        // Get clan id's that might be involved in war.
        $wid= $society_char[id];
        $oid= $society[id];
        $wcon = unserialize($myWar[contestants]);
        if ($wcon[$wid] || $wcon[$oid]) 
        {
          $atWar=1;
        }
      }
    }  
  }
  
  if ((!$atWar || $new_stance != -1))
  {
  // Send non-ally or enemy notification
  if ($new_stance == -1 || $new_stance == 2 )
  {
    if ($new_stance < 0) 
    {
      $new_stance = 0;
    }
    $align = getClanAlignment($society_char[align],$society_char[members]);
    $align2 = getClanAlignment($society[align],$society[members]);
    if ($stance[str_replace(" ","_",$soc_name)]==1 && $new_stance != 1)
    {
      $query = "SELECT * FROM Locations WHERE 1";
      $result2 = mysql_query($query);
      $support = unserialize($society_char[support]);
      $support2 = unserialize($society[support]);

      while ( $listchar = mysql_fetch_array( $result2 ) )
      {
        $loco = $listchar[id];
        if ($support[$loco] == $society[id])
        {
          $support[$loco]=0;
        }
        if ($support2[$loco] == $society_char[id])
        {
          $support2[$loco]=0;
        }
      }
      
      $support_str = serialize($support);
      $result = mysql_query("UPDATE Soc SET support='$support_str' WHERE name='$soc_name_char' ");
    
      $support2_str = serialize($support2);
      $result = mysql_query("UPDATE Soc SET support='$support2_str' WHERE name='$soc_name' ");
    }
              
    if ($new_stance == 2)
    {
      if ($align2 == 2) $society_char[align] += -25;
      else if ($align2 == 1) $society_char[align] += -10;
      else if ($align2 == -1) $society_char[align] += 10;
      else if ($align2 == -2) $society_char[align] += 25;
    }
    else if ($new_stance == 0)
    {
      if ($align2 == 2) $society_char[align] += -15;
      else if ($align2 == 1) $society_char[align] += -5;
      else if ($align2 == -1) $society_char[align] += 5;
      else if ($align2 == -2) $society_char[align] += 15;
    }
    
    $stance[str_replace(" ","_",$soc_name)] = $new_stance;
    $changed_stance = serialize($stance);
    //$result = mysql_query("UPDATE Soc SET stance='$changed_stance' WHERE name='$soc_name_char' ");
    $result = mysql_query("UPDATE Soc SET stance='$changed_stance', align='".$society_char[align]."' WHERE name='$soc_name_char' ");

    // notifiy other leader and update their stances
    $soc_lead = mysql_fetch_array(mysql_query("SELECT * FROM Users WHERE name='$society[leader]' AND lastname='$society[leaderlast]'",$db));
    
    $notesub = "";
    $note = "";
    $note_extra = "$soc_name_char";    
    if ($new_stance == 1)
    {
      $note="An alliance has been formed with ".$soc_name_char."!";
      $notesub = "Alliance Notice!";
    }
    elseif ($new_stance == 2)
    {
      $note= $soc_name_char." has declared you as their enemy!";
      $notesub = "Declaration of War!";
    }      
    else
    {
      $note= $soc_name_char." has decided to remain neutral towards you in the future!";
      $notesub = "Declaration of Neutrality!";
    }       

    $result = mysql_query("INSERT INTO Notes (from_id,    to_id,          del_from,del_to,type,root,sent,        cc,subject,   body,   special) 
                                      VALUES ('$char[id]','$soc_lead[id]','0',     '0',   '4', '0', '".time()."','','$notesub','$note','$note_extra')");

    $message = "Declaration Sent";
    $cid = $soc_lead[id];
    mysql_query("UPDATE Users SET lastmsg='".time()."' WHERE id=$cid");
    
    $stance2 = unserialize($society[stance]);
    $stance2[str_replace(" ","_",$soc_name_char)] = $new_stance;
    $changed_stance2 = serialize($stance2);
    $query = "UPDATE Soc SET stance='$changed_stance2' WHERE name='$soc_name' ";
    $result = mysql_query($query);
  }  
  else if (($new_stance == 1 || $new_stance == -2))
  {
    $reqnote = mysql_fetch_array(mysql_query("SELECT * FROM Notes WHERE id='$noter'"));
    $align = getClanAlignment($society_char[align],$society_char[members]);
    $align2 = getClanAlignment($society[align],$society[members]);

    if ($new_stance == 1)
    {
      if ($align2 == 2) $society_char[align] += 25;
      else if ($align2 == 1) $society_char[align] += 10;
      else if ($align2 == -1) $society_char[align] += -10;
      else if ($align2 == -2) $society_char[align] += -25;
      
      if ($align == 2) $society[align] += 25;
      else if ($align == 1) $society[align] += 10;
      else if ($align == -1) $society[align] += -10;
      else if ($align == -2) $society[align] += -25;
    }
    else if ($new_stance == -2)
    {
      if ($align2 == 2) $society_char[align] += 15;
      else if ($align2 == 1) $society_char[align] += 5;
      else if ($align2 == -1) $society_char[align] += -5;
      else if ($align2 == -2) $society_char[align] += -15;
      
      if ($align == 2) $society[align] += 15;
      else if ($align == 1) $society[align] += 5;
      else if ($align == -1) $society[align] += -5;
      else if ($align == -2) $society[align] += -15;
    }

    // Ally or non-Enemy notification
    if ($request && $reqnote[special] == $soc_name)
    {
      $reqnote[body]="Offer accepted.";
      $result = mysql_query("UPDATE Notes SET body='$reqnote[body]' WHERE id='$noter' ");

      if ($new_stance < 0) $new_stance = 0;      
      $stance[str_replace(" ","_",$soc_name)] = $new_stance;
      $changed_stance = serialize($stance);
      //$query = "UPDATE Soc SET stance='$changed_stance', align='".$society_char[align]."' WHERE name='$soc_name_char' ";
      $query = "UPDATE Soc SET stance='$changed_stance' WHERE name='$soc_name_char' ";
      $result = mysql_query($query);

      // notifiy other leader and update their stances
      $soc_lead = mysql_fetch_array(mysql_query("SELECT * FROM Users LEFT JOIN Users_data ON Users.id=Users_data.id WHERE Users.name='$society[leader]' AND Users.lastname='$society[leaderlast]'",$db));
   
      $note="";
      $notesub="";
      $note_extra = "$soc_name_char";
      if ($new_stance == 1)
      {
        $note="An alliance has been formed with ".$soc_name_char."!";
        $notesub = "Alliance Notice!";
      }
      elseif ($new_stance == 2)
      {
        $note= $soc_name_char." has declared you as their enemy!";
        $notesub = "Declaration of War!";
      }      
      else
      {
        $note= $soc_name_char." has decided to remain neutral towards you in the future!";
        $notesub = "Declaration of Neutrality!";
      } 
            
      $result = mysql_query("INSERT INTO Notes (from_id,    to_id,          del_from,del_to,type,root,sent,        cc,subject,   body,   special) 
                                        VALUES ('$char[id]','$soc_lead[id]','0',     '0',   '4', '0', '".time()."','','$notesub','$note','$note_extra')");

      $message = "Declaration Sent";
      $cid = $soc_lead[id];
      mysql_query("UPDATE Users SET lastmsg='".time()."' WHERE id=$cid");
    
      $stance2 = unserialize($society[stance]);
      $stance2[str_replace(" ","_",$soc_name_char)] = $new_stance;
      $changed_stance2 = serialize($stance2);
      $query = "UPDATE Soc SET stance='$changed_stance2' WHERE name='$soc_name' ";
//            $query = "UPDATE Soc SET stance='$changed_stance2', align='".$society[align]."' WHERE name='$soc_name' ";
      $result = mysql_query($query);      
    }
    // ally or non-enemy request
    else 
    {
      // Send Request
      $soc_lead = mysql_fetch_array(mysql_query("SELECT * FROM Users WHERE name='".$society[leader]."' AND lastname='".$society[leaderlast]."'",$db));

      $note="";
      $notesub="";
      $note_extra="$soc_name_char";
      if ($new_stance == 1)
      {
        $note="An alliance request from";
        $notesub = "Alliance Request!";
      }  
      else
      {
        $note="A peace treaty from";
        $notesub = "Peace Treaty!";
      }
      $socnameother2 = str_replace(" ","+",$soc_name_char);
      $mytime = time();
      $result = mysql_query("INSERT INTO Notes (from_id,    to_id,          del_from,del_to,type,root,sent,         cc,subject,   body,   special) 
                                        VALUES ('$char[id]','$soc_lead[id]','0',     '0',   '4', '0', '".$mytime."','','$notesub','$note','$note_extra')"); 

      // Update the message's body with the message's own id
      $myrequest = mysql_fetch_array(mysql_query("SELECT * FROM Notes WHERE sent='".$mytime."' AND from_id='".$char[id]."'"));
      $myrequest[body] = "<b>$note $society[name]</b><br><br><a href=joinclan.php?request=1&name=$socnameother2&stance=$new_stance&note=".$myrequest[id]." >Accept Offer</a>";
      mysql_query("UPDATE Notes SET body='".$myrequest[body]."' WHERE id='".$myrequest[id]."'");
 
      $message = "Request Sent";
      $cid = $soc_lead[id];
      mysql_query("UPDATE Users SET lastmsg='".time()."' WHERE id=$cid");

    }
  }
  else $message = 'Invalid Request';
  }
  else $message = "You cannot change alliances in the middle of a clan battle!";
}

// JOIN CLAN

if ($doit == 1)
{
  if (!$blocked[$char[id]][0])
  {
    if ($society[members] < $soclimit)
    {
      $charip = unserialize($char[ip]); 
      $alts = getAlts($charip);
      $result = mysql_query("SELECT name, lastname, id FROM Users WHERE society='".$society[name]."'");
      $socalts=0;
      while ( $listchar = mysql_fetch_array( $result ) )
      {  
        $username = $listchar[name]."_".$listchar[lastname];
        if ($alts[$username]) $socalts++;
      }

      if ($socalts < $maxsocalts)
      {
        $alignnum = getAlignment($char);
        if ($society[invite] < 3 || ($society[invite] == 3 && $alignnum >= 0) || ($society[invite] == 4 && $alignnum <= 0) || ($society[invite] == 5 && $alignnum == 0))
        {
          $reqnote = mysql_fetch_array(mysql_query("SELECT * FROM Notes WHERE id='$noter'"));
  
          if ($reqnote[special] == $soc_name || $society[invite] == 0 || (strtolower($name) == "the" && strtolower($lastname) == "creator"))
          {
            if ($reqnote[special] == $soc_name)
            {
              $reqnote[body]="Invite Accepted. Welcome to $soc_name.";
              $result = mysql_query("UPDATE Notes SET body='".$reqnote[body]."' WHERE id='$noter' ");
            }
         
            // SET DATABASE
            $ustats = mysql_fetch_array(mysql_query("SELECT * FROM Users_stats WHERE id='$id'"));
            $ustats[clans_joined]++;
            mysql_query("UPDATE Users SET society='$soc_name' WHERE id='$id' ");
            mysql_query("UPDATE Users_stats SET clans_joined='$ustats[clans_joined]' WHERE id='$id' ");
            // ADD TO NUMBER OF MEMBERS
            $society = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$soc_name' "));
            $memnumb = $society[members] + 1;
            $result = mysql_query("UPDATE Soc SET members='$memnumb' WHERE name='$soc_name' ");
            header("Location: $server_name/clan.php?message=You Have Joined $soc_name&time=$curtime");
            exit;
          }
          else if ($society[invite]) $message = "Invalid invitation";
        }
        else $message = "You are of the wrong alignment for this Clan";
      }
      else $message = "You already have ".$socalts." alts in this clan. Why not join another?";
    }
    else $message = "This clan is already at the current size limit of ".$soclimit.". No more may join at this time.";
  }
  else $message = "You have been blocked from this Clan";
}


// CONFIRM JOIN
include('header.htm');
?>

  <div class="row solid-back">
    <div class="col-sm-4 col-md-3 hidden-xs">
      <div class='img-optional' style="background-image: url('images/Flags/<?php echo $society[flag];?>'); background-repeat: no-repeat; background-position: center;">
        <img src="images/Sigils/<?php echo $society[sigil];?>" align='top' width=160 height=197 class='img-optional'>
      </div>
    </div>
    <div class="col-sm-4 col-md-6"><br/>
    <?php
      echo "<p class='lead'>".$soc_name."</p>";
      echo "<p>The Leader of ".$soc_name." is ";
      echo "<a class='btn btn-xs btn-primary' href='bio.php?name=".$society[leader]."&last=".$society[leaderlast]."'>".$society[leader]." ".$society[leaderlast]."</a></p>";
      echo "<p>".$soc_name." has earned ".number_format($society[score])." Ji globally.</p>";
      echo "<a class='btn btn-xs btn-info' href='look.php?first=1&clan=".$soc_name."'>View Members (".$society[members].")</a>";
      if ($name == $society_char[leader] && $lastname == $society_char[leaderlast] && $soc_name_char != $soc_name)
      {
        echo "<p>Mark as: ";        
        if ($stance[str_replace(" ","_",$soc_name)] != 1) 
        {
    ?>
      <a data-href='joinclan.php?stance=1&name=<?php echo $soc_name; ?>' data-toggle="confirmation" data-placement="top" title="Send alliance request?" class="btn btn-success btn-sm btn-wrap">Ally</a>
    <?php
        }
        else
        {
    ?>
      <a data-href='joinclan.php?stance=-1&name=<?php echo $soc_name; ?>' data-toggle="confirmation" data-placement="top" title="End alliance?" class="btn btn-warning btn-sm btn-wrap">Non-Ally</a>
    <?php
        }
        if ($stance[str_replace(" ","_",$soc_name)] != 2)
        {
    ?>
      <a data-href='joinclan.php?stance=2&name=<?php echo $soc_name; ?>' data-toggle="confirmation" data-placement="top" title="Send declaration of war?" class="btn btn-danger btn-sm btn-wrap">Enemy</a>
    <?php
        }
        else
        {
    ?>
      <a data-href='joinclan.php?stance=-2&name=<?php echo $soc_name; ?>' data-toggle="confirmation" data-placement="top" title="Send peace treaty?" class="btn btn-warning btn-sm btn-wrap">Non-Enemy</a>
    <?php
        }
      }
    ?>
      <p><i><?php echo nl2br($society[about]); ?></i></p>

<?php
if ($char[society] != '')
{
  if ($char[society] != $soc_name) echo "<p class='text-info'>You may only be in one clan at a time</p>";
  else echo "<p class='text-info'>You are already in this clan</p>";
}

$alignnum = getAlignment($char);
$inCB = inClanBattle($society[id]);

if ($society[invite] < 3 || ($society[invite] == 3 && $alignnum >= 0) || ($society[invite] == 4 && $alignnum <= 0) || ($society[invite] == 5 && $alignnum == 0))
{
  if ($char[society] == '' && $inCB == 0 && $society[members] < $soclimit && ( $society[invite] == 0 || $society[invite] >= 3 || $_GET[invite] > 200) || (strtolower($name) == "the" && strtolower($lastname) == "creator"))
  {
?>
      <a class='btn btn-block btn-success' href="joinclan.php?doit=1&name=<?php echo "$soc_name"; ?>&note=<?php echo "$noter"; ?>" >Join <?php echo "$soc_name"; ?></a>
<?php
  }
  elseif ($inCB == 1)
  {
?>
      <p class='text-danger'><b>This clan is currently in a Clan Battle</b></p>
<?php
  }
  elseif ($society[invite] == 1 || $society[invite] == 2)
  {
?>
      <p class='text-warning'><b>This clan requires an invitation to join</b></p>
<?php
  }
}
else echo "<p class='text-info'><b>You are of the wrong alignment for this clan</b></p>";

?>  
    </div>
    <div class="col-sm-4 col-md-3 hidden-xs">
      <div class='img-optional' style="background-image: url('images/Flags/<?php echo $society[flag];?>'); background-repeat: no-repeat; background-position: center;">
        <img src="images/Sigils/<?php echo $society[sigil];?>" align='top' width=160 height=197 class='img-optional'>
      </div>
    </div>
  </div>
<?php
include('footer.htm');
?>