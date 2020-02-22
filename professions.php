<?php 
include("admin/jobFuncs.php");
// establish a connection with the database
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/locFuncs.php");
include_once("admin/itemFuncs.php");
$jobs = unserialize($char[jobs]);
function displayProInfo($pro, $plvl, $name)
{
  $prodata = "binfos[".$pro."] = \"<div class='panel panel-success' style='width: 100%;'><div class='panel-heading'><h3 class='panel-title'>".$name." Bonuses</h3></div><div class='panel-body abox' align='center'><p>".itm_info(cparse(getJobBonuses($pro, $plvl)))."</p><p><i>Next Level:<br/>".itm_info(cparse(getJobBonuses($pro, $plvl+1)))."</p></div></div>\";";
  return $prodata;
}

$wikilink = "Professions";
$message=mysql_real_escape_string($_GET['message']);
$show=intval(mysql_real_escape_string($_GET['show']));
$sell=intval(mysql_real_escape_string($_GET['sell']));
$proup=mysql_real_escape_string($_POST['proup']);
$tab = mysql_real_escape_string($_GET['tab']);
$col='#090909';

$stats = mysql_fetch_array(mysql_query("SELECT * FROM Users_stats WHERE id='$char[id]'"));

// See if we have any Seals broken...
$cityRumors = mysql_fetch_array(mysql_query("SELECT * FROM messages WHERE id='50000'"));
$brokenSeals = 0;
if ($cityRumors[checktime]> 0) $brokenSeals = 1;

mysql_query("UPDATE Users SET newprof=0 WHERE id=$id");

if ($proup != '' && $proup != '-1')
{
  if ($char[propoints]>0)
  {
    if ( $jobs[$proup] < 9)
    {
      $jobs[$proup]++;
      $char[propoints]--;
      $stats[prof_pts_used]++;
      $numj=0;
      for ($j=0; $j<13; $j++)
      {
        if ($jobs[$j]) $numj++;
      }
      if ($stats[num_profs] < $numj) $stats[num_profs] = $numj;
      if ($stats[highest_prof] < $jobs[$proup]) $stats[highest_prof] = $jobs[$proup];
      $message = "Congratulations! You are now a level ".$jobs[$proup]." ".$job_names[$proup]."!";
      $newjobs = serialize($jobs);
      mysql_query("UPDATE Users SET jobs='".$newjobs."', propoints=".$char[propoints]." WHERE id='".$char[id]."'");
      mysql_query("UPDATE Users_stats SET prof_pts_used='".$stats[prof_pts_used]."', highest_prof=".$stats[highest_prof].", num_profs='".$stats[num_profs]."' WHERE id='".$char[id]."'");
    }
    else
      $message = "That job is already maxed out. Why not try something else?";
  }
  else 
    $message = "You don't have any points left to level up your professions!";
}

if ($sell != "" && $sell > 0)
{
  if (!$brokenSeals)
  {
    $sbiz = mysql_fetch_array( mysql_query("SELECT * FROM Profs WHERE id='$sell'") );
    if ($sbiz[id])
    {
      if ($sbiz[owner] == $char[id])
      {
        $gold = floor($sbiz[value]/2);
        $char[gold] += $gold;
        $message = "Business sold for ".displayGold($gold);
        mysql_query("UPDATE Users SET gold='".$char[gold]."' WHERE id='".$char[id]."'");
        $query = "DELETE FROM Profs WHERE id='$sell'";
        $result5 = mysql_query($query, $db);  
      }
      else $message = "You cannot sell a business you do not own!";
    }
    else $message= "Invalid business selected to be sold!";
  }
  else $message= "The Last Battle nears! No one wants to buy your business you fool!";
}

if (!$tab) $tab=1;
include('header.htm');
?>
<script type="text/javascript">
var binfos = new Array(13);
<?php 
for ($i = 0; $i < 13; $i++)
{
  echo displayProInfo($i, $jobs[$i],$job_names[$i]);
}
?>

function swapinfo(pro)
{
  document.getElementById('bonuses').innerHTML=binfos[pro];
}

  function ProUp (num)
  {
    document.proform.proup.value=num;
    document.proform.submit();
  }
</script>

<!-- CONTENT -->
  <div class="row solid-back">
    <div class="col-sm-12">
      <div id="content">
        <ul id="proftabs" class="nav nav-tabs" data-tabs="tabs">
          <li <?php if ($tab == 1) echo "class='active'";?>><a href="#prof_tab" data-toggle="tab">Professions</a></li>         
          <li <?php if ($tab == 2) echo "class='active'";?>><a href="#biz_tab" data-toggle="tab">My Businesses</a></li>
          <li <?php if ($tab == 3) echo "class='active'";?>><a href="#estate_tab" data-toggle="tab">My Estates</a></li>         
        </ul>

        <div class="tab-content">
          <div class="tab-pane <?php if ($tab == 1) echo 'active';?>" id="prof_tab"> 
            <div class='row'>
              <p><b>Profession Points: <?php echo $char[propoints];?></b></p>
            </div>
            <div class='row'>
              <div class='col-sm-6 col-sm-push-6'>
                <div id='bonuses'>
                  <?php echo "Click on Profession name for bonus information!";?>
                </div>
              </div>            
              <div class='col-sm-6 col-sm-pull-6'>
                <form name='proform' action='professions.php' method='post'>
                  <table class='table table-repsonsive table-hover table-clear small'>
                    <tr>
                      <th>Profession</th>
                      <th>Level</th> 
                      <th width='100'>&nbsp;</th>    
                    </tr>
                  <?php
                    for ($y=0; $y<13; $y++)
                    {
                  ?>
                    <tr>
                      <td><a onClick='javascript:swapinfo(<?php echo $y;?>)' class='btn btn-xs btn-block btn-wrap btn-warning'><?php echo $job_names[$y];?></a></td>
                      <td align='center'><?php echo $jobs[$y];?></td>
                      <td>
                      <?php
                        if ($jobs[$y]<9) 
                        { 
                      ?>
                        <input type='button' name='lvlup<?php echo $y;?>' class='btn btn-xs btn-primary btn-wrap' value='Level Up' onClick='javascript:ProUp(<?php echo $y;?>)'/>
                      <?php
                        } 
                      ?>
                      </td>
                    </tr>
                  <?php
                    }
                  ?>
                  </table>
                  <input type='hidden' name='proup' value='-1'/>
                </form>
              </div>
            </div>
          </div>
          <div class="tab-pane <?php if ($tab == 2) echo 'active';?>" id="biz_tab">     
            <table class='table table-repsonsive table-hover table-clear small'>
              <tr>
                <th>Type</th>
                <th>Location</th>
                <th class='hidden-xs'>Value</th>
                <th>Inactive</th>
                <th>Active</th>
                <th>Done</th>
                <th>Action</th>
              </tr>
              <?php
                $query = "SELECT * FROM Profs WHERE owner='$id'";
                $result = mysql_query($query);  
                while ($bq = mysql_fetch_array( $result ) )
                {
                  $bstatus = unserialize($bq['status']);
                  $bupgrades = unserialize($bq['upgrades']);
                  $bsubs = array(0,0,0);
                  for ($i=0; $i<$bupgrades[0]; $i++)
                  {
                    if ($bstatus[$i][0]==1)
                    {
                      $timeleft = ($bstatus[$i][2][1] - time());
                      if ($timeleft <0 && $bq[type] != 0)
                      {
                        $bstatus[$i][0]=2;
                        if ($bq[type]<7)
                        {
                          $bstatus[$i][4]=5;
                          $qualbonus = $bupgrades[1][1]*5 + $bupgrades[1][2]*3;
                          $qualnum=$jobs[$bq[type]]*5+$qualbonus+rand(1,50);
                          $bstatus[$i][5]=floor($qualnum/25);
                        }
                        $sstatus = serialize($bstatus);
                        mysql_query("UPDATE Profs SET status='".$sstatus."' WHERE id='".$bq[id]."'");          
                      }
                    }
                    $bsubs[$bstatus[$i][0]]++;
                  }
                  echo "<tr>";
                  echo "<td align='center'>".$job_biz_names[$bq[type]][0]."</td>";
                  echo "<td align='center'>".str_replace('-ap-','&#39;',$bq[location])."</td>";
                  echo "<td align='center' class='hidden-xs'>".displayGold($bq[value])."</td>";
                  echo "<td align='center'>".$bsubs[0]."</td>";
                  echo "<td align='center'>".$bsubs[1]."</td>";
                  echo "<td align='center'>".$bsubs[2]."</td>";
                  if (!$brokenSeals)
                  {
                    echo "<td align='center'><a data-href='professions.php?sell=".($bq[id])."' data-toggle='confirmation' data-placement='top' title='Are you sure you want to sell this business? (Once it\'s sold, there is no getting it back!)' class='btn btn-success btn-sm btn-wrap'>Sell</a></td>";
                  }
                  else
                  {
                    echo "<td></td>";
                  }
                  echo "</tr>";
                }
              ?>
            </table>
          </div>
          <div class="tab-pane <?php if ($tab == 3) echo 'active';?>" id="estate_tab">  
            <table class='table table-repsonsive table-hover table-clear small'>
              <tr>
                <th>Location</th>
                <th>Grid</th>
                <th>Type</th>
                <th>Value</th>
                <th>Upgrades</th>
                <th>Items</th>
                <th>Name</th>
              </tr>
              <?php
                $query = "SELECT * FROM Estates WHERE owner='$id'";
                $result = mysql_query($query);  
                while ($est = mysql_fetch_array( $result ) )
                {
                  $estinv=mysql_num_rows(mysql_query("SELECT * FROM Items WHERE owner='".(20000+$est[id])."' AND type<15"));
                  $eups = unserialize($est[upgrades]);
                  $maxestinv = 3+3*$eups[2];
                  echo "<tr>";
                  echo "<td>".str_replace('-ap-','&#39;',$est[location])."</td>";
                  echo "<td>(".($est[row]+1).",".($est[col]+1).")</td>";
                  echo "<td>".$estate_lvls[$est[level]]."</td>";
                  echo "<td>".displayGold($est[value])."</td>";
                  echo "<td>".$est[num_ups]."</td>";
                  echo "<td>".$estinv." / ".$maxestinv."</td>";
                  echo "<td>".$est[name]."</td>";
                  echo "</tr>";
                }
              ?>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>  

<?php
include('footer.htm');
?>