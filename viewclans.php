<?php

/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/locFuncs.php");
include_once("map/mapdata/coordinates.inc");
$numbofresults = 25;
$time=time();
$types = array("Any","Shadow","Light","Not Shadow","Not Light","Neutral");

$wikilink = "Controlling+Cities";

$soc_name = $char[society];
$showloc=mysql_real_escape_string($_GET['loc']);
if ($showloc) $loc = $showloc;
else $loc = $char['location'];

// ACTUAL SELECT DISPLAYED CLANS
$query = "SELECT * FROM Soc WHERE 1 ORDER BY score DESC, members DESC";
$result = mysql_query($query);

if ($message == '') $message = "Clan Ranks in ".str_replace('-ap-','&#39;',$loc);

//HEADER
include('header.htm');
if ($showloc) $loc = $showloc;
else $loc = $char['location'];
?>

  <div class="row solid-back">
    <div class="col-sm-12">
      <table class="table table-condensed table-responsive table-hover table-clear small">
        <tr>
        <?php 
          if ($location_array[$loc][2]) 
          {
        ?> 
          <th width="20">&nbsp;</th>
        <?php
          }
        ?>    
          <th width="150">Clan</th>
          <th width="100">Type</th>
          <th width="75">Members</th>
          <th width="100">Align</th>
          <th width="75">Global Ji</th>
        <?php 
          if ($location_array[$loc][2]) 
          {
        ?>
          <th width="75">Ji</th>
          <th width="75">Total Ji</th>
        <?php
          }
        ?>   
        </tr>
<?php
$clans = array();
$supporting=array();

updateSocScores();
$loc_query = mysql_fetch_array(mysql_query("SELECT id,clan_scores, clan_support FROM `Locations` WHERE name = '$loc'")); 
$loco = $loc_query['id'];
while ($soc = mysql_fetch_array( $result ) )
{
  if ($location_array[$loc][2])
  {
    $clans=unserialize($loc_query[clan_scores]);
    $supporting=unserialize($loc_query[clan_support]);
  }
  else
  {
    $clans[$soc['id']] = $soc['score'];
    asort($clans);
    $clans = array_reverse($clans, TRUE);
  }
}

$x=0;
foreach ($clans as $key => $value)
{
  $soc = mysql_fetch_array(mysql_query("SELECT * FROM `Soc` WHERE id = '$key'")); 
  $soc_offices = unserialize($soc[offices]);
  $area_score = unserialize($soc['area_score']);
?>
        <tr>
        <?php 
          if ($location_array[$loc][2]) 
          {
        ?>  
          <td><?php if ($soc_offices[$loco]) echo " <img src='images/scale.gif' />";?></td>
        <?php
          }
          $alignnum = getClanAlignment($soc[align], $soc[members]);
          $alignClass='default';
          if ($alignnum == -2) $alignClass='danger';
          else if ($alignnum == -1) $alignClass='warning';
          else if ($alignnum == 1) $alignClass='success';
          else if ($alignnum == 2) $alignClass='primary';
        ?>    
          <td align='center'>
            <a href="joinclan.php?name=<?php echo $soc['name'];?>" class='btn btn-block btn-sm btn-wrap btn-<?php echo $alignClass;?>'><?php echo $soc[name]; ?></a>
          </td>
          <td align='center'>
          <?php
            if ($soc[invite] == 0) echo "Open"; 
            else if ($soc[invite] == 1 || ($soc[invite] == 2)) echo "Invite Only"; 
            else if ($soc[invite] == 3) echo "No Shadow";
            else if ($soc[invite] == 4) echo "No Light";
            else if ($soc[invite] == 5) echo "Neutral Only";
            else echo "Unknown";
          ?>
          </td>
          <td align='center'><?php echo number_format($soc[members]); ?></td>
          <td align='center'>
          <?php   
            $alignnum = getClanAlignment($soc[align], $soc[members]);
            $lalign = getAlignmentText($alignnum);
            echo number_format($soc[align])." (".$lalign.")";
          ?>    
          </td>
          <td align='center'><?php echo number_format($soc[score]); ?></td>
        <?php 
          if ($location_array[$loc][2]) 
          {
        ?>
          <td align='center'><?php echo number_format($area_score[$loco]); ?></td>
          <?php 
            if ($supporting[$key] == 0)
            {
          ?>
          <td align='center'><?php echo number_format($value); ?></td>
          <?php 
            }
            else 
            {
          ?>
          <td align='center'>Supporting</td>
        <?php 
            }
          }
        ?>
        </tr>
<?php
  $x++;
  if ($x == $numbofresults) {break;}
}
if ($x == 0) echo "<tr><td colspan=8 align='center'><br/><b>-No Clans-</b><br/><br/></td></tr>";
?>
      </table>

<?php if (!$soc_name)
{
?>
      <a href='makeclan.php' class='btn btn-sm btn-danger'>Form a Clan</a>
<?php
}
?>
    </div>
  </div>
<?php

include('footer.htm');

?>