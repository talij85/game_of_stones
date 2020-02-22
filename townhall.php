<?php

/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/jobFuncs.php");
include_once("admin/locFuncs.php");


// Check if city has been destroyed. If so, don't display anything.
if (!$location[isDestroyed])
{
function displayBuildInfo($build, $blvl, $name )
{
  $prodata = "binfos[".$build."] = \"<div class='panel panel-primary'><div class='panel-heading'><h3 class='panel-title'>".$name." Bonuses</h3></div><div class='panel-body abox' align='center'>";
  $prodata .= itm_info(cparse(getBuildBonuses($build, $blvl)))."<br/>";
  if ($blvl < 5) $prodata .= "<br/><i>Next Level:<br/>".itm_info(cparse(getBuildBonuses($build, $blvl+1)));
  $prodata .= "</div></div>\";";  
  return $prodata;
}

function displayUniqBuildInfo($build, $blvl, $name, $ubb)
{
  $prodata = "binfos[".$build."] = \"<div class='panel panel-info'><div class='panel-heading'><h3 class='panel-title'>".$name." Bonuses</h3></div><div class='panel-body abox' align='center'>";
  if (!$blvl) { $prodata .= "<br/><i>Next Level:<br/>"; }
  $prodata .= "City Bonus: ".itm_info(cparse($ubb[$name][0]));
  $prodata .= "Clan Bonus: ".itm_info(cparse($ubb[$name][1]));
  if (!$blvl) { $prodata .= "</i>"; }
  $prodata .= "</div></div>\";";   
  return $prodata;
}

$id=$char['id'];
$jobs = unserialize($char[jobs]);
$wikilink = "City+Hall";
$tab = mysql_real_escape_string($_REQUEST[tab]);

$loc_name = str_replace('-ap-','&#39;',$char['location']);
$loc_query = $char['location'];
$soc_name = $char[society];

$message = $loc_name." City Hall";

$shoplvls=unserialize($location['shoplvls']);

// KICK OFF PAGE IF NOT CLAN LEADER
$society = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$soc_name' "));
$subleaders = unserialize($society['subleaders']);
$subs = $society['subs'];
$offices = unserialize($society['offices']);
$areascore = unserialize($society['area_score']);
$locscore = $areascore[$location[id]];

$slead = 0;
$soff = 0;
if ($offices[$location[id]][0]) $soff=1;
if (strtolower($name) == strtolower($society[leader]) && strtolower($lastname) == strtolower($society[leaderlast]) ) 
{
  $slead=1;
}
if ($subs > 0)
{
  foreach ($subleaders as $c_n => $c_s)
  {
    if (strtolower($name) == strtolower($c_s[0]) && strtolower($lastname) == strtolower($c_s[1]) )
    {
      $slead=1;
    }
  }
}  
if ($offices[$location[id]][0] != '1')
{
  if (strtolower($name) == strtolower($offices[$location[id]][0]) && strtolower($lastname) == strtolower($offices[$location[id]][1]) ) 
  {
    $slead=1;  
  }
} 

$b = 0;
$ruler = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$location[ruler]' "));
if ($location['ruler'] == $society[name])
{
  $rulerscore = $locscore;
  if ($slead==1)
  {
    $b=1;
  }    
}
else 
{
  $rareascore = unserialize($ruler['area_score']);
  $rulerscore = $rareascore[$location[id]];
}

$gtype=mysql_real_escape_string($_POST[game]);
$minw=mysql_real_escape_string($_POST[minw]);
$maxw=mysql_real_escape_string($_POST[maxw]);
$bought=mysql_real_escape_string($_POST[bought]);
$buildup=mysql_real_escape_string($_POST[buildup]);

$ustats = mysql_fetch_array(mysql_query("SELECT * FROM Users_stats WHERE id='$id'"));

$myLocBiz = array (0,0,0,0,0,0,0,0,0,0,0,0,0);
$myLocBiz[99]=0;
$myTotBiz = array (0,0,0,0,0,0,0,0,0,0,0,0,0);
$myTotBiz[99]=0;
$query = "SELECT * FROM Profs WHERE owner='$id'";
$result = mysql_query($query);
$most_biz=0;
while ($bq = mysql_fetch_array( $result ) )
{
  $myTotBiz[$bq[type]]++;
  if($most_biz<$myTotBiz[$bq[type]]) $most_biz = $myTotBiz[$bq[type]];
  if ($bq[location]==$loc_query)
  {
    $myLocBiz[$bq[type]]++;
  }
}
$num_biz_types=0;
for ($x=0; $x< count($myTotBiz); $x++)
{
  if ($myTotBiz[$x]) $num_biz_types++;
}
if ($myTotBiz[99]) $num_biz_types++;

if ($bought != '' && $bought != '-1')
{
  $gobiz = 1;
  if ($myLocBiz[$bought]) $gobiz=0;
  if ($gobiz)
  {
    if ($myTotBiz[$bought] < 6)
    {
      $bcost=($biz_costs[$bought][$myTotBiz[$bought]]*(100+$town_bonuses[bG])/100);
      if ($bcost <= $char[gold])
      {
        $char[gold] -= $bcost;
        $ustats[prof_earn] -= $bcost;
        mysql_query("UPDATE Users_stats SET prof_earn='".$ustats[prof_earn]."' WHERE id='".$char[id]."'");
        $tools = array(0,0,0);
        $spec = array(0,0,0,0,0,0,0,0,0);
        $bupgrades = serialize(array(1,$tools,$spec));
        $prod = 0;
        $proname=array("");
        $protime=array(0,0);
        $prostore=0;
        $myTotBiz[$bought]++;
        if ($myTotBiz[$bought]>$most_biz) $most_biz=$myTotBiz[$bought];
        if ($myTotBiz[$bought]==1) $num_biz_types++;
        $ustats[num_biz]++;
        if ($ustats[most_biz]< $most_biz) $ustats[most_biz] = $most_biz;
        if ($ustats[num_biz_types]< $num_biz_types) $ustats[num_biz_types] = $num_biz_types;
        if ($ustats[highest_biz]<1) $ustats[highest_biz]=1;
        
        for ($i=0;$i<9;$i++)
        {
          $status[$i]=array($prod,$proname,$protime,$prostore);
        }
        $statuss = serialize($status);
        $sql = "INSERT INTO Profs (name,type,     owner,location,    value, started,     upgrades,    status)
                           VALUES ('',  '$bought','$id','$loc_query','2500','".time()."','$bupgrades','$statuss')";
        $resultt = mysql_query($sql, $db);
        mysql_query("UPDATE Users SET gold='".$char[gold]."' WHERE id='".$char[id]."'");
        mysql_query("UPDATE Users_stats SET prof_earn='".$ustats[prof_earn]."', highest_biz='".$ustats[highest_biz]."', num_biz='".$ustats[num_biz]."', most_biz='".$ustats[most_biz]."', num_biz_types='".$ustats[num_biz_types]."' WHERE id='".$char[id]."'");
        $message = "Business Bought for ".displayGold($bcost)."!";    
     
        $myLocBiz[0] = '';
        $query = "SELECT * FROM Profs WHERE owner='$id' AND location='$loc_query'";
        $result = mysql_query($query);  
        while ($bq = mysql_fetch_array( $result ) )
        {
          $myLocBiz[$bq[type]]=1;
        }
      }
      else { $message="You can't afford that!"; }
    }
    else { $message="You already own the maximum number of these!"; }
  }
  else
    $message = "You already own one of those in this city.";
}

if ($buildup != '' && $buildup != '-1')
{
  if ($b)
  {
    $lvl = $upgrades[$buildup];
    if ($buildup<6)
    {
      $maxlvl = 4;
      if ($location['num_ups']>=10) $maxlvl=5;
      $cost=15000*($location['num_ups']+1)*($lvl+1);
      $gcost = floor($cost/1000);
      $good1 = $build_goods[$buildup];
      $good2 = 0;
      $bname = $build_names[$buildup];
      $numups=$location['num_ups']+1;
    }
    else
    {
      $maxlvl = 1;
      $bname = $unique_buildings[$location[name]][$buildup-6];
      $cost=$build_costs[$lvl+5];
      $gcost= floor($build_costs[$lvl+5]/4000);
      $good1=$unique_build_goods[$bname][0];
      $good2=$unique_build_goods[$bname][1];
      $numups=$location['num_ups'];
    }
    if ($lvl < $maxlvl )
    {
      if ($cost <= $location['bank'])
      {
        if ($gcost <= $location[$estate_unq_prod[$good1]])
        {
          if ($good2 == 0 || $gcost <= $location[$estate_unq_prod[$good2]])
          {
            $location['bank'] -= $cost;
            $upgrades[$buildup] += 1;
            $newups = serialize($upgrades);
            $location['num_ups']=$numups;
            $location[$estate_unq_prod[$good1]] -= $gcost;
            $gquery = $estate_unq_prod[$good1]."='".$location[$estate_unq_prod[$good1]]."', ";
            if ($good2 != 0) 
            {
              $location[$estate_unq_prod[$good2]] -= $gcost;
              $gquery .= $estate_unq_prod[$good2]."='".$location[$estate_unq_prod[$good2]]."', ";
            }
            $result = mysql_query("UPDATE Locations SET ".$gquery." upgrades='$newups', bank='$location[bank]', num_ups='$numups' WHERE name='$loc_query'");
            $message = $bname." upgraded to level ".($lvl+1)."!";
          }
          else {$message = "The city doesn't have enough ".$estate_unq_prod[$good2]." to do that!";}
        }
        else {$message = "The city doesn't have enough ".$estate_unq_prod[$good1]." to do that!";}
      }
      else {$message = "The city doesn't have enough money to do that!";}
    }
    else {$message = "That building is already at it's max level!";}
  }
  else {$message = "You don't have permission to do that!";}
}

// dice settings
if ($gtype)
{
  if ($minw <= $maxw)
  {
    if ($gtype == "t") $gtype = "";
    $location[gtype] = $gtype;
    $location[minw] = $minw;
    $location[maxw] = $maxw;    
    mysql_query("UPDATE Locations SET gtype='$gtype', minw='$minw', maxw='$maxw' WHERE name='$loc_query'");
    $message = "Dice Game Setup!";
  }
  else
  {
    $message = "You can't set the tavern minumum to be higher than the maximum!";
  }
}

// announcements
if ($_POST['changer2'])
{
  $newtext = htmlspecialchars(stripslashes($_POST['info']),ENT_QUOTES);
  if (strlen($newtext) < 501)
  {
    if (preg_match('/^[-a-z0-9+.,!@*_&#:\/%;?\s]*$/i',$newtext))
    {
      $message = "City Announcement successfully posted";
      $query = "UPDATE Locations SET info='$newtext' WHERE name='$loc_query'";
      $result = mysql_query($query);
      $location['info']=$newtext;
    }
    else {$message="What strange characters you used. Officials refuse to post your note";}
  }
  else {$message="Info must be a max of 500 characters";}
}

// tournaments
if ($_POST['maketourney'])
{
  $ttype = mysql_real_escape_string($_POST[ttype]);
  $minp = mysql_real_escape_string($_POST[minp]);
  $maxp = mysql_real_escape_string($_POST[maxp]);
  $pduels = mysql_real_escape_string($_POST[pduels]);
  $tfee = mysql_real_escape_string($_POST[tfee]);
  $pdistro = mysql_real_escape_string($_POST[pdistro]);
  
  if ($maxp == '0' || $maxp >= $minp)
  {
    if ($ttype > 0)
    {
      if ($location[bank]>=$tfee*2)
      {
        $location[bank]-= $tfee*2;
        $tstarts = intval(time()/3600)+24;
        $tends = $tstarts+24;
        $tprize= $tfee*2;
        $players = serialize(array(array('0','0')));
        $rewards = serialize(array("$tprize",'0'));
        $rules = serialize(array($minp*10,$maxp*10,$pduels,$tfee));
        $sql = "INSERT INTO Contests (type,    location,    starts,    ends,    done,distro,    contestants,rules,   results   ,reward)
                              VALUES ('$ttype','$loc_query','$tstarts','$tends','0', '$pdistro','$players', '$rules','$players','$rewards')";
        $resultt = mysql_query($sql, $db);
        $myTourney = mysql_fetch_array(mysql_query("SELECT * FROM Contests WHERE location='$loc_query' AND starts='$tstarts'"));
        mysql_query("UPDATE Locations SET last_tourney='$myTourney[id]', bank='$location[bank]' WHERE name='$loc_query'");
        $message = "Tournament Announced!";
        
        // Update City Rumors
        $cityRumors = mysql_fetch_array(mysql_query("SELECT * FROM messages WHERE id='50000'"));

        $rumorMessages = unserialize($cityRumors[message]);
        $numRumors = count($rumorMessages);
        
        $myMsg = "<".$loc_query."_".time().">` A new Tournament has been announced!|";
        $rumorMessages[$numRumors] = $myMsg;
    
        $rumorMessages = pruneMsgs($rumorMessages, 50);
        $newRumors = serialize($rumorMessages);
        mysql_query("UPDATE messages SET message='$newRumors' WHERE id='50000'");        
      }
      else $message = "There is not enough in the city bank to setup that tournament!";
    }
    else $message = "You must select a Battle Type!";
  }
  else $message = "Invalid player level range. Please try again!";
}

// sign up
if ($_POST[signup])
{
  $myTourney = mysql_fetch_array(mysql_query("SELECT * FROM Contests WHERE id='$location[last_tourney]' "));
  $rules = unserialize($myTourney[rules]);
  if ($myTourney[starts] > intval(time()/3600)) 
  {
    if ($char[lastcontest] != $myTourney[id])
    {
      $lastdone=0;
      if ($char[lastcontest])
      {
        $lastTourney = mysql_fetch_array(mysql_query("SELECT * FROM Contests WHERE id='$char[lastcontest]' "));
        $lastdone=$lastTourney[done];
      }
      else
      {
        $lastdone=1;
      }
      if ($lastdone)
      {
        if ($rules[0] <= $char[level] && (!($rules[1]) || $char[level] <= $rules[1]))
        {
          if ($char[gold] >= $rules[3])
          {
            $char[gold] -= $rules[3];
            $tcontestants = unserialize($myTourney[contestants]);
            $cc = $char[id];
            $tcontestants[$cc][0] = $char[name]."_".$char[lastname];
            $tcontestants[$cc][1] = 0; 
            $sc = serialize($tcontestants);
            $prize=unserialize($myTourney[reward]);
            $prize[0]+=$rules[3];
            $sp = serialize($prize);
            $ustats[num_tourney]++;
            mysql_query("UPDATE Users_stats SET num_tourney='$ustats[num_tourney]' WHERE id='$char[id]'");
            mysql_query("UPDATE Users SET lastcontest='$myTourney[id]', gold='$char[gold]' WHERE id='$char[id]'");
            mysql_query("UPDATE Contests SET contestants='$sc', reward='$sp' WHERE id='$myTourney[id]'");
            $message = "You have signed up for the tournament. Prepare yourself!";
          }
          else $message= "You do not have enough to pay the entry fee!";
        }
        else $message="You are non within the level range of this tournament!";
      }
      else $message="You cannot be in more than one tournament at a time!";
    }
    else $message="You are signed up for this tournament!";
  }
}

if ($_POST['makewar'] && $slead)
{
  $lastdone=0;
  if ($society[last_war])
  {
    $lastWar = mysql_fetch_array(mysql_query("SELECT * FROM Contests WHERE id='$society[last_war]' "));
    if (($lastWar[ends]+24) <= intval(time()/3600)) $lastdone=1;
  }
  else
  {
    $lastdone=1;
  }
  if ($lastdone)
  {
    $ttype = 10;
    $minp = 0;
    $maxp = 0;
    $pduels = 0;
    $tfee = intval($location[bank]/10);
    $pdistro = 10;
  
    if ($society[bank]>=$tfee)
    {
      $society[bank]-= $tfee;
      $tstarts = intval(time()/3600)+24;
      $tends = $tstarts+24;
      $tprize= $tfee;
    
      $contestants = array(array());
      $cc = $society[id];
      $contestants[$cc][0] = $society[name];
      $contestants[$cc][1] = 0;
      $rc = $ruler[id];
      $contestants[$rc][0] = $ruler[name];
      $contestants[$rc][1] = 0;       
      $sc = serialize($contestants);
    
      $part = array(array());
      $part[0][$cc] = 0;
      $part[0][$rc] = 0;
      $sp = serialize($part);
    
      $swars= unserialize($society[wars]);           
      $rwars= unserialize($ruler[wars]);
     
      $rewards = serialize(array("$tprize",'0'));
      $rules = serialize(array($rc,$cc,0,$tfee));
      $sql = "INSERT INTO Contests (type,    location,    starts,    ends,    done,distro,    contestants,rules,   participation,results,   reward)
                            VALUES ('$ttype','$loc_query','$tstarts','$tends','0', '$pdistro','$sc',      '$rules','$sp',        '$players','$rewards')";
      $resultt = mysql_query($sql, $db);
      $myWar = mysql_fetch_array(mysql_query("SELECT * FROM Contests WHERE location='$loc_query' AND starts='$tstarts'"));
      $location[last_war]=$myWar[id];
      mysql_query("UPDATE Locations SET last_war='$myWar[id]' WHERE name='$loc_query'");
      $swars[$myWar[id]]=0;
      $rwars[$myWar[id]]=0;
      $ssw=serialize($swars);
      $srw=serialize($rwars);
    
      $align = $ruler[align];
      if ($align == 2) $society[align] += -25;
      else if ($align == 1) $society[align] += -10;
      else if ($align == 0) $society[align] += -5;
      else if ($align == -1) $society[align] += 10;
      else if ($align == -2) $society[align] += 25;
     
      mysql_query("UPDATE Soc SET bank='$society[bank]', wars='$ssw', last_war='$myWar[id]' WHERE name='$society[name]'");
      //    mysql_query("UPDATE Soc SET bank='$society[bank]', wars='$ssw', last_war='$myWar[id]', align='".$society[align]."' WHERE name='$society[name]'");
      mysql_query("UPDATE Soc SET wars='$srw' WHERE name='$ruler[name]'");
      $message = "Clan Battle Announced!";
    
      // Update City Rumors
      $cityRumors = mysql_fetch_array(mysql_query("SELECT * FROM messages WHERE id='50000'"));

      $rumorMessages = unserialize($cityRumors[message]);
      $numRumors = count($rumorMessages);
        
      $myMsg = "<".$loc_query."_".time().">` ".$society[name]." has started a clan battle against ".$ruler[name]."!|";
      $rumorMessages[$numRumors] = $myMsg;
    
      $rumorMessages = pruneMsgs($rumorMessages, 50);
      $newRumors = serialize($rumorMessages);
      mysql_query("UPDATE messages SET message='$newRumors' WHERE id='50000'");     
    }
    else $message = "Your clan does not have enough funds to take part in such a battle!";
  }
  else $message = "You must wait 24 hours after your last clan battle completed before declaring a new one!";
}

if ($_POST['joinwar'] && $slead)
{
  $myWar = mysql_fetch_array(mysql_query("SELECT * FROM Contests WHERE id='$location[last_war]' "));
  $rules = unserialize($myWar[rules]);
  if ($myWar[starts] > intval(time()/3600)) 
  {
      $lastdone=0;
      if ($society[last_war])
      {
        $lastWar = mysql_fetch_array(mysql_query("SELECT * FROM Contests WHERE id='$society[last_war]' "));
        if (($lastWar[ends]+24) <= intval(time()/3600)) $lastdone=1;
      }
      else
      {
        $lastdone=1;
      }
      
      if ($lastdone)
      {
          if ($society[bank] >= $rules[3])
          {
            $society[bank] -= $rules[3];
            $wrewards = unserialize($myWar[reward]);
            $wrewards[0] += $rules[3];
            $swr = serialize($wrewards);
            $contestants = unserialize($myWar[contestants]);
            $cc = $society[id];
            $contestants[$cc][0] = $society[name];
            $contestants[$cc][1] = 0; 
            $sc = serialize($contestants);
            mysql_query("UPDATE Contests SET reward='$swr', contestants='$sc' WHERE id='$myWar[id]'");
            $swars= unserialize($society[wars]);
            $swars[$myWar[id]]=0;
            $ssw=serialize($swars);
            $align = $ruler[align];
            if ($align == 2) $society[align] += -25;
            else if ($align == 1) $society[align] += -10;
            else if ($align == 0) $society[align] += -5;
            else if ($align == -1) $society[align] += 10;
            else if ($align == -2) $society[align] += 25;
            mysql_query("UPDATE Soc SET bank='$society[bank]', wars='$ssw', last_war='$myWar[id]' WHERE name='$society[name]'");
            //            mysql_query("UPDATE Soc SET bank='$society[bank]', wars='$ssw', last_war='$myWar[id]', align='".$society[align]."' WHERE name='$society[name]'");
            $message = "Clan Battle Joined!";
          }
          else $message= "You do not have enough to pay the entry fee!";
      }
      else $message="You cannot start a new clan battle yet! Wait 24 hours after your last one finishes!";
  }
}

$bg = "background-image:url('images/townback/".str_replace(' ','_',strtolower($char['location'])).".jpg'); ";
if (!$tab) $tab =1;
include('header.htm');
?>
<script language="Javascript">
  var binfos = new Array(6);
  <?php 
    for ($i = 0; $i < 6; $i++)
    {
      echo displayBuildInfo($i, $upgrades[$i],$build_names[$i])."\n";
    }
    for ($i=6; $i<8; $i++)
    {
      echo displayUniqBuildInfo($i,$upgrades[$i], $unique_buildings[$location[name]][$i-6], $unique_build_bonuses)."\n";
    }
  ?>

  function swapinfo(build)
  {
    document.getElementById('bonuses').innerHTML=binfos[build];
  }

  function buildUp (num)
  {
    document.buildform.buildup.value=num;
    document.buildform.submit();
  }
</script>

  <div class="row solid-back">
    <div id="content">
      <ul id="halltabs" class="nav nav-tabs" data-tabs="tabs">
        <li <?php if ($tab == 1) echo "class='active'";?>><a href="#biz_tab" data-toggle="tab">Businesses</a></li>         
        <li <?php if ($tab == 2) echo "class='active'";?>><a href="#tourney_tab" data-toggle="tab">Tournament</a></li>
        <li <?php if ($tab == 3) echo "class='active'";?>><a href="#cb_tab" data-toggle="tab">Clan Battle</a></li>
      <?php 
        if ($b)
        {
      ?>
        <li <?php if ($tab == 4) echo "class='active'";?>><a href="#settings_tab" data-toggle="tab">City Settings</a></li>
      <?php 
        }
      ?>
      </ul>
      <div class="tab-content">
        <div class="tab-pane <?php if ($tab == 1) echo 'active';?>" id="biz_tab">
          <div class='col-sm-6 col-sm-push-3'>      
            <form name="bizForm" action="townhall.php" method="post">
              <b>Businesses For Sale</b></font><br/><br/>
              <table class='table table-repsonsive table-hover table-clear small'>
                <tr>
                  <th>Business</th>
                  <th>Costs</th>
                  <th>Action</th>
                </tr>
                <?php
                  // Build a list of businesses in the right order to display
                  $bizarray = array();
                  $bizarray[] = 0;
                  for ($t=4; $t<7; $t++)
                  {
                    $bizarray[] = $t;
                  }
                  $bizarray[] = $loc_biz[$loc_query][0];
                  $bizarray[] = 99;

                  for ($x=0; $x<count($bizarray); $x++)
                  {
                    $t = $bizarray[$x];
                    echo "<tr><td align='center'>".$job_biz_names[$t][0]."</td>";
                    echo "<td align='center'>".displayGold(($biz_costs[$t][$myTotBiz[$t]]*(100+$town_bonuses[bG])/100))."</td>";
                    if (($jobs[$t] >0 && $myTotBiz[$t] < 6)|| ($t==99 && $stats['loc_ji'.$location[id]] > 100))
                    {
                      if ($myLocBiz[$t])
                      {
                        echo "<td align='center'><font class='foottext'>Owned</font></td></tr>";
                      }
                      else 
                        echo "<td align='center'><input type='button' class='btn btn-xs btn-success' value='Buy' onClick='javascript:buyBiz(".$t.")'/></td></tr>";
                    }
                    else
                    {
                      echo "<td align='center'><font class='foottext'>Not Qualified</font></td></tr>";
                    }
                  }
                ?>
              </table>
              <input type='hidden' name="bought" value='-1'/>
            </form>
          </div>
        </div>
        <div class="tab-pane <?php if ($tab == 2) echo 'active';?>" id="tourney_tab">
          <div class='row'>
        <?php
          if ($location[last_tourney])
          {
            $myTourney = mysql_fetch_array(mysql_query("SELECT * FROM Contests WHERE id='$location[last_tourney]' "));
          }
          else
          {
            $myTourney = 0;
          }
          if ($myTourney != 0) // Previous tournament
          {
            if ($myTourney[starts] > intval(time()/3600))
            {
              $tminus = intval(($myTourney[starts]*3600-time())/60);
              echo "Contest starts in: ".displayTime($tminus,0)."<br/>";
            }
            else if ($myTourney[ends] > intval(time()/3600))
            {
              $tminus = intval(($myTourney[ends]*3600-time())/60);
              echo "Contest ends in: ".displayTime($tminus,0)."<br/>";
            }
            else
            {
              $next_tourney = 0;
              $tpast = intval((time() - $myTourney[ends]*3600)/60);
              $tourney_ago = displayTime($tpast);
              $next_tourney = 7200-$tpast;
              echo "Last Tourney: ".$tourney_ago."<br/>";
              if ($next_tourney >0)
              {  
                echo "Wait before starting new tourney: ".displayTime($next_tourney,0)."<br/>";
              }
            }
        ?>
          </div>
          <div class='row'>
            <table class='table table-repsonsive table-hover table-clear small'>
              <tr>
                <td align=right><b>Battle Type:</b></td>
                <td><?php echo $tourneyType[$myTourney[type]];?></td>
              </tr>
              <tr>
                <td align=right><b>Prize Distribution:</b></td>
                <td><?php echo $tourneyDistro[$myTourney[distro]];?></td>
              </tr>
              <tr>
                <td align=right><b>Player level range:</b></td>
                <td>
              <?php 
                $rules = unserialize($myTourney[rules]);
                if ($rules[0] > 0) $myMin= "Level ".$rules[0]; else $myMin = "Any";
                if ($rules[1] > 0) $myMax= "Level ".$rules[1]; else $myMax = "Any";
                echo $myMin." - ".$myMax;
              ?>
                </td>
              </tr>
              <tr>
                <td align=right><b>Battles per Player:</b></td>
                <td><?php echo $rules[2];?></td>
              </tr>
              <tr>
                <td align=right><b>Entry Fee:</b></td>
                <td><?php echo displayGold($rules[3]);?></td>
              </tr>
            </table>
        <?php 
            // Join tournament?
            if ($myTourney[starts] > intval(time()/3600)) 
            {
              if ($char[lastcontest] != $myTourney[id])
              {
                $lastdone=0;
                if ($char[lastcontest])
                {
                  $lastTourney = mysql_fetch_array(mysql_query("SELECT * FROM Contests WHERE id='$char[lastcontest]' "));
                  $lastdone=$lastTourney[done];
                }
                else
                {
                  $lastdone=1;
                }
                if ($lastdone)
                {
                  if ($rules[0] <= $char[level] && (!($rules[1]) || $char[level] <= $rules[1]))
                  {
                    if ($char[gold] >= $rules[3])
                    {
        ?>
            <form name='joinForm' id='joinForm' action='townhall.php' method='post'>
              <input type='hidden' name='signup' id='signup' value=1 />
              <input type="hidden" name="tab" value="2" id="tab" />
              <input type='submit' value='Sign Up!' class='btn btn-sm btn-warning' />
            </form>

        <?php
                    }
                    else echo "You do not have enough to pay the entry fee!";
                  }
                  else echo "You are non within the level range of this tournament!";
                }
                else echo "You cannot be in more than one tournament at a time!";
              }
              else 
              {
                echo "You are signed up for this tournament!";
              }
            }
          }
          if ( $myTourney[id] != 0 )
          {      
            $tcontestants = unserialize($myTourney[contestants]);
            $tresults = unserialize($myTourney[results]);
            echo "<table class='table table-repsonsive table-hover table-clear small'>";
            echo "<tr>";
              echo "<th></th>";
              echo "<th colspan=2><a href='#' class='btn btn-xs btn-block btn-default'>Total</a></th>";
              echo "<th colspan=4><a href='#' class='btn btn-xs btn-block btn-default'>My Results</a></th>";
            echo "</tr>";
            echo "<tr>";
              echo "<th>Contestant</th>";            
              echo "<th>Score</th>";
              echo "<th>Battles</th>";
              echo "<th>Attacked</th>";
              echo "<th>Defended</th>";
              echo "<th>My Points</th>";
              echo "<th>Opponent</th>";
            echo "</tr>";
            if (count($tcontestants) > 1)
            {
              $numbat= array();
              foreach ($tcontestants as $cid => $cdata)
              {
                if ($cdata[0]) 
                { 
                  $ranks[$cdata[0]]=$cdata[1]; 
                  $rid[$cdata[0]]=$cid;
                }
              }
              arsort($ranks);
              foreach ($tresults as $cid => $cres)
              {
                if ($cres)
                {
                  foreach ($cres as $oid => $ores)
                  {
                    $numbat[$oid]+=$ores[0];
                    $numbat[$cid]+=$ores[0];
                  }
                }
              }
              if (!$tresults[$char[id]]) $tresults[$char[id]] = array();
              foreach ($ranks as $rname => $rscore) 
              {
                if (!$tresults[$rid[$rname]]) $tresults[$rid[$rname]] = array();
                if (!$tresults[$char[id]][$rid[$rname]]) $tresults[$char[id]][$rid[$rname]] = array(0,0,0);
                if (!$tresults[$rid[$rname]][$char[id]]) $tresults[$rid[$rname]][$char[id]] = array(0,0,0);
                $cname = explode('_', $rname);
                
                if ($rid[$rname] == $id) $classn="btn-info";
                else $classn="btn-primary";
                echo "<tr>";
                echo "<td align='center'><a class='btn btn-xs btn-block btn-wrap ".$classn."' href='bio.php?name=".$cname[0]."&last=".$cname[1]."'>".$cname[0]." ".$cname[1]."</a></td>";
                echo "<td align='center'>".$rscore."</td>";
                echo "<td align='center'>".$numbat[$rid[$rname]]."</td>";
                echo "<td align='center'>".$tresults[$char[id]][$rid[$rname]][0]."</td>";
                echo "<td align='center'>".$tresults[$rid[$rname]][$char[id]][0]."</td>";
                echo "<td align='center'>".($tresults[$char[id]][$rid[$rname]][1]+$tresults[$rid[$rname]][$char[id]][2])."</td>";
                echo "<td align='center'>".($tresults[$char[id]][$rid[$rname]][2]+$tresults[$rid[$rname]][$char[id]][1])."</td>";
                echo "</tr>";
              }
            }
            echo "</table>";
          }
          if ($b && ($myTourney[ends]+72) <= intval(time()/3600))
          {
        ?>
            <b>Tournament Rules</b><br/><br/>
            <form class="form-horizontal" name='tourneyForm' id='tourneyForm' action="townhall.php" method="post">
              <div class="form-group form-group-sm">
                <label for='ttype' class='control-label col-sm-4'>Battle Type: </label>
                <div class='col-sm-8'>
                    <select id="ttype" name='ttype' class="form-control gos-form">
                      <option value="0">-Select-</option>
                      <option value="1"><?php echo $tourneyType[1];?></option>
                      <option value="2"><?php echo $tourneyType[2];?></option>
                      <option value="3"><?php echo $tourneyType[3];?></option>
                      <option value="4"><?php echo $tourneyType[4];?></option>
                    </select> 
                </div>
              </div>
              <div class="form-group form-group-sm">
                <label for='pdistro' class='control-label col-sm-4'>Prize Distribution: </label>
                <div class='col-sm-8'>
                    <select id="pdistro" name='pdistro' class="form-control gos-form">
                      <option value="0">-Select-</option>
                      <option value="1"><?php echo $tourneyDistro[1];?></option>
                      <option value="2"><?php echo $tourneyDistro[2];?></option>
                    </select>
                </div>
              </div>  
              <div class="form-group form-group-sm">
                <label for='minp' class='control-label col-sm-4'>Minimum Level: </label>
                <div class='col-sm-8'>
                    <select id="minp" name='minp' class="form-control gos-form">
                    <?php
                      for ($l=0; $l<=10; $l++)
                      {
                        if ($l==0) $minp = "None"; else $minp= $l*10;
                        echo "<option value=".$l.">".$minp."</option>";
                      } 
                    ?>
                    </select>
                </div>
              </div>  
              <div class="form-group form-group-sm">
                <label for='maxp' class='control-label col-sm-4'>Maximum Level: </label>
                <div class='col-sm-8'>
                    <select id="maxp" name='maxp' class="form-control gos-form">
                  <?php
                    for ($l=0; $l<=10; $l++)
                    {
                      if ($l==0) $maxp = "None"; else $maxp= $l*10;
                      echo "<option value=".$l.">".$maxp."</option>";
                    }
                  ?>
                    </select>
                </div>
              </div>  
              <div class="form-group form-group-sm">
                <label for='pduels' class='control-label col-sm-4'>Battles per Player: </label>
                <div class='col-sm-8'>
                    <select id="pduels" name='pduels' class="form-control gos-form">
                  <?php
                    for ($l=1; $l<10; $l+=2)
                    {
                      echo "<option value=".$l.">".$l."</option>";
                    }
                  ?>
                    </select>
                </div>
              </div>  
              <div class="form-group form-group-sm">
                <label for='tfee' class='control-label col-sm-4'>Entrance Fee: </label>
                <div class='col-sm-8'>
                    <select id="tfee" name='tfee' class="form-control gos-form">
                      <option value="10">10 Copper</option>
                      <option value="100">1 Silver</option>
                      <option value="1000">10 Silver</option>
                      <option value="10000">1 Gold</option>
                      <option value="100000">10 Gold</option>
                      <option value="500000">50 Gold</option>
                    </select>
                </div>
              </div>         
                    <input type="hidden" name="tab" value="2" id="tab" />
                    <input type="hidden" name="maketourney" value="1" id="maketourney" />
                    <input type="submit" name="submit" value="Announce" class="btn btn-success btn-sm"/>
            </form>
        <?php
          } 
        ?>
          </div>
        </div>
        <div class="tab-pane <?php if ($tab == 3) echo 'active';?>" id="cb_tab">
        <?php
          $myWar = 0;
          $rules = '';
          $lastBattle = 0;
          $lastBattle = mysql_fetch_array(mysql_query("SELECT * FROM Contests WHERE type='99' "));
          if ($lastBattle != 0)
          {
            $myWar = $lastBattle;
            $bType = "Last";
          }
          else if ($location[last_war])
          {
            $myWar = mysql_fetch_array(mysql_query("SELECT * FROM Contests WHERE id='$location[last_war]' "));
            $bType = "Clan";
          }
    
          if ($myWar != 0) // Previous clan battle
          {
            if ($myWar[starts] > intval(time()/3600))
            {
              $tminus = intval(($myWar[starts]*3600-time())/60);
              echo $bType." Battle starts in: ".displayTime($tminus,0)."<br/><br/>";
            }
            else if ($myWar[ends] > intval(time()/3600))
            {
              $tminus = intval(($myWar[ends]*3600-time())/60);
              echo $bType." Battle ends in: ".displayTime($tminus,0)."<br/><br/>";
            }
            else
            {
              $next_war = 0;
              $tpast = intval((time() - $myWar[ends]*3600)/60);
              $war_ago = displayTime($tpast);
              $next_war = 1440-$tpast;
              echo "Previous Battle: ".$war_ago."<br/>";
              if ($next_war >0)
              {  
                echo "Wait before starting new clan battle here: ".displayTime($next_war,0)."<br/><br/>";
              }
            }
            $rules = unserialize($myWar[rules]);
            $reward = unserialize($myWar[reward]);
        ?>
          <div class='row'> 
        <?php        
            if (!$lastBattle)
            {
        ?>
       
            <table class='table table-repsonsive table-hover table-clear small'>
              <tr>
                <td align=right><b>War Costs:</b></td>
                <td><?php echo displayGold($rules[3]);?></td>
              </tr>
              <tr>
                <td align=right><b>City&#39;s Spoils:</b></td>
                <td><?php echo displayGold($reward[0]);?></td>
              </tr>
              <tr>
                <td align=right><b>Ji Prize Pool:</b></td>
                <td><?php echo round($reward[1])." Ji";?></td>
              </tr>
            </table>
        <?php
            } 
          }
          if ( $myWar[id] != 0 )
          {    
            $ranks=array();
            $rid= array();  
            $contestants = unserialize($myWar[contestants]);
            $tresults = unserialize($myWar[results]);
        ?>
            <table class='table table-repsonsive table-hover table-clear small'>
              <tr>
                <th colspan=3></th>
                <th colspan=4><a href='#' class='btn btn-xs btn-block btn-default'>Total From</a></th>
              </tr>
              <tr>
                <th>Contestant</th>
                <th>Ji at Risk</th>
                <th>Score</th>
                <th>Duels</th>
                <th>NPCs</th>
                <th>Support</th>
                <th>Participation</th>
              </tr>
        <?php
            $numbat= array();
            foreach ($contestants as $cid => $cdata)
            {
              if ($cdata[0]) 
              { 
                $ranks[$cdata[0]]=$cdata[1]; 
                $rid[$cdata[0]]=$cid;
              }
            }
            arsort($ranks);
            foreach ($ranks as $rname => $rscore) 
            {
              if (!$tresults[$rid[$rname]]) $tresults[$rid[$rname]] = array();
              $cname = explode('_', $rname);
              
              if ($lastBattle && $cname[0] == "Light") $classn="btn-primary";              
              else if ($lastBattle) $classn="btn-danger";
              else if ($rid[$rname] == $id) $classn="btn-info";
              else $classn="btn-warning";
              echo "<tr class='listtab'>";
              if (!$lastBattle)
                echo "<td align='center'><b><a class='btn btn-xs btn-block ".$classn."' href='joinclan.php?name=".$cname[0]."'>".$cname[0]."</a></b></td>";
              else
              {
                $img = "";
                if ($cname[0] == "Light")
                {
                  $img = "<img src='images/1.gif'/>&nbsp;";
                }
                else
                {
                  $img = "<img src='images/0.gif'/>&nbsp;";
                }
                
                echo "<td align='center'><a href='#' class='btn btn-xs btn-block ".$classn."'>".$img."<b>".$cname[0]."</b></a></td>";
              }
              
              // Determine Ji risk display
              $jiRisk = 0;
              if ($myWar[winner] == null)
              {
                $tsoc = mysql_fetch_array(mysql_query("SELECT id, area_score FROM `Soc` WHERE id = '$rid[$rname]'")); 
                $area_score = unserialize($tsoc['area_score']);
                $jpercent = 0.10;
                if ($rules[0] == $rid[$rname]) $jpercent = 0.05;
                $jiRisk = floor($area_score[$location[id]]*$jpercent);
              }
              else 
              {
                if ($myWar[winner] != $rname) $jiRisk = $rules[2];
              }
              echo "<td align='center'>".$jiRisk."</td>";
              echo "<td align='center'>".$rscore."</td>";
              echo "<td align='center'>".$tresults[$rid[$rname]][0]."</td>";
              echo "<td align='center'>".$tresults[$rid[$rname]][1]."</td>";
              echo "<td align='center'>".$tresults[$rid[$rname]][2]."</td>";
              echo "<td align='center'>".$tresults[$rid[$rname]][3]."</td>";
              echo "</tr>";
            }
        ?>
            </table>
          </div>
        <?php
          }

          $lastdone=0;
          if ($society[last_war])
          {
            $lastWar = mysql_fetch_array(mysql_query("SELECT * FROM Contests WHERE id='$society[last_war]' "));
            if (($lastWar[ends]+24) <= intval(time()/3600)) $lastdone=1;
          }
          else
          {
            $lastdone=1;
          }
    
          $mc = unserialize($myWar[contestants]);
          if (!$lastBattle && !$b && $slead && $lastdone && ($myWar==0 || $myWar[starts] > intval(time()/3600) || $myWar[done]) && !($mc[$society[id]] && !$myWar[done]))
          {
            if ($locscore*2 > $rulerscore) $halfji=1; else $halfji=0;
            if ($society[bank]*10 > $location[bank]) $bfunds=1; else $bfunds=0;
            $senemy =0;
            $stance = unserialize($society['stance']);
            foreach ($stance as $c_n => $c_s)
            {
              if ($c_s ==2 && $ruler[name]==str_replace("_"," ",$c_n))
              {
                $senemy = 1;
              }
            }
            if ($society[last_war])
            {
              $lastWar = mysql_fetch_array(mysql_query("SELECT * FROM Contests WHERE id='$society[last_war]' "));
              if (($lastWar[ends]+24) <= intval(time()/3600)) $lastdone=1;
            }
            else
            {
              $lastdone=1;
            }
         
            echo "<table class='table table-repsonsive table-hover table-clear small'>";
              echo "<tr><td colspan='2' align='center'><b>Are You Prepared?</b></td></tr>";
              echo "<tr><td>Do you have a Clan Office?</td><td>".yesno($soff)."</td></tr>";
              echo "<tr><td>Are you enemies with the Ruler?</td><td>".yesno($senemy)."</td></tr>";
              echo "<tr><td>Do you have at least half of $location[ruler]'s Ji?</td><td>".yesno($halfji)."</td></tr>";
              echo "<tr><td>Do you have enough funds available for battle?</td><td>".yesno($bfunds)."</td></tr>";
              echo "<tr><td>Has it been 24 hours since finished the last clan battle you started?</td><td>".yesno($lastdone)."</td></tr>";
              echo "<tr><td colspan='2' align='center'>";
                if ($soff && $halfji && $bfunds && $senemy && $lastdone)
                {
                  echo "<form name='joinForm' id='joinForm' action='townhall.php' method='post'>";
                  if ($myWar == 0 || $myWar[done])
                  {
                    echo "<input type='hidden' name='makewar' id='makewar' value=1 />";
                  }
                  else
                  {
                    echo "<input type='hidden' name='joinwar' id='joinwar' value=1 />";
                  }
                  echo "<input type='hidden' name='tab' value='3' id='tab' />";
                  echo "<input type='submit' class='form' value='Prepare for Battle!'/>";
                  echo "</form>";
                }
              echo "</td></tr>";
            echo "</table>";
          } 
        ?>    
        </div>

      <?php 
        if ($b)
        {
      ?>
        <div class="tab-pane <?php if ($tab == 4) echo 'active';?>" id="settings_tab">
          <div class='row'>
            <p>City Bank: <?php echo displayGold($location['bank']);?></p>
            <?php
              if ($lochorde) echo "<p class='text-danger'>No upgrades can be purchased until the attacking horde is defeated!</p>";
            ?>
          </div>
          <div class='row'>
            <div class='col-xs-12'>
              <div class="panel panel-success">
                 <div class="panel-heading">
                  <h3 class="panel-title">City Goods</h3>
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
                      <?php echo $location[$estate_unq_prod[$i]]; ?>
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
            <div class='col-sm-8'>
              <div class="panel panel-primary">
                <div class="panel-heading">
                  <h3 class="panel-title">City Improvements</h3>
                </div>
                <div class="panel-body abox">            
                  <form name="buildform" action="townhall.php" method="post">
                    <table class='table table-responsive table-hover table-clear small'>
                      <tr>
                        <th>Local Buildings</td>
                        <th>Level</td>
                        <th>Price</td>
                        <th>Goods</td>
                        <th>&nbsp;</td>
                      </tr>
                    <?php 
                      for ($x=0; $x<6; $x++)
                      {
                        echo "<tr>";
                        $lvl = $upgrades[$x];
                        echo "<td><a class='btn btn-sm btn-warning btn-block' onClick='javascript:swapinfo(".$x.")'>".$build_names[$x]."</a></td>";
                        echo "<td align='center'>".$lvl."</td>";
                        $maxlvl=4;
                        if ($location['num_ups']>=10) $maxlvl=5;

                        if ($lvl < $maxlvl) 
                        {
                          $coin_costs = 15000*($location['num_ups']+1)*($lvl+1);
                          echo "<td align='center'>".displayGold($coin_costs)."</td>"; 
                          echo "<td align='center'>".floor($coin_costs/1000)." ".ucwords($estate_unq_prod[$build_goods[$x]])."</td>";
                        }
                        else 
                        { 
                          echo "<td>&nbsp;</td><td>&nbsp;</td>";
                        }
                        if ($lvl < $maxlvl && $lochorde==0) echo "<td align='center'><input type='button' class='btn btn-success btn-sm btn-block' value='Upgrade' onClick='javascript:buildUp($x);'/></td>"; else echo "<td>&nbsp;</td>";
                        echo "</tr>";
                      }
                      for ($x=6; $x<8; $x++)
                      {
                        echo "<tr>";
                        $lvl = $upgrades[$x];
                        $unq_name = $unique_buildings[$location[name]][$x-6];
                        echo "<td><a class='btn btn-sm btn-warning btn-block' onClick='javascript:swapinfo(".$x.")'>".$unq_name."</a></td>";
                        echo "<td align='center'>".$lvl."</td>";
                        if ($lvl < 1)
                        {
                          echo "<td align='center'>".displayGold($build_costs[$lvl+5])."</td>";
                          echo "<td align='center'>".floor($build_costs[$lvl+5]/4000)." ".ucwords($estate_unq_prod[$unique_build_goods[$unq_name][0]])."<br/>";
                          echo floor($build_costs[$lvl+5]/4000)." ".ucwords($estate_unq_prod[$unique_build_goods[$unq_name][1]])."</td>";
                        }
                        else echo "<td>&nbsp;</td><td>&nbsp;</td>";
                        if ($lvl < 1 && $lochorde==0) echo "<td><input type='button' class='btn btn-success btn-sm btn-block' value='Upgrade' onClick='javascript:buildUp($x);'/></td>"; else echo "<td>&nbsp;</td>";
                        echo "</tr>";
                      }
                    ?>
                    </table>
                    <input type='hidden' name='buildup' id='buildup' value='-1'/>
                  </form> 
                </div>
              </div>
            </div>
            <div class='col-sm-4'>           
              <div id='bonuses'>  
                <?php echo "<center><font class='littletext'>Click on Building name for bonus information!</font></center>";?>
              </div>
            </div>
          </div>
          <div class='row'>
            <div class='col-sm-4'>
              <div class="panel panel-danger">
                <div class="panel-heading">
                  <h3 class="panel-title">Tavern Settings</h3>
                </div>
                <div class="panel-body abox">            
                  <form class='form-inline' name="diceform" action="townhall.php" method="post">  
                    <div class="form-group form-group-sm">
                      <label for="game">Dice Game:</label>
                      <select id="game" name='game' class="form-control gos-form">
                       <option value="t" <?php if ($location[gtype] != "d") echo "selected=true";?>>Tops</option>
                        <option value="d" <?php if ($location[gtype] == "d") echo "selected=true";?>>Crowns</option>
                      </select>
                    </div>
                    <div class="form-group form-group-sm">
                      <label for="minw">Minimum Wager:</label>
                      <select id="minw" name='minw' class="form-control gos-form">
                        <option value="1" <?php if ($location[minw] == 1) echo "selected=true";?>>10 Copper</option>
                        <option value="2" <?php if ($location[minw] == 2) echo "selected=true";?>>1 Silver</option>
                        <option value="3" <?php if ($location[minw] == 3) echo "selected=true";?>>10 Silver</option>
                        <option value="4" <?php if ($location[minw] == 4) echo "selected=true";?>>1 Gold</option>
                        <option value="5" <?php if ($location[minw] == 5) echo "selected=true";?>>10 Gold</option>
                        <option value="6" <?php if ($location[minw] == 6) echo "selected=true";?>>100 Gold</option>
                      </select>
                    </div>
                    <div class="form-group form-group-sm">
                      <label for="maxw">Maximum Wager:</label>
                      <select id="maxw" name='maxw' class="form-control gos-form">
                        <option value="1" <?php if ($location[maxw] == 1) echo "selected=true";?>>10 Copper</option>
                        <option value="2" <?php if ($location[maxw] == 2) echo "selected=true";?>>1 Silver</option>
                        <option value="3" <?php if ($location[maxw] == 3) echo "selected=true";?>>10 Silver</option>
                        <option value="4" <?php if ($location[maxw] == 4) echo "selected=true";?>>1 Gold</option>
                        <option value="5" <?php if ($location[maxw] == 5) echo "selected=true";?>>10 Gold</option>
                        <option value="6" <?php if ($location[maxw] == 6) echo "selected=true";?>>100 Gold</option>
                      </select>
                    </div>
                    <br/>
                    <input type="submit" class="btn btn-success btn-sm" value="Setup Dice"/></td>
                  </form>
                </div>
              </div>
            </div>
            <div class='col-sm-8'>
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title">City Announcements</h3>
                </div>
                <div class="panel-body abox"> 
                  <form class='form-horizontal' action="townhall.php" method="post">
                    <div class="form-group form-group-sm">
                      <div class='col-sm-12'>
                        <label for="info" class='sr-only'>City Announcement:</label>
                        <textarea name="info" class="form-control gos-form" rows="4" cols="100" wrap="soft" style="overflow:hidden"><?php echo $location['info']; ?></textarea>
                      </div>
                    </div>
                    <br/>
                    <input type="hidden" name="changer2" value="1" id="changer2" />
                    <input type="submit" name="submit" value="Update Info" class="btn btn-info btn-sm"/>
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
<script type='text/javascript'>
  function buyBiz(biz)
  {
    document.bizForm.bought.value= biz;
    document.bizForm.submit();
  } 
</script>
<?php
} // !isDestroyed
else
{
  $message = "The remains of the City Hall in ".$location[name];
  if ($mode == 1) { include('header2.htm'); }
  else 
  {
    $bg = "background-image:url('images/townback/".str_replace(' ','_',strtolower($char['location'])).".jpg'); ";
    include('header.htm');
  }
}
include('footer.htm');
?>