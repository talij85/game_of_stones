<?php
include_once("admin/itemJava.php");
include_once('admin/skills.php');

$askills = unserialize($char['active']);
$types = unserialize($char['type']);

$fullness=0;
$presult=mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND type>=19 AND istatus>0");
while ($qitem = mysql_fetch_array($presult))
{
  $stomach[$fullness++] = $qitem;
}

$a = '';
$b = '';
$c = '';
$d = '';
$e = '';
$a = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus='1' AND type<15"));
$b = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus='2' AND type<15"));
$c = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus='3' AND type<15"));
$d = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus='4' AND type<15"));
$e = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus='5' AND type<15"));

// Find Active Skills CHAR ONE
$skill_bonuses1 = "A0 B0";

for ($t=0; $t<count($types); ++$t)
{
  $curr = $types[$t];
  $typeSkills=typeSkills($curr);
  // passive skills
  if ($curr != 108)
  {
    for ($x = 1; $x < count($typeSkills); $x+=2) 
    {  
      if ($skills[$typeSkills[$x]] > 0)
      {
        $skill_bonuses1 .= " ".getSkillEffect($skill_name[$typeSkills[$x]],$skills[$typeSkills[$x]]);
      }
    }
  }
  else
  {
    for ($x = 0; $x < count($typeSkills); $x++) 
    {  
      $skill_bonuses1 .= " ".getSkillEffect($skill_name[$typeSkills[$x]],$skills[$typeSkills[$x]],$char[sex]);
    }
  }
}
for ($x = 0; $x < count($askills); $x++) 
{
  $skill_bonuses1 .= " ".getSkillEffect($skill_name[$askills[$x]],$skills[$askills[$x]]);
}
$estats = getstats($a,$b,$c,$d,$e,$skill_bonuses1);
$skill_tmp = cparse($skill_bonuses1." ".$estats[0]." ".$clan_building_bonuses, 0);
$tinitial = array('','s','x','p','o','b','k','h','w','a','l');
for ($t=1; $t <8; $t++)
{
  // adjust for weaves
  if ($t!=7) $ti = $t;
  else $ti=$t+1;
  if (mysql_num_rows(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus>0 AND type='$ti'")))
  {
    if ($skill_tmp[$tinitial[$ti].'A'])
    {
      $mainbonus[0][0]+=$skill_tmp[$tinitial[$ti].'A'];
      $mainbonus[1][0]+=$skill_tmp[$tinitial[$ti].'A']*2;
    }
    if ($skill_tmp[$tinitial[$ti].'O'])
      $mainbonus[2][0]+=$skill_tmp[$tinitial[$ti].'O'];
  }
}
$skill_bonuses1 .= " A".$mainbonus[0][0]." B".$mainbonus[1][0]." O".$mainbonus[2][0];
$mainbonus[0][0]=0;
$mainbonus[1][0]=0;
$mainbonus[2][0]=0;
for ($t=8; $t <11; $t++)
{
  // adjust for shields
  if ($t!=8) $ti = $t;
  else $ti=$t-1;
  if (mysql_num_rows(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus>0 AND type='$ti'")))
  {
    if ($skill_tmp[$tinitial[$ti].'A'])
    {
      $mainbonus[0][0]+=$skill_tmp[$tinitial[$ti].'A'];
      $mainbonus[1][0]+=$skill_tmp[$tinitial[$ti].'A']*2;
    }
    if ($skill_tmp[$tinitial[$ti].'O'])
      $mainbonus[2][0]+=$skill_tmp[$tinitial[$ti].'O'];
  }
}
$skill_bonuses1 .= " D".$mainbonus[0][0]." E".$mainbonus[1][0]." N".$mainbonus[2][0];

// Stamina effects
$stam_diff1 = $char[stamina]/$char[stamaxa];
$stamina_effect1 = getStamEffects($stam_diff1);

// check stomachs
$stomach_bonuses1 = "A0 B0";
for ($x = 0; $x < $fullness; $x++) 
{
  $stomach_bonuses1 .= " ".itp($item_base[$stomach[$x][base]][0],$stomach[$x][type]);
}


if ($char2)
{
  $skills2 = unserialize($char2['skills']);
  $askills2 = unserialize($char2['active']);
  $types2 = unserialize($char2['type']);
  
  $fullness2=0;
  $presult=mysql_query("SELECT * FROM Items WHERE owner='$char2[id]' AND type>=19 AND istatus>0");
  while ($qitem = mysql_fetch_array($presult))
  {
    $stomach2[$fullness2++] = $qitem;
  }
  $a = -1;
  $b = -1;
  $c = -1;
  $d = -1;
  $e = -1;

  // Find Equipped Items CHAR 2
  $a = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char2[id]' AND istatus='1' AND type<15"));
  $b = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char2[id]' AND istatus='2' AND type<15"));
  $c = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char2[id]' AND istatus='3' AND type<15"));
  $d = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char2[id]' AND istatus='4' AND type<15"));
  $e = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char2[id]' AND istatus='5' AND type<15"));

  // Find Active Skills CHAR 2
  $skill_bonuses2 = "A0 B0";
  for ($t=0; $t<count($types2); ++$t)
  {
    $curr = $types2[$t];
    $typeSkills=typeSkills($curr);
    // passive skills
    if ($curr != 108)
    {
      for ($x = 1; $x < count($typeSkills); $x+=2) 
      {  
        if ($skills2[$typeSkills[$x]] > 0)
        {
          $skill_bonuses2 .= " ".getSkillEffect($skill_name[$typeSkills[$x]],$skills2[$typeSkills[$x]]);
        }
      }
    }
    else
    {
      for ($x = 0; $x < count($typeSkills); $x++) 
      {  
        $skill_bonuses2 .= " ".getSkillEffect($skill_name[$typeSkills[$x]],$skills2[$typeSkills[$x]],$char[sex]);
      } 
    }    
  }
  for ($x = 0; $x < count($askills2); $x++) 
  {
    $skill_bonuses2 .= " ".getSkillEffect($skill_name[$askills2[$x]],$skills2[$askills2[$x]]);
  }
  
  $estats2 = getstats($a,$b,$c,$d,$e,$skill_bonuses2);
  $skill_tmp = cparse($skill_bonuses2." ".$estats2[0], 0);
  $mainbonus[0][1]=0;
  $mainbonus[1][1]=0;
  $mainbonus[2][1]=0;
  // add type bonuses
  for ($t=1; $t <8; $t++)
  {
    // adjust for weaves
    if ($t!=7) $ti = $t;
    else $ti=$t+1;
    if (mysql_num_rows(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus>0 AND type='$ti'")))
    {
      if ($skill_tmp[$tinitial[$ti].'A'])
      {
        $mainbonus[0][1]+=$skill_tmp[$tinitial[$ti].'A'];
        $mainbonus[1][1]+=$skill_tmp[$tinitial[$ti].'A']*2;
      }
      if ($skill_tmp[$tinitial[$ti].'O'])
        $mainbonus[2][1]+=$skill_tmp[$tinitial[$ti].'O'];
    }   
  }
  $skill_bonuses2 .= " A".$mainbonus[0][1]." B".$mainbonus[1][1]." O".$mainbonus[2][1];
  $mainbonus[0][1]=0;
  $mainbonus[1][1]=0;
  $mainbonus[2][1]=0;
  for ($t=8; $t <11; $t++)
  {
    // adjust for shields
    if ($t!=8) $ti = $t;
    else $ti=$t-1;
    if (mysql_num_rows(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus>0 AND type='$ti'")))
    {
      if ($skill_tmp[$tinitial[$ti].'A'])
      {
        $mainbonus[0][1]+=$skill_tmp[$tinitial[$ti].'A'];
        $mainbonus[1][1]+=$skill_tmp[$tinitial[$ti].'A']*2;
      }
      if ($skill_tmp[$tinitial[$ti].'O'])
        $mainbonus[2][1]+=$skill_tmp[$tinitial[$ti].'O'];
    }
  }  
  $skill_bonuses2 .= " D".$mainbonus[0][1]." E".$mainbonus[1][1]." N".$mainbonus[2][1];

  $stam_diff2 = $char2[stamina]/$char2[stamaxa];
  $stamina_effect2 = getStamEffects($stam_diff2);
   
  $stomach_bonuses2 = "A0 B0";
  for ($x = 0; $x < $fullness2; $x++) 
  {
    $stomach_bonuses2 .= " ".itp($item_base[$stomach2[$x][base]][0],$stomach2[$x][type]);
  }  
}
?>