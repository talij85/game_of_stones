<?php

function delete_blank($array)
{
  $new_array='';
  foreach ($array as $key => $value)
  {
    if ($value[0]) $new_array[$key]=$value;
  }
  return $new_array;
}

/* establish a connection with the database */
include("admin/connect.php");
include("admin/userdata.php");

include("admin/skills.php");

$message = "Edit clan settings";

$leadtit=mysql_real_escape_string($_POST['leadtit']);
$subtit=mysql_real_escape_string($_POST['subtit']);
$avatar=mysql_real_escape_string($_POST['newav']);
$newtype=mysql_real_escape_string($_POST['type']);
$newtext=mysql_real_escape_string($_POST['aboutchar']);
$newpriv=mysql_real_escape_string($_POST['privchar']);
$kick=mysql_real_escape_string($_POST['kick']);
$unblock=mysql_real_escape_string($_POST['unblock']);
$allow=mysql_real_escape_string($_POST['allow']);
$addsub=mysql_real_escape_string($_POST['addsub']);
$delsub=mysql_real_escape_string($_POST['delsub']);
$support=mysql_real_escape_string($_POST['support']);
$supportloc=mysql_real_escape_string($_POST['supportloc']);
$id=$char['id'];
$soc_name = $char[society];

// KICK OFF PAGE IF NOT CLAN LEADER

$society = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$soc_name' "));
$subleaders = unserialize($society['subleaders']);
$subs = $society['subs'];

$b = 0; 
if (strtolower($name) == strtolower($society[leader]) && strtolower($lastname) == strtolower($society[leaderlast]) ) 
{
  $b=1;
}
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
if ($b==0)
{
  header("Location: $server_name/clan.php?time=$time&message=Stop trying to cheat");
  exit;
}


$blocked = unserialize($society['blocked']);
$block_num = $society['block_num'];

// Leader title
if ($leadtit) 
{
    $society[leadertitle]=$leadtit;
    $query = "UPDATE Soc SET leadertitle='$leadtit' WHERE name='$soc_name'";
    $result = mysql_query($query);
    $message="Title updated";
}
// Subleader title
if ($subtit) 
{
    $society[subtitle]=$subtit;
    $query = "UPDATE Soc SET subtitle='$subtit' WHERE name='$soc_name'";
    $result = mysql_query($query);
    $message="Title updated";
}
// Add subleader
if ($addsub)
{
  $result = mysql_query("SELECT * FROM Users WHERE id='$addsub'");
  $char_sub = mysql_fetch_array($result);
  $subleaders[$addsub]= array($char_sub[name],$char_sub[lastname]);
  $subleaders_str = serialize($subleaders);
  ++$subs;
  if ($subs < 3)
  {
    $query = "UPDATE Soc SET subleaders='$subleaders_str', subs='$subs' WHERE name='$soc_name'";
    $result = mysql_query($query);
    $message="Character added as subleader";   
  }
  else
  {
    $message="You may only have two subleaders";
  }
}


// Remove Subleader

if ($delsub)
{
  --$subs;
  $subleaders[$delsub][0]=0;
  $subleaders=delete_blank($subleaders);

  if ($subs > 0)
  {
    $query = "UPDATE Soc SET subleaders='".serialize($subleaders)."', subs='$subs' WHERE name='$soc_name'";
  }
  else
  {
    $query = "UPDATE Soc SET subleaders='', subs='$subs' WHERE name='$soc_name'";
  }
  $result = mysql_query($query);
  $message="Character removed as subleader";  
}


// Kick Character

if ($kick)
{
$query = "UPDATE Users SET society='' WHERE id='$kick'";
$result = mysql_query($query);
$result = mysql_query("SELECT * FROM Users WHERE id='$kick'");
$char_kick = mysql_fetch_array($result);
// UPDATE NUMBER OF CLAN MEMBERS AND BLOCK
$memnumb = $society[members] - 1;
$blocked[$kick]=array($char_kick[name],$char_kick[lastname]);
$blocked_str=serialize($blocked);
++$block_num;
if (strlen($blocked_str)<5000)
{
$query = "UPDATE Soc SET members='$memnumb', blocked='$blocked_str', block_num='$block_num' WHERE name='$soc_name'";
$result = mysql_query($query);
$message="Character kicked and blocked from Clan";
}
else $message="Character kicked but could not be blocked - too many blocked already";
}


// Unblock Character

if ($unblock)
{
--$block_num;
$blocked[$unblock][0]=0;
$blocked=delete_blank($blocked);
if ($block_num > 0)
{
  $query = "UPDATE Soc SET blocked='".serialize($blocked)."', block_num='$block_num'  WHERE name='$soc_name'";
}
else
{
  $query = "UPDATE Soc SET blocked='', block_num='$block_num' WHERE name='$soc_name'";
}
$result = mysql_query($query);
$message="Character unblocked from Clan";
}


// Set Allowances

if ($allow)
{
$allow--;
$query = "UPDATE Soc SET allow='$allow' WHERE name='$soc_name' ";
$society[allow]=$allow;
$result = mysql_query($query);
$message="Member requirements updated";
}

// Set Support
if ($support && $supportloc)
{
  $supporting = unserialize($society['support']);
  $supporting[$supportloc] = $support;
  $supporting_str = serialize($supporting);
  mysql_query("UPDATE Soc SET support='$supporting_str' WHERE name='$society[name]' ");
  $message="Clan Supported";
}

// Update Clan Info

if ($_POST['changer2'])
{
$newtext = htmlspecialchars(stripslashes($newtext),ENT_QUOTES);
if (strlen($newtext) < 501 && preg_match('/^[-a-z0-9+.,!@*_&#:\/%;?\s]*$/i',$newtext))
{
$message = "Clan Info updated successfully";
$query = "UPDATE Soc SET about='$newtext' WHERE name='$soc_name' ";
$result = mysql_query($query);
$society[about]=$newtext;
}
else {$message="What strange characters you used. Officials refuse to post your note";}

$newpriv = htmlspecialchars(stripslashes($newpriv),ENT_QUOTES);
if (strlen($newpriv) < 501 && preg_match('/^[-a-z0-9+.,!@*_&#:\/%;?\s]*$/i',$newpriv))
{
$message = "Clan Info updated successfully";
$query = "UPDATE Soc SET private_info='$newpriv' WHERE name='$soc_name' ";
$result = mysql_query($query);
$society[private_info]=$newpriv;
}
else {$message="What strange characters you used. Officials refuse to post your note";}

}

$upgrades = unserialize($society['upgrades']);

// skill levels
if ($_POST[skill] != "")
{
  $upgrade= mysql_real_escape_string($_POST[skill]);
  $uplvl = $upgrades[$upgrade];
  $cost = pow(10,$uplvl)*10000;

  if ($society[bank] >= $cost)
  {
    $society[bank] = $society[bank] - $cost;
    $gold = $society[bank];
    $upgrades[$upgrade] = $upgrades[$upgrade] + 1;
    $upgrades_str= serialize($upgrades);
    mysql_query("UPDATE Soc SET upgrades='$upgrades_str', bank='$gold' WHERE name='$soc_name'");
    $message = "Clan Upgrade purchased!";
  }
  else
  {
    $message = "You don't have enough money!";
  }
}

// Update Invite Settings

if ($newtype)
{
$message = "Clan Requirements Updated";
$newtype--;
$query = "UPDATE Soc SET invite='$newtype' WHERE name='$soc_name' ";
$result = mysql_query($query);
$society[invite]=$newtype;
}

if ($messages == '') $messages = "$soc_name settings";

include('header.htm');
?>
<font class="littletext">
<font face="VERDANA"><font class="littletext"><center>

<?php

// GET ALL SOCIETY MEMBERS
$query = "SELECT id, name, lastname FROM Users WHERE society='$soc_name' ORDER BY lastname, name";
$result = mysql_query($query);
$numpeople = mysql_num_rows($result);
?>
<table border="0" cellpadding="0" cellspacing="0" width='100%'>
  <tr>
    <td valign='top'>
<!-- Clan upgrades -->
      <table border="0" cellpadding="0" cellspacing="0">
        <form action="clansettings.php" method="post" name='skillform' id='skillform'>
        <input type='hidden' name='skill' value='-1'>
        <tr>
          <td><font class="littletext"><b>Clan Bank:<b></td>
          <td><font class="littletext"><?php echo displayGold($society[bank]); ?></td>
        </tr>
        <tr>
          <td><font class="littletext"><b>Hourly Upkeep:<b></td>
          <?php
            $upkeep=0;
            for ($i=0; $i<5; ++$i)
            {
              $upkeep += $upgrades[$i];
            }
            $upkeep = $upkeep*$numpeople;
          ?>
          <td><font class="littletext"><?php echo displayGold($upkeep); ?></td>
        </tr>        
        <tr><td><br><font class="littletext"><b>Clan Upgrades</b></td></tr>
        <?php
          for ($index=0; $index<5; ++$index)
          {
            if ($upgrades[$index]<5)
            {
              $level = $upgrades[$index];
        ?>
        <tr>
          <td><font class="littletext">
            Upgrade <?php echo $clan_skill_names[$index];?> to level <?php echo $level+1;?>: 
          </td>
          <td><font class="littletext">
            <?php 
              echo displayGold(pow(10,$level)*10000);
            ?>
          </td>
          <td><font class="littletext">
            <input type="Button" name="submit<?php echo $index;?>" value="Upgrade" onclick="javascript: submitFormClanSettings(<?php echo $index;?>);" class="form">
          </td>
        </tr>
        <?php
            }
          }
        ?>
        </form>
      </table>
      <br><hr width='90%' align=left><br>
      <?php
        if ($numpeople - $subs> 1 && $subs < 3 && ($name == $society[leader] && $lastname == $society[leaderlast] ))
        {
      ?>
<!-- Add Subleader -->
      <form action="clansettings.php" method="post">
        <font class="littletext"><b>Add Subleader </b>
        <select name="addsub" size="1" class="form">  
          <?php
            $b = 0;
            $x = 0;
            while ($x < $numpeople)
            { 
              $charnew = mysql_fetch_array($result);
              if ( strtolower($charnew[name]) != strtolower($char[name]) || strtolower($charnew[lastname]) != strtolower($char[lastname]) )
              {
                $b=1;
                foreach ($subleaders as $c_n => $c_s)
                {
                  if (strtolower($charnew[name]) == strtolower($c_s[0]) && strtolower($charnew[lastname]) == strtolower($c_s[1]) )
                  {
                    $b=0;
                  }
                }
              }
              if ($b ==1)
              {
          ?>
          <option value="<?php echo $charnew[id]; ?>"><?php echo $charnew[name]." ".$charnew[lastname]; ?></option>
          <?php
              }
              $x++;
              $b = 0;
            }
          ?>
        </select>
        from <?php echo "$soc_name"; ?>   
        <input type="Submit" name="submit" value="Add Sub" class="form">
      </form>
      <?php
        }
        if ($subs > 0 && ($name == $society[leader] && $lastname == $society[leaderlast] ))
        {
      ?>
<!-- Remove Subleader -->
      <form action="clansettings.php" method="post">
        <font class="littletext"><b>Remove Subleader </b>
        <select name="delsub" size="1" class="form">
          <option value="0">no one</option>
          <?php
            foreach ($subleaders as $c_n => $c_s)
            {
          ?>
          <option value="<?php echo $c_n; ?>"><?php echo $c_s[0]." ".$c_s[1]; ?></option>
          <?php
            }
          ?>
        </select>
        from <?php echo "$soc_name"; ?>   
        <input type="Submit" name="submit" value="Remove Sub" class="form">
      </form>
      <?php
        }
        if (($numpeople > 1 && ($name == $society[leader] && $lastname == $society[leaderlast] )) || $numpeople > 2) 
        {
          $query = "SELECT id, name, lastname FROM Users WHERE society='$soc_name' ORDER BY lastname, name";
          $result = mysql_query($query);
      ?>
<!-- KICK CLAN MEMBER -->
      <form action="clansettings.php" method="post">
        <font class="littletext"><b>Kick </b>
        <select name="kick" size="1" class="form">
          <?php
            $y = 0;
            while ($y < $numpeople)
            {
              $charnew = mysql_fetch_array($result);
              if (( strtolower($charnew[name]) != strtolower($char[name]) || strtolower($charnew[lastname]) != strtolower($char[lastname]) ) && 
                (strtolower($charnew[name]) != strtolower($society[leader]) && strtolower($charnew[lastname]) != strtolower($society[leaderlast]) ))
              {
          ?>
          <option value="<?php echo $charnew[id]; ?>"><?php echo $charnew[name]." ".$charnew[lastname]; ?></option>
          <?php
              }
              $y++;
            }
          ?>
        </select>
        from <?php echo "$soc_name"; ?>   
        <input type="Submit" name="submit" value="Kick" class="form">
      </form>
      <?php
        }
      ?>
<!-- UNBLOCK CLAN MEMBER -->
      <?php
        if ($block_num > 0)
        {
      ?>
      <form action="clansettings.php" method="post">
        <font class="littletext"><b>Unblock </b>
        <select name="unblock" size="1" class="form">
          <option value="0">no one</option>
          <?php
            foreach ($blocked as $c_n => $c_s)
            {
          ?>
          <option value="<?php echo $c_n; ?>"><?php echo $c_s[0]." ".$c_s[1]; ?></option>
          <?php
            }
          ?>
        </select>
        from <?php echo "$soc_name"; ?>   
        <input type="Submit" name="submit" value="unblock" class="form">
      </form>
      <?php
        }
      ?>
<!-- SUPPORT -->
      <form action="clansettings.php" method="post">
        <font class="littletext"><b>Support</b>
        <select name="support" size="1" class="form">
          <option value="0">no one</option>
          <?php
            $query = "SELECT * FROM Soc WHERE 1 ORDER BY score DESC, members DESC";
            $result2 = mysql_query($query);
            $stance = unserialize($society['stance']);
            while ( $listchar = mysql_fetch_array( $result2 ) )
            {
              if ($stance[str_replace(" ","_",$listchar[name])] == 1)
              {
          ?>
          <option value="<?php echo $listchar[id]; ?>"><?php echo str_replace('-ap-','&#39;',$listchar[name]); ?></option>
          <?php
              }
            }
          ?>
        </select>
        <b> in </b>
        <select name="supportloc" size="1" class="form">
          <option value="0">no where</option>
          <?php
            $query = "SELECT * FROM Locations WHERE 1";
            $result2 = mysql_query($query);
            while ( $listchar = mysql_fetch_array( $result2 ) )
            {
          ?>
          <option value="<?php echo $listchar[id]; ?>"><?php echo str_replace('-ap-','&#39;',$listchar[name]); ?></option>
          <?php
            }
          ?>
        </select>    
        <input type="Submit" name="submit" value="Support" class="form">
      </form>
<!-- ALLOWANCES -->
      <?php
        if (0)
        {
      ?>
     <form action="clansettings.php" method="post">
       <font class="littletext"><b>Allow:</b> 
        <select name="allow" size="1" class="form">
         <option value="0">[keep current]</option>
         <option value="1">any type</option>
         <option value="2">darkfriends</option>
         <option value="3">pure</option>
         <option value="4">not darkfriends</option>.
         <option value="5">not pure</option>
         <option value="6">neutral</option>
       </select>
     </form>
     <?php
       }
     ?>
    </td>
    <td valign='top'>
<!-- CLAN INFO -->
      <form action="clansettings.php" method="post">
        <font class="littletext"><b>Leader Title:</b>
        <input type='text' class='form' name="leadtit" size="12" id="leadtit" value='<?php echo $society[leadertitle];?>' maxlength='12' /><br>
        <font class="littletext"><b>Subleader Title:</b>
        <input type='text' class='form' name="subtit" size="12" id="subtit" value='<?php echo $society[subtitle];?>' maxlength='12' /><br>
        <font class="littletext"><b>New members:</b> 
        <select name="type" size="1" class="form">
          <option value="0">[keep current]</option>
          <option value="1">freely</option>
          <option value="2">by member invitation</option>
          <option value="3">by leader invitation</option>
        </select><br>
        <font class="littletext"><b>Public Information</b><br><textarea name="aboutchar" class="form" rows="6" cols="60" wrap="soft" style="overflow:hidden"><?php echo $society['about']; ?></textarea><br>
        <font class="littletext"><b>Private Information</b><br><textarea name="privchar" class="form" rows="6" cols="60" wrap="soft" style="overflow:hidden"><?php echo $society['private_info']; ?></textarea><br>
        <input type="hidden" name="changer2" value="1" id="changer2" /><br>
        <input type="Submit" name="submit" value="Update Info" class="form">
      </form>
    </td>
  </tr>
</table>
</center>

<?php
include('footer.htm');
?>