<?php
// establish a connection with the database
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/charFuncs.php");
include_once("admin/duelFuncs.php");
include_once("admin/locFuncs.php");
include_once("admin/equipped.php"); 

$fP=mysql_real_escape_string($_POST[fp]);
$wP=mysql_real_escape_string($_POST[wp]);
$sP=mysql_real_escape_string($_POST[sp]);
$aP=mysql_real_escape_string($_POST[ap]);
$eP=mysql_real_escape_string($_POST[ep]);

$tskills = $stomach_bonuses1." ".$stamina_effect1." ".$clan_bonus1." ".$skill_bonuses1." ".$clan_building_bonuses;
$wid=0;
$wEq=0;
$a = "";
$b = "";
$c = "";
$d = "";
$e = "";
$currw = "";
$a = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus='1' AND type<15"));
$b = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus='2' AND type<15"));
$c = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus='3' AND type<15"));
$d = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus='4' AND type<15"));
$e = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND istatus='5' AND type<15"));
$currw = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE owner='$char[id]' AND type='8'"));
$estats = getstats($a,$b,$c,$d,$e,$tskills);
$tskills = $tskills." ".$estats[0];
$invWeave=$currw[base];
if ($currw[istatus] > 0) $wEq = 1;

  $ms = cparse($tskills);
  if ($ms[fP]== "") $ms[fP]=0;
  if ($ms[wP]== "") $ms[wP]=0;
  if ($ms[sP]== "") $ms[sP]=0;
  if ($ms[aP]== "") $ms[aP]=0;
  if ($ms[eP]== "") $ms[eP]=0;
  
  if ($fP != "")
  {
    $tskills = $tskills." fP".$fP." wP".$wP." sP".$sP." aP".$aP." eP".$eP." wC-".($eP+$aP);
  }
  else
  {
    $fP=0;
    $wP=0;
    $sP=0;
    $aP=0;
    $eP=0;
  }

  $myWeaves = getWeaves($item_list,$item_base,$tskills);

include ('header.htm');
?>

<script type="text/javascript">
  var skills = '<?php echo $tskills; ?>';
<?php
  echo "  var weaveinfo = new Array();";
  for ($w=0; $w < count($myWeaves); $w++)
  {
      $weaveinfo[$w] = "<div class='panel panel-primary'><div class='panel-heading'><h3 class='panel-title'>".ucwords($myWeaves[$w][0])."</h3></div><div class='panel-body abox' align='center'><img class='img-responsive hidden-xs img-optional-nodisplay' border='0' bordercolor='black' src='items/".str_replace(' ','',$myWeaves[$w][0]).".gif'/>";
      $weaveinfo[$w] .= itm_info(cparse(weaveStats($myWeaves[$w][0],$tskills)));
      $weaveinfo[$w] .= "</div></div>";  
      echo "  weaveinfo[".$w."] = \"".$weaveinfo[$w]."\";\n";
  }
?> 
function weaveSwapinfo(myitm)
{
  document.getElementById('weavestats').innerHTML=weaveinfo[myitm];
}
</script>
  <div class='row solid-back'>
    <div class='col-sm-4 col-sm-push-8' align='center'>
      <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title">Weaves Calculator</h3>
        </div>
        <div class="panel-body abox" id='currstats'>    
          <form name="itemForm" action="weaveStats.php" method="post">
          <?php
            echo "Fire: ".$ms[fP]." + <input type='text' class='gos-form' name='fp' value='".$fP."' size='2'/><br/>";
            echo "Water: ".$ms[wP]." + <input type='text' class='gos-form' name='wp' value='".$wP."' size='2'/><br/>";
            echo "Spirit: ".$ms[sP]." + <input type='text' class='gos-form' name='sp' value='".$sP."' size='2'/><br/>";
            echo "Air: ".$ms[aP]." + <input type='text' class='gos-form' name='ap' value='".$aP."' size='2'/><br/>";
            echo "Earth: ".$ms[eP]." + <input type='text' class='gos-form' name='ep' value='".$eP."' size='2'/><br/>";
            echo "<input type='submit' value='Update' class='btn btn-sm btn-primary btn-block'>";
          ?>
          </form>                            
        </div>
      </div>

      <form name="weaveForm" action="items.php" method="post">
        <div id='weavestats' width='95%'>
          <?php echo "<center><font class='littletext'>Click on a Weave to display current stats!";?>
        </div>
      </form>
    </div>  
    <div class='col-sm-8 col-sm-pull-4' align='center'>
        <table class='table table-condensed table-striped table-clear small'>
            <tr>
              <th>Weave</th>
              <th>Pts</th>
              <th>Fire</th>
              <th>Water</th>
              <th>Spirit</th>
              <th>Air</th>
              <th>Earth</th>
            </tr>
            <?php
              for ($w=0; $w < count($myWeaves); $w++)
              {
                $ptscost= lvl_req($myWeaves[$w][1],getTypeMod($tskills,8));
                $iclass='btn-primary';
                if ($ptscost > $char['equip_pts']/2) $iclass = "btn-danger";
                if ($invWeave==$myWeaves[$w][0]) $iclass='btn-info';
                echo "<tr><td><a class='btn btn-xs btn-block ".$iclass."' href=\"javascript:weaveSwapinfo('".$w."')\">".ucwords($myWeaves[$w][0])."</a></td>";
                echo "<td align='center'>".$ptscost."</td>";
                echo "<td align='center'>".itm_info(cparse($myWeaves[$w][2],0))."</td>";
                echo "<td align='center'>".itm_info(cparse($myWeaves[$w][3],0))."</td>";
                echo "<td align='center'>".itm_info(cparse($myWeaves[$w][4],0))."</td>";
                echo "<td align='center'>".itm_info(cparse($myWeaves[$w][5],0))."</td>";
                echo "<td align='center'>".itm_info(cparse($myWeaves[$w][6],0))."</td>";
              }
            ?>
        </table>
    </div>
  </div>
<?php
include('footer.htm');
?>