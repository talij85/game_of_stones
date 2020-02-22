<?php
include("admin/itemFuncs.php");

$itmlist = unserialize($char['itmlist']);
$itmlist2 = unserialize($char2['itmlist']);
$skills = unserialize($char['skills']);
$skills2 = unserialize($char2['skills']);
$stomach = unserialize($char['stomach']);
$stomach2 = unserialize($char2['stomach']);

// Find Equipped Items CHAR ONE
$y = 0;
while ($y < count($itmlist))
{
if ($itmlist[$y][3]) $itm_a[$itmlist[$y][3]-1] = $itmlist[$y];
$y++;
}
if (!$itm_a[0][0]) $itm_a[0][0] = "fist";
if (!$itm_a[1][0]) $itm_a[1][0] = "body";
if (!$itm_a[2][0]) $itm_a[2][0] = "fist";

// Find Active Skills CHAR ONE
$skill_bonuses1 = "A0 B0";
for ($x = 0; $x < count($skills); $x++) 
{
  $add_skill = 1;
  if ($skills[$x][1] > 0)
  {
    $skill_tmp = cparse(getSkillEffect($skills[$x][0],$skills[$x][1]), 0);
    if ($skill_tmp['W'])
    {
      if (!(($item_base[$itm_a[0][0]][1] == $skill_tmp['W']) || ($item_base[$itm_a[2][0]][1] == $skill_tmp['W']) || ($item_base[$itm_a[2][0]][1] == $skill_tmp['W']))) $add_skill =0; 
    }
  }
  else { $add_skill =0; }
  if ($add_skill)  $skill_bonuses1 .= " ".getSkillEffect($skills[$x][0],$skills[$x][1]);
}

// Find Equipped Items CHAR 2
$y = 0;
while ($y < count($itmlist2))
{
if ($itmlist2[$y][3]) $itm_b[$itmlist2[$y][3]-1] = $itmlist2[$y];
$y++;
}
if (!$itm_b[0][0]) $itm_b[0][0] = "fist";
if (!$itm_b[1][0]) $itm_b[1][0] = "body";
if (!$itm_b[2][0]) $itm_b[2][0] = "fist";

// Find Active Skills CHAR 2
$skill_bonuses2 = "A0 B0";
for ($x = 0; $x < count($skills2); $x++) 
{
  $add_skill = 1;
  if ($skills2[$x][1] > 0)
  {
    $skill_tmp = cparse(getSkillEffect($skills2[$x][0],$skills2[$x][1]), 0);
    if ($skill_tmp['W'])
    {
      if (!(($item_base[$itm_b[0][0]][1] == $skill_tmp['W']) || ($item_base[$itm_b[2][0]][1] == $skill_tmp['W']) || ($item_base[$itm_b[2][0]][1] == $skill_tmp['W']))) $add_skill =0; 
    }
  }
  else { $add_skill =0; }
  if ($add_skill)  $skill_bonuses2 .= " ".getSkillEffect($skills2[$x][0],$skills2[$x][1]);
}

?>