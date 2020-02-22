<?php

/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/locFuncs.php");
include_once("admin/charFuncs.php");
include_once("admin/duelFuncs.php");
include_once("admin/questFuncs.php");
include_once("admin/skills.php");
include_once('map/mapdata/coordinates.inc');
if ($location_array[$char['location']][2]) $is_town=1;

// Find all equipped items
include_once("admin/equipped.php");
$loc=$char['location'];

if ($location_array[$char['location']][2]) $is_town=1;
else $is_town=0;
$no_query=1;

$result3 = mysql_query("SELECT * FROM Hordes WHERE done='0' AND location='$char[location]'");
$numhorde = mysql_num_rows($result3);

$message=mysql_real_escape_string($_REQUEST['message']);
$myRow=mysql_real_escape_string($_REQUEST['row']);
$myCol=mysql_real_escape_string($_REQUEST['col']);
$myeup=mysql_real_escape_string($_POST['eup']);
$upsupport=mysql_real_escape_string($_POST['upsupport']); 
$uptrade=mysql_real_escape_string($_POST['uptrade']);
$etrade=mysql_real_escape_string($_POST['etrade']); 
$action = mysql_real_escape_string($_POST['action']);
$ename = mysql_real_escape_string($_POST['ename']);
$sortBy=mysql_real_escape_string($_POST['sort']);
$accepted= mysql_real_escape_string($_POST[accepted]);
$atype= mysql_real_escape_string($_POST[atype]);
$tab = mysql_real_escape_string($_GET['tab']);
if ($sortBy != "") {  mysql_query("UPDATE Users SET sort_estate='".$sortBy."' WHERE id=$id"); $char[sort_estate]= $sortBy;}
$invSort = sortItems($char[sort_estate]);

$loc_name = $char['location'];
$wikilink = "Estates";

$myquests= unserialize($char[quests]);

// GET SURROUNDING AREAS
$surrounding_area = $map_data[$loc];

$myEstate= mysql_fetch_array(mysql_query("SELECT * FROM Estates WHERE owner='$id' AND location='$loc_name'"));

if ($ename)
{
  mysql_query("UPDATE Estates SET name='$ename' WHERE id='$myEstate[id]'");
}

if ($myEstate[good] == '')
{
  mysql_query("UPDATE Estates SET good='0' WHERE id='$myEstate[id]'");
}

// Find all local estates
for ($x=0; $x<5; $x++)
{
  for ($y=0; $y<5; $y++)
  {
    $loc_estates[$x][$y][0]=0;
  }
}

$myNumEstates =  mysql_num_rows( mysql_query("SELECT * FROM Estates WHERE owner='$id'"));

$tot_loc_estates=0;
$myLocEstates=0;
$result = mysql_query("SELECT * FROM Estates WHERE location='$loc'");
while ($testate = mysql_fetch_array( $result ) )
{
  $loc_estates[$testate[row]][$testate[col]][0]++;
  if ($testate[name] != "") $loc_estates[$testate[row]][$testate[col]][1] = $testate[name];
  $tot_loc_estates++;
  if ($testate[owner]==$char[id]) $myLocEstates++;
}

$equery = mysql_query("SELECT * FROM Estates WHERE owner='$id' ORDER BY id");
$myEstate= array();
$enum =0;
$x=0;
while ($tmpEstate=mysql_fetch_array($equery))
{
  if ($tmpEstate[location] == $loc_name) 
  {
    $myEstate= $tmpEstate;
    $enum = ++$x;
  }
  else if ($tmpEstate[level] < 7)
  {
    $x++;
  }
}

// Calculate max per cell
$maxPerCell=1;

// Check if player can buy an estate.
$canbuy=0;

if ($myNumEstates < floor(($char[level]+10)/20))
{
  if ($myLocEstates == 0)
  {
    if ($tot_loc_estates < $maxPerCell*25)
    {
      if ($numhorde==0)
      {
        $canbuy=1;
      }
      else
      {
        $nobuy = "You cannot buy a new estate here until the horde gathering nearby is defeated!";      
      }
    }
    else
    {
      $nobuy = "You cannot buy a new estate here because there are no plots currently available. Try building elsewhere.";
    }
  }
  else
  {
    $nobuy = "You cannot buy a new estate here because you already own one here. Try building elsewhere.";
  }
}
else
{
  if ($myNumEstates) $another= "another "; else $another="an";
  $nobuy= "You cannot buy a new estate because you aren't high enough level to own $another estate yet.";
}

$estateCost=10000;

if ($myRow != '' && $myCol != '')
{
  if ($canbuy) 
  {
    if ( $loc_estates[$myRow][$myCol][0]< $maxPerCell)
    {
      if ($char[gold] >= $estateCost)
      {
        $char[gold] -= $estateCost;
        $loc_estates[$myRow][$myCol]++;
        $tot_loc_estates++;
        $myLocEstates++;  
        $myNumEstates++;
        $upLvls=array(0,0,0,0,0,0,0,0,0,0);
        $eups = serialize($upLvls);
        mysql_query("UPDATE Users SET gold='".$char[gold]."' WHERE id='".$char[id]."'");
        $sql = "INSERT INTO Estates (owner,level,location,   row,     col,     value,  num_ups,supporting,upgrades)
                             VALUES ('$id','0',  '$loc_name','$myRow','$myCol','10000','0',    '',        '$eups')";
        $resultt = mysql_query($sql, $db);    
        $message= "Estate built!";
      }
      else
      {
        $message = "You do not have enough coin to build an estate right now!";
      }
    }
    else
    {
      $message = "There are no available plots in this cell. Select a different one.";
    }
  }
  else
  {
    $message = "You do not meet the criteria to build an estate here!";
  }
}

// ACTIONS
if ($action==1) // withdraw
{
  $eid=20000+$myEstate[id];
  $listsize=0;
  $itmlist='';
  $iresult=mysql_query("SELECT * FROM Items WHERE owner='$eid' AND type<15 ".$invSort);
  while ($qitem = mysql_fetch_array($iresult))
  {
    $itmlist[$listsize++] = $qitem;
  }
  $num_itmso=0;
  $iresult=mysql_query("SELECT * FROM Items WHERE owner='$id' AND type<15 ".$invSort);
  while ($qitem = mysql_fetch_array($iresult))
  {
    $itmlistchar[$num_itmso++] = $qitem;
  }
  $good = 1;
  $z=0;
  $x=0;
  $q=0;
  $dlist = "";
  while ($x < $listsize)
  {
    $tmpItm =$_POST[$x];
    if (isset($tmpItm))
    {
      $tmpItm =  mysql_real_escape_string($tmpItm);
      $sitem=mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE id='$tmpItm'"));

      if ($num_itmso+$q<$inv_max) 
      {
        $dlist[$q++]= $sitem;
      }
      else 
      {
        $message = "You don't have enough room in your inventory!";
        $good = 0;
      }        
    }
    $x++;
  }

  if ($good && $q > 0)
  {
    for ($x=0; $x<$q; $x++)
    {
      $result = mysql_query("UPDATE Items SET owner='".$char[id]."', last_moved='".time()."' WHERE id='".$dlist[$x][id]."'");
    }
    $s = "";
    if ($q > 1) $s ="s";
    $message = "Item$s placed in inventory.";
  }
  else if ($q == 0) 
  {
    $message = "You did not select any items!";
  }
}

if ($myeup != '' && $myeup != '-1')
{
  if ($myEstate[id])
  {
    $eups=unserialize($myEstate[upgrades]);
    $ucost=2500*($myEstate[num_ups]+1)*($eups[$myeup]+1)*(($enum+1)/2);
    if (($estate_up_lvls[$eups[$myeup]] <= $myEstate[level] ) && ($myeup !=9 || $myEstate[level] >= 2))
    {
      if ($char[gold] >= $ucost)
      {
        $uname= $estate_ups[$myeup][0];
        if ($myeup==9) $uname = $estate_unq_wild[$wild_types[$loc_name]];
        $eups[$myeup]++;
        $char[gold] -= $ucost;
        $myEstate[value] += $ucost/2;
        $myEstate[num_ups]++;
        if ($myEstate[num_ups] >= $estate_lvls_req[$myEstate[level]]) $myEstate[level]++;
        $myEstate[upgrades] = serialize($eups);
        $result = mysql_query("UPDATE Estates SET value='$myEstate[value]', level='$myEstate[level]', num_ups='$myEstate[num_ups]', upgrades='$myEstate[upgrades]' WHERE id='$myEstate[id]'");
        $result = mysql_query("UPDATE Users SET gold='$char[gold]' WHERE id='$char[id]'");
        $message = $uname." Upgraded!";
        // update all estate support
        updateEstateSupport($estate_ji);
      }
      else { $message = "You can't afford that!";}
    }
    else { $message = "Your Estate isn't a high enough level to buy that upgrade!";}
  }
  else { $message = "You can't do that!";}
}

if ($upsupport)
{
  if ($myEstate[id])
  {
    $supchange=0;
    $supporting = unserialize($myEstate[supporting]);

    for ($x=0; $x<4; $x++) 
    {
      $varname="support".$x;
      if ($supporting[$surrounding_area[$x]] != $_POST[$varname])
      {
        $supchange=1;
        $supporting[$surrounding_area[$x]] = mysql_real_escape_string($_POST[$varname]);
      }
    }
    if ($supchange)
    {
      $myEstate[supporting]= serialize($supporting);
      mysql_query("UPDATE Estates SET supporting='$myEstate[supporting]' WHERE id='$myEstate[id]'");
      $message = "Support updated";
      
      // update all estate support
      updateEstateSupport($estate_ji);
    }
    else
    {
      $message = "No change in support.";
    }
  }
  else { $message = "You can't do that!";}
}

if ($uptrade)
{
  if ($myEstate[id])
  {
    if ($myEstate[good] > 0)
    {
    $myEstate[trade]=$etrade;
    $wildGood = $estate_unq_prod[$wild_types[$myEstate[location]]];

    if ($etrade == 0) 
    {
      $message = "You must select someone to trade with.";
    }
    else if ( $etrade < 100)
    {
      $tloc = mysql_fetch_array(mysql_query("SELECT * FROM Locations WHERE id='$etrade'"));

      $rgold = round(10*1000/$tloc[$wildGood]);
      if ($rgold>100) $rgold=100;
      $rgold=$rgold*$myEstate[good];
      $tloc[$wildGood] += $myEstate[good];

      mysql_query("UPDATE Locations SET ".$wildGood."='".$tloc[$wildGood]."' WHERE id='".$tloc[id]."'");
      mysql_query("UPDATE Users SET gold=gold+".$rgold." WHERE id='$myEstate[owner]'");
      mysql_query("UPDATE Estates SET value=value+".$rgold.", trade='$myEstate[trade]', good=0 WHERE id='$myEstate[id]'");
      $message = "Goods traded for ".displayGold($rgold);
    }
    else
    {
      $sid = $etrade-100;
      $society = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE id='".$sid."'"));
      if ($society[name] == $char[society])
      {
        $sgoods = unserialize($society[goods]);
        $sgoods[$wild_types[$myEstate[location]]] += $myEstate[good];

        mysql_query("UPDATE Soc SET goods='".serialize($sgoods)."' WHERE id='$sid'");
        mysql_query("UPDATE Estates SET good=0 WHERE id='$myEstate[id]'");
        $message = "Goods sent to ". $society[name];
      }
      else
      {
        $message = "You cannot send goods to a clan you're not in.";
      }
    }


    }
    else { $message = "You don't have any goods to trade!"; }
  }
  else { $message = "You can't do that!";}
}

if ($accepted != '')
{
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

$eid=20000+$myEstate[id];
$listsize=0;
$itmlist='';
$iresult=mysql_query("SELECT * FROM Items WHERE owner='$eid' AND type<15 ".$invSort);
while ($qitem = mysql_fetch_array($iresult))
{
  $itmlist[$listsize++] = $qitem;
}

$bonuses1 = $stomach_bonuses1." ".$stamina_effect1." ".$clan_bonus1." ".$skill_bonuses1;
$skills = $bonuses1;

if ($is_town) { $message = "There are no estates in cities!"; }
if (!$tab) $tab = 1;
include('header.htm');

if (!$is_town)
{
?>
<script type="text/javascript">

  var rowIndex = 0;
  var cellIndex = 0;
  var nTable = "";
  var nRows = 0;
  var nCells = 0;

  function showCoords(r,c)
  {
    rowIndex = r;
    cellIndex = c;
    <?php
      if ($canbuy)
      {
    ?>    
        submitFormBuildEstate(rowIndex,cellIndex);
    <?php
      }
    ?>
     
  }

  function getCoords(currCell)
  {
    var i=0;
    var n=0;
    for (i=0; i<nRows; i++)
    {
       for (n=0; n<nCells; n++)
       {
         if (nTable.rows[i].cells[n] == currCell)
         {
           showCoords(i,n);
         }
       }
    }
  }

  function mapTable()
  {
    nTable = document.getElementById('mapTable');
    nRows = nTable.rows.length;
    nCells = nTable.rows[0].cells.length;
    var i=0;
    var n=0;
    for (i=0; i<nRows; i++)
    {
       for (n=0; n<nCells; n++)
       {
         nTable.rows[i].cells[n].onclick = function()
         {
           getCoords(this);           
         }
         nTable.rows[i].cells[n].onmouseover = function()
         {
           this.style.cursor = 'pointer';
           this.style.border='2px ridge';
         }
         nTable.rows[i].cells[n].onmouseout = function()
         {
           this.style.border='none';
         }
       }
    }
  }

  function submitFormBuildEstate(r,c)
  {
    document.buildForm.row.value = r;
    document.buildForm.col.value = c;
    document.buildForm.submit();
  }

  function buyEstateUp (num)
  {
    document.eupForm.eup.value=num;
    document.eupForm.submit();
  }
  
  function changeUpSupport(ups)
  {
    document.supportForm.upsupport.value = ups;
    document.supportForm.submit();
  }

  function changeUpTrade(upt)
  {
    document.supportForm.uptrade.value = upt;
    document.supportForm.submit();
  }
  
  var invinfo = new Array();
  var inv = new Array();
  var skills = '<?php echo $skills; ?>';
  var myPts = <?php echo $char['equip_pts'];?>;
<?php
  for ($i=0; $i<$listsize; ++$i)
  {
    echo "  inv[".$i."] = new Array('".str_replace(" ","_",$itmlist[$i][base])."','".str_replace(" ","_",$itmlist[$i][prefix])."','".str_replace(" ","_",$itmlist[$i][suffix])."','".str_replace(" ","_",$itmlist[$i][cond])."');";
    echo "  invinfo[".$i."] = \"<FIELDSET class=abox><LEGEND><b>".iname($itmlist[$i])."</b></LEGEND><center><br/><img border='0' bordercolor='black' src='items/".str_replace(' ','',$itmlist[$i][base]).".gif' class='img-optional-nodisplay><br/><br/>\" + ";
    echo "itm_info(cparse(iparse('".str_replace(" ","_",$itmlist[$i][base])."','".str_replace(" ","_",$itmlist[$i][prefix])."','".str_replace(" ","_",$itmlist[$i][suffix])."','".str_replace(" ","_",$itmlist[$i][cond])."')))+\"";
    echo "<br/><br/></FIELDSET>\";\n";
  }
?>

function swapinfo(myitm)
{
  document.getElementById('itemstats').innerHTML=invinfo[myitm];
}
    
var qinfo = new Array();

<?php
  if ($myEstate[level] >= 6)
  {    
    for ($x=0; $x<4; $x++) 
    {
      $location = mysql_fetch_array(mysql_query("SELECT * FROM Locations WHERE name='$surrounding_area[$x]'"));
      // setup bonuses for the current location
      include("admin/locBonuses.php");

      $result = mysql_query("SELECT * FROM Quests WHERE location='".$location[name]."' AND done='0'");
      while ($quest = mysql_fetch_array( $result ) )
      {
        $qid = $quest[id];
        
        if (!$myquests[$qid][0])
        {
          echo str_replace('-ap-','&#39;',"qinfo[$qid] = \"<FIELDSET class=abox><LEGEND><b>".$quest[name]."</b></LEGEND><center><br/><br/>\" + ");
          echo str_replace('-ap-','&#39;',"\"".getQuestInfo($quest, $goodevil)."\" + ");
          echo str_replace('-ap-','&#39;',"\"<br/><br/></FIELDSET>\";\n");
        }
      }
    }
  }
?>
function swapQinfo(myQuest)
{
  document.getElementById('queststats').innerHTML=qinfo[myQuest];
}
</script>


<?php
$myEstate= mysql_fetch_array(mysql_query("SELECT * FROM Estates WHERE owner='$id' AND location='$loc_name'"));
?>
  <div class="row solid-back">
    <div class="col-sm-12">
      <div id="content">
        <ul id="esttabs" class="nav nav-tabs" data-tabs="tabs">
        <?php
          if ($myEstate[id])
          {
        ?>
          <li <?php if ($tab == 1) echo "class='active'";?>><a href="#main_tab" data-toggle="tab">My Estate</a></li>         
          <li <?php if ($tab == 2) echo "class='active'";?>><a href="#inv_tab" data-toggle="tab">Estate Inventory</a></li>
          <?php
            if ($myEstate[level] >= 6)
            {
          ?>
          <li <?php if ($tab == 3) echo "class='active'";?>><a href="#quest_tab" data-toggle="tab">Nearby Quests</a></li>        
          <?php
            }
          ?>
          <li <?php if ($tab == 4) echo "class='active'";?>><a href="#city_tab" data-toggle="tab">City Relations</a></li>
        <?php
          }
        ?>  
          <li <?php if ($tab == 5) echo "class='active'";?>><a href="#local_tab" data-toggle="tab">Local</a></li>
        </ul>
<?php
if ($myEstate)
{
  $eimage= $wild_type_names[$wild_types[$loc_name]].($estate_lvls[$myEstate[level]]).".gif";
  
  if (!$myEstate[inv])
  {
    $myEstate[inv] = serialize(array());
    mysql_query("UPDATE Estates SET inv='$myEstate[inv]' WHERE id='$myEstate[id]'");
  }
  if (!$myEstate[supporting])
  {
    $myEstate[supporting] = serialize(array());
    mysql_query("UPDATE Estates SET supporting='$myEstate[supporting]' WHERE id='$myEstate[id]'");
  }  
  $eups=unserialize($myEstate[upgrades]);
  $maxUpLvl = $estate_max_upgrade[$myEstate[level]];
?>
        <div class="tab-content">
          <div class="tab-pane <?php if ($tab == 1) echo 'active';?>" id="main_tab">
            <div class='row'>
              <div class='col-sm-8 col-md-5 col-md-push-3'>
                <!-- ESTATE PIC -->
                <?php
                  if ($myEstate[name])
                  {
                    echo "<p class='h5'>Welcome to the ".$estate_lvls[$myEstate[level]]." of ".$myEstate[name]."!</p>";
                  }
                  else
                  {
                    echo "<p class='h5'>Welcome to your ".$estate_lvls[$myEstate[level]]."!</p>";
                  }
                  echo "<img src='images/Estates/".$eimage."' class='img-responsive img-rounded hidden-xs img-optional'/><br/>";
                ?>
              </div>
              <div class='col-sm-4 col-md-3 col-md-pull-5'>
                <div class="panel panel-info">
                  <div class="panel-heading">
                    <h3 class="panel-title">Estate Stats</h3>
                  </div>
                  <div class="panel-body abox">
                  <?php
                    $goodName = ucwords($estate_unq_prod[$wild_types[$char['location']]]);
                    echo "<table class='table table-responsive table-condensed table-striped table-clear small'>";
                    echo "<tr><td align='right'>Current Estate Level: </td><td>".($myEstate[level]+1)."</td></tr>";
                    echo "<tr><td align='right'>Number of Upgrades: </td><td>".$myEstate[num_ups]."</td></tr>";
                    echo "<tr><td align='right'>Max Upgrade Level: </td><td>".$maxUpLvl."</td></tr>";
                    echo "<tr><td align='right'>Current Estate Value: </td><td>".displayGold($myEstate[value])."</td></tr>";
                    echo "<tr><td align='right'>Support to Nearby Cities: </td><td>".$estate_ji[$myEstate[level]]." Ji</td></tr>";
                    echo "<tr><td align='right'>Stored ".$goodName.": </td><td>".$myEstate[good]." ".$goodName."</td></tr>";
 
                    if ($myEstate[level]>=6)
                    {
                      echo "<tr><td align='center' colspan='2'><form class='form-inline' name='eNameForm' action='estate.php' method='post'>";
                      echo "<div class='form-group'><label for='ename'>Name: </label>";
                      echo "<input type='text' class='form-control gos-form input-sm' name='ename' size='25' id='ename' value='".$myEstate[name]."' maxlength='20'/>";
                      echo "<input type='submit' name='submit' value='Set Name' class='btn btn-xs btn-success btn-block'/></div></form></td></tr>";
                    }
                    echo "</table>";
                  ?>
                  </div>
                </div>
              </div>
              <div class='col-sm-12 col-md-4'>
                <div class="panel panel-warning">
                  <div class="panel-heading">
                    <h3 class="panel-title">Estate Upgrades</h3>
                  </div>
                  <div class="panel-body abox">
                  <?php
                    if ($numhorde) echo "<p class='text-danger'>You cannot purchase upgrades until the horde gathering nearby is defeated!</p>";
                    $maxEstInv = 3+(3*$eups[2]);
                    echo "<table class='table table-responsive table-condensed table-striped table-clear small'>";
                    echo "<tr><td align='center'><b>Upgrade</b></td><td align='center'><b>Level</b></td><td align='center'><b>Cost</b></td><td align='center'>&nbsp;</td></tr>";
                    for ($i=0; $i<10; $i++)
                    {
                      $uname= $estate_ups[$i][0];
                      $ucost=2500*($myEstate[num_ups]+1)*($eups[$i]+1)*(($enum+1)/2);
                      $upbonus = getUpgradeBonus($eups, $estate_ups, $i);
                      $upbonus1 = getUpgradeBonus($eups, $estate_ups, $i,1);
                      
                      if ($i==9)
                      {
                        $uname = $estate_unq_wild[$wild_types[$loc_name]][0];
                        $upbonus = getUpgradeBonus($eups, $estate_unq_wild, $wild_types[$char['location']]);
                        $upbonus1 = getUpgradeBonus($eups, $estate_unq_wild, $wild_types[$char['location']],1);
                      }        
                      $etitle = str_replace("&#39;","\&#39;","Current Bonus:<br/>".itm_info(cparse($upbonus))."<br/><i>Next Level:<br/>".itm_info(cparse($upbonus1))."</i>");        
                      echo "<tr>";
                      
                      echo "<td align='center'>";
                      echo "<button type='button' class='btn btn-block btn-warning btn-xs btn-wrap' data-html='true' data-toggle='popover' data-placement='top' data-content='".$etitle."'>".$uname."</button>";
                      echo "</td>";
                      echo "<td align='center'>".$eups[$i]."</td>";
                      echo "<td align='center'>".displayGold($ucost)."</td>";
                      if ($numhorde==0) echo "<td align='center'><input type='button' class='btn btn-xs btn-success' value='Upgrade' onclick='javascript:buyEstateUp(".$i.")'/></td>";
                      echo "</tr>";
                    }
                    echo "</table>";  
                  ?>
                    <form name='eupForm' action='estate.php' method='post'>
                      <input type='hidden' name='eup' value='-1'/>
                    </form>  
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane <?php if ($tab == 2) echo 'active';?>" id="inv_tab">              
          <?php
            $maxestinv = 3+3*$eups[2];
            $estinvsize = $listsize;
            echo "<p class='h5'>Estate Inventory: ".$estinvsize."/".$maxestinv."</p>";
          ?>
            <div class="row">
              <div class="col-sm-12">
                <div class='row'>
                  <form name="sortForm" action="items.php" method="post">
                    <input type="hidden" name="sort" id="sort" value="" />
                    <input type="hidden" name="tab" value="1"/>
                  </form>
                  <form id="itemForm" name="itemForm" action="estate.php" method="post">
                    <table class="table table-condensed table-responsive table-hover table-clear small">
                      <tr>
                        <th width="15"><input type="checkbox" name="toggler" onClick="javascript:checkedAll()"/></td>
                        <th width="275"><a class="btn btn-success btn-md btn-block" href="javascript:resortForm('0');">Item</a></th>
                        <th width="75"><a class="btn btn-success btn-md btn-block" href="javascript:resortForm('1');">Type</a></th>
                        <th width="50"><a class="btn btn-success btn-md btn-block" href="javascript:resortForm('7');">Status</a></th>	
                        <th width="110"><a class="btn btn-success btn-md btn-block" href="javascript:resortForm('5');">Worth</a></th>	
                        <th width="60"><a class="btn btn-success btn-md btn-block" href="javascript:resortForm('2');">Points</a></th>	
                      </tr>
<?php
            $draw_some = 0;
            for ($itemtoblit = 0; $itemtoblit < $listsize; $itemtoblit++)
            {
              // DRAW ITEM IN LIST
?>
                      <tr>
<?php
                if ($itmlist[$itemtoblit][society] == 0)
                {
?>          
                        <td width="15"><input type="checkbox" name="<?php echo $itemtoblit; ?>" value="<?php echo $itmlist[$itemtoblit][id]; ?>"/></td>
<?php
                }
                else
                {
                  echo "<td width='15'>&nbsp;</td>";
                }
                if ($itmlist[$itemtoblit][type] == 8)
                {
                  $weapon = weaveStats($itmlist[$itemtoblit][base],$tskills);
                }
                else
                {
                  $weapon = itp($itmlist[$itemtoblit][stats],$itmlist[$itemtoblit][type],$itmlist[$itemtoblit][cond]);
                }
                $itmStats= itm_info(cparse($weapon));
                
                $iclass="btn-primary";
                if (lvl_req($weapon, getTypeMod($tskills,$itmlist[$itemtoblit][type])) > $char['equip_pts']/2) $iclass = "btn-danger";
                if ($itmlist[$itemtoblit][society] != 0 || $itmlist[$itemtoblit][istatus] == -2) $iclass = "btn-warning";
                if ($itmlist[$itemtoblit][type] == 8 && !isChanneler($types)) $iclass = "btn-danger";
                if ($itmlist[$itemtoblit][type] == 1 && $char[nation] == 1) $iclass = "btn-danger";
                if ($itmlist[$itemtoblit][istatus] > 0) $iclass = "btn-info";
                $i = $itemtoblit;
                $invinfo = "<div class='panel panel-success' style='width: 150px;'><div class='panel-heading'><h3 class='panel-title'>".ucwords($itmlist[$i][prefix]." ".$itmlist[$i][base])." ".str_replace("Of","of",ucwords($itmlist[$i][suffix]))."</h3></div><div class='panel-body abox' align='center'><img class='img-responsive hidden-xs img-optional-nodisplay' border='0' bordercolor='black' src='items/".str_replace(' ','',$itmlist[$i][base]).".gif'/>";
                $invinfo .= itm_info(cparse(iparse($itmlist[$i],$item_base, $item_ix, $ter_bonuses)));
                $invinfo .= "</div></div>";                
?>
                        <td width="275" class="popcenter">
                          <button type="button" class="btn <?php echo $iclass; ?> btn-sm btn-block btn-wrap link-popover" data-toggle="popover" data-html="true" data-placement="bottom" data-content="<?php echo $invinfo;?>"><?php echo iname($itmlist[$itemtoblit]); ?></button>
                        </td>

<!-- DISPLAY ITEM TYPE -->
                        <td width="75" align='center'>
<?php
                      echo ucwords($item_type[$itmlist[$itemtoblit][type]]);
?>
                        </td>

<!-- DISPLAY ITEM CONDITION -->
                        <td width="50" align='center'>
<?php
                  if ($itmlist[$itemtoblit][type] < 12 && $itmlist[$itemtoblit][type] != 8) echo $itmlist[$itemtoblit][cond]."%";
                  else echo "-";
?>
                        </td>
<!-- STATUS -->
                        <td width="110" align='center'>
<?php               
                  $points = lvl_req($weapon, getTypeMod($tskills,$itmlist[$itemtoblit][type]));
                  
                  if ($itmlist[$itemtoblit][type] == 8) $worth = $item_base[$itmlist[$itemtoblit][base]][2];
                  else $worth = item_val($weapon);
                  echo displayGold($worth);
?>
                        </td>
                        <td width="60" align='center'>
<?php
                  echo $points."pts";
?>
                        </td>
                      </tr>
<?php
              $draw_some = 1;
            }
            if ($draw_some == 0) 
              echo "<tr><td colspan='8'><center><br/><b>You are not carrying any items</b><br/><br/></center></td></tr>";
?>
                    </table>    
                    <input type="hidden" name="action" value="0"/>
                    <input type="hidden" name="tab" value="1"/>
                    <a data-href="javascript:submitFormItem(1);" data-toggle="confirmation" data-placement="top" title="Take selected items?" class="btn btn-warning btn-sm btn-wrap">Take Selected</a>

                  </form>
                </div>
              </div>
            </div>                  
          </div>
<?php
  if ($myEstate[level] >= 6)
  {
?>
          <div class="tab-pane <?php if ($tab == 3) echo 'active';?>" id="quest_tab"> 
            <form name="questForm" action="estate.php" method="post">
              <input type="hidden" name="accepted" value="0">
              <input type="hidden" name="atype" value="0">    
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
                  for ($x=0; $x<4; $x++) 
                  {
                    $result = mysql_query("SELECT * FROM Quests WHERE location='".$surrounding_area[$x]."' AND done='0' ORDER BY expire ASC");  
                    $y=0;
                    while ($quest = mysql_fetch_array( $result ) )
                    { 
                      $qid = $quest[id]; 
                      if (!$myquests[$qid][0])
                      {
                        $goals = unserialize($quest[goals]); 
                        if ($quest[type] == 0 || $goals[2] == $char[location])
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
                  <td class='hidden-xs'><?php echo $qtype; ?></td> 
                  <?php
                    if ($quest[num_avail] > 0)
                    {
                      $qavail=$quest[num_avail]-$quest[num_done];
                    }
                    else $qavail="-";
                  ?> 
                  <td>
                  <?php
                    if ($quest[type] == 1 || $quest[type] == 2)
                    {
                      echo $goals[2];
                    }
                    else
                    {
                      echo $quest[location]; 
                    }
                  ?>
                  </td>  
                  <td class='hidden-xs'>
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
                  ?>  
                  </td>
                  <td class='hidden-xs'><?php echo $qavail; ?></td>
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
                  <td>
                  <?php
                    $reqs=cparse($quest[reqs],0);
                    $charip = unserialize($char[ip]); 
                    $alts = getAlts($charip); 
                    if (!$alts[str_replace(' ','_',$quest[offerer])])
                    {
                      if (check_quest_reqs($reqs,$char))
                      {
                        $acceptLink = "javascript:acceptQuestForm(".$quest[id].",".$quest[type].");";
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
                    }
                  }
              ?>
              </table>
            </form>           
          </div>
<?php
  } 
?>
          <div class="tab-pane <?php if ($tab == 4) echo 'active';?>" id="city_tab">
            <form name='supportForm' action='estate.php' method='post'>
              <div class='row'>
                <div class='col-sm-5'>
                  <div class="panel panel-primary">
                    <div class="panel-heading">
                      <h3 class="panel-title">Estate Support</h3>
                    </div>
                    <div class="panel-body solid-back">       
                    <?php
                      $supporting = unserialize($myEstate[supporting]);
                      $city="";
                      echo "<table class='table table-responsive table-condensed table-hover table-clear small'>";
                      for ($x=0; $x<4; $x++) 
                      {
                        echo "<tr><td align='right'>".$surrounding_area[$x].":</td><td>";
                        $city[$x] = mysql_fetch_array(mysql_query("SELECT id, name, grain, livestock, lumber, stone, fish, luxury FROM Locations WHERE name='$surrounding_area[$x]'"));            
                        echo "<select name='support".$x."' size='1' class='form-control gos-form input-sm'>";
                        echo "  <option value='0'>No one</option>";
                        $result2 = mysql_query("SELECT id, name FROM Soc WHERE 1 ORDER BY score DESC, members DESC");
                        while ( $listchar = mysql_fetch_array( $result2 ) )
                        {
                    ?> 
                          <option value="<?php echo $listchar[id];?>" <?php if ($listchar[id]==$supporting[$surrounding_area[$x]]) echo ' selected';?>><?php echo str_replace('-ap-','&#39;',$listchar[name]); ?></option>
                    <?php
                        }
                        echo "</select></td>";
                        echo "</tr>";
                      }
                      echo "<tr><td></td><td align='center'>";
                      echo "<input type='hidden' name='upsupport' value='0'/>";
                      echo "<input type='button' class='btn btn-primary btn-block btn-sm' value='Update Support' onclick='javascript:changeUpSupport(1);'/>";
                      echo "</tr>";
                      echo "</table>";
                    ?>
                    </div>
                  </div>
                </div>
                <div class='col-sm-7'>
                  <div class="panel panel-info">
                    <div class="panel-heading">
                      <h3 class="panel-title">Estate Trade</h3>
                    </div>
                    <div class="panel-body solid-back">
                    <?php
                      echo "<table class='table table-responsive table-condensed table-hover table-clear small'>";
                      echo "<tr><th>Grains</th><th>Livestock</th><th>Lumber</th>";
                      echo "<th>Stones</th><th>Fish</th><th>Luxuries</th></tr>";
                      for ($x=0; $x<4; $x++) 
                      {
                        echo "<tr><td align='center'>".$city[$x][grain]."</td><td align='center'>".$city[$x][livestock]."</td><td align='center'>".$city[$x][lumber]."</td>";
                        echo "<td align='center'>".$city[$x][stone]."</td><td align='center'>".$city[$x][fish]."</td><td align='center'>".$city[$x][luxury]."</td></tr>";
                      }
                      echo "<tr>";
                      echo "<td align='right' colspan='2'>Trade ".$myEstate[good]." ".$estate_unq_prod[$wild_types[$char['location']]]." with: </td>";
                      echo "<td colspan='2'><select name='etrade' size='1' class='form-control gos-form input-sm'>";
                      echo "  <option value='0'>No one</option>";
                      for ($x=0; $x<4; $x++) 
                      {
                        echo "<option value='".$city[$x][id]."'";
                        if ($myEstate[trade] == $city[$x][id]) echo " selected";
                        echo ">".str_replace('-ap-','&#39;',$city[$x][name])."</option>";
                      }

                      $society = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$char[society]'"));
                      if ($society[id] != "") {
                        echo "<option value='".(100+$society[id])."'>".$society[name]."</option>";
                      }
                      echo "</select></td>";
                      echo "<td colspan='2'><input type='hidden' name='uptrade' value='0'/>";
                      echo " <input type='button' class='btn btn-sm btn-info btn-block btn-wrap' value='Perform Trade' onclick='javascript:changeUpTrade(1);'/>";
                      echo "</td></tr>";          
                      echo "</table>";
                    ?>  
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
<?php
} 
?>
          <div class="tab-pane <?php if ($tab == 5) echo 'active';?>" id="local_tab">
            <div class='row'>
              <div class='col-sm-8'>
<div id="wrapper">
    <div id="innerWrapper">              
              <?php
                $wild_img_name = str_replace(' ','_',$char['location']);
                $wild_img_name = str_replace('&#39;','',$wild_img_name);
                if ($mode != 1) 
                {
                  echo "<table style='background: url(images/wildmap_v9/".$wild_img_name.".gif); background-size: 100% 100%; background-repeat: no-repeat; width:98%; height: 98%;'><tr><td align='center'>";
                }
                else
                {
                  echo "<table style='background-color: #dddddd; width:98%; height: 98%;'><tr><td align='center'>";
                }                
                echo "<table class='table-clear' id='mapTable' style='width:93%; height:93%; border: 3px;'>";
                echo "<col width='20%' /><col width='20%' /><col width='20%' /><col width='20%' /><col width='20%' />";
                for ($x=0; $x<5; $x++)
                {
                  echo "<tr style='height:20%;'>";
                  for ($y=0; $y<5; $y++)
                  {
                    if ( $loc_estates[$x][$y][0]< $maxPerCell)
                      echo "<td align='center'><font class='estate_t'>Available</font></td>";
                    else if ($loc_estates[$x][$y][1] && $loc_estates[$x][$y][1] != "")
                      echo "<td align='center'><font class='estate_r'>".$loc_estates[$x][$y][1]."</font></td>";
                    else
                      echo "<td align='center'><font class='estate_r'>Occupied</font></td>";
                  }
                  echo "</tr>";
                }
                echo "</table>";
                echo "</td></tr></table>";
              ?>
                <form name="buildForm" action="estate.php" method="post">
                  <input type='hidden' name='row' value='-1'/>
                  <input type='hidden' name='col' value='-1'/>
                </form>
                  </div>
                </div>
              </div>
              <div class='col-sm-4'>
                <div class="panel panel-primary">
                  <div class="panel-heading">
                    <h3 class="panel-title">Wilderness Plots</h3>
                  </div>
                  <div class="panel-body abox">
                  <?php
                    if ($canbuy)
                    {
                      echo "You can buy land to found an estate in this area!<br/><br/>";
                      echo "Number of estates in this area: ".$tot_loc_estates."<br/>";
                      echo "Estates you own: ".$myNumEstates."<br/>";
                      echo "Cost to found an estate: ".displayGold($estateCost)."<br/><br/>";
                      echo "Click on an Available plot of land to build your estate.<br/>";          
                    }
                    else 
                    {
                      echo $nobuy;
                    } 
                  ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<script type="text/javascript">
  mapTable();
  
  function submitFormItem(act) 
  {
    document.itemForm.action.value= act;
    document.itemForm.submit();
  }  
  function acceptQuestForm(accept,type)
  {
    document.questForm.accepted.value= accept;
    document.questForm.atype.value= type;    
    document.questForm.submit();
  } 
</script>
<?php
}
include("footer.htm");
?>