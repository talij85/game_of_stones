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
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("map/mapdata/coordinates.inc");
include_once("admin/skills.php");
include_once("admin/locFuncs.php");

$message=mysql_real_escape_string($_GET['message']);
$upsupport=mysql_real_escape_string($_POST['upsupport']);
$leadtit=mysql_real_escape_string($_POST['leadtit']);
$subtit=mysql_real_escape_string($_POST['subtit']);
$newtype=mysql_real_escape_string($_POST['type']);
$newinactivity=mysql_real_escape_string($_POST['inactivity']);
$kick=mysql_real_escape_string($_POST['kick']);
$unblock=mysql_real_escape_string($_POST['unblock']);
$addsub=mysql_real_escape_string($_POST['addsub']);
$delsub=mysql_real_escape_string($_POST['delsub']);
$uprank=mysql_real_escape_string($_POST['uprank']);
$tab = mysql_real_escape_string($_GET['tab']);

// handling of special chars handled below
$newtext=$_POST['aboutchar'];
$newpriv=$_POST['privchar'];
$wikilink = "Clans";

$wait_post = 30;

if (!$char['society']) 
{ 
  header("Location: $server_name/viewclans.php?autologin=$time");
}
?>
<script language="javascript">
// getXMLHttpRequest object
function getXMLHttpRequestObject(){
   var xmlobj;

    // check for existing requests
    if(xmlobj!=null&&xmlobj.readyState!=0&&xmlobj.readyState!=4){
        xmlobj.abort();
    }
    if (window.XMLHttpRequest) {
        // instantiate object for Mozilla, Nestcape, etc.
        xmlobj=new XMLHttpRequest();
    }
    else if ( window.ActiveXObject ) 
    {
      try 
      {
        xmlobj = new ActiveXObject("Microsoft.XMLHTTP");
      } 
      catch( e ) 
      {
        xmlobj = new ActiveXObject("Msxml2.XMLHTTP");
      }
    }
    
    if (xmlobj==null) {
        return false;
    }
    return xmlobj;
}  
</script>
<?php
include("chat_code.php");

$id=$char['id'];

$soc_name = $char['society'];

// LOAD SOCIETY TABLE

$query = "SELECT * FROM Soc WHERE name='$soc_name'";
$result = mysql_query($query);
$society = mysql_fetch_array($result);
$stance = unserialize($society['stance']);
$subleaders = unserialize($society['subleaders']);
$subs = $society['subs'];
$support = unserialize($society['support']);
$upgrades = unserialize($society['upgrades']);

// UPDATE NUMBER OF MEMEBERS

$resultf = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM Users WHERE society='$soc_name' "));
$numchar = $resultf[0];
$query = "UPDATE Soc SET members='$numchar' WHERE name='$soc_name' ";
if ($numchar!=$society['members']) $result = mysql_query($query);

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
      $b=2;
    }
  }
}

$message = "<b>$soc_name</b> - Contains ".$society[members]." members";

// upgrade levels
if ($_POST[skill] != "")
{
  $upgrade= mysql_real_escape_string($_POST[skill]);
  $uplvl = $upgrades[$upgrade];
  $count = 0;
  for ($i=0; $i<8; ++$i)
  {
    $count += $upgrades[$i];
  }
  $cost = (($count+1)*($uplvl+1)*10000);

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

if (mysql_real_escape_string($_REQUEST[inCB] == 1))
{
  $message = "You cannot leave the clan during a clan battle!";
}

// clan support - DISABLED
if ($upsupport && 0)
{
  $supchange=0;
  $result = mysql_query("SELECT id, last_war FROM Locations");
  $tot_sup = 0;
  $max_sup = 0;
  $supTarget[0] = 0;
  while ($loc = mysql_fetch_array( $result ) )
  {
    $varname="support".$loc[id];
    $myWar = 0;
    $atWar = 0;
    if ($loc[last_war]) $myWar = mysql_fetch_array(mysql_query("SELECT * FROM Contests WHERE id='$loc[last_war]' "));
    if ($myWar && !$myWar[done] && ($myWar[starts]<time()/3600)) $atWar=1;
    if ($support[$loc[id]] != $_POST[$varname] && !$atWar)
    {
      $supchange=1;
      $support[$loc[id]] = $_POST[$varname];
    }
    if ($support[$loc[id]] != 0) 
    {
      $tot_sup += 1;
      $supTarget[$support[$loc[id]]] += 1;
    }
  }
  foreach ( $supTarget as $target => $count) 
  {
     if ($max_sup < $count) $max_sup = $count;
  }

  if ($supchange && $max_sup < 7 && $tot_sup < 13)
  {
    $ssupport= serialize($support);
    mysql_query("UPDATE Soc SET support='$ssupport' WHERE name='$society[name]' ");
    $message = "Support updated";
  }
  else if ($max_sup > 6)
  {
     $message = "You cannot support a single clan in more than six cities at a time!";
  }
  else if ($tot_sup > 12)
  {
     $message = "You cannot support other clans in a total of more than 12 cities at a time!";
  }
  else
  {
    $message = "No change in support.";
  }
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
}
if ($_POST['changer3'])
{
  $newpriv = htmlspecialchars(stripslashes($newpriv),ENT_QUOTES);
  if (strlen($newpriv) < 501 && preg_match('/^[-a-z0-9+.,!@*_&#:\/%;?\s]*$/i',$newpriv))
  {
    $message = "Clan Info updated successfully";
    $result = mysql_query("UPDATE Soc SET private_info='$newpriv' WHERE name='$soc_name' ");
    $society[private_info]=$newpriv;
  }
  else {$message="What strange characters you used. Officials refuse to post your note";}
}

// Update Invite Settings
if ($newtype)
{
  $message = "Clan Requirements Updated";
  $newtype--;
  $result = mysql_query("UPDATE Soc SET invite='$newtype' WHERE name='$soc_name' ");
  $society[invite]=$newtype;
}

// Update Inactivity Settings
if ($newinactivity)
{
  $message = "Clan Requirements Updated";
  $newinactivity--;
  $result = mysql_query("UPDATE Soc SET inactivity='$newinactivity' WHERE name='$soc_name' ");
  $society[inactivity]=$newinactivity;
}

// Update Ranks
if ($uprank)
{
  $nranks = '';
  for ($n=0; $n<5; $n++)
  {
    $rname="rname".$n;
    $rlvl="rlvl".$n;
    $rwin="rwin".$n;
    $rji= "rji".$n;
    $nranks[$n][0] = htmlspecialchars(stripslashes($_REQUEST[$rname]));
    $nranks[$n][1] = $_REQUEST[$rlvl];
    $nranks[$n][2] = $_REQUEST[$rwin];
    $nranks[$n][3] = $_REQUEST[$rji];
  }
  $society[ranks]= serialize($nranks);
  $result = mysql_query("UPDATE Soc SET ranks='$society[ranks]' WHERE name='$soc_name' ");  
  $message = "Clan Ranks Updated!";
}

// Update member ranks
$result = mysql_query("SELECT id, name, lastname, level, soc_rank FROM Users WHERE society='$soc_name' ORDER BY lastname, name");
$numchar = mysql_num_rows($result);

while ($cmember = mysql_fetch_array($result))
{
  $cmem_stats = mysql_fetch_array(mysql_query("SELECT id, ji, wins FROM Users_stats WHERE id='$cmember[id]'"));
  $memrank = 0;
  $br = 0; 
  if (strtolower($cmember[name]) == strtolower($society[leader]) && strtolower($cmember[lastname]) == strtolower($society[leaderlast]) ) 
  {
    $br=1;
  }
  if ($subs > 0)
  {
    foreach ($subleaders as $c_n => $c_s)
    {
      if (strtolower($cmember[name]) == strtolower($c_s[0]) && strtolower($cmember[lastname]) == strtolower($c_s[1]) )
      {
        $br=2;
      }
    }
  }  
  if ($br) $memrank=$br;
  else
  {
    $cranks = unserialize($society[ranks]);
    for ($r=0; $r<5; $r++)
    {
      if ($cranks[$r][0])
      {
        if ((!$cranks[$r][1] || ($cranks[$r][1] <= $cmember[level])) &&
            (!$cranks[$r][2] || ($cranks[$r][2] <= $cmem_stats[wins])) &&
            (!$cranks[$r][3] || ($cranks[$r][3] <= $cmem_stats[ji])))
        {
          $memrank = 7-$r;
        }
      }
    }
  }
  if ($cmember[soc_rank] != $memrank)
  {
    $result1 = mysql_query("UPDATE Users SET soc_rank='$memrank' WHERE id='$cmember[id]' ");      
  }
}

// JAVASCRIPT
$timeout = 1000 * intval($wait_post - (time() - $char['lastpost']));
if (time() - intval($char['lastpost']) <= $wait_post) $needShow = "setTimeout('setShow();',$timeout);";
else $needShow = "";
$javascripts=<<<SJAVA
<SCRIPT LANGUAGE="JavaScript">
  $needShow
  function setShow() {
    document.getElementById('showBut').style.display='block';
    document.getElementById('showBut2').style.display='none';
  }
  
  function doRanks() 
  {
    var temp = 0;
    for (var i=0; i< 5; i++)
    {
       temp = document.getElementById('rname'+i).value;
       temp = encodeURIComponent(temp); 
       alert(temp);
       document.getElementById('rname'+i).value = temp;
    }
    document.rankForm.submit();
  }
  

</SCRIPT>
SJAVA;
?>
<script language="javascript">  
// initialize chat
window.onload=intitializeChat;
</script>
<?php
  if (!$tab) $tab = 1;
  include('header.htm');
?>
  <div class="row solid-back">
    <div class="col-sm-3 hidden-xs">
      <table height=197 width=160 class="table-clear">
        <tr>
          <td class='img-optional' width=160 valign='top' style="background-image: url('images/Flags/<?php echo $society[flag];?>'); background-repeat: no-repeat;">
            <img src="images/Sigils/<?php echo $society[sigil];?>" width=160 height=197/>
          </td>
        </tr>
      </table>
    </div>
    <div class="col-sm-6"><br/>
      <div class="row">
        <div class="col-sm-6 col-md-3">
          <div class="panel panel-success">
            <div class="panel-heading">
              <h3 class="panel-title"><?php if ($society[leadertitle] != "") echo $society[leadertitle]; else echo "Leader";?></h3>
            </div>
            <div class="panel-body abox">
              <a class='btn btn-block btn-xs btn-success btn-wrap' href="bio.php?name=<?php echo $society[leader]; echo "&last="; echo $society[leaderlast]; echo "&time="; echo time(); ?>"><?php echo $society[leader]; echo " "; echo $society[leaderlast]; ?></a> 
            </div>
          </div>
        </div>
    <?php
      if ($subs > 0)
      {
    ?>
        <div class="col-sm-6 col-md-3">
          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title"><?php if ($society[subtitle] != "") echo $society[subtitle]; else echo "Subleader";?></h3>
            </div>
            <div class="panel-body abox">
            <?php
              foreach ($subleaders as $c_n => $c_s)
              {
            ?>
              <a class='btn btn-block btn-xs btn-info btn-wrap' href="bio.php?name=<?php echo $c_s[0]; echo "&last="; echo $c_s[1]; echo "&time="; echo time(); ?>"><?php echo $c_s[0]; echo " "; echo $c_s[1]; ?></a><br/>  
            <?php
              }
            ?>
            </div>
          </div>
        </div>
    <?php
      }
      else
      {
        echo "<div class='col-sm-6 visible-sm'>&nbsp;</div><div class='clearfix visible-sm'></div>";
      }
    ?>
        <div class="col-sm-6 col-md-3">
          <div class="panel panel-primary">
            <div class="panel-heading">
              <h3 class="panel-title">Allies</h3>
            </div>
            <div class="panel-body abox">
            <?php
              $draw_any = 0;
              foreach ($stance as $c_n => $c_s)
              {
                if ($c_s == 1 && str_replace(" ", "_",$society[name]) != $c_n) {echo "<a class='btn btn-block btn-xs btn-primary btn-wrap' href=\"joinclan.php?name=".str_replace("_"," ",$c_n)."&time=$curtime\">".str_replace("_"," ",$c_n)."</a><br/>"; $draw_any = 1;}
              }
              if ($draw_any == 0) echo "No Allies";
            ?> 
            </div>
          </div>
        </div>          
        <div class="col-sm-6 col-md-3">
          <div class="panel panel-danger">
            <div class="panel-heading">
              <h3 class="panel-title">Enemies</h3>
            </div>
            <div class="panel-body abox">
            <?php
              $draw_any = 0;
              foreach ($stance as $c_n => $c_s)
              {
                if ($c_s == 2) {echo "<a class='btn btn-block btn-xs btn-danger btn-wrap' href=\"joinclan.php?name=".str_replace("_"," ",$c_n)."&time=$curtime\">".str_replace("_"," ",$c_n)."</a><br/>"; $draw_any = 1;}
              }
              if ($draw_any == 0) echo "No Enemies";
            ?>  
            </div>
          </div>
        </div>
      </div>        
      <p><i><?php echo nl2br($society[about]); ?></i></p>
      <p><?php echo nl2br($society[private_info]); ?></p>
    </div>
    <div class="col-sm-3 hidden-xs">
      <table height=197 width=160 class="table-clear">
        <tr>
          <td class='img-optional' width=160 valign='top' style="background-image: url('images/Flags/<?php echo $society[flag];?>'); background-repeat: no-repeat;">
            <img src="images/Sigils/<?php echo $society[sigil];?>" width=160 height=197/>
          </td>
        </tr>
      </table>
    </div>
    <div class="col-sm-12">
      <div id="content">
        <ul id="clantabs" class="nav nav-tabs" data-tabs="tabs">
          <li <?php if ($tab == 1) echo "class='active'";?>><a href="#status_tab" data-toggle="tab">Status</a></li>
          <li <?php if ($tab == 2) echo "class='active'";?>><a href="#cities_tab" data-toggle="tab">Cities</a></li>
          <li <?php if ($tab == 3) echo "class='active'";?>><a href="#upgrades_tab" data-toggle="tab">Upgrades</a></li>
        <?php 
          if ($b)
          {
        ?>          
          <li <?php if ($tab == 4) echo "class='active'";?>><a href="#settings_tab" data-toggle="tab">Settings</a></li>
        <?php 
          }
        ?>          
        </ul>

        <div class="tab-content">
          <div class="tab-pane <?php if ($tab == 1) echo 'active';?>" id="status_tab">  
            <div class='col-sm-8'>
              <div id='chatdiv' align='left'></div>
              <input type='hidden' id='mid' value='<?php echo $society[id];?>'/><br/>
            </div>
            <div class='col-sm-4'>
              <div class="panel panel-danger">
                <div class="panel-heading">
                  <h3 class="panel-title">Clan Info</h3>
                </div>
                <div class="panel-body abox">            
                  <p>
                    <b>Global Ji</b>: <?php echo $society[score];?><br/>
                    <b>Clan Bank</b>: <?php echo displayGold($society[bank]);?><br/>
                    <b>Upkeep</b>:
              <?php
                $upkeep=0;
                for ($i=0; $i<8; ++$i)
                {
                  if ($upgrades[$i]) $upkeep += pow(2,$upgrades[$i]-1);
                }
                $upkeep = $upkeep*$society[members];
                echo displayGold($upkeep); 
              ?>
                    <br/>
                    <b>Declared Alignment</b>: 
            <?php

              $alignnum = getClanAlignment($society[align], $society[members]);
              $lalign = getAlignmentText($alignnum);
              
              // Check alignment vs declared alignment
              $dnum = $society[declared];
              if ($dnum >0 && $alignnum < 0) $dnum = 0;
              if ($dnum <0 && $alignnum > 0) $dnum = 0;
              if ($dnum == 0 && $alignnum == 2) $dnum = 1;
              if ($dnum == 0 && $alignnum == -2) $dnum = -1;
              
              if ($dnum != $society[declared]) 
              {
                $society[declared] = $dnum;
                mysql_query("UPDATE Soc SET declared='".$dnum."' WHERE name='$soc_name'");
              }
              $declared = "Neutral";
              if ($dnum > 0) $declared = "Light";
              else if ($dnum < 0) $declared = "Shadow";
              
              echo $declared;
           ?>
                    <br/>                  
                    <b>Alignment Score</b>: 
            <?php 

              echo number_format($society[align])." (".$lalign.")";
            ?>
                    <br/>
                  </p>
                  <p><b>Member Bonuses</b>:<br/>
              <?php 
                $count=0;
                $hire_bonus = "";
                for ($i=1; $i<8; ++$i)
                {
                  $count += $upgrades[$i];
                }
                $hire_bonus = getUpgradeBonuses($upgrades, $clan_hires, 8);
                $hire_bonus .= " ".$clan_building_bonuses;
                if ($count || $clan_building_bonuses)
                {
                  echo itm_info(cparse($hire_bonus))."<br/>";
                }
                else
                  echo "<i>None</i><br/>";
                $count+=$upgrades[0];
              ?>
                  </p>
                </div>
              </div>
              <a data-href='clanleave.php?doit=1' data-toggle="confirmation" data-placement="top" title="Are you sure you want to leave this clan?" class="btn btn-danger btn-sm btn-wrap">Leave Clan</a>        
            </div>
          </div>
          <div class="tab-pane <?php if ($tab == 2) echo 'active';?>" id="cities_tab">
            <form action="clan.php" method="post">
              <table class="table table-condensed table-striped table-responsive table-center">
                <tr>
                  <th style='vertical-align: bottom;'>City</th>
                  <th style='vertical-align: bottom;' class='hidden-xs'>Reputation</th>
                  <th style='vertical-align: bottom;' class='visible-xs'>Rep</th>
                  <th style='vertical-align: bottom;'>Our Ji</th>
                  <th style='vertical-align: bottom;'>Ruler</th>
                  <th style='vertical-align: bottom;'>Ji</th>
                  <!--<th style='vertical-align: bottom;' class='hidden-xs'>Supporting</th>
                  <th style='vertical-align: bottom;' class='visible-xs'>Support</th>-->                  
                  <th style='vertical-align: bottom;'>War Status</th>
                </tr>
              <?php
                updateSocScores();
                $clanreps = unserialize($society[area_rep]);
                $result = mysql_query("SELECT * FROM Locations ORDER BY name");
                while ($loc = mysql_fetch_array( $result ) )
                {
                  $clans = array();
                  $locid = $loc['id'];
                  $clanscores = unserialize($loc[clan_scores]);
                  $supporting = unserialize($loc[clan_support]);
          
                  $ruler=$loc[ruler]; 
                  $ruler_wins = 0;
                  if ($ruler=='No One' && $rival == 'No One')
                  { 
                    $ruler_wins = 100;
                  }
                  else
                  {
                    foreach ($clanscores as $key => $value)
                    {
                      if ($ruler_wins==0)
                      {
                        $tsoc = mysql_fetch_array(mysql_query("SELECT * FROM `Soc` WHERE id = '".$key."'"));
                        if ($ruler == $tsoc[name])
                        {
                          $ruler_wins = $value;
                        }
                      }
                    }
                  }
              ?>
                <tr class='small'>
                  <td><?php echo str_replace('-ap-','&#39;',$loc[name]); ?></td>
                  <td><?php echo round($clanreps[$locid]); ?></td>
                  <td><?php echo round($clanscores[$society[id]]); ?></td>
                  <td>
                  <?php  
                    if ($ruler != "No One") 
                      echo "<a href='joinclan.php?name=".$ruler."'>".$ruler."</a>";
                    else 
                      echo $ruler;
                  ?>
                  </td>
                  <td><?php echo number_format($ruler_wins); ?></td>
                  <!-- Hide support column -->
                  <!--<td>-->
                  <?php          
                    /*if ($b)
                    {
                      echo "<select name='support".$loc[id]."' size='1' class='form-control gos-form input-sm'>";
                      echo "  <option value='0'>No one</option>";
                      $query = "SELECT * FROM Soc WHERE 1 ORDER BY score DESC, members DESC";
                      $result2 = mysql_query($query);
                      $stance = unserialize($society['stance']);
                      while ( $listchar = mysql_fetch_array( $result2 ) )
                      {
                        if ($stance[str_replace(" ","_",$listchar[name])] == 1 && $listchar[id] != $society[id])
                        {
                  ?> 
                    <option value="<?php echo $listchar[id];?>" <?php if ($listchar[id]==$supporting[$society[id]]) echo ' selected';?>><?php echo str_replace('-ap-','&#39;',$listchar[name]); ?></option>
                  <?php
                        }
                      }
                      echo "</select>";
                    }
                    else
                    {
                      if ($supporting[$society[id]])
                      {
                        $ssid = $supporting[$society[id]];
                        $ssoc = mysql_fetch_array(mysql_query("SELECT * FROM `Soc` WHERE id = '$ssid'"));
                        echo "<a href='joinclan.php?name=".$ssoc[name]."'>".$ssoc[name]."</a>";
                      }
                      else
                      {
                        echo "No One";
                      }
                    } */
                  ?>  
                  <!--</td>-->
                  <td>
                  <?php
                    $myWar = 0;
                    if ($loc[last_war])
                    {
                      $myWar = mysql_fetch_array(mysql_query("SELECT * FROM Contests WHERE id='$loc[last_war]' "));
                    }          
                    if ($myWar) // previous war
                    {
                      if ($myWar[starts] > intval(time()/3600))
                      {
                        $tminus = intval(($myWar[starts]*3600-time())/60);
                        echo "Starts in: ".displayTime($tminus,0)."!";
                      }
                      else if ($myWar[ends] > intval(time()/3600))
                      {
                        $tminus = intval(($myWar[ends]*3600-time())/60);
                        echo "Ends in: ".displayTime($tminus,0);
                      }
                      else
                      {
                        $tminus = intval((time()-$myWar[ends]*3600)/60);
                        echo "Last: ".displayTime($tminus,1);
                      }
                    }
                    else 
                    {
                      echo "None";
                    }        
                  ?>
                  </td>
                </tr>
              <?php
                }
                if ($b)
                {
              ?>
                <tr>
                  <td colspan='6' align='center'>
                    <input type='hidden' name='upsupport' id='upsupport' value='1'/>
                    <input type="submit" name="submit" value="Update Support" class="btn btn-sm btn-primary"/>
                  </td>
                </tr>
              <?php
                }
              ?>
              </table>
            </form>
          </div>
          <div class="tab-pane <?php if ($tab == 3) echo 'active';?>" id="upgrades_tab">
            <div class='row'>
              <div class='col-xs-12'>
                <h4>Clan Bank: <?php echo displayGold($society[bank]);?></h4>
              </div>
            </div>
            <?php
              if ($society[goods] == null)
              {
                $empty_goods = array(0,0,0,0,0,0,0);
                $society[goods] = serialize($empty_goods);
                mysql_query("UPDATE Soc SET goods='".$society[goods]."' WHERE name='$soc_name'");
              }
              $cgoods = unserialize($society[goods]);
            ?>
            <div class='row'>
              <div class='col-xs-12'>
                <div class="panel panel-success">
                  <div class="panel-heading">
                    <h3 class="panel-title">Clan Goods</h3>
                  </div>
                  <div class="panel-body abox">
                    <div class='row'>
                      <?php 
                        for ($i=1; $i<7; $i++)
                        {
                      ?>
                      <div class='col-sm-1 col-xs-6 text-right'>
                        <?php echo ucwords($estate_unq_prod[$i]).":"; ?>
                      </div>
                      <div class='col-sm-1 col-xs-6'>
                        <?php echo $cgoods[$i]; ?>
                      </div>
                      <?php
                        }
                      ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class='row'>
              <div class='col-sm-6'>            
                <div class="panel panel-primary">
                  <div class="panel-heading">
                    <h3 class="panel-title">Clan Hires</h3>
                  </div>
                  <div class="panel-body abox">
                    <form action="clan.php" method="post" name='skillform' id='skillform'>
                      <input type='hidden' name='skill' value='-1'/>
                      <table class="table table-condensed table-striped table-responsive">
                      <?php
                        for ($index=0; $index<8; ++$index)
                        {
                          $level = $upgrades[$index];
                          $upbonus = getUpgradeBonus($upgrades, $clan_hires, $index);
                          $upbonus1 = getUpgradeBonus($upgrades, $clan_hires, $index,1);
                          $htitle = str_replace("&#39;","\&#39;","Current Bonus:<br/>".itm_info(cparse($upbonus))."<br/>Next Level:<br/>".itm_info(cparse($upbonus1)));
                          echo "<tr>";
                      ?>
                          <td><button type="button" class="btn btn-warning btn-xs btn-block btn-wrap" data-html="true" data-toggle="popover" data-placement="top" data-content="<?php echo $htitle;?>"><?php echo $clan_hires[$index][0]." Level ".($upgrades[$index]);?></button></td>
                          <td>
                          <?php echo displayGold(($count+1)*($level+1)*10000); ?>
                          </td>
                          <?php
                            if ($b && $upgrades[$index]<getMaxClanHire($society[score]))
                            {
                          ?>
                          <td>
                            <input type="Button" name="submit<?php echo $index;?>" value="Upgrade" onclick="javascript: submitFormClanSettings(<?php echo $index;?>);" class="btn btn-xs btn-primary">
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
              <div class='col-sm-6'>            
                <div class="panel panel-info">
                  <div class="panel-heading">
                    <h3 class="panel-title">Clan Ranks (low to high)</h3>
                  </div>
                  <div class="panel-body abox">              
                    <form action="clan.php" name='rankForm' id='rankForm' method="post">
                      <table class="table table-condensed table-striped table-responsive">
                        <tr>
                          <th><b>Rank Name</th>
                          <th>Level</th>
                          <th>Wins</th> 
                          <th>Ji</th>         
                        </tr>
                      <?php 
                        $d = " disabled='disabled'";
                        if ($b) $d="";
                        $cranks = unserialize($society[ranks]);
                        for ($r=0; $r<5; $r++)
                        {
                          echo "<tr>";
                          echo "<td><input type='text' class='form-control gos-form input-sm' name='rname".$r."' size='25' id='rname".$r."' value='".$cranks[$r][0]."' maxlength='20'".$d." /></td>";
                          echo "<td><input type='text' class='form-control gos-form input-sm' name='rlvl".$r."' size='4' id='rlvl".$r."' value='".$cranks[$r][1]."' maxlength='4'".$d." /></td>";
                          echo "<td><input type='text' class='form-control gos-form input-sm' name='rwin".$r."' size='6' id='rwin".$r."' value='".$cranks[$r][2]."' maxlength='6'".$d." /></td>";
                          echo "<td><input type='text' class='form-control gos-form input-sm' name='rji".$r."' size='6' id='rji".$r."' value='".$cranks[$r][3]."' maxlength='6'".$d." /></td>";
                          echo "</tr>";
                        }
                      ?>
                        <tr>
                          <td colspan=4 align='center'>
                        <?php
                          if ($b)
                          {
                        ?>
                            <input type="hidden" name="uprank" value="1" id="uprank" />
                            <input type="submit" name="submit" value="Update Ranks" class="btn btn-sm btn-info"/>
                        <?php
                          }
                        ?>
                          </td>
                        </tr>
                      </table>
                    </form>
                  </div>
                </div>
              </div>
            </div>            
          </div>
<?php 
  if ($b)
  {
?>

          <div class="tab-pane <?php if ($tab == 4) echo 'active';?>" id="settings_tab">
            <div class='row'>
              <div class='col-sm-6 col-md-5 col-lg-4'>
                <div class="panel panel-primary">
                  <div class="panel-heading">
                    <h3 class="panel-title">General Settings</h3>
                  </div>
                  <div class="panel-body abox">
                  <?php
                    if ($numchar - $subs> 1 && $subs < 3 && ($name == $society[leader] && $lastname == $society[leaderlast] ))
                    {
                  ?>                  
                    <form class="form-inline" action="clan.php" method="post">
                      <div class="form-group form-group-sm">
                        <label for="addsub">Add Subleader:</label>
                        <select name="addsub" size="1" class="form-control gos-form">  
                      <?php
                        $query = "SELECT id, name, lastname FROM Users WHERE society='$soc_name' ORDER BY lastname, name";
                        $result = mysql_query($query);          
                        $b1 = 0;
                        $x = 0;
                        while ($x < $numchar)
                        { 
                          $charnew = mysql_fetch_array($result);
                          if ( strtolower($charnew[name]) != strtolower($char[name]) || strtolower($charnew[lastname]) != strtolower($char[lastname]) )
                          {
                            $b1=1;
                            foreach ($subleaders as $c_n => $c_s)
                            {
                              if (strtolower($charnew[name]) == strtolower($c_s[0]) && strtolower($charnew[lastname]) == strtolower($c_s[1]) )
                              {
                                $b1=0;
                              }
                            }
                          }
                          if ($b1 ==1)
                          {
                      ?>
                        <option value="<?php echo $charnew[id]; ?>"><?php echo $charnew[name]." ".$charnew[lastname]; ?></option>
                      <?php
                          }
                          $x++;
                          $b1 = 0;
                        }
                      ?>
                        </select>
                        <input type="Submit" name="submit" value="Add Sub" class="btn btn-xs btn-success"/>
                      </div>
                    </form>
                  <?php
                    }
                    if ($subs > 0 && ($name == $society[leader] && $lastname == $society[leaderlast] ))
                    {
                  ?>
                    <form class="form-inline" action="clan.php" method="post">
                      <div class="form-group form-group-sm">
                        <label for="delsub">Remove Subleader:</label>
                        <select name="delsub" size="1" class="form-control gos-form">
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
                        <input type="Submit" name="submit" value="Remove Sub" class="btn btn-xs btn-danger"/>
                      </div>
                    </form>
                  <?php
                    }
                    if (($numchar > 1 && ($name == $society[leader] && $lastname == $society[leaderlast] )) || $numchar > 2) 
                    {
                      $query = "SELECT id, name, lastname FROM Users WHERE society='$soc_name' ORDER BY lastname, name";
                      $result = mysql_query($query);
                  ?>
                    <form class="form-inline" action="clan.php" method="post">
                      <div class="form-group form-group-sm">
                        <label for="kick">Boot Member:</label>
                        <select name="kick" size="1" class="form-control gos-form">
                      <?php
                        $y = 0;
                        while ($y < $numchar)
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
                        <input type="Submit" name="submit" value="Kick" class="btn btn-xs btn-warning"/>
                      </div>
                    </form>
                  <?php
                    }
                    if ($block_num > 0)
                    {
                  ?>
                    <form class="form-inline" action="clan.php" method="post">
                      <div class="form-group form-group-sm">
                        <label for="unblock">Unblock Character:</label>
                        <select name="unblock" size="1" class="form-control gos-form">
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
                        <input type="Submit" name="submit" value="unblock" class="btn btn-xs btn-primary"/></td>
                      </div>
                    </form>
                  <?php
                    }
                  ?>
                    <form class="form-inline" action="clan.php" method="post">
                      <div class="form-group form-group-sm">
                        <label for="leadtit">Leader Title:</label>
                        <input type='text' class='form-control gos-form input-sm' name="leadtit" size="12" id="leadtit" value='<?php echo $society[leadertitle];?>' maxlength='12' />
                      </div>
                      <div class="form-group form-group-sm">
                        <label for="subtit">Subleader Title:</label>
                        <input type='text' class='form-control gos-form input-sm' name="subtit" size="12" id="subtit" value='<?php echo $society[subtitle];?>' maxlength='12' />
                      </div>
                      <div class="form-group form-group-sm">
                        <label for="type">Membership:</label>
                        <select name="type" size="1" class="form-control gos-form">
                          <option value="1"<?php if ($society[invite] == 0) echo " selected";?>>freely</option>
                          <option value="2"<?php if ($society[invite] == 1) echo " selected";?>>by member invitation</option>
                          <option value="3"<?php if ($society[invite] == 2) echo " selected";?>>by leader invitation</option>
                          <option value="4"<?php if ($society[invite] == 3) echo " selected";?>>No Shadow</option>
                          <option value="5"<?php if ($society[invite] == 4) echo " selected";?>>No Light</option>
                          <option value="6"<?php if ($society[invite] == 5) echo " selected";?>>Neutral only</option>
                        </select>
                      </div>
                      <div class="form-group form-group-sm">
                        <label for="inactivity">Boot Inactive Members:</label>
                        <select name="inactivity" size="1" class="form-control gos-form">
                          <option value="1"<?php if ($society[inactivity] == 0) echo " selected";?>>Never</option>
                          <option value="2"<?php if ($society[inactivity] == 1) echo " selected";?>>After 5 Days</option>
                          <option value="3"<?php if ($society[inactivity] == 2) echo " selected";?>>After 10 Days</option>
                          <option value="4"<?php if ($society[inactivity] == 3) echo " selected";?>>After 15 Days</option>
                          <option value="5"<?php if ($society[inactivity] == 4) echo " selected";?>>After 20 Days</option>
                          <option value="6"<?php if ($society[inactivity] == 5) echo " selected";?>>After 25 Days</option>
                        </select>
                      </div>
                      <input type="Submit" name="submit" value="Update Settings" class="btn btn-xs btn-primary"/>
                    </form>
                  </div>
                </div>
              </div>
              <div class='col-sm-6 col-md-7 col-lg-8'>
                <div class="panel panel-info">
                  <div class="panel-heading">
                    <h3 class="panel-title">Public Information</h3>
                  </div>
                  <div class="panel-body abox">
                    <form class="form-inline" action="clan.php" method="post">
                      <textarea name="aboutchar" class="form-control gos-form" rows="6" wrap="soft" style="overflow:hidden; width: 100%;"><?php echo $society['about']; ?></textarea>
                      <input type="hidden" name="changer2" value="1" id="changer2" />
                      <input type="Submit" name="submit" value="Update Public Info" class="btn btn-xs btn-info"/>
                    </form>
                  </div>
                </div>
              </div>                    
              <div class='col-sm-6 col-md-7 col-lg-8'>
                <div class="panel panel-warning">
                  <div class="panel-heading">
                    <h3 class="panel-title">Private Information</h3>
                  </div>
                  <div class="panel-body abox">
                    <form class="form-inline" action="clan.php" method="post">
                      <textarea name="privchar" class="form-control gos-form" rows="6" wrap="soft" style="overflow:hidden; width: 100%;"><?php echo $society['private_info']; ?></textarea>
                      <input type="hidden" name="changer3" value="1" id="changer3" />
                      <input type="Submit" name="submit" value="Update Private Info" class="btn btn-xs btn-warning"/>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
<?php
  }
?>
        </div>
      </div>
    </div>
  </div>
  <script type='text/javascript'>
  // clansettings.php
  function submitFormClanSettings(index)
  {
    document.skillform.skill.value= index;
    document.skillform.submit();
  }
  </script>
<?php
include('footer.htm');
?>