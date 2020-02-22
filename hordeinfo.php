<?php
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/charFuncs.php");
include_once("admin/locFuncs.php");
include_once('map/mapdata/coordinates.inc');

$wikilink = "Hordes";

if ($location_array[$char['location']][2]) $is_town=1;

$query='';
if ($is_town)
{
  $query = "target='".$char[location]."'";
}
else
{
  $query = "location='".$char[location]."'";
}



$result3 = mysql_query("SELECT * FROM Hordes WHERE ".$query." ORDER BY ends DESC LIMIT 1");
$myHorde = mysql_fetch_array( $result3 );

  $npc_info = unserialize($myHorde[npcs]);
  $hordemsg = "";
  for ($n=0; $n < count($npc_info); $n++)
  {
    if ($n==0) $hordemsg .= "A "; else $hordemsg.= " vs. A ";
    $hordemsg .= $horde_types[$npc_info[$n][0]]." of ".$npc_info[$n][0]."s";
  }

  $stime = intval((time()-$myHorde[starts]*3600)/60);
  $ago = 1;
  $atext = "Attack Time";
  
  // Horde not done
  if ($myHorde[done] == 0)
  {
    $atime = intval(($myHorde[ends]*3600-time())/60);
    $ago=0;
  }
  // Horde defeated
  elseif ($myHorde[defeated])
  {
    $atime = intval((time()-$myHorde[defeated])/60);  
    $atext = "Defeated";
  }
  // Horde ended
  elseif ($myHorde[ends]*3600 <= time())
  {
    $atime = intval((time()-$myHorde[ends]*3600)/60);
    $atext = "Attacked";
  }
  // We really shouldn't get here...
  else 
  {
    $atime = intval((time()-$myHorde[ends]*3600)/60);
  }
  
  $hstatus = "Defeated";
  if ($myHorde[done]== 0)
  {
    $hstatus = "Gathering";
  }
  elseif ($myHorde[done]== 3)
  {
    $hstatus = "Victorious";
  }
  
  $dchamp='';
  $wchamp='';
  $fchamp='';
  if ($myHorde[users])
  {
    $husers = unserialize($myHorde[users]);
    foreach ($husers as $uid => $udata)
    {
      $uhwins[$uid]=$udata[0];
      $uhdmg[$uid]=$udata[1];
      $uawins[$uid]=$udata[2];
      $uadmg[$uid]=$udata[3];
    }
    arsort($uhwins);
    arsort($uhdmg);
    arsort($uawins);
    arsort($uadmg);
    $whid=0;
    $dhid=0;
    $waid=0;
    $daid=0;

    foreach ($uhdmg as $uid => $dmg)
    {
      if ($dmg > 0)
      {
        if ($dhid == 0) $dhid = $uid;
        else
        {
          if ($dmg == $uhdmg[$dhid])
          {
            if ($uhwins[$uid] > $uhwins[$dhid]) $dhid = $uid;
          }
        }
      }
    }
    foreach ($uhwins as $uid => $wins)
    {
      if ($wins > 0)
      {
        if ($whid == 0) $whid = $uid;
        else
        {
          if ($wins == $uhwins[$whid])
          {
            if ($uhdmg[$uid] > $uhdmg[$whid]) $whid = $uid;
          }
        }
      }
    } 
    foreach ($uadmg as $uid => $dmg)
    {
      if ($dmg > 0)
      {
        if ($daid == 0) $daid = $uid;
        else
        {
          if ($dmg == $uadmg[$daid])
          {
            if ($uawins[$uid] > $uawins[$dhid]) $daid = $uid;
          }
        }
      }
    }
    foreach ($uawins as $uid => $wins)
    {
      if ($wins > 0)
      {
        if ($waid == 0) $waid = $uid;
        else
        {
          if ($wins == $uawins[$waid])
          {
            if ($uadmg[$uid] > $uadmg[$waid]) $waid = $uid;
          }
        }
      }
    }
  
    if ($dhid)
    {
      $dchamp[0] = mysql_fetch_array(mysql_query("SELECT id, name, lastname FROM Users WHERE id='".$dhid."'"));
    }
    if ($whid)
    {
      $wchamp[0] = mysql_fetch_array(mysql_query("SELECT id, name, lastname FROM Users WHERE id='".$whid."'"));
    }
    if ($daid)
    {
      $dchamp[1] = mysql_fetch_array(mysql_query("SELECT id, name, lastname FROM Users WHERE id='".$daid."'"));
    }
    if ($waid)
    {
      $wchamp[1] = mysql_fetch_array(mysql_query("SELECT id, name, lastname FROM Users WHERE id='".$waid."'"));
    }     
    if ($myHorde[finisher])
    {
      $fchamp[0] = mysql_fetch_array(mysql_query("SELECT id, name, lastname FROM Users WHERE id='".$myHorde[finisher]."'"));
    }
    if ($myHorde[afinisher])
    {
      $fchamp[1] = mysql_fetch_array(mysql_query("SELECT id, name, lastname FROM Users WHERE id='".$myHorde[afinisher]."'"));
    }
  }
$message = $hordemsg;  
include('header.htm');  
?>
  <div class="row solid-back">
    <div class='row'>
      <div class='col-sm-4'>
        <div class="panel panel-info">
          <div class="panel-heading">
            <h3 class="panel-title">General Info</h3>
          </div>
          <div class="panel-body abox">
            <table class='table table-responsive table-clear small'>
              <tr>
                <td align='right' width='50%'>Horde Status: </td>
                <td><?php echo $hstatus;?></td>
              </tr>
              <tr>
                <td align='right' width='50%'>Gathered in: </td>
                <td><?php echo str_replace('-ap-','&#39;',$myHorde[location]);?></td>
              </tr>
              <tr>
                <td align='right' width='50%'>Targeting: </td>
                <td><?php echo str_replace('-ap-','&#39;',$myHorde[target]);?></td>
              </tr>
              <tr>
                <td align='right' width='50%'>Start Time: </td>
                <td><?php echo displayTime($stime,$ago,1);?></td>
              </tr>
              <tr>
                <td align='right' width='50%'><?php echo $atext;?>: </td>
                <td><?php echo displayTime($atime,$ago,1);?></td>
              </tr>
            </table>
          </div>
        </div>
      </div>
      <div class='col-sm-4'>
        <div class="panel panel-danger">
          <div class="panel-heading">
            <h3 class="panel-title">Horde Stats</h3>
          </div>
          <div class="panel-body abox">
            <table class='table table-responsive table-clear small'>
              <tr>
                <td align='right' width='50%'>Type: </td>
                <td><?php echo $npc_info[0][0];?></td>
              </tr>
              <tr>
                <td align='right' width='50%'>Health: </td>
                <td><?php echo $npc_info[0][1];?></td>
              </tr>
              <tr>
                <td align='right' width='50%'>Most Damage: </td>
                <td>
                  <?php 
                    if ($dchamp[0]) { echo $dchamp[0][name]." ".$dchamp[0][lastname]." (".$uhdmg[$dhid].")"; }
                    else { echo "No one"; }
                  ?>
                </td>
              </tr>
              <tr>
                <td align='right' width='50%'>Most Wins: </td>
                <td>
                  <?php 
                    if ($wchamp[0]) { echo $wchamp[0][name]." ".$wchamp[0][lastname]." (".$uhwins[$whid].")"; }
                    else { echo "No one"; }
                  ?>
                </td>
              </tr>
              <tr>
                <td align='right' width='50%'>Final Blow: </td>
                <td>
                  <?php 
                    if ($fchamp[0]) { echo $fchamp[0][name]." ".$fchamp[0][lastname]; }
                    else { echo "No one"; }
                  ?>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
      <div class='col-sm-4'>
        <div class="panel panel-primary">
          <div class="panel-heading">
            <h3 class="panel-title">Army Stats</h3>
          </div>
          <div class="panel-body abox">
            <table class='table table-responsive table-clear small'>
              <tr>
                <td align='right' width='50%'>Type: </td>
                <td><?php echo $npc_info[1][0];?></td>
              </tr>   
              <tr>
                <td align='right' width='50%'>Health: </td>
                <td><?php echo $npc_info[1][1];?></td>
              </tr>
              <tr>
                <td align='right' width='50%'>Most Damage: </td>
                <td>
                  <?php 
                    if ($dchamp[1]) { echo $dchamp[1][name]." ".$dchamp[1][lastname]." (".$uadmg[$daid].")"; }
                    else { echo "No one"; }
                  ?>
                </td>
              </tr>
              <tr>
                <td align='right' width='50%'>Most Wins: </td>
                <td>
                  <?php 
                    if ($wchamp[1]) { echo $wchamp[1][name]." ".$wchamp[1][lastname]." (".$uawins[$waid].")"; }
                    else { echo "No one"; }
                  ?>
                </td>
              </tr>
              <tr>
                <td align='right' width='50%'>Final Blow: </td>
                <td>
                  <?php 
                    if ($fchamp[1]) { echo $fchamp[1][name]." ".$fchamp[1][lastname]; }
                    else { echo "No one"; }
                  ?>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php
include('footer.htm');
?>