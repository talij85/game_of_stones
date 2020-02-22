<?php
// establish a connection with the database
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/charFuncs.php");
include_once("admin/duelFuncs.php");
include("admin/equipped.php"); 

$wikilink = "Skills";

function draw_skill($name,$lvl,$cost,$colnum,$act=0,$gender=0,$next=1) 
{
  $colors = array('danger','warning','success','primary');
  $text = "<div class='panel panel-".$colors[$colnum]."' style='width: 100%;'><div class='panel-heading'><h3 class='panel-title'>".$name."</h3></div><div class='panel-body abox' align='center'>";
  $currStats= cparse(getSkillEffect($name,$lvl,$gender),0);
  $text .= itm_info($currStats)."<br/>";
  if ($next) $text .="<i>Next Level (".($lvl+1)*$cost." points):<br/>".itm_info(cparse(getSkillEffect($name,$lvl+1,$gender),0), $currStats)."<br/></i>";
  $text .= "</div></div>";

  return $text;
}

function arrayToJSObject($array, $varname, $sub = false ) 
{
  $jsarray = $sub ? $varname . "{" : $varname . " = {\n";
  $varname = "\t$varname";
  reset ($array);

  // Loop through each element of the array
  while (list($key, $value) = each($array)) 
  {
    $jskey = "'" . $key . "' : ";
     
    if (is_array($value)) 
    {
      // Multi Dimensional Array
      $temp[] = arrayToJSObject($value, $jskey, true);
    } 
    else 
    {
      if (is_numeric($value)) 
      {
        $jskey .= "$value";
      } 
      elseif (is_bool($value)) 
      {
        $jskey .= ($value ? 'true' : 'false') . "";
      }
      elseif ($value === NULL) 
      {
        $jskey .= "null";
      } 
      else 
      {
        static $pattern = array("\\", "'", "\r", "\n");
        static $replace = array('\\', '\\\'', '\r', '\n');
        $jskey .= "'" . str_replace($pattern, $replace, $value) . "'";
      }
      $temp[] = $jskey;
    }
  }
  $jsarray .= implode(', ', $temp);

  $jsarray .= "}\n";
  return $jsarray;
}

$message=mysql_real_escape_string($_GET['message']);
$show=intval($_GET['show']);
$skillup=mysql_real_escape_string($_POST['skillup']);
$classup=mysql_real_escape_string($_POST['classup']);
$activate=mysql_real_escape_string($_POST['activate']);
$deactivate=mysql_real_escape_string($_POST['deactivate']);
$doUneq = mysql_real_escape_string($_POST['doUneq']);
$tab = mysql_real_escape_string($_POST['tab']);

$col='#090909';
$types = unserialize($char['type']);
$maxactive= floor(($char[level])/15)+3;
$askills = unserialize($char[active]);
$divtext[0] = '';

// Check active skills for invalid alignment
$myAlign = getAlignment($char[align])+3;
$typeSkills=typeSkills(900);
for ($x = 0; $x < 7; $x++)
{
  $doable = 0;
   
  if (abs($myAlign-$x)<= 1) $doable =1;
  for ($a=0; $a < count($askills); $a++)
  {
    if (!$doable && ($typeSkills[$x] == $askills[$a])) 
    {
      $deactivate=$askills[$a];
    }
  }
}

$tskills = $stomach_bonuses1." ".$stamina_effect1." ".$clan_bonus1." ".$skill_bonuses1;
$stats = mysql_fetch_array(mysql_query("SELECT * FROM Users_stats WHERE id='$char[id]'"));


$itmlist="";
$listsize=0;
$a = -1;
$b = -1;
$c = -1;
$d = -1;
$e = -1;
$currw=-1;
$wid=0;
$wEq=0;
$pts_tot = 0;

if (isChanneler($types))
{
  $iresult=mysql_query("SELECT * FROM Items WHERE owner='$id' AND type<15 ".$invSort);
  while ($qitem = mysql_fetch_array($iresult))
  {
    $itmlist[$listsize++] = $qitem;
  
    if ($qitem[istatus]==1) $a = $listsize-1;
    else if ($qitem[istatus]==2) $b = $listsize-1;
    else if ($qitem[istatus]==3) $c = $listsize-1;
    else if ($qitem[istatus]==4) $d = $listsize-1;
    else if ($qitem[istatus]==5) $e = $listsize-1;
    
    if ($qitem[type]==8)
    {
      $currw = $listsize-1;
      if ($qitem[istatus] > 0) $wEq = 1;
    }
  }

  $estats = getstats($itmlist[$a],$itmlist[$b],$itmlist[$c],$itmlist[$d],$itmlist[$e],$tskills);
  $tskills = $tskills." ".$estats[0];

  if ($a >=0 && $itmlist[$a][type]!=8) $pts_tot += (lvl_req($estats[1], getTypeMod($tskills,$itmlist[$a][type])));
  if ($b >=0 && $itmlist[$b][type]!=8) $pts_tot += (lvl_req($estats[2], getTypeMod($tskills,$itmlist[$b][type])));
  if ($c >=0 && $itmlist[$c][type]!=8) $pts_tot += (lvl_req($estats[3], getTypeMod($tskills,$itmlist[$c][type])));
  if ($d >=0 && $itmlist[$d][type]!=8) $pts_tot += (lvl_req($estats[4], getTypeMod($tskills,$itmlist[$d][type])));      
  if ($e >=0 && $itmlist[$e][type]!=8) $pts_tot += (lvl_req($estats[5], getTypeMod($tskills,$itmlist[$e][type])));

  $myWeaves = getWeaves($item_list,$item_base,$tskills);

  for ($w=0; $w<count($myWeaves); $w++)
  {
    if ($itmlist[$currw][base]==$myWeaves[$w][0]) $wid=$w;
  }
}

$noclass = '';
$nofocus = '';

// setup divtext
if (floor($char[level]/20)+2>count($types))
{
  // Set all classes that are restricted to true, then come back and unset them as needed.
  for ($i=0; $i<count($restricted_classes); $i++)
  {
    $noclass[$restricted_classes[$i]] = 1;
  }        
  
  // Turn on national classes
  for ($i=0; $i<count($nat_classes[$char[nation]]); $i++)
  {
    $noclass[$nat_classes[$char[nation]][$i]] = 0;
  }
  
  // check for channeler classes  
  if (isChanneler($types))
  {
    // Turn on Channeling classses
    for ($i=0; $i<count($chan_classes); $i++)
    {
      $noclass[$chan_classes[$i]] = 0;
    }
    // Turn off Non-channeling classes
    for ($i=0; $i<count($no_chan_classes); $i++)
    {
      $noclass[$no_chan_classes[$i]] = 1;
    }    
  }
  // Darkfriends can't be Fanatics
  if ($types[0] == 5) { $noclass[19] = 1; }
    
  
  // Turn opposite gender classes off.
  if ($char[sex] == 0 ) { $gender_classes = $female_classes; }
  else { $gender_classes = $male_classes; }
  for ($i=0; $i<count($gender_classes); $i++)
  {
    $noclass[$gender_classes[$i]] = 1;
  }
    
  // Aiel can't use Swords
  if ($char[nation] == 1) { $noclass[101]=1; }  
  
  // Check for Noble Rank. If so, turn on Noble class.
  $stats = mysql_fetch_array(mysql_query("SELECT id, net_worth FROM Users_stats WHERE id='$char[id]'"));
  $w=0;
  for ($y=1; $y < count($worth_ranks); $y++)
  {
    if ($stats[net_worth] >= $worth_ranks[$y][0]) $w = $y;
  }
  if ($w >= 4) // I'm noble or above
  { $noclass[33]=0; } 

  for ($a=1; $a < count($types); $a++)
  {
    $noclass[$types[$a]] = 1;    
    
    // thiefcatcher/bandit check
    if ($types[$a] == 17) $noclass[18] = 1;
    elseif ($types[$a] == 18) $noclass[17] = 1;
        
    // whitecloak/wolfbrother check
    if ($types[$a] == 21) $noclass[22] = 1;
    elseif ($types[$a] == 22) $noclass[21] = 1;        
  }
  
  foreach ($char_class as $cl => $cname) 
  {
    if ($noclass[$cl] != 1)
    {
      $divtext[$cl] = '';
      $divtext[$cl] .= "<div class='row'>";
      $typeSkills=typeSkills($cl);      
      // odd skills
      for ($x = 1; $x < count($typeSkills); $x+=2) 
      {
        $doable=0;
        $divtext[$cl] .= "<div class='col-sm-4 col-sm-push-2'>";
        if ($skills[$typeSkills[$x]]>= 0) $doable=2;
        $divtext[$cl] .= draw_skill($skill_name[$typeSkills[$x]],$skills[$typeSkills[$x]],1,$doable);
        $divtext[$cl] .= "</div>";
      } 
      $divtext[$cl] .= "</div><div class='row'>";  
      // even skills
      for ($x = 0; $x < count($typeSkills); $x+=2) 
      {
        $divtext[$cl] .= "<div class='col-sm-4'>";
        $divtext[$cl] .= draw_skill($skill_name[$typeSkills[$x]],$skills[$typeSkills[$x]],1,1);
        $divtext[$cl] .= "</div>";
      }
      $divtext[$cl] .= "</div>";
    }
  }
}

mysql_query("UPDATE Users SET newskills=0 WHERE id=$id");

// FIGURE OUT BATTLE SKILL PROGRESSION
if ($skillup != '' && $skillup != '-1')
{
  $doable=0;
  $preskill=1;
  $postskill=1;
  $typeSkills=typeSkills($tab);
  $x = $skillup - $typeSkills[0];

  if ($x%2 != 0 || $tab == 108)
  {
    $doable = 1;
  }
  else if ($skills[$typeSkills[$x]]>= 0)
  {
    if (($x-1>=0) && ($skills[$typeSkills[$x]] >= $skills[$typeSkills[$x-1]])) $preskill=0; // requirement of skill-1
    if (($x+1<count($typeSkills)) && ($skills[$typeSkills[$x]]>=$skills[$typeSkills[$x+1]])) $postskill=0;  // requirement of skill+1
    if ($preskill && $postskill) $doable=1;   
  }
  if ($doable)
  {
    $costup = 1; 
    if ($char['points'] >= $costup*($skills[$skillup]+1))
    {
      $message = $skill_name[$skillup]." has been upgraded!";
      $skills[$skillup] = $skills[$skillup] + 1;
      $char['points'] = $char['points']-($costup*$skills[$skillup]);
      $stats[skill_pts_used]= $stats[skill_pts_used]+($costup*$skills[$skillup]);
      if ($skills[$skillup] > $stats[highest_skill]) $stats[highest_skill] = $skills[$skillup];
      $newskills = serialize($skills);
      $char[skills] = $newskills;

      mysql_query("UPDATE Users_data SET skills='".$newskills."' WHERE id='".$char[id]."'");
      mysql_query("UPDATE Users SET points='".$char[points]."' WHERE id='".$char[id]."'");
      mysql_query("UPDATE Users_stats SET skill_pts_used='".$stats[skill_pts_used]."', highest_skill='".$stats[highest_skill]."' WHERE id='".$char[id]."'");
      if ($doUneq)
      {
        $itmlist[$currw][istatus]=-2;
        mysql_query("UPDATE Items SET istatus='-2' WHERE id='".$itmlist[$currw][id]."'");
      }
      $skills = unserialize($char['skills']);
      include("admin/equipped.php"); 
      if (isChanneler($types))
      {
        $iresult=mysql_query("SELECT * FROM Items WHERE owner='$id' AND type<15 ".$invSort);
        while ($qitem = mysql_fetch_array($iresult))
        {
          $itmlist[$listsize++] = $qitem;
      
          if ($qitem[istatus]==1) $a = $listsize-1;
          else if ($qitem[istatus]==2) $b = $listsize-1;
          else if ($qitem[istatus]==3) $c = $listsize-1;
          else if ($qitem[istatus]==4) $d = $listsize-1;  
          else if ($qitem[istatus]==5) $e = $listsize-1;  
        
          if ($qitem[type]==8)
          {
            $currw = $listsize-1;
            if ($qitem[istatus] > 0) $wEq = 1;
          }
        }
      
        $tskills = $stomach_bonuses1." ".$stamina_effect1." ".$clan_bonus1." ".$skill_bonuses1;
        $estats = getstats($itmlist[$a],$itmlist[$b],$itmlist[$c],$itmlist[$d],$itmlist[$e],$tskills);
        $tskills = $tskills." ".$estats[0];

        if ($a >=0 && $itmlist[$a][type]!=8) $pts_tot += (lvl_req($estats[1], getTypeMod($tskills,$itmlist[$a][type])));
        if ($b >=0 && $itmlist[$b][type]!=8) $pts_tot += (lvl_req($estats[2], getTypeMod($tskills,$itmlist[$b][type])));
        if ($c >=0 && $itmlist[$c][type]!=8) $pts_tot += (lvl_req($estats[3], getTypeMod($tskills,$itmlist[$c][type])));
        if ($d >=0 && $itmlist[$d][type]!=8) $pts_tot += (lvl_req($estats[4], getTypeMod($tskills,$itmlist[$d][type])));      
        if ($e >=0 && $itmlist[$e][type]!=8) $pts_tot += (lvl_req($estats[5], getTypeMod($tskills,$itmlist[$e][type])));

        $myWeaves = getWeaves($item_list,$item_base,$tskills);

        for ($w=0; $w<count($myWeaves); $w++)
        {
          if ($itmlist[$currw][base]==$myWeaves[$w][0]) $wid=$w;
        }
      }
    }
    else { $message = "You don't have enough skill points!"; }
  }
  else { $message = "You do not meet the requirements to upgrade this skill!"; }
}

if ($activate != '' && $activate != '-1')
{
  if (count($askills) < $maxactive)
  {
    $active = 0;
    for ($a=0; $a < count($askills); $a++)
    {
      if ($activate == $askills[$a]) $active=1;
    }
    if (!$active)
    {
      $message = $skill_name[$activate]." has been activated!";  
      $askills[count($askills)] = $activate;
      $stats[num_active_skills]=count($askills);
      $reactive=serialize($askills);
      mysql_query("UPDATE Users_data SET active='".$reactive."' WHERE id='".$char[id]."'");    
      mysql_query("UPDATE Users_stats SET num_active_skills='".$stats[num_active_skills]."' WHERE id='".$char[id]."'");
    }
    else
    {
      $message = $skill_name[$activate]." is already activated!"; 
    }
  }
  else
  {
    $message = "While you are obviously talented, you already have the max number of skills activated...";
  }
}

if ($deactivate != '' && $deactivate != '-1')
{
  for ($a=0; $a < count($askills); $a++)
  {
    if ($askills[$a] != $deactivate) $newactive[count($newactive)]=$askills[$a];
  }
  $message = $skill_name[$deactivate]." has been deactivated!";  
  $askills=$newactive;
  $stats[num_active_skills]=count($askills);
  $reactive=serialize($newactive);
  mysql_query("UPDATE Users_data SET active='".$reactive."' WHERE id='".$char[id]."'");
  mysql_query("UPDATE Users_stats SET num_active_skills='".$stats[num_active_skills]."' WHERE id='".$char[id]."'");   
}

if ($classup != '' && $classup != '-1')
{
  if (floor($char[level]/20)+2>count($types))
  {
    $types[count($types)]=$classup;
    $message = "Congratulations! You are now a ".$char_class[$classup]."!";
    $newtypes = serialize($types);
    $stats[num_classes]=count($types)-1;
    mysql_query("UPDATE Users SET type='".$newtypes."' WHERE id='".$char[id]."'");
    mysql_query("UPDATE Users_stats SET num_classes='".$stats[num_classes]."' WHERE id='".$char[id]."'");
  }
  else 
    $message = "You already have the max number of classes for your level!";
}

// JAVASCRIPT
?>
<script language="javascript">
  function skillUp (num,tab,doUE)
  {
    if (!doUE) doUE = 0;
    document.statform.doUneq.value=doUE;
    document.statform.skillup.value=num;
    document.statform.tab.value=tab;
    document.statform.submit();
  }
  
  function classUp ()
  {
    var nc = document.getElementById("newclass");
    var ncv = nc.options[nc.selectedIndex].value;
    if (ncv >0)
    {
      document.statform2.classup.value=ncv;
      document.statform2.submit();
    }
  }  
  
  function Activate (num,tab)
  {
    document.statform.activate.value=num;
    document.statform.tab.value=tab;
    document.statform.submit();
  }
  
  function deActivate (num)
  {
    document.statform.deactivate.value=num;
    document.statform.submit();
  }  
  
  function setNewClass ()
  {
    var nc = document.getElementById("newclass");
    var ncv = nc.options[nc.selectedIndex].value;

    <?php
      echo arrayToJSObject($divtext,text);
    ?>
    document.getElementById('ncinfo').innerHTML=text[ncv];
  }
</SCRIPT>
<?php
if (!$tab) $tab=-2;

include('header.htm');

$color = array("000000","171717","AAAAAA");
$cell_size = 75;
$alignLevel=floor($char[level]/10)+1;
?>
  <div class="row solid-back">
    <div class="col-sm-12">
      <div id="content">
        <ul id="classtabs" class="nav nav-tabs" data-tabs="tabs">
          <li <?php if ($tab == -2) echo "class='active'";?>><a href="#active_tab" data-toggle="tab">Active</a></li>
          <li <?php if ($tab == -1) echo "class='active'";?>><a href="#align_tab" data-toggle="tab">Alignment</a></li>
        <?php
          for ($t=0; $t<count($types); ++$t)
          {
            $curr = $types[$t];
            $cname = $char_class[$types[$t]];
        ?>
          <li <?php if ($tab == $curr) echo "class='active'";?>><a href="#<?php echo str_replace("'",'_',str_replace(' ','_',$cname)); ?>_tab" data-toggle="tab"><?php echo $cname;?></a></li>      
        <?php
          }
          if (floor($char[level]/20)+2>count($types))
          {
        ?>
          <li <?php if ($tab == -3) echo "class='active'";?>><a href="#new_tab" data-toggle="tab">New Class</a></li>
        <?php
          }
        ?>          
        </ul>

        <div class="tab-content">
          <div class="tab-pane <?php if ($tab == -2) echo 'active';?>" id="active_tab">
            <div class='row'>
              <p><b>Active Skills: <?php echo count($askills)."/".$maxactive;?></b></p>
            </div>
            <div class='row'>
              <?php
                for ($x = 0; $x < count($askills); $x++) 
                {                
                  echo "<div class='col-sm-4 col-md-3'>";
                  if ($askills[$x] <900)
                    echo draw_skill($skill_name[$askills[$x]],$skills[$askills[$x]],1,2,0,0,0);
                  else
                    echo draw_skill($skill_name[$askills[$x]],$alignLevel,1,2,0,0,0);                  
                  echo "<input type='button' name='deactivate".$askills[$x]."' value='Deactivate' class='btn btn-sm btn-danger' onClick='javascript:deActivate(".$askills[$x].");'/>";              
                  echo "</div>";
                }
              ?>
            </div>
          </div>   
          <div class="tab-pane <?php if ($tab == -1) echo 'active';?>" id="align_tab">                         
            <div class='row'>
              <p><b>Active Skills: <?php echo count($askills)."/".$maxactive;?></b></p>
            </div>
            <?php 
              // update skill levels
              $typeSkills=typeSkills(900);
              $upAlignSkill = 0;
              for ($x=0; $x < count($typeSkills); $x++)
              {
                if ($skills[$typeSkills[$x]] != $alignLevel)
                {
                  $skills[$typeSkills[$x]] = $alignLevel;
                  $upAlignSkill = 1;
                }
              }
              if ($upAlignSkill)
              {
                $newskills = serialize($skills);
                mysql_query("UPDATE Users_data SET skills='".$newskills."' WHERE id='".$char[id]."'");
              }
            ?>
            <div class='row hidden-xs'>
              <div class='col-sm-4 col-sm-push-4'>
            <?php
              $doable=0;
              $active=0;
              $x=3;
              if (abs($myAlign-$x)<= 1) $doable =1;
              for ($a=0; $a < count($askills); $a++)
              {
                if ($typeSkills[$x] == $askills[$a]) $active=1;
              }     
              echo draw_skill($skill_name[$typeSkills[$x]],$alignLevel,1,$doable+1,$active,0,0);
        
              if (count($askills)<$maxactive && $alignLevel>0 && !$active && $doable)
              {
                echo " <input type='button' name='activate".$typeSkills[$x]."' value='Activate' class='btn btn-sm btn-success' onClick='javascript:Activate(".$typeSkills[$x].",-1);'/><br/><br/>";
              }
            ?>
              </div>
            </div>
            <div class='row hidden-xs'>
            <?php
              for ($x=2; $x<5; $x+=2)
              {
                if ($x==2) $push = 2;
                else $push = 2;
                echo "<div class='col-sm-4 col-sm-push-$push'>";
                $doable=0;
                if (abs($myAlign-$x)<= 1) $doable =1;
                $active=0;
                for ($a=0; $a < count($askills); $a++)
                {
                  if ($typeSkills[$x] == $askills[$a]) $active=1;
                }     
                echo draw_skill($skill_name[$typeSkills[$x]],$alignLevel,1,$doable+1,$active,0,0);
                if (count($askills)<$maxactive && $alignLevel>0 && !$active && $doable)
                {
                  echo " <input type='button' name='activate".$typeSkills[$x]."' value='Activate' class='btn btn-sm btn-success' onClick='javascript:Activate(".$typeSkills[$x].",-1);'/><br/><br/>";
                }
                echo "</div>";
              }
            ?>
            </div>
            <div class='row hidden-xs'>
            <?php            
              for ($x=1; $x<6; $x+=4)
              {
                if ($x==1) $push = 1;
                else $push = 3;
                echo "<div class='col-sm-4 col-sm-push-$push'>";
                $doable=0;
                if (abs($myAlign-$x)<= 1) $doable =1;
                $active=0;
                for ($a=0; $a < count($askills); $a++)
                {
                  if ($typeSkills[$x] == $askills[$a]) $active=1;
                }     
                echo draw_skill($skill_name[$typeSkills[$x]],$alignLevel,1,$doable+1,$active,0,0);
                if (count($askills)<$maxactive && $alignLevel>0 && !$active && $doable)
                {
                  echo " <input type='button' name='activate".$typeSkills[$x]."' value='Activate' class='btn btn-sm btn-success' onClick='javascript:Activate(".$typeSkills[$x].",-1);'/><br/><br/>";
                }
                echo "</div>";
              }
            ?>
            </div>
            <div class='row hidden-xs'>
            <?php
              for ($x=0; $x<7; $x+=6)
              {
                if ($x==0) $push = 0;
                else $push = 4;
                echo "<div class='col-sm-4 col-sm-push-$push'>";
                $doable=0;
                if (abs($myAlign-$x)<= 1) $doable =1;
                $active=0;
                for ($a=0; $a < count($askills); $a++)
                {
                  if ($typeSkills[$x] == $askills[$a]) $active=1;
                }     
                echo draw_skill($skill_name[$typeSkills[$x]],$alignLevel,1,$doable+1,$active,0,0);
                if (count($askills)<$maxactive && $alignLevel>0 && !$active && $doable)
                {
                  echo " <input type='button' name='activate".$typeSkills[$x]."' value='Activate' class='btn btn-sm btn-success' onClick='javascript:Activate(".$typeSkills[$x].",-1);'/><br/><br/>";
                }
                echo "</div>";
              }
            ?>
            </div>
            <div class='row visible-xs'>
            <?php
              for ($x=0; $x<7; $x++)
              {
                echo "<div class='col-sm-4'>";
                $doable=0;
                if (abs($myAlign-$x)<= 1) $doable =1;
                $active=0;
                for ($a=0; $a < count($askills); $a++)
                {
                  if ($typeSkills[$x] == $askills[$a]) $active=1;
                }     
                echo draw_skill($skill_name[$typeSkills[$x]],$alignLevel,1,$doable+1,$active,0,0);
                if (count($askills)<$maxactive && $alignLevel>0 && !$active && $doable)
                {
                  echo " <input type='button' name='activate".$typeSkills[$x]."' value='Activate' class='btn btn-sm btn-success' onClick='javascript:Activate(".$typeSkills[$x].",-1);'/><br/><br/>";
                }
                echo "</div>";
              }
            ?>
            </div>            
          </div>   
        <?php
          for ($t=0; $t<count($types); ++$t)
          {
            $curr = $types[$t];
            $cname = $char_class[$types[$t]];
            $typeSkills=typeSkills($curr);    
        ?>
          <div class="tab-pane <?php if ($tab == $types[$t]) echo 'active';?>" id="<?php echo str_replace("'",'_',str_replace(' ','_',$cname));?>_tab">
            <div class='row'>
              <p><b>Skill Points: <?php echo $char[points];?></b></p>
            </div>  
            <?php
              if ($curr != 108)
              {
            ?>
            <div class='row'>
            <?php
              // odd skills
              for ($x = 1; $x < count($typeSkills); $x+=2) 
              {
                $doable=1;
                echo "<div class='col-sm-4 col-sm-push-2'>";
                if ($skills[$typeSkills[$x]]>= 0) $doable=3;
                echo draw_skill($skill_name[$typeSkills[$x]],$skills[$typeSkills[$x]],1,$doable);
                if ($doable)
                {
                  echo "<input type='button' name='upgrade".$typeSkills[$x]."' value='Upgrade' class='btn btn-sm btn-primary' onClick='javascript:skillUp(".$typeSkills[$x].",".$types[$t].");'/>";
                }
                echo "</div>";
              }
            ?>
            </div>  
            <div class='row'><br/>
            <?php
              // even skills
              for ($x = 0; $x < count($typeSkills); $x+=2) 
              {
                $preskill=1;
                $postskill=1;
                $active=0;
                for ($a=0; $a < count($askills); $a++)
                {
                  if ($typeSkills[$x] == $askills[$a]) $active=1;
                }            
                $doable=0;  
                echo "<div class='col-sm-4'>";
                if ($skills[$typeSkills[$x]]>= 0)
                {
                  if (($x-1>=0) && ($skills[$typeSkills[$x]] >= $skills[$typeSkills[$x-1]])) $preskill=0; // requirement of skill-1
                  if (($x+1<count($typeSkills)) && ($skills[$typeSkills[$x]]>=$skills[$typeSkills[$x+1]])) $postskill=0;  // requirement of skill+1
                  if ($preskill && $postskill) $doable=1;   
                }
                $color = $doable;
                if ($active) $color += 2;
                echo draw_skill($skill_name[$typeSkills[$x]],$skills[$typeSkills[$x]],1,$color,$active);
           
                if ($doable)
                {
                  echo "<input type='button' name='upgrade".$typeSkills[$x]."' value='Upgrade' class='btn btn-sm btn-primary' onClick='javascript:skillUp(".$typeSkills[$x].",".$types[$t].");'/>";
                }
                if (count($askills)<$maxactive && $skills[$typeSkills[$x]]>0 && !$active)
                {
                  echo "<input type='button' name='activate".$typeSkills[$x]."' value='Activate' class='btn btn-sm btn-success' onClick='javascript:Activate(".$typeSkills[$x].",".$types[$t].");'/>";
                }
                echo "</div>";
              }
            ?>
            </div>  
            <?php
              }
              else
              {
                $gen=$char[sex];
                if ($gen == 0)
                {
                  $r1=array(0,4);
                  $r2=array(1,2,3);
                }
                else
                {
                  $r1=array(1,3);
                  $r2=array(0,2,4);
                }
            ?>
            <div class='row'>
            <?php
              // Row 1
              for ($x = 0; $x < 2; $x++) 
              {
                $doable=1;
                echo "<div class='col-sm-4 col-sm-push-2'>";
                if ($skills[$typeSkills[$r1[$x]]]>= 0) $doable=3;
                echo draw_skill($skill_name[$typeSkills[$r1[$x]]],$skills[$typeSkills[$r1[$x]]],1,$doable,0,$gen);
                $currWpts = lvl_req($myWeaves[$wid][1],getTypeMod($tskills,8));
                $postWpts = lvl_req($myWeaves[$wid][1]." ".$myWeaves[$wid][($r1[$x]+2)],getTypeMod($tskills,8));
                $overPts = 0;
                $overHalf = 0;
                if (($postWpts - $currWpts) > ($char['equip_pts'] - $char['used_pts']))
                {
                  $overPts = 1;
                }
                if ($postWpts > ($char['equip_pts']/2))
                {
                  $overHalf = 1;
                }
                if ($doable)
                {
                  if ($overHalf)
                  {
                    echo "<a data-href=\"javascript:skillUp(".$typeSkills[$r1[$x]].",".$types[$t].",1);\" data-toggle='confirmation' data-placement='top' title='Warning: This upgrade will cause your equipped weave to be unable to be equipped. Upgrade anyways?' class='btn btn-sm btn-primary'>Upgrade</a>";
                  }
                  else if ($overPts)
                  {
                    echo "<a data-href=\"javascript:skillUp(".$typeSkills[$r1[$x]].",".$types[$t].",1);\" data-toggle='confirmation' data-placement='top' title='Warning: This upgrade will cause your equipped weave to be unequipped. Upgrade anyways?' class='btn btn-sm btn-primary'>Upgrade</a>";
                  }
                  else 
                  {
                    echo "<input type='button' name='upgrade".$typeSkills[$r1[$x]]."' value='Upgrade' class='btn btn-sm btn-primary' onClick=\"javascript:skillUp(".$typeSkills[$r1[$x]].",".$types[$t].",0);\"/>";
                  }
                }
                echo "</div>";
              }
            ?> 
            </div>  
            <div class='row'><br/>
            <?php
              // Row 2
              for ($x = 0; $x < 3; $x++) 
              {
                $doable=1;
                echo "<div class='col-sm-4'>";
                if ($skills[$typeSkills[$r2[$x]]]>= 0) $doable=3;
                echo draw_skill($skill_name[$typeSkills[$r2[$x]]],$skills[$typeSkills[$r2[$x]]],1,$doable,0,$gen);
                $currWpts = lvl_req($myWeaves[$wid][1],getTypeMod($tskills,8));
                $postWpts = lvl_req($myWeaves[$wid][1]." ".$myWeaves[$wid][($r2[$x]+2)],getTypeMod($tskills,8));
                $overPts = 0;
                $overHalf = 0;
                if (($postWpts - $currWpts) > ($char['equip_pts'] - $char['used_pts']))
                {
                  $overPts = 1;
                }
                if ($postWpts > ($char['equip_pts']/2))
                {
                  $overHalf = 1;
                }
                if ($doable)
                {
                  if ($overHalf)
                  {
                    echo "<a data-href=\"javascript:skillUp(".$typeSkills[$r2[$x]].",".$types[$t].",1);\" data-toggle='confirmation' data-placement='top' title='Warning: This upgrade will cause your equipped weave to be unable to be equipped. Upgrade anyways?' class='btn btn-sm btn-primary'>Upgrade</a>";
                  }
                  else if ($overPts)
                  {
                    echo "<a data-href=\"javascript:skillUp(".$typeSkills[$r2[$x]].",".$types[$t].",1);\" data-toggle='confirmation' data-placement='top' title='Warning: This upgrade will cause your equipped weave to be unequipped. Upgrade anyways?' class='btn btn-sm btn-primary'>Upgrade</a>";
                  }
                  else 
                  {
                    echo "<input type='button' name='upgrade".$typeSkills[$r1[$x]]."' value='Upgrade' class='btn btn-sm btn-primary' onClick=\"javascript:skillUp(".$typeSkills[$r2[$x]].",".$types[$t].",0);\"/>";
                  }
                }
                echo "</div>";
              }
            ?>
            </div>  
            <div class='row'><br/>
              <p><A HREF="weaveStats.php" target="_blank" class='btn btn-primary btn-wrap'>Click here to see current weave stats</A></p>
            </div>  
          <?php
            }
          ?>
          </div>         
        <?php
          }
          if (floor($char[level]/20)+2>count($types))
          {
        ?>
          <div class="tab-pane <?php if ($tab == -3) echo 'active';?>" id="new_tab">                         
            <div class='row'>
              <form class='form-inline' name='statform2' action='stats.php' method='POST'>
                <input type='hidden' name='classup' value='-1' />
                <div class="form-group form-group-sm">
                  <label for="newclass">New Class:</label>
                  <select class='form-control gos-form' id="newclass" name="newclass" onChange="javascript:setNewClass();">
                    <option value='0'>-Select-</option>
                  <?php
                    foreach ($char_class as $cl => $cname) 
                    {
                      if ($noclass[$cl] != 1)
                      {
                        echo "<option value='".$cl."'>".$char_class[$cl]."</option>";
                      }
                    }
                  ?>
                  </select>
                  <input type='button' name='addclass' value='Add Class' class='btn btn-sm btn-success' onClick='javascript:classUp();'/>
                </div>
              </form>
            </div>
            <div class='row'>
              <div name='ncinfo' id='ncinfo'>&nbsp;</div>     
            </div>
          </div>
        <?php
          }
        ?>
        </div>
      </div>
    </div
  </div>
<form name='statform' action='stats.php' method='POST'> 
  <input type='hidden' id='doUneq' name='doUneq' value='0'/>
  <input type='hidden' name='skillup' value='-1' />
  <input type='hidden' name='activate' value='-1' />
  <input type='hidden' name='deactivate' value='-1' />
  <input type='hidden' name='tab' value='-2' />
</form>


<?php
include('footer.htm');
?>