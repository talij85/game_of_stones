<html>
<head>
<title>Admin Recreate Society Table</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<u>Resets the table "IP_logs"</u><br><br>
<?php
// Connect
include("connect.php");
include("itemarray.php");

if (strtolower($name) != "the" && strtolower($lastname) != "creator")
{
  echo "Only the Creator has such powers!";
}
else
{
  // Drop Old Table
  $query  = 'DROP TABLE Users_stats';
  $result = mysql_query($query);
  echo "<b>Results</b><br><br>Drop Old Table: $result";

  // Create New Table
  $query = 'CREATE TABLE Users_stats( '.
  'id int NOT NULL, '.
  'wins int, '.
  'battles int, '.
  'duel_wins int, '.
  'tot_duels int, '.
  'enemy_wins int, '.
  'enemy_duels int, '.
  'off_wins int, '.
  'off_bats int, '.
  'npc_wins int, '.
  'tot_npcs int, '.
  'duel_earn int, '.
  'item_earn int, '.
  'dice_earn int, '.
  'quests_done int, '.
  'npc_types text, '.
  'quest_types text, '.
  'PRIMARY KEY (id)'.
  ')';
  $result = mysql_query($query);
  echo "<br>Create New Table: $result";

}
// Close Database
mysql_close($db);
?>

<br><br>
~Table reset and recreator for societies "THE START"~
</body>
</html>