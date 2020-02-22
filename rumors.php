<?php

/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("map/mapdata/coordinates.inc");
if ($location_array[$char['location']][2]) $is_town=1;
else $is_town=0;

$wikilink = "Rumors";

$sealToCity=mysql_real_escape_string($_POST['sealCity']);
$hornTarget=mysql_real_escape_string($_POST['hornTarget']);
$tab = mysql_real_escape_string($_GET['tab']);
$curHorn = mysql_fetch_array( mysql_query("SELECT * FROM Items WHERE type='-1' ORDER BY last_moved" ));

if ($sealToCity != "" && $sealToCity > 0)
{
  $soc_name = $char[society];
  $loc =  mysql_fetch_array(mysql_query("SELECT id, name, ruler FROM Locations WHERE id='".$sealToCity."' AND ruler='".$soc_name."'"));
  $mySeal = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE type=0 AND owner='$char[id]'"));
  if ($mySeal[id])
  {
    if ($loc[id])
    {
      $locid = 50000 + $loc[id];
      $myTime = time();
      mysql_query("UPDATE Items SET owner = '".$locid."', last_moved='".$myTime."' WHERE id='".$mySeal[id]."' ");
      $message = "Seal added to $loc[name]!";
    }
    else
      $message = "Invalid city selected.";
  }
  else
    $message = "You don't have a Seal...";
}

if ($hornTarget != "" && $hornTarget > 0)
{
  $myHorde = mysql_fetch_array( mysql_query("SELECT id, npcs, done, ends, location FROM Hordes WHERE target='$char[location]' AND type='1' ORDER BY ends DESC LIMIT 1"));
  
  if ($myHorde[id])
  {
    $npc_info = unserialize($myHorde[npcs]);
    if ($npc_info[$hornTarget-1][1] > 0)
    {
      $oldHealth = $npc_info[$hornTarget-1][1];
      $npc_info[$hornTarget-1][1] = ceil($oldHealth/2);
      $hornDmg = $oldHealth - $npc_info[$hornTarget-1][1];
      $sni = serialize($npc_info);
      $alignMod = 100;
      if ($hornTarget == 2) $alignMod = -100;
      mysql_query("UPDATE Users SET align = align + '".$alignMod."' WHERE id='".$char[id]."'");
      mysql_query("UPDATE Hordes SET npcs='".$sni."' WHERE id='".$myHorde[id]."'");
      mysql_query("DELETE FROM Items WHERE id='".$curHorn[id]."'");
      
      // Update City Rumors
      $groupName = "horde";
      if ($hornTarget == 2) $groupName = "army";
      $myMsg = "<".$char[location]."_".time().">` The Horn of Valere has sounded! ";
      $myMsg .= "The Heroes of the Horn dealt ".$hornDmg." to the ".$groupName."!|";
      $cityRumors = mysql_fetch_array(mysql_query("SELECT * FROM messages WHERE id='50000'"));
      $rumorMessages = unserialize($cityRumors[message]);
      $numRumors = count($rumorMessages);
      $rumorMessages[$numRumors] = $myMsg;
      $rumorMessages = pruneMsgs($rumorMessages, 50);
      $newRumors = serialize($rumorMessages);
      $resultR = mysql_query("UPDATE messages SET message='$newRumors' WHERE id='50000'"); 
      
      $message = "The Heroes of the Horn deal $hornDmg to the ".$npc_info[$hornTarget-1][0]."!";
    }
    else
    {
      $message = "Your target is already destroyed!";
    }
  }
  else
  {
    $message = "There is no horde to target!";
  }
}

if ($message == '') $message = "You listen around the city for the latest rumors...";

include_once("rumors_code.php");
?>
<script language="javascript">
// initialize chat
window.onload=intitializeChat;
</script>

<?php 
//HEADER
if (!$is_town) $message = "Rumors spread slowly in ".str_replace('-ap-','&#39;',$char['location']);

if (!$tab) $tab=1;
include('header.htm');

if ($is_town)
{
  $style="style='border-width: 0px; border-bottom: 1px solid #333333'";
  $numSeals=mysql_num_rows(mysql_query("SELECT id FROM Items WHERE type=0"));
  $numHorn=mysql_num_rows(mysql_query("SELECT id FROM Items WHERE type='-1'"));
  $sealList[0]='';
  $sealNum=1;
  $result = mysql_query("SELECT * FROM Items WHERE type=0 ORDER BY last_moved");  
  while ($tmpSeal = mysql_fetch_array( $result ) )
  {
    $sealList[$sealNum++] = $tmpSeal;
  }
  $curHorn = mysql_fetch_array( mysql_query("SELECT * FROM Items WHERE type='-1' ORDER BY last_moved" ));

?>
  <div class="row solid-back">
    <div class="col-sm-12">
      <div id="content">
        <ul id="rumortabs" class="nav nav-tabs" data-tabs="tabs">
          <li <?php if ($tab == 1) echo "class='active'";?>><a href="#city_tab" data-toggle="tab">City Rumors</a></li>
          <li <?php if ($tab == 2) echo "class='active'";?>><a href="#item_tab" data-toggle="tab">Items of Legend</a></li>
        </ul>

        <div class="tab-content">
          <div class="tab-pane <?php if ($tab == 1) echo 'active';?>" id="city_tab">
            <div class='col-sm-12 col-md-10 col-md-push-1 col-lg-8 col-lg-push-2'>
              <div id='chatdiv' align='left'></div>
              <input type='hidden' id='mid' value='50000'/>
            </div>
          </div>
          <div class="tab-pane <?php if ($tab == 2) echo 'active';?>" id="item_tab">
            <div class='row'>
              <div class='col-sm-12'>
              <?php
                $cityRumors = mysql_fetch_array(mysql_query("SELECT * FROM messages WHERE id='50000'"));
                // If LB is coming/started, put a countdown. Numbers tied to admin/lastbattle.php
                if ($cityRumors[checktime] > 0)
                {
                  $minFromBreak = intval(((time()/60)-$cityRumors[checktime]*60));
                  if ($minFromBreak < 43200) // 720 hours after first seal breaks LB starts
                  {
                    echo "<b>The Last Battle begins in ".displayTime(43200-$minFromBreak,0,1)."!</b><br/><br/>" ;
                  }
                  else if ($minFromBreak < 53280) // 888 hours after first seal break LB ends
                  {
                    echo "<b>The Last Battle in progress!</b><br/><br/>" ;
                  }
                  else
                  {
                    echo "<b>The Last Battle is over!</b><br/><br/>" ;
                  }
                }
              ?>              
                <table class='table table-responsive table-condensed table-clear table-hover table-striped small'>
                  <tr>
                    <th>Item</th>
                    <th>Owner</th>
                    <th>Location</th>
                    <th>Time Held</th>	
                  </tr>
                  <?php
                    $hasHorn = 0;
                    $topchar = mysql_fetch_array(mysql_query("SELECT id, level FROM Users WHERE 1 ORDER BY level DESC, exp DESC LIMIT 1"));
                    $availHorn = getMaxHorn($topchar[level]);
                  ?>
                  <tr>
                    <td align='center'>Horn</td>
                  <?php
                    if ($numHorn > 0 )
                    {
                      // Display Horn Info
                      $owner = mysql_fetch_array(mysql_query("SELECT id, name, lastname, goodevil, location FROM Users WHERE id='".$curHorn[owner]."'"));
                      if ($owner['id'] == $char[id])
                      {
                        $classn="btn-info";
                        $hasHorn = 1;
                      }
                      else $classn="btn-primary";
                        
                      $owner_name = "<a class='btn btn-xm btn-block btn-wrap ".$classn."' href='bio.php?name=".$owner['name']."&last=".$owner['lastname']."'>".$owner['name']." ".$owner['lastname']."</a>";
                      $loc_name = str_replace('-ap-','&#39;',$owner[location]);
                      
                      $heldTime = intval((time()-$curHorn[last_moved])/60);
                  ?>
                    <td align='center'><b><?php echo $owner_name;?></b></td>
                    <td align='center'><b><?php echo $loc_name; ?></b></td>
                    <td align='center'><?php echo displayTime($heldTime,0,1);?></td>
                  <?php
                    } // end if horn
                    else if ($availHorn > 0)
                    {
                      // Horn not found yet
                  ?>
                    <td align='center' colspan=4>Horn not recovered!</td>
                  <?php
                    } 
                    else
                    {
                      // Horn not found yet
                  ?>
                    <td align='center' colspan=4>Horn still unavailable!</td>
                  <?php
                    } 
                  ?>
                  </tr>
                  <?php
                    $hasSeal = 0;
                    $heldFor = 0;
                    $availSeals = getMaxSeals($topchar[level]);

                    for ($s = 1; $s <= 7; $s++)
                    {
                  ?>
                  <tr>
                    <td align='center'>Seal <?php echo $s; ?></td>
                    <?php
                      if ($numSeals >= $s )
                      {
                        // Display Seal Info
                        if ($sealList[$s][owner] < 50000)
                        {
                          // Seal held by player
                          $owner = mysql_fetch_array(mysql_query("SELECT id, name, lastname, goodevil, location FROM Users WHERE id='".$sealList[$s][owner]."'"));
                          if ($owner['id'] == $char[id])
                          {
                            $classn="btn-info";
                            $hasSeal = $s;
                            $heldFor = intval((time()-$sealList[$s][last_moved])/60);
                          }
                          else $classn="btn-primary";
                          $owner_name = "<a class='btn btn-xs btn-block btn-wrap ".$classn."'  href='bio.php?name=".$owner['name']."&last=".$owner['lastname']."'>".$owner['name']." ".$owner['lastname']."</a>";
                          $loc_name = str_replace('-ap-','&#39;',$owner[location]);
                        }
                        else if ($sealList[$s][owner] == 99999)
                        {
                          $classn="btn-default";
                          $owner_name = "<a class='btn btn-xm btn-block btn-wrap ".$classn."' href='#'>No One</a>";                          
                          $loc_name = "Broken!";
                        }
                        else
                        {
                          // Seal held by a city
                          $locid = $sealList[$s][owner] - 50000;
                          $owner = mysql_fetch_array(mysql_query("SELECT id, name, ruler FROM Locations WHERE id='".$locid."'"));
                          $classn="btn-warning";
                          $owner_name = "<a class='btn btn-xm btn-block btn-wrap ".$classn."' href=\"joinclan.php?name=".$owner[ruler]."\">".$owner[ruler]."</a>";
                          $loc_name = $owner[name];
                        }        
                        $heldTime = intval((time()-$sealList[$s][last_moved])/60);
                    ?>
                    <td align='center'><b><?php echo $owner_name;?></b></td>
                    <td align='center'><b><?php echo $loc_name; ?></b></td>
                    <td align='center'><?php echo displayTime($heldTime,0,1);?></td>
                    <?php
                      } // end if seal
                      else if ($s <= $availSeals)
                      {
                        // Seal not found yet
                    ?>
                    <td align='center' colspan=4>Seal not recovered!</td>
                    <?php
                      } 
                      else
                      {
                        // Seal not found yet
                    ?>
                    <td align='center' colspan=4>Seal still unavailable!</td>
                    <?php
                      } 
                    ?>
                  </tr>
                  <?php       
                    } // end for seals
                  ?>
                </table>
    <form id="hornForm" name="hornForm" action="rumors.php" method="post">
<?php
      
      $result3 = mysql_query("SELECT id, npcs, done, ends, location FROM Hordes WHERE target='$char[location]' AND type='1' AND done='0' ORDER BY ends DESC LIMIT 1");
      while ($myHorde = mysql_fetch_array( $result3 ) )
      {
        if ($hasHorn)
        {
          $npc_info = unserialize($myHorde[npcs]);
          echo "Use Horn to target (".$myHorde[id]."): ";
?>
        <select name="hornTarget" size="1" class="form-control gos-form"><option value="0">-Select-</option>
<?php 
        for ($i = 0; $i<2; $i++)
        {
          if ($npc_info[$i][1] > 0)
          {
            echo "<option value='".($i+1)."'>".$npc_info[$i][0]."</option>";
          }
        }
        echo "</select>";
?>
        <input type="submit" name="submit" value="Sound the Horn!" class="btn btn-sm btn-warning"/>
<?php
        }
      }
?>
    </form>

    <form id="sealForm" name="sealForm" action="rumors.php" method="post">
<?php
      $soc_name = $char[society];
      $society = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$soc_name' "));
      if ($hasSeal && $heldFor > 60*24 && $society[id])
      {
        echo "Add Seal to Ruled City: ";
?>
        <select name="sealCity" size="1" class="form-control gos-form"><option value="0">-Select-</option>
<?php 
        $result = mysql_query("SELECT id, name FROM Locations WHERE ruler='".$society[name]."'");  
        while ($ruledCity = mysql_fetch_array( $result ) )
        {
          $sealid = $ruledCity[id]+50000;
          $hasSeal = mysql_num_rows(mysql_query("SELECT id FROM Items WHERE type=0 && owner='$sealid'"));
          if (!$hasSeal)
          {
            echo "<option value='".$ruledCity[id]."'>".$ruledCity[name]."</option>";
          }
        }
        echo "</select>";
?>
        <input type="submit" name="submit" value="Add Seal" class="btn btn-sm btn-warning"/>
<?php
      }
?>
    </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php
}
include('footer.htm');
?>