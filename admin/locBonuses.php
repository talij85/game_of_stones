<?php
// $location needs to be set before this is included.
// $clan_building_bonuses is also referenced, but is optional. Only needed for cities.

$result3 = mysql_query("SELECT id FROM Hordes WHERE done='0' AND target='$location[name]'");
$numhorde = mysql_num_rows($result3);
if ($numhorde) $lochorde = 1;
else $lochorde=0;

$upgrades= unserialize($location[upgrades]);
if (!$location[isDestroyed])
{
  $display_town_bonuses = cparse($city_duel_bonus[$location[name]]." ".getTownBonuses($upgrades, $unique_buildings[$location[name]], $unique_build_bonuses, $lochorde));
  $town_bonuses = cparse($city_duel_bonus[$location[name]]." ".getTownBonuses($upgrades, $unique_buildings[$location[name]], $unique_build_bonuses, $lochorde)." ".$clan_building_bonuses);
}
else
{
  $display_town_bonuses = "";
  $town_bonuses = "";
}

// if local war, divide bonuses in half.
if ($location[last_war])
{
  $myWar = mysql_fetch_array(mysql_query("SELECT id, starts, ends FROM Contests WHERE id='$location[last_war]' "));
}
else
{
  $myWar = 0;
}
if ($myWar != 0) // Previous clan battle
{
  if ($myWar[starts] <= intval(time()/3600) && $myWar[ends] > intval(time()/3600))
  {
    foreach ($town_bonuses as $tb => $tbv)
    {
      $town_bonuses[$tb] = intval($tbv/2);
    }
  }
}

if ($char[id])
{
  $town_bonuses[tV]=0;
  $surrounding_area_temp=$map_data[$location[name]];

  for ($tx=0; $tx<4; $tx++) 
  {
    $tmpEstate = mysql_fetch_array(mysql_query("SELECT id, upgrades FROM Estates WHERE location='$surrounding_area_temp[$tx]' AND owner='$char[id]'"));
    if ($tmpEstate[id])
    {
      $tmpUps = unserialize($tmpEstate[upgrades]);
      if ($tmpUps[5]>0)
      {
        $town_bonuses[tV] -= $tmpUps[5];
      }
    }
  }
}
?>