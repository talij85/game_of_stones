<?php
include_once("itemFuncs.php");
include_once("displayFuncs.php");
include_once("charFuncs.php");
include_once("skills.php");

echo "<b>ITEMS DUMP</b><br/>";
foreach ($item_base as $item => $stats)
{
  echo $item."<br/>";
  echo itm_info(cparse($stats[0],0));
  echo "<br/>";
}
echo "<br/><hr/><br/><";
echo "<b>CLASS DUMP</b><br/><br/>";
foreach ($char_class as $c => $cname)
{
  echo "<u>$cname</u><br/>";
  $skills = typeSkills($c);
  for ($s=0; $s <= count($skills); $s++)
  {
    echo $skill_name[$skills[$s]]."<br/>";
    echo itm_info(cparse(getSkillEffect($skill_name[$skills[$s]],1),0));
    echo "<br/>";
  }
  echo "<hr/>";
}
?>