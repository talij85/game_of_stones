<?php

/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/questFuncs.php");
include_once("admin/itemFuncs.php");
include_once("admin/charFuncs.php");
include_once("admin/locFuncs.php");

// Check if city has been destroyed. If so, don't display anything.
if (!$location[isDestroyed])
{
include_once("map/mapdata/coordinates.inc");
if ($location_array[$char['location']][2]) $is_town=1;
else $is_town=0;

$time=time();
$accepted= mysql_real_escape_string($_POST[accepted]);
$myquests= unserialize($char[quests]);

$loc = $char['location'];

$wikilink = "Quests";
  
if ($accepted != '')
{
  $noaccept = 0;
  $quest = mysql_fetch_array(mysql_query("SELECT * FROM Quests WHERE id='$accepted'"));
  $qgoals = unserialize($quest[goals]);
  $myquests[$accepted][0] = 1;
  $myquests[$accepted][1] = 0; 
  if ($quest[type] == $quest_type_num["Find"])
  {
    $myquests[$accepted][2] = rand(0,4);
    $myquests[$accepted][3] = rand(0,4);     
  }
  else if ($quest[type] == $quest_type_num["Horde"])
  {
    $myquests[$accepted][1] = $char[vitality]*$qgoals[0];
    $myquests[$accepted][2] = $char[vitality]*$qgoals[0];
  }
  else if ($quest[type] == $quest_type_num["Escort"])
  {
    $myquests[$accepted][2] = 0;
  }
  else if ($quest[type] == $quest_type_num["Support"]) 
  {
    // Check for other support quests
    $osupport = 0;
    $curTime = intval(time()/3600);
    $query = "SELECT * FROM Quests WHERE expire >= '".$curTime."' ORDER BY expire ASC";
    $result = mysql_query($query);
    while ($quest2 = mysql_fetch_array( $result ) )
    {
      $qid = $quest2[id];
      $goals = unserialize($quest2[goals]);      
      
      if ($myquests[$qid][0]==1)
      {
        if (($quest2[expire] != -1 && $quest2[expire]*3600 < time()) || $quest2[done]==1)
        {
          $myquests[$qid][0] = 0;
          $smyq = serialize($myquests);
          mysql_query("UPDATE Users LEFT JOIN Users_data ON Users.id=Users_data.id SET quests='$smyq' WHERE Users.id=".$char[id]);
          $resultt = mysql_query("UPDATE Quests SET done='1' WHERE id='".$quest2[id]."'");
        }
        if ($quest2[type] == $quest_type_num["Support"])
        {
          $osupport++;
        }
      }
    }
    if ($osupport > 2) {$noaccept=1;}
  }
  
  if (!$noaccept) 
  {
    $myquests_ser = serialize($myquests);
    mysql_query("UPDATE Users LEFT JOIN Users_data ON Users.id=Users_data.id SET quests='$myquests_ser' WHERE Users.id=".$char[id]);
    
    $qusers = unserialize($quest[users]);
    $qusers[count($qusers)] = $char[id];
    $qusers2 = serialize($qusers);
    mysql_query("UPDATE Quests SET users='$qusers2' WHERE id='$accepted'"); 
    $message = "Quest accepted!";
  }
  else
  {
    $message = "You already have accepted two Support quests.";
    $myquests= unserialize($char[quests]);
  }
}
      
if ($message == '') $message = "Local Quests";

//HEADER
if (!$is_town) $message = "There are no quests in ".str_replace('-ap-','&#39;',$char['location']);

$bg = "background-image:url('images/townback/".str_replace(' ','_',strtolower($char['location'])).".jpg'); ";
include('header.htm');
if ($is_town)
{
?>

  <div class="row solid-back">
    <div class="col-sm-12">
      <form name="questForm" action="quests.php" method="post">
        <input type="hidden" name="accepted" value="0">
        <table class="table table-condensed table-responsive table-hover table-striped table-clear small">
          <tr>
            <th width="140">Quest</th>
            <th width="50" class='hidden-xs'>Type</th>
            <th width="90">Location</th>
            <th width="100" class='hidden-xs'>Reward</th>
            <th width="50" class='hidden-xs'>Avail</th>    
            <th width="75">Expire</th> 
            <th width="70">&nbsp;</th> 
          </tr>
        <?php   
          $result = mysql_query("SELECT * FROM Quests WHERE location='".$char[location]."' && done='0' && cat != '1' ORDER BY expire ASC");
          $y=0;
          while ($quest = mysql_fetch_array( $result ) )
          { 
            $qid = $quest[id]; 
            if (!$myquests[$qid][0])
            {
              $y++;
              $iclass='warning';
              if ($quest[align]==1) $iclass='primary';
              else if ($quest[align]==2) $iclass='danger';
              
              $qinfo[$qid] = "<div class='panel panel-".$iclass."' style='width: 200px;'><div class='panel-heading'><h3 class='panel-title'>".str_replace('-ap-','&#39;',$quest[name])."</h3></div><div class='panel-body solid-back' align='center'>";
              $qinfo[$qid] .= getQuestInfo($quest);
              $qinfo[$qid] .= "</div></div>";
              
              $goals = unserialize($quest[goals]);      
        ?>
          <tr>
            <td class="popcenter"><button type="button" class="btn btn-<?php echo $iclass; ?> btn-xs btn-block btn-wrap link-popover" data-toggle="popover" data-html="true" data-placement="bottom" data-content="<?php echo $qinfo[$qid];?>"><?php echo str_replace('-ap-','&#39;',$quest[name]); ?></button></td>
            <?php
              $qtype = $quest_type[$quest[type]];
            ?>
            <td class='hidden-xs' align='center'><?php echo $qtype; ?></td> 
            <?php
              if ($quest[num_avail] > 0)
              {
                $qavail=$quest[num_avail]-$quest[num_done];
              }
              else $qavail="-";
            ?> 
            <td align='center'>
            <?php
              if ($quest[type] == $quest_type_num["NPC"] || $quest[type] == $quest_type_num["Find"])
              {
                echo $goals[2];
              }
              else
              {
                echo $quest[location]; 
              }
            ?>
            </td>  
            <td class='hidden-xs' align='center'>
            <?php
              $qreward= unserialize($quest[reward]);
              if ($qreward[0]=="G")
              {  echo displayGold($qreward[1]); }
              elseif ($qreward[0] == "LI")
              { echo ucfirst($item_type[$qreward[1]]); }
              elseif ($qreward[0] == "I")
              {
                $item = explode("|",$qreward[1]);
                echo ucwords($item[1])." ".ucwords($item[0])." ".ucwords($item[2]);
              }
              elseif ($qreward[0] == "GP")
              {
                echo $qreward[1]."% Value";
              }
              elseif ($qreward[0] == "LG")
              {
                echo "Leveled Coin";
              }
              elseif ($qreward[0]=="GJ")
                { echo displayGold($qreward[1])." per Ji"; }
            ?>  
            </td>
            <td class='hidden-xs' align='center'><?php echo $qavail; ?></td>
            <td>
            <?php  
              if ($quest[expire] != -1)
              {
                $expire = ($quest[expire]*3600) - time();
                if ($expire < 3600) echo number_format($expire/60)." minutes";
                elseif ($expire < 86400) echo number_format($expire/3600)." hours";
                else echo number_format($expire/86400)." days";
              }
              else echo "&nbsp;";        
            ?>
            </td>
            <td align='center'>
            <?php
              $reqs=cparse($quest[reqs],0);
              $charip = unserialize($char[ip]); 
              $alts = getAlts($charip); 
              if (!$alts[str_replace(' ','_',$quest[offerer])])
              {
                if (check_quest_reqs($reqs,$char))
                {
                  $acceptLink = "javascript:acceptQuestForm(".$quest[id].");";
                  echo "<button name='accept' onclick='".$acceptLink."' class='btn btn-xs btn-block btn-success'>Accept</button>";
                }
                else echo "&nbsp;";
              }
            ?>
            </td>
          </tr>
        <?php
            }
          }
        ?>
        </table>
      </form>
    </div>
  </div>
<script type='text/javascript'>
  function acceptQuestForm(accept)
  {
    document.questForm.accepted.value= accept;
    document.questForm.submit();
  } 
</script>
<?php
} // isTown
} // !isDestroyed
else
{
  $message = "No quests available in the remains of ".$location[name];
  $bg = "background-image:url('images/townback/".str_replace(' ','_',strtolower($char['location'])).".jpg'); ";
  include('header.htm');
}
include('footer.htm');

?>