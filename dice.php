<?php

/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");

$game = mysql_real_escape_string($_GET[game]);
$buyin = mysql_real_escape_string($_POST[buyin]);
$dwager = mysql_real_escape_string($_POST[dwager]);

$message="<b>Tavern Dice Games</b>";
$players = 2;
$dice = '';
$list = '';
$name = $char[name]." ".$char[lastname];
$p_name = array("One","Two","Three","Four","Five");
$p_info = '';
$winning_score = 0;
$winners = 0;
$text = '';
$query = "SELECT * FROM Locations WHERE name='$char[location]'";
$result = mysql_query($query);
$location = mysql_fetch_array($result);
if ($location[curr_dice]) $curr_dice = unserialize($location[curr_dice]);
if ($location[prev_dice]) $prev_dice = unserialize($location[prev_dice]);
$wager = $location[wager];
$gtype = $location[gtype];


if ($buyin)
{
  if (($wager > $char[gold] || $wager <= 0)) {$message="Why don't you put your money where your mouth is?";} 
  elseif( $dwager != $wager) {$message="The wager amount changed while you held the dice. Toss again.";}
  elseif (!$curr_dice[$name])
  {
    $newdice = '';
    for ($x=0; $x<5; $x++) 
    {
      $newdice[$x] = rand(1,6);
    }
    $curr_dice[$name] = $newdice;
    $new_dice = serialize($curr_dice);
    $query = "UPDATE Locations SET curr_dice='$new_dice' WHERE name='$char[location]'"; 
    $result = mysql_query($query); 
    $gold = $char[gold]-$wager;
    $query2 = "UPDATE Users SET gold='$gold' WHERE id='$char[id]'";
    $result2 = mysql_query($query2); 
    $char[gold]=$gold;
    $ustats = mysql_fetch_array(mysql_query("SELECT * FROM Users_stats WHERE id='".$id."'"));
    $ustats['dice_earn'] -= $wager;
    $result = mysql_query("UPDATE Users_stats SET dice_earn='".$ustats[dice_earn]."' WHERE id='".$id."'");
  }
  else { $message = "You are already in this game! Wait for the next one to start."; }
}

// PREVIOUS RESULTS
if ($prev_dice)
{
 $owager = $location[old_wager];
 // COMPUTE WINNER
 foreach ($prev_dice as $p => $a)
 {
  for ($x=0; $x<5; $x++)
  {
   $list[$p][$a[$x]-1] += 1;
  }
  $p_info[$p] = ComputeScore($list[$p],$gtype);
  if ($p_info[$p] > $winning_score) $winning_score=$p_info[$p];
 }
 
 // DISPLAY
 $text .= "<center><font class='littletext'><b>Previous Game Results:</b></font>";
 $text .= "<font class='littletext' style='font-size: 14px'>";
 $text .= "<table border='0' cellpadding='5' cellspacing='0'>";
 foreach ($prev_dice as $p => $a)
 {
  $text .= "<tr><td><font class='littletext'>";
  if ($p == "NPC") $text .= "<i>".str_replace('-ap-','&#39;',$char['location'])." Gambler's Roll: </i>"; 
  else $text .= "<i>".$p."'s Roll</i>";
  $text .= "</td><td width='5'></td><td><table rules='none' border='";
  if ($p_info[$p] == $winning_score) {$text .= "2"; $winners++;}
  else $text .= "0";
  $text .= "' bordercolor='ED3915' cellpadding='2' cellspacing='0'>";
  for ($x=0; $x<5; $x++) $text .= "<td><img src='dice/d".$gtype.$prev_dice[$p][$x].".gif'></td>";
  $text .= "</table></td></tr>";
 }
 $winnings = intval(($owager*count($prev_dice))/$winners);
 $text .= "<tr><td colspan='3'><center><font class='littletext'><i><b>This round was worth </i>".displayGold($winnings);
 if ($winners>1) $text .= "<i> - $winners way tie</i>";
 $text .= "</td></tr></table><br><br>";
}
// DISPLAY
include('header.htm');
?>


<center>

<table border=0 cellpadding=4 cellspacing=0>
 <form method="post" action="dice.php">
  <tr><td rowspan='5'><img src='dice/dice.gif'></td>
    <td>&nbsp;<input type="hidden" name="buyin" id="buyin" value="1" class="form"><input type="hidden" name="dwager" id="dwager" value="<?php echo $wager;?>" class="form"></td></tr>
  <tr><td valign=center>
    <?php
      if (!$curr_dice[$name]) {
    ?>
      <center><input type="Submit" name="submit" value="Toss the Dice" class="btn btn-sm btn-success">
    <?php
      } else {
        $text2 = "<font class='littletext'>";
        $text2 .= "<i>".$name."'s Roll: </i>";
        for ($x=0; $x<5; $x++) $text2 .= "<img src='dice/d".$gtype.$curr_dice[$name][$x].".gif'>";
        echo $text2;
      }
    ?>
  </td></tr>
  <tr><td><font class='littletext'><b>Wager</b>: <?php echo displayGold($wager);?></td></tr>
  <tr><td><font class='littletext'><b>Players so far: <?php echo count($curr_dice);?>
  <tr><td><font class='littletext'><b>Pot size: <?php echo displayGold(count($curr_dice)*$wager);?>  
 </form>
</table>
<br><br><br>
<?php
echo $text;
?>
<?php
  include('footer.htm');
?>