<?php

$wait_post = 15;

/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/charFuncs.php");
include_once("admin/locFuncs.php");
include_once('map/mapdata/coordinates.inc');

if ($location_array[$char['location']][2])
{
  $is_town=1;
  $dw= $town_bonuses[dW];
}
else 
{
  $is_town=0;
  $dw=0;
}

$namesender = $_COOKIE['name'];
$lastnamesender = $_COOKIE['lastname'];
$name=mysql_real_escape_string($_GET['name']);
$submitted=mysql_real_escape_string($_POST['submitted']);
$lastname=mysql_real_escape_string($_GET['last']);
$extra=mysql_real_escape_string($_POST['extra']);
$message=mysql_real_escape_string($_REQUEST['message']);
$tab = mysql_real_escape_string($_GET['tab']);
$time=time();
$ipaddy = $_SERVER['REMOTE_ADDR'];

$wikilink = "Profile";

// if no name, show own bio
if ($name == '' || $lastname == '') {$name = $namesender; $lastname = $lastnamesender;}
if (strtolower($namesender) == strtolower($name) && strtolower($lastnamesender) == strtolower($lastname)) $is_same = 1; else $is_same = 0;

// copy own data to charother
$charother = $char;
$socnameother = $charother[society];
$query = "SELECT * FROM Soc WHERE name='$socnameother' ";
$result = mysql_query($query);
$societyo = mysql_fetch_array($result);

$friends=unserialize($charother[friends]);

// get bio to be displayed
$result = mysql_query("SELECT * FROM Users LEFT JOIN Users_data ON Users.id=Users_data.id WHERE Users.name='$name' AND Users.lastname='$lastname'",$db);
$char = mysql_fetch_array($result);
$cid=$char['id'];
$time=time();
$username= $char[name]."_".$char[lastname];

$socname = $char[society];
$query = "SELECT * FROM Soc WHERE name='$socname'";
$result = mysql_query($query);
$society = mysql_fetch_array($result);
$classes=unserialize($char[type]);

$stats = mysql_fetch_array(mysql_query("SELECT * FROM Users_stats WHERE id='$char[id]'"));

$query = "SELECT * FROM Users_stats WHERE id='10011'";
$result = mysql_query($query);
$heroes = mysql_fetch_array($result);

$titles= '';
$title_why = '';
$x=0;
for ($y = 0; $y < count($rank_data); $y++)
{
  if ($heroes[$rank_data[$y][0]] == $char[id] && $stats[$rank_data[$y][0]] > 0)
  {
    $titles[$x] = $rank_data[$y][2];
    $title_why[$x] = $rank_data[$y][1];
    $x++;
  }
}

$cNat = $char['nation'];
$topNat =  mysql_fetch_array(mysql_query("SELECT id, name, lastname FROM Users WHERE nation='".$cNat."' ORDER BY exp DESC LIMIT 1"));
if ($topNat[id] == $char[id]) {
  $titles[$x]= "Champion of the ".$nat_champs[$cNat];
  $title_why[$x] = "Experienced ".$nationalities[$cNat];
  $x++;
}

$resultb = mysql_query("SELECT * FROM IP_logs WHERE addy='$ipaddy'");
$fullname = $charother[name]."_".$charother[lastname];

$charip = unserialize($charother[ip]);
$alts = getAlts($charip);

$user_ips = unserialize($charother[ip]);
$uips_count = count($user_ips);
$found = 0;
for ($i=0; $i<$uips_count; $i++)
{
  if ($user_ips[$i] == $ipaddy) $found = 1;
}
if (!$found)
{
  $user_ips[$uips_count] = $ipaddy;
  $user_ips2 = serialize($user_ips);
  mysql_query("UPDATE Users SET ip='$user_ips2' WHERE id=".$charother[id]);  
}


if ($char[id] == $charother[id]) // only update IPs on your own profile
{
  if (mysql_fetch_row($resultb)) 
  {
    $result = mysql_query("SELECT * FROM IP_logs WHERE addy='$ipaddy'");
    $ip_log = mysql_fetch_array($result);
    $ip_users = unserialize($ip_log['users']);
    $found = 0;
    $ip_count = count($ip_users);
    for ($i=0; $i<$ip_count; $i++)
    {
      if ($ip_users[$i] == $fullname)
      {
        $found = 1;
      }
    }
    if (!$found)
    {
      $ip_users[$ip_count] = $fullname;  
      $ip_users2 = serialize($ip_users);
      $ip_count++;
      mysql_query("UPDATE IP_logs SET users='$ip_users2', num='$ip_count', test='".$ip_users[test]++."' WHERE addy='$ipaddy'");
    }
    else if ($ip_log[maxnum] ==2  && $char[donor]>0)
      mysql_query("UPDATE IP_logs SET maxnum='$maxalts' WHERE addy='$ipaddy'");
  }
  else 
  {
    $ip_users[0] = $fullname;
    $ip_users2 = serialize($ip_users);
    mysql_query("INSERT INTO IP_logs (addy, users, num, maxnum, test) VALUES('$ipaddy', '$ip_users2', '1', '$maxalts','1')", $db);
  }
}

// IF CREATOR

if ($classes[0]==0) $is_creator = 1; else $is_creator = 0;

// ADD / REMOVE FRIENDS

if ($_GET[set_s] && !$is_same) {
  function delete_ele($array,$ele)
  {
    $new_array=array();
    foreach ($array as $key => $value)
    {
      if ($key!=$ele) $new_array[$key]=$value;
    }
    return $new_array;
  }

  if ($_GET[set_s]==3) {$friends=delete_ele($friends,$char[id]);  $message="$name $lastname removed";}
  elseif (count($friends)<=25) {$friends[$char[id]]=array($char[name],$char[lastname],$_GET[set_s]-1); $message="$name $lastname added";}
  else $message="You have too many friends/enemies";
  
  mysql_query("UPDATE Users_data SET friends='".serialize($friends)."' WHERE id=".$charother[id]);
}

// TRANSFER ORG LEADERSHIP
if ($_GET[transfer] && $char[society] == $charother[society] && strtolower($societyo[leader]) == strtolower($charother[name]) && strtolower($societyo[leaderlast]) == strtolower($charother[lastname]))
{
  $query = "UPDATE Soc SET leader='$name', leaderlast='$lastname' WHERE name='".$char[society]."'";
  $result = mysql_query($query);
  $message = $char[society]." leadership transfered";
}

if (!$message && $is_same) $message = "Today's date is ".wotDate();
elseif (!$message && $char['born']) 
{
  $message="Residing in ".str_replace('-ap-','&#39;',$char['location']);
}

$charb=$char;
$char=$charother;
if (!$tab) $tab = 1;
include('header.htm');
$char=$charb;
if (!$char['born']) {echo "<center><br><br><font class='medtext'>$name $lastname does not exist"; include('footer.htm'); exit;}

?>
  <div class="row solid-back">
    <div class="col-sm-12">
      <div id="content">
        <ul id="biotabs" class="nav nav-tabs" data-tabs="tabs">
          <li <?php if ($tab == 1) echo "class='active'";?>><a href="#bio_tab" data-toggle="tab">Bio</a></li>
<?php
        if ($is_same || ($charother['name'] == 'The' && $charother['lastname'] == 'Creator'))
        {
?>          
          <li <?php if ($tab == 2) echo "class='active'";?>><a href="#stats_tab" data-toggle="tab">Stats</a></li>
          <li <?php if ($tab == 3) echo "class='active'";?>><a href="#list_tab" data-toggle="tab">Watch List</a></li>
<?php
          if ($char[donor]==1)
          { 
?>          
          <li <?php if ($tab == 4) echo "class='active'";?>><a href="#alt_tab" data-toggle="tab">Alt Status</a></li>
<?php 
          }
        }
?>        
          <li <?php if ($tab == 5) echo "class='active'";?>><a href="#achieve_tab" data-toggle="tab">Accomplishments</a></li>  
        </ul>

        <div class="tab-content">
          <div class="tab-pane <?php if ($tab == 1) echo "active";?>" id="bio_tab">         

            <!-- CHARACTER DISPLAY STUFF -->
            <div class="col-sm-4 col-md-3">
<?php
        $avatar=$char['avatar'];
        if (!($avatar))
        {
          $nat = str_replace(" ","_",$nationalities[$char['nation']]);
          $sex = $char['sex'];
          $sexChar = "M";
          if ($sex) $sexChar = "F";
          $w=0;
          for ($y=1; $y < count($worth_ranks); $y++)
          {
            if ($stats[net_worth] >= $worth_ranks[$y][0]) $w = $y;
          }
          if ($is_creator) $w = 0;

          $avatar = "char/".$nat.$sexChar.$w.".jpg";
        }
  
        $gold = ($char['gold']);
        $lvl = number_format($char['level']);
        $wins = number_format($stats['wins']);
        $win_per="(".intval(100*$stats['wins']/($stats['battles']+0.0001)+0.5)."%) ";
        $dwins = number_format($stats['duel_wins']);
        $dwin_per="(".intval(100*$stats['duel_wins']/($stats['tot_duels']+0.0001)+0.5)."%) ";
        $ewins = number_format($stats['enemy_wins']);
        $ewin_per="(".intval(100*$stats['enemy_wins']/($stats['enemy_duels']+0.0001)+0.5)."%) ";
        $owins = number_format($stats['off_wins']);
        $owin_per="(".intval(100*$stats['off_wins']/($stats['off_bats']+0.0001)+0.5)."%) ";
        $nwins = number_format($stats['npc_wins']);
        $nwin_per="(".intval(100*$stats['npc_wins']/($stats['tot_npcs']+0.0001)+0.5)."%) ";    
        $bgold = $char['bankgold'];
        $ggold = $stats['dice_earn'];        
        $dgold = $stats['duel_earn'];
        $tgold = $stats['item_earn'];
        $qgold = $stats['quest_earn'];
        $pgold = $stats['prof_earn'];
        $npc1_wins = number_format($stats['shadow_wins']);
        $npc1_per = "(".intval(100*$stats['shadow_wins']/($stats['shadow_npcs']+0.0001)+0.5)."%) ";
        $npc2_wins = number_format($stats['military_wins']);
        $npc2_per = "(".intval(100*$stats['military_wins']/($stats['military_npcs']+0.0001)+0.5)."%) ";
        $npc3_wins = number_format($stats['ruffian_wins']);
        $npc3_per = "(".intval(100*$stats['ruffian_wins']/($stats['ruffian_npcs']+0.0001)+0.5)."%) ";
        $npc4_wins = number_format($stats['channeler_wins']);
        $npc4_per = "(".intval(100*$stats['channeler_wins']/($stats['channeler_npcs']+0.0001)+0.5)."%) ";
        $npc5_wins = number_format($stats['animal_wins']);
        $npc5_per = "(".intval(100*$stats['animal_wins']/($stats['animal_npcs']+0.0001)+0.5)."%) ";
        $npc6_wins = number_format($stats['exotic_wins']);
        $npc6_per = "(".intval(100*$stats['exotic_wins']/($stats['exotic_npcs']+0.0001)+0.5)."%) ";
    
        $stance = unserialize($societyo[stance]);
        $associated = "";
        if ($stance[str_replace(" ","_",$char[society])] == 1 && $char[society]!=$charother[society] && $char[society]) $associated = "ally ";
        if ($stance[str_replace(" ","_",$char[society])] == 2 && !$is_same) $associated = "enemy ";

        if ($char[society] != '')
        {
          if ($char[soc_rank] == 1)
          {
            $soctit= "Leader";
            if ($society[leadertitle]!= "") $soctit = $society[leadertitle];
            $societyn = "<img src='images/SocLead.gif' height=20 width=28> ".$soctit." of ";  
          }
          elseif ($char[soc_rank]==2)
          {
            $soctit = "Subleader";
            if ($society[subtitle]!="") $soctit= $society[subtitle];
            $societyn = "<img src='images/SocSub.gif' height=20 width=28> ".$soctit." of ";
          }
          elseif ($char[soc_rank]>0)
          {
            $cranks= unserialize($society[ranks]);
            $societyn=$cranks[7-$char[soc_rank]][0]." of ";
          }
          else
          { 
            $societyn = "Member of ";  
          } 
          $societyn .= $associated;
          if ($char[society]!=$charother[society]) $societyn .= "<a href='joinclan.php?name=".$char[society]."'>".str_replace(" ","&nbsp;",$char[society])."</a><br><br>";
          else $societyn .= "<a href='clan.php'>".str_replace(" ","&nbsp;",$char[society])."</a><br><br>";
        }
        else $societyn = '';
        $health = $char[vitality];

        if ($char['goodevil']==2) $classn="color: #5483C9;";
        elseif ($char['goodevil']==1) $classn="color: #E14545;";
        else $classn="";
        if ($char[sex]) $gender="Female";
        else $gender = "Male";
        $align = $char[align];
        
        if ($is_creator) {$gender = "Unknown"; $associated = ""; $classn=""; $lvl="Infinite"; $health="Limitless"; $wins="Irrelevent"; $align="Guess";}

        if (strlen($name." ".$lastname)>16) {$font_size="class='medtext' style='font-size: 11px; $classn'"; $societyn="<br>".$societyn;} else $font_size="class='medtext' style='$classn'";

        $num_notes=mysql_num_rows(mysql_query("SELECT * FROM Notes WHERE to_id='$id' AND del_to='0' AND type < 4 ORDER BY sent DESC"));
        $num_logs=mysql_num_rows(mysql_query("SELECT * FROM Notes WHERE to_id='$id' AND del_to='0' AND type = 9 ORDER BY sent DESC"));
        $smin = $char[stamina];
        $smax = $char[stamaxa];
?>      
              <div class="panel panel-success">
                <div class="panel-heading">
                  <h3 class="panel-title"><?php echo "$name $lastname";?></h3>
                </div>
                <div class="panel-body abox">
                  <p>
                    <i>Level:</i> <?php echo $lvl;?><br/>
                    <i>Nationality:</i> <?php echo $nationalities[$char[nation]];?><br/>
                    <i>Class:</i>
<?php           
        for ($i = 0; $i<count($classes); $i++)
        {
          echo "<img src='images/classes/".$classes[$i].".gif' alt='".$classes[$i]."' title='".$char_class[$classes[$i]]."' height='15' width='15'/>";
        }
?>
                    <br/> 
                    <i>Sex:</i> <?php echo $gender;?><br/>
                    <i>Stamina:</i> <?php echo "$smin/$smax";?><br/>
                    <i>Health:</i> <?php echo $health;?><br/>
                    <i>Alignment:</i> <?php echo $align;?><br/>
                    <i>Purse:</i><?php echo displayGold($gold);?>
                  </p>
<?php
                if ($char[lastonline] >= time()-900)
                  echo "<button class='btn btn-xs btn-success active'>Online</button>";
                else
                  echo "<button class='btn btn-xs btn-default disabled'>Offline</button>";
?>                  
                </div>
              </div>

               <form name='duelform' action='duel.php' method='post'>
                 <input type='hidden' name='enemyid' value='<?php echo $cid;?>' id='enemyid' />
                 <input type='hidden' name='ddd' value='0' id='ddd' />
               </form>

              <div class="panel panel-warning">
                <div class="panel-heading">
                  <h3 class="panel-title">Actions</h3>
                </div>
                <div class="panel-body abox">
                  <div class="btn-group-vertical btn-block">
<?php
// OTHER CHARACTER ACTIONS  
        if (!$is_same) 
        {
          // BATTLE
          $find_battle = unserialize($charother['find_battle']);
          $numbahs=array("Zero","One","Two","Three","Four","Five","Six","Seven","Eight","Nine","Ten","Ten");
 
          if ($char[location]==$charother[location]) 
          {
            if ($find_battle[$char['id']] <= time() - intval(300*(100+$dw)/100)) // DUEL FIX
            {
              $duel_link = "javascript:submitFormBio(0)";   
              $duel_link2 = "javascript:submitFormBio(1)"; 
?>
                    <a href="<?php echo $duel_link;?>" class="btn btn-danger btn-sm"><img border='0' src='images/duel.gif' /> Duel <img border='0' src='images/duel.gif' /></a>
<?php                  
              if ($charother[donor]) 
              {
?>
                    <a href="<?php echo $duel_link2;?>" class="btn btn-danger btn-sm"><img border='0' src='images/duel.gif' /> Double Duel <img border='0' src='images/duel.gif' /></a>
<?php
              }                          
            }
            elseif ($find_battle[$char['id']] > time() - intval(300*(100+$dw)/100)) 
            {
              $time_to_battle=intval((intval(300*(100+$dw)/100) - (time() - $find_battle[$char['id']]))/60 + 1);
?>              
                    <img src='images/noduel.gif' alt='X'/> <?php echo $numbahs[$time_to_battle];?> minute <img src='images/noduel.gif' alt='X'/>
<?php                  
              if ($time_to_battle!=1) echo "s";
            }
          }
?>                       
                    <a href="messages.php?id=<?php echo $char[id];?>&tab=3" class="btn btn-info btn-sm"><img border='0' src='images/barter.gif'/> Send message <img border='0' src='images/barter.gif'/></a>
<?php
          // TRADE
          if (!$alts[$username])
          {
?>
                    <a href="messages.php?id=<?php echo $char[id];?>&tab=4" class="btn btn-warning btn-sm"><img border='0' src='images/scale.gif'/> Offer trade <img border='0' src='images/scale.gif'/></a>
<?php
          }
          // INVITE
          if ( $char[society] != $charother[society] && 
              ($societyo[invite] == 1 || 
               ($societyo[invite] == 2 && strtolower($societyo[leader]) == strtolower($charother[name]) && strtolower($societyo[leaderlast]) == strtolower($charother[lastname])) && 
               $socnameother))
          {
?>
                    <a href="messages.php?<?php echo "name=$name&last=$lastname&time=$time&join=59320112";?>" class="btn btn-info btn-sm"><img border='0' src='images/invite.gif' /> Send invite <img border='0' src='images/invite.gif' /></a>
<?php
          }
?>
                  </div>
<?php
          // BEFRIEND
          if (!isset($friends[$char[id]][0])) 
          {
?>              
                    <a href="bio.php?<?php echo "set_s=1&time=$time&name=$name&last=$lastname";?>" class="btn btn-success btn-sm btn-block"><img border='0' src='images/handshake.gif'/> Friend <img border='0' src='images/handshake.gif'/></a>
                    <a href="bio.php?<?php echo "set_s=2&time=$time&name=$name&last=$lastname";?>" class="btn btn-danger btn-sm btn-block"><img border='0' src='images/duel.gif'/> Enemy <img border='0' src='images/duel.gif'/></a>
<?php
          }  
          else 
          {
?>              
                  <a href="bio.php?<?php echo "set_s=3&time=$time&name=$name&last=$lastname";?>" class="btn btn-info btn-sm btn-block"><img border='0' src='images/handshake.gif'/> Neutral <img border='0' src='images/handshake.gif'/></a>
<?php
          }
        }
// SAME CHARACTER ACTIONS
        else 
        {
          if ($num_notes)
          {
?>
                    <a href="messages.php" class="btn btn-primary btn-sm"><img border='0' src='images/barter.gif'/> Messages <span class="badge"><?php echo $num_notes;?></span> <img border='0' src='images/barter.gif'/></a>
<?php
          }
          if ($num_logs)
          {
?>
                    <a href="battlelogs.php" class="btn btn-danger btn-sm"><img border='0' src='images/shield.gif'/> Def log <span class="badge"><?php echo $num_logs;?></span> <img border='0' src='images/shield.gif'/></a>
<?php
          }
?>
                    <a href="myquests.php" class="btn btn-success btn-sm btn-block"><img border='0' src='images/loc.gif'/> My Quests <img border='0' src='images/loc.gif'/></a>
                    <a href='avatar.php' class="btn btn-info btn-sm btn-block"><img border='0' src='images/anvil.gif'/> Settings <img border='0' src='images/anvil.gif'/></a>
                  </div>               
<?php
        }
?>
                </div> 
              </div> <!-- close action panel -->
            </div> <!-- close 1st column -->
            <div class="col-sm-4 col-md-6 hidden-xs">
              <center><img id="avi" class="img-responsive img-optional" border='0' bordercolor='#000000' src="<?php echo $avatar;?>"/><br/>
              <p>
<?php              
          $curnote=$char['about'];
          $curnote=nl2br($curnote);
          echo "<i>$curnote</i>";
?> 
              </p> </center>        
            </div>
            <div class="col-sm-4 col-md-3">
<?php

        if ($ageday ==1) $days = "";
        $percent_up = 100-intval(100*($char[exp_up]-$char[exp])/$char[exp_up_s]);
        if ($percent_up > 99) $percent_up = 99;

            if ($char[society] != '')
            {
?>
              <div class='hidden-xs img-optional-nodisplay' style="width:160px; background-image: url('images/Flags/<?php echo $society[flag];?>'); background-repeat: no-repeat;">
                <img src="images/Sigils/<?php echo $society['sigil'];?>" align='top' width=160 height=197/>
              </div>
<?php
            }
?>  
              <div class="panel panel-primary">
                <div class="panel-heading">
                  <h3 class="panel-title">Titles</h3>
                </div>
                <div class="panel-body abox">              
<?php
            echo $societyn;
            $w=0;
            for ($y=1; $y < count($worth_ranks); $y++)
            {
              if ($stats[net_worth] >= $worth_ranks[$y][0]) $w = $y;
            }
            $wrank = $worth_ranks[$w][$char[sex]+1];
            echo "<font title='".displayGold($stats[net_worth],1)."'>".$wrank."</font><br/><br/>";
            
            for ($x=0; $x < count($titles); $x++)
            {
              echo "<font title='Most ".$title_why[$x]."'>".$titles[$x]."</font><br/>";
            }            
?>
                </div>
              </div> <!-- close title panel -->
            </div> <!-- close 3rd column -->         
          </div> <!-- close bio_tab content -->
  
<?php
if ($is_same || ($charother['name'] == 'The' && $charother['lastname'] == 'Creator'))
{
include('admin/duelFuncs.php');
include('admin/jobFuncs.php');
include('admin/equipped.php');

// Profession Bonuses
$jobs = unserialize($char[jobs]);

// Clan Bonuses
$upgrades = unserialize($society['upgrades']);
$hire_bonus = "";
$hire_bonus = getUpgradeBonuses($upgrades, $clan_hires, 8);
$clan_bonus1[0] = $hire_bonus;
$clan_bonus1[1] = $clan_building_bonuses;

// nation bonuses
$nat_bonus = $nation_bonus[$char['nation']][0];

// combine all bonuses
$bonuses1 = $stomach_bonuses1." ".$stamina_effect1." ".$skill_bonuses1;

$weapon_a = $estats[0];

$fstats = $bonuses1." ".$weapon_a;

$full_stats = itm_info(cparse($fstats,0));
$weap_stats = itm_info(cparse($weapon_a,0));
$skill_stats = itm_info(cparse($skill_bonuses1,0));
$stom_stats = itm_info(cparse($stomach_bonuses1,0));
$stam_stats = itm_info(cparse($stamina_effect1,0));
$clan_stats0 = itm_info(cparse($clan_bonus1[0],0));
$clan_stats1 = itm_info(cparse($clan_bonus1[1],0));
$nat_stats = itm_info(cparse($nat_bonus,0));
$pro_stats = itm_info(cparse(getAllJobBonuses($jobs)));
?>
          <div class="tab-pane <?php if ($tab == 2) echo "active";?>" id="stats_tab">
            <div class="col-sm-4 col-md-3">
              <div class="panel panel-success">
                <div class="panel-heading">
                  <h3 class="panel-title">Full Stats</h3>
                </div>
                <div class="panel-body abox"> 
                  <?php echo $full_stats;?>
                </div>
              </div>
            </div> 
            <div class="col-sm-4 col-md-3">
              <div class="panel">
                <div class="panel-heading">
                  <h3 class="panel-title">Equipment Stats</h3>
                </div>
                <div class="panel-body abox"> 
                  <?php echo $weap_stats;?>
                </div>
              </div>
            </div> 
            <div class="col-sm-4 col-md-3">
              <div class="panel">
                <div class="panel-heading">
                  <h3 class="panel-title">Skills Stats</h3>
                </div>
                <div class="panel-body abox"> 
                  <?php echo $skill_stats;?>
                </div>
              </div>
            </div> 
 
             <div class="col-sm-4 col-md-3">
              <div class="panel">
                <div class="panel-heading">
                  <h3 class="panel-title">Profession Bonuses</h3>
                </div>
                <div class="panel-body abox"> 
                  <?php echo $pro_stats;?>
                </div>
              </div>
            </div> 
                       
            <div class="col-sm-4 col-md-3">
              <div class="panel">
                <div class="panel-heading">
                  <h3 class="panel-title">Nation Bonuses</h3>
                </div>
                <div class="panel-body abox"> 
                  <?php echo $nat_stats;?>
                </div>
              </div>
            </div>
            

<?php
if ( $clan_stats0 || $clan_stats1 )
{
?>
            <div class="col-sm-4 col-md-3">
              <div class="panel">
                <div class="panel-heading">
                  <h3 class="panel-title">Clan Bonuses</h3>
                </div>
                <div class="panel-body abox"> 
                  <?php
                    if ($clan_stats0) 
                      echo "<i>From Clan Hires</i><br/>".$clan_stats0."<br/>";
                    if ($clan_stats1)  
                      echo "<i>From Ruled Cities</i><br/>".$clan_stats1."<br/>";            
                  ?> 
                </div>
              </div>
            </div>      
<?php
}
if ($stom_stats)
{
?>                  
            <div class="col-sm-4 col-md-3">
              <div class="panel panel-warning">
                <div class="panel-heading">
                  <h3 class="panel-title">Consumable Effects</h3>
                </div>
                <div class="panel-body abox"> 
                  <?php echo $stom_stats; ?>
                </div>
              </div>
            </div>  
<?php
}
if ($stam_stats)
{
?>       
            <div class="col-sm-4 col-md-3">
              <div class="panel panel-danger">
                <div class="panel-heading">
                  <h3 class="panel-title">Stamina Effects</h3>
                </div>
                <div class="panel-body abox"> 
                  <?php echo $stam_stats; ?>
                </div>
              </div>
            </div>  

<?php
}
?>                   
          </div> <!-- close stats_tab content -->

          <div class="tab-pane <?php if ($tab == 3) echo "active";?>" id="list_tab">
            <div class="col-sm-12">
    <?php
      for ($lists = 0; $lists <=1; $lists++)
      {
        if ($lists == 0) echo "<center><b>--Friends List--</b><br/><br/></center>";
        else echo "<hr/><center><b>--Enemies List--</b><br/><br/></center>";
    ?>
              <table class="table table-condensed table-striped table-responsive">
                <tr>
                  <th><b>Name</b></th>
                  <th><b>Classes</b></th>
                  <th><b>Equipped</b></th> 
                  <th><b>Alignment</b></th>
                  <th><b>Location</b></th>
                  <th><img border='0' src='images/till.gif' style='vertical-align:middle'/>&nbsp;<b>Coin</b></th>
                  <th><img border='0' src='images/lvlup.gif' style='vertical-align:middle'/>&nbsp;<b>Lvl</b></th>
                  <th><img border='0' src='images/classes/0.gif' style='vertical-align:middle'/>&nbsp;<b>Online</b></th>
                </tr>
      <?php
        foreach ($friends as $fid => $fval)
        {
          $listchar = mysql_fetch_array(mysql_query("SELECT * FROM Users WHERE id='$fid'"));
          if ($fval[2] ==$lists && $listchar[id])
          {
            $btnstyle = "btn-default";
            $lastBattle = mysql_fetch_array(mysql_query("SELECT id, starts, ends, winner FROM Contests WHERE type='99' "));
            if ($lastBattle != 0)
            {
              if ($listchar[align] > 0)
                $btnstyle = "btn-primary";
              else
                $btnstyle = "btn-danger";
            }
            else if ($stance[str_replace(" ","_",$listchar[society])] && $listchar[id] != $id) 
            {
              if ($stance[str_replace(" ","_",$listchar[society])] == 1)
                $btnstyle = "btn-primary";
              else
                $btnstyle = "btn-danger";
            }
      ?>
                <tr>
                  <td style="text-align: center;">
                    <a class="btn btn-xs btn-block <?php echo $btnstyle; ?>" href="bio.php?name=<?php echo $listchar['name']."&last=".$listchar['lastname']."&time=".time(); ?>">
<?php
                      $hasSeal = mysql_num_rows(mysql_query("SELECT id FROM Items WHERE type=0 && owner='$listchar[id]'"));
                      if ($hasSeal)
                        echo "<img src='images/classes/4.gif' height=18 width=18/>&nbsp;"; 

                      echo $listchar['name']." ".$listchar['lastname']; 
                      
                      if ($listchar[soc_rank] == 1) 
                        echo "<img src='images/SocLead.gif' height=20 width=28/>";
                      elseif ($listchar[soc_rank] == 2) 
                        echo "<img src='images/SocSub.gif' height=20 width=28/>";
?>
                    </a>
                  </td>
                  <td style="text-align: center;">
                    <?php   
                      $oclasses=unserialize($listchar[type]);
                      for ($i = 0; $i<count($oclasses); $i++)
                      {
                        echo "<img src='images/classes/".$oclasses[$i].".gif' alt='".$oclasses[$i]."' title='".$char_class[$oclasses[$i]]."' height='15' width='15'>";
                      }
                    ?>
                  </td>  
                  <td style="text-align: center;">
                    <?php
                      $cstr = getStrength($listchar[used_pts], $listchar[equip_pts], $listchar[stamina], $listchar[stamaxa]);
                      $slvl = getStrengthLevel($cstr);
                      echo $slvl;
                    ?>                
                  </td>
                  <td style="text-align: center;">  
                    <?php   
                      $alignnum = getAlignment($listchar[align]);
                      $lalign = getAlignmentText($alignnum);
                      echo $lalign;
                    ?>                
                  </td> 
                  <td style="text-align: center;">
                    <?php echo $listchar['location'];?>
                  </td>           
                  <td style="text-align: center;">
                    <?php echo displayGold($listchar['gold']);?>
                  </td>
                  <td style="text-align: center;">
                    <?php echo $listchar['level']; ?>
                  </td>
                  <td style="text-align: center;">
                    <?php 
                      if ($listchar['lastonline'] >= time()-900)
                      {
                    ?>
                    <b>Online</b></font>
                    <?php
                      }
                    ?>  
                  </td>
                </tr>
                <?php  
          }
        }
      ?>
              </table>
              <br/><br/>
  <?php
    }
  ?>
            </div>
          </div> <!-- close watch_tab content -->

  <?php

    if ($char[donor]==1)
    { 
  ?>

          <div class="tab-pane <?php if ($tab == 4) echo "active";?>" id="alt_tab">
            <div class="col-sm-12">
    <table class="table table-condensed table-striped table-responsive">
      <tr>
        <th><b>Name</b></th>
        <th><b>Location</b></th>
        <th><b>Purse</b></th>
        <th class='hidden-xs'><b>Level</b></th> 
        <th class='hidden-xs'><b>Stamina</b></th>
        <th><b>Turns</b></th>
        <th><b>New Message</b></th>
        <th><b>New Log</b></th>
        <th><b>Biz Done</b></th>
        <th><b>Biz Inactive</b></th>
      </tr>
    <?php
      // Find all other characters with same e-mail
      $aresult = mysql_query("SELECT id, name, lastname, level, gold, jobs, location, stamina, stamaxa, battlestoday, newmsg, newlog, donor FROM Users WHERE email = '".$char[email]."'");
      
      // For each other character, pull information to put in table
      while ( $listchar = mysql_fetch_array( $aresult ) )
      {
        $btnstyle = "btn-default";
        if ($listchar[id] == $char[id]) $btnstyle = "btn-info";
        echo "<tr>";
        echo "<td style='text-align: center;'><b><a class='btn $btnstyle btn-xs btn-block btn-wrap' href=\"bio.php?name=".$listchar['name']."&last=".$listchar['lastname']."\">";
        echo $listchar['name']." ".$listchar['lastname']."</a></b></td>";
        echo "<td style='text-align: center;'>".$listchar[location]."</td>";
        echo "<td style='text-align: center;'>".displayGold($listchar[gold])."</td>";        
        echo "<td class='hidden-xs' style='text-align: center;'>".$listchar[level]."</td>";        
        echo "<td class='hidden-xs' style='text-align: center;'>".$listchar[stamina]."/".$listchar[stamaxa]."</td>";
        echo "<td style='text-align: center;'>".((100*($listchar[donor]+1))-$listchar[battlestoday])."</td>";
        echo "<td style='text-align: center;'>".$listchar[newmsg]."</td>";
        echo "<td style='text-align: center;'>".$listchar[newlog]."</td>";
       
        $pquery = "SELECT * FROM Profs WHERE owner='".$listchar[id]."'";
        $presult = mysql_query($pquery);  
        $bsubs = array(0,0,0);
        $ajobs = unserialize($listchar[jobs]);
        while ($bq = mysql_fetch_array( $presult ) )
        {
          $bstatus = unserialize($bq['status']);
          $bupgrades = unserialize($bq['upgrades']);
          for ($i=0; $i<$bupgrades[0]; $i++)
          {
            if ($bstatus[$i][0]==1)
            {
              $timeleft = ($bstatus[$i][2][1] - time());
              if ($timeleft <0 && $bq[type] != 0)
              {
                $bstatus[$i][0]=2;
                if ($bq[type]<7)
                {
                  $bstatus[$i][4]=5;
                  $qualbonus = $bupgrades[1][1]*5 + $bupgrades[1][2]*3;
                  $qualnum=$ajobs[$bq[type]]*5+$qualbonus+rand(1,50);
                  $bstatus[$i][5]=floor($qualnum/25);
                }
                $sstatus = serialize($bstatus);
                mysql_query("UPDATE Profs SET status='".$sstatus."' WHERE id='".$bq[id]."'");          
              }
            }
            $bsubs[$bstatus[$i][0]]++;
          }
        }
        echo "<td style='text-align: center;'>".$bsubs[2]."</td>";
        echo "<td style='text-align: center;'>".$bsubs[0]."</td>";
        echo "</tr>";
      }
    ?>   
    </table>
    </center>         

            </div>
          </div> <!-- close alt_tab content -->
  <?php
    }
}
  ?>
          <div class="tab-pane <?php if ($tab == 5) echo "active";?>" id="achieve_tab">
            <div class="col-sm-12">
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title">Local Ji</h3>
                </div>
                <div class="panel-body abox">
                  <div class='col-sm-12 visible-xs'> 
                    <table class='table table-responsive table-hover table-condensed table-clear small'>
                    <?php
                      for ($l=1; $l<=24; $l++)
                      {
                        $location = mysql_fetch_array(mysql_query("SELECT * FROM Locations WHERE id='$l'"));
                        $locji = $stats['loc_ji'.$l];
                    ?>
                      <tr>
                        <td align='right'><?php echo $location[name];?>: </td> 
                        <td><?php echo $locji;?></td>
                      </tr>
                    <?php
                      }
                    ?>
                    </table> 
                  </div>
                  <div class='col-sm-12 visible-sm'> 
                    <div class="col-sm-4">
                      <table class='table table-responsive table-hover table-condensed table-clear small'>
                      <?php
                        for ($l=1; $l<=8; $l++)
                        {
                          $location = mysql_fetch_array(mysql_query("SELECT * FROM Locations WHERE id='$l'"));
                          $locji = $stats['loc_ji'.$l];
                      ?>
                        <tr>
                          <td align='right'><?php echo $location[name];?>: </td> 
                          <td><?php echo $locji;?></td>
                        </tr>
                      <?php
                        }
                      ?>
                      </table> 
                    </div>
                    <div class="col-sm-4">
                      <table class='table table-responsive table-hover table-condensed table-clear small'>
                      <?php
                        for ($l=9; $l<=16; $l++)
                        {
                          $location = mysql_fetch_array(mysql_query("SELECT * FROM Locations WHERE id='$l'"));
                          $locji = $stats['loc_ji'.$l];
                      ?>
                        <tr>
                          <td align='right'><?php echo $location[name];?>: </td> 
                          <td><?php echo $locji;?></td>
                        </tr>
                      <?php
                        }
                      ?>
                      </table> 
                    </div>
                    <div class="col-sm-4">
                      <table class='table table-responsive table-hover table-condensed table-clear small'>
                      <?php
                        for ($l=17; $l<=24; $l++)
                        {
                          $location = mysql_fetch_array(mysql_query("SELECT * FROM Locations WHERE id='$l'"));
                          $locji = $stats['loc_ji'.$l];
                      ?>
                        <tr>
                          <td align='right'><?php echo $location[name];?>: </td> 
                          <td><?php echo $locji;?></td>
                        </tr>
                      <?php
                        }
                      ?>
                      </table> 
                    </div>
                  </div>                  
                  <div class='col-sm-12 visible-md visible-lg'> 
                    <div class="col-sm-3">
                      <table class='table table-responsive table-hover table-condensed table-clear small'>
                      <?php
                        for ($l=1; $l<=6; $l++)
                        {
                          $location = mysql_fetch_array(mysql_query("SELECT * FROM Locations WHERE id='$l'"));
                          $locji = $stats['loc_ji'.$l];
                      ?>
                        <tr>
                          <td align='right'><?php echo $location[name];?>: </td> 
                          <td><?php echo $locji;?></td>
                        </tr>
                      <?php
                        }
                      ?>
                      </table> 
                    </div>
                    <div class="col-sm-3">
                      <table class='table table-responsive table-hover table-condensed table-clear small'>
                      <?php
                        for ($l=7; $l<=12; $l++)
                        {
                          $location = mysql_fetch_array(mysql_query("SELECT * FROM Locations WHERE id='$l'"));
                          $locji = $stats['loc_ji'.$l];
                      ?>
                        <tr>
                          <td align='right'><?php echo $location[name];?>: </td> 
                          <td><?php echo $locji;?></td>
                        </tr>
                      <?php
                        }
                      ?>
                      </table> 
                    </div>
                    <div class="col-sm-3">
                      <table class='table table-responsive table-hover table-condensed table-clear small'>
                      <?php
                        for ($l=13; $l<=18; $l++)
                        {
                          $location = mysql_fetch_array(mysql_query("SELECT * FROM Locations WHERE id='$l'"));
                          $locji = $stats['loc_ji'.$l];
                      ?>
                        <tr>
                          <td align='right'><?php echo $location[name];?>: </td> 
                          <td><?php echo $locji;?></td>
                        </tr>
                      <?php
                        }
                      ?>
                      </table> 
                    </div>
                    <div class="col-sm-3">
                      <table class='table table-responsive table-hover table-condensed table-clear small'>
                      <?php
                        for ($l=19; $l<=24; $l++)
                        {
                          $location = mysql_fetch_array(mysql_query("SELECT * FROM Locations WHERE id='$l'"));
                          $locji = $stats['loc_ji'.$l];
                      ?>
                        <tr>
                          <td align='right'><?php echo $location[name];?>: </td> 
                          <td><?php echo $locji;?></td>
                        </tr>
                      <?php
                        }
                      ?>
                      </table> 
                    </div>
                  </div>                                               
                </div>
              </div>
            </div>           
            <div class="col-sm-4 col-md-3">
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title">General</h3>
                </div>
                <div class="panel-body abox"> 
                  <table class='table table-responsive table-hover table-condensed table-clear small'>
                    <tr>
                      <td align='right'>Achievements: </td> 
                      <td><?php echo $stats[achieved];?></td>
                    </tr>                  
                    <tr>
                      <td align='right'>Experience: </td> 
                      <td><?php echo $stats[xp];?></td>
                    </tr>
                    <tr>
                      <td align='right'>Ji: </td> 
                      <td><?php echo $stats[ji];?></td>
                    </tr>
                    <tr>
                      <td align='right'>Total Wins: </td> 
                      <td><?php echo $stats[wins]; if ($stats[battles]) echo " (".round($stats[wins]*100/$stats[battles])."%)";?></td>
                    </tr>
                    <tr>
                      <td align='right'>Duel Wins: </td> 
                      <td><?php echo $stats[duel_wins]; if ($stats[tot_duels]) echo " (".round($stats[duel_wins]*100/$stats[tot_duels])."%)";?></td>
                    </tr>
                    <tr>
                      <td align='right'>Total NPC Wins: </td> 
                      <td><?php echo $stats[npc_wins]; if ($stats[tot_npcs]) echo " (".round($stats[npc_wins]*100/$stats[tot_npcs])."%)";?></td>
                    </tr>                    
                    <tr>
                      <td align='right'>Purse: </td> 
                      <td><?php echo displayGold($stats[coin]);?></td>
                    </tr>
                    <tr>
                      <td align='right'>Bank: </td> 
                      <td><?php echo displayGold($stats[bankcoin]);?></td>
                    </tr>
                    <tr>
                      <td align='right'>Businesses: </td> 
                      <td><?php echo $stats[num_biz]." (".$stats[num_biz_types]." types)";?></td>
                    </tr>
                    <tr>
                      <td align='right'>Estates: </td> 
                      <td><?php echo $stats[num_estates];?></td>
                    </tr>
                    <tr>
                      <td align='right'>Tournament Wins: </td> 
                      <td><?php echo $stats[win_tourney];?></td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
            <div class="col-sm-4 col-md-3">
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title">Battles</h3>
                </div>
                <div class="panel-body abox"> 
                  <table class='table table-responsive table-hover table-condensed table-clear small'>
                    <tr>
                      <td align='right'>Enemy Wins: </td> 
                      <td><?php echo $stats[enemy_wins]; if ($stats[enemy_duels]) echo " (".round($stats[enemy_wins]*100/$stats[enemy_duels])."%)";?></td>
                    </tr>
                    <tr>
                      <td align='right'>Offensive Duel Wins: </td> 
                      <td><?php echo $stats[off_wins]; if ($stats[off_bats]) echo " (".round($stats[off_wins]*100/$stats[off_bats])."%)";?></td>
                    </tr>
                    <tr>
                      <td align='right'>Defensive Duel Wins: </td> 
                      <td><?php echo $stats[def_wins]; if ($stats[def_bats]) echo " (".round($stats[def_wins]*100/$stats[def_bats])."%)";?></td>
                    </tr>
                    <tr>
                      <td align='right'>Shadow NPC Wins: </td> 
                      <td><?php echo $stats[shadow_wins]; if ($stats[shadow_npcs]) echo " (".round($stats[shadow_wins]*100/$stats[shadow_npcs])."%)";?></td>
                    </tr>
                    <tr>
                      <td align='right'>Military NPC Wins: </td> 
                      <td><?php echo $stats[military_wins]; if ($stats[military_npcs]) echo " (".round($stats[military_wins]*100/$stats[military_npcs])."%)";?></td>
                    </tr>
                    <tr>
                      <td align='right'>Ruffian NPC Wins: </td> 
                      <td><?php echo $stats[ruffian_wins]; if ($stats[ruffian_npcs]) echo " (".round($stats[ruffian_wins]*100/$stats[ruffian_npcs])."%)";?></td>
                    </tr>
                    <tr>
                      <td align='right'>Channeler NPC Wins: </td> 
                      <td><?php echo $stats[channeler_wins]; if ($stats[channeler_npcs]) echo " (".round($stats[channeler_wins]*100/$stats[channeler_npcs])."%)";?></td>
                    </tr>
                    <tr>
                      <td align='right'>Animal NPC Wins: </td> 
                      <td><?php echo $stats[animal_wins]; if ($stats[animal_npcs]) echo " (".round($stats[animal_wins]*100/$stats[animal_npcs])."%)";?></td>
                    </tr>
                    <tr>
                      <td align='right'>Exotic NPC Wins: </td> 
                      <td><?php echo $stats[exotic_wins]; if ($stats[exotic_npcs]) echo " (".round($stats[exotic_wins]*100/$stats[exotic_npcs])."%)";?></td>
                    </tr>
                    <tr>
                      <td align='right'>Horde Wins: </td> 
                      <td><?php echo $stats[horde_wins];?></td>
                    </tr>
                    <tr>
                      <td align='right'>Army Wins: </td> 
                      <td><?php echo $stats[army_wins];?></td>
                    </tr>
                  </table>                  
                </div>
              </div>
            </div>
            <div class="col-sm-4 col-md-3">
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title">Coin</h3>
                </div>
                <div class="panel-body abox"> 
                  <table class='table table-responsive table-hover table-condensed table-clear small'>
                    <tr>
                      <td align='right'>Net Worth: </td> 
                      <td><?php echo displayGold($stats[net_worth]);?></td>
                    </tr>
                    <tr>
                      <td align='right'>Coin Donated: </td> 
                      <td><?php echo displayGold($stats[coin_donated]);?></td>
                    </tr>
                    <tr>
                      <td align='right'>Duel Earnings: </td> 
                      <td><?php echo displayGold($stats[duel_earn]);?></td>
                    </tr>
                    <tr>
                      <td align='right'>Merchant Earnings: </td> 
                      <td><?php echo displayGold($stats[item_earn]);?></td>
                    </tr>
                    <tr>
                      <td align='right'>Gambling Earnings: </td> 
                      <td><?php echo displayGold($stats[dice_earn]);?></td>
                    </tr>
                    <tr>
                      <td align='right'>Business Earnings: </td> 
                      <td><?php echo displayGold($stats[prof_earn]);?></td>
                    </tr>
                    <tr>
                      <td align='right'>Quest Earnings: </td> 
                      <td><?php echo displayGold($stats[quest_earn]);?></td>
                    </tr>
                  </table>                  
                </div>
              </div>
            </div>
            <div class="col-sm-4 col-md-3">
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title">Quests</h3>
                </div>
                <div class="panel-body abox"> 
                  <table class='table table-responsive table-hover table-condensed table-clear small'>
                    <tr>
                      <td align='right'>Total Quest Done: </td> 
                      <td><?php echo $stats[quests_done];?></td>
                    </tr>
                    <tr>
                      <td align='right'>Find Quest Done: </td> 
                      <td><?php echo $stats[find_quests_done];?></td>
                    </tr>
                    <tr>
                      <td align='right'>NPC Quest Done: </td> 
                      <td><?php echo $stats[npc_quests_done];?></td>
                    </tr>
                    <tr>
                      <td align='right'>Horde Quest Done: </td> 
                      <td><?php echo $stats[horde_quests_done];?></td>
                    </tr>
                    <tr>
                      <td align='right'>Escort Quest Done: </td> 
                      <td><?php echo $stats[escort_quests_done];?></td>
                    </tr>
                    <tr>
                      <td align='right'>Item Quest Done: </td> 
                      <td><?php echo $stats[item_quests_done];?></td>
                    </tr>
                    <tr>
                      <td align='right'>Support Quest Ji: </td> 
                      <td><?php echo $stats[support_quest_ji];?></td>
                    </tr>
                    <tr>
                      <td align='right'>Player Quest Done: </td> 
                      <td><?php echo $stats[play_quests_done];?></td>
                    </tr>                    
                    <tr>
                      <td align='right'>Quests Created: </td> 
                      <td><?php echo $stats[quests_created];?></td>
                    </tr>
                    <tr>
                      <td align='right'>Own Quest Completed: </td> 
                      <td><?php echo $stats[my_quests_done];?></td>
                    </tr>
                  </table>
                </div>
              </div>
            </div> 
          </div> <!-- close achieve_tab content -->
        </div> <!-- close tab content -->
      </div> <!-- close tabbable -->
    </div> <!-- close outer col -->
  </div> <!-- close row -->
<script type="text/javascript">
  function submitFormBio(ddd)
  {
    document.duelform.ddd.value = ddd;
    document.duelform.submit();
  }
</script>  
<?php
include('footer.htm');
?>