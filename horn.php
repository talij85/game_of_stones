<?php

/* establish a connection with the database */
include("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/charFuncs.php");

$time=time();

$wikilink = "Heroes";

//HEADER
$tab = mysql_real_escape_string($_GET['tab']);
$message = "View current <a href='viewtowns.php'>City Information</a> of this Age or the <a href='look.php?world=1&amp;first=1&amp;time=".time()."'>Full Rankings</a>";
if (!$tab) $tab=1;
include('header.htm');

$soc_name = $char[society];

if ($soc_name)
{
  $society = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$soc_name' ORDER BY id"));
  $stance = unserialize($society['stance']);
}

$query = "SELECT * FROM Users_stats WHERE id>'10000'";
$result = mysql_query($query);
$x=0;
while ( $listchar = mysql_fetch_array( $result ) )
{
  $x = $listchar[id]-10001;
  if ($x<10)
  {
    $heroes[$x][0]=$listchar;
    $x++;
  }
  else
  {
    $heroes[$x-10][1]=$listchar;
    $x++;
  }
}

$query = "SELECT * FROM Soc_stats WHERE id>'10000'";
$result = mysql_query($query);
$x=0;
while ( $listsoc = mysql_fetch_array( $result ) )
{
  $x = $listsoc[id]-10000;
  $topClans[$x]=$listsoc;
}

$result99 = mysql_query("SELECT id, winner FROM Contests WHERE type='99' AND done='1'");
$lastBattleDone = mysql_num_rows($result99);
if ($lastBattleDone)
{
  $lb = mysql_fetch_array($result99);
  echo "<center><b>";
  if ($lb[winner] == "Shadow")
  {
    echo "The Last Battle is over. The Shadow has fallen over the pattern. Hail the Great Lord!.";
  }
  else
  {
    echo "The Last Battle is over. The Light has prevailed over the Shadow. The Wheel turns.";
  }
  echo "</b></center>";
}
?>
<!-- TOP TENS -->
  <div class="row solid-back">
    <div class="col-sm-12">
      <div id="content">
        <ul id="horntabs" class="nav nav-tabs" data-tabs="tabs">
          <li <?php if ($tab == 1) echo "class='active'";?>><a href="#hero_tab" data-toggle="tab">Heroes</a></li> 
          <li <?php if ($tab == 2) echo "class='active'";?>><a href="#win_tab" data-toggle="tab">Wins</a></li>
          <li <?php if ($tab == 3) echo "class='active'";?>><a href="#npc_tab" data-toggle="tab">NPC Types</a></li>
          <li <?php if ($tab == 4) echo "class='active'";?>><a href="#quest_tab" data-toggle="tab">Quest</a></li>
          <li <?php if ($tab == 5) echo "class='active'";?>><a href="#coin_tab" data-toggle="tab">Coin</a></li>
          <li <?php if ($tab == 6) echo "class='active'";?>><a href="#earn_tab" data-toggle="tab">Earnings</a></li>
          <li <?php if ($tab == 7) echo "class='active'";?>><a href="#nat_tab" data-toggle="tab">Nationalities</a></li>
          <li <?php if ($tab == 8) echo "class='active'";?>><a href="#clan_tab" data-toggle="tab">Clans</a></li>
          <li <?php if ($tab == 8) echo "class='active'";?>><a href="#city_tab" data-toggle="tab">Cities</a></li>    
        </ul>

        <div class="tab-content">
      <?php
        $users="";
        // START LOOPS
        for ($y = 0; $y < count($rank_data); $y++)
        {
          if ($y==0)
          {
            if ($tab == 1) $active='active'; else $active='';
            $count=0;
            echo "<div class='tab-pane $active' id='hero_tab'><div class='row'>";  
          }
          elseif ($y==6)
          {
            echo "</div></div>";
            if ($tab == 2) $active='active'; else $active='';
            $count=0;
            echo "<div class='tab-pane $active' id='win_tab'><div class='row'>";  
          } 
          elseif ($y==14)
          {
            echo "</div></div>";
            if ($tab == 3) $active='active'; else $active='';
            $count=0;
            echo "<div class='tab-pane $active' id='npc_tab'><div class='row'>";  
          }
          elseif ($y==20)
          {
            echo "</div></div>";
            if ($tab == 4) $active='active'; else $active='';
            $count=0;
            echo "<div class='tab-pane $active' id='quest_tab'><div class='row'>";  
          }  
          elseif ($y==28)
          {
            echo "</div></div>";
            if ($tab == 5) $active='active'; else $active='';
            $count=0;
            echo "<div class='tab-pane $active' id='coin_tab'><div class='row'>";  
          }  
          elseif ($y==34)
          {
            echo "</div></div>";
            if ($tab == 6) $active='active'; else $active='';
            $count=0;
            echo "<div class='tab-pane $active' id='earn_tab'><div class='row'>";  
          }
      ?>
            <div class='col-sm-6 col-md-4'>
              <div class="panel panel-success">
                <div class="panel-heading">
                  <h3 class="panel-title"><?php echo "Top Ten by ".$rank_data[$y][1];?></h3>
                </div>
                <div class="panel-body solid-back">
                  <table class='table table-responsive table-hover table-condensed table-clear small'>
                <?php                  
                  $img_ar = Array("s1","w1");
                  for($x=0; $x<10; $x++)
                  {
                    $tid=$heroes[$x][1][$rank_data[$y][0]];
                    if (!$tid)
                    {
                      $x=10;
                    }
                    else
                    { 
                      if (!$users[$tid])
                      {
                        $users[$tid] = mysql_fetch_array(mysql_query("SELECT id, name, lastname, goodevil, society FROM Users WHERE id='".$tid."'"));
                      }
                      $listchar=$users[$tid];
                      if ($listchar['id'] == $id) $classn="btn-info";
                      else $classn="btn-default";
                ?>
                    <tr>
                      <td width="25" valign='bottom'><?php echo ($x+1)."."; ?></td>
                      <td valign='bottom' align='center'>
                      <?php 
                        if ($y < 26) echo number_format($heroes[$x][0][$rank_data[$y][0]]); 
                        else echo displayGold($heroes[$x][0][$rank_data[$y][0]]);?>
                      </td>
                      <td valign='bottom'>
                        <a class='btn btn-xs btn-block btn-wrap <?php echo $classn; ?>' href="bio.php?name=<?php echo $listchar['name']; echo "&amp;last="; echo $listchar['lastname']; echo "&amp;time="; echo time(); ?>"><?php echo $listchar['name']." ".$listchar['lastname']; ?>
                          <?php if ($stance[str_replace(" ","_",$listchar[society])] && $listchar[id] != $id) echo "<img src='images/".$img_ar[$stance[str_replace(" ","_",$listchar[society])]-1].".gif'/>"; ?></a>
                      </td>
                    </tr>
                <?php
                    }
                  }
                ?>
                  </table>
                </div> 
              </div>
            </div>               
      <?php
          $count++;
        }
        echo "</div></div>";

        // Top by Nations
        if ($tab == 7) $active='active'; else $active='';
        echo "<div class='tab-pane $active' id='nat_tab'>";
        for ($i=1; $i<19; $i++)
        {
          $x=0;
      ?>
            <div class='col-sm-6 col-md-4'>
              <div class="panel panel-success">
                <div class="panel-heading">
                  <h3 class="panel-title"><?php echo "Top ".$nationalities[$i];?></h3>
                </div>
                <div class="panel-body solid-back">
                  <table class='table table-responsive table-hover table-condensed table-clear small'>
                <?php
                  $result = mysql_query("SELECT id, name, lastname, exp, goodevil, society, level FROM Users WHERE nation='".$i."' ORDER BY exp DESC LIMIT 0,3");
                  while ($listchar = mysql_fetch_array($result))
                  {
                      if ($listchar['id'] == $id) $classn="btn-info";
                      else $classn="btn-default";
                ?>
                    <tr>
                      <td width="25" valign='bottom'><?php echo ($x+1)."."; ?></td>
                      <td valign='bottom' align='center'><?php echo number_format($listchar[exp]); ?></td>
                      <td valign='bottom'>
                        <a class='btn btn-xs btn-block btn-wrap <?php echo $classn; ?>' href="bio.php?name=<?php echo $listchar['name']; echo "&amp;last="; echo $listchar['lastname']; echo "&amp;time="; echo time(); ?>"><?php echo $listchar['name']." ".$listchar['lastname']; ?>
                          <?php if ($stance[str_replace(" ","_",$listchar[society])] && $listchar[id] != $id) echo "<img src='images/".$img_ar[$stance[str_replace(" ","_",$listchar[society])]-1].".gif'/>"; ?></a>
                      </td>
                    </tr>
                <?php
                    $x++;
                  }
                ?>
                  </table>
                </div> 
              </div>
            </div>  
      <?php
        }
        echo "</div>";

        // Top Clans
        if ($tab == 8) $active='active'; else $active='';
        echo "<div class='tab-pane $active' id='clan_tab'>";
        for ($y = 0; $y < count($soc_rank_data); $y++)
        {
          $x=1;
      ?>
            <div class='col-sm-6 col-md-4'>
              <div class="panel panel-success">
                <div class="panel-heading">
                  <h3 class="panel-title"><?php echo "Top Ten by ".$soc_rank_data[$y][1];?></h3>
                </div>
                <div class="panel-body solid-back">
                  <table class='table table-responsive table-hover table-condensed table-clear small'>
                <?php
                  $rank_by = $soc_rank_data[$y][0];
                  $idStr = $soc_rank_map[$rank_by]."Id";
                  $numStr = $soc_rank_map[$rank_by]."Num";

                  for($x=1; $x<=10; $x++)
                  {
                    $sid=$topClans[$x][$idStr];
                    if (!$sid)
                    {
                      $x=11;
                    }
                    else
                    { 
                      if (!$soc[$sid])
                      {
                        $soc[$sid] = mysql_fetch_array(mysql_query("SELECT id, name FROM Soc WHERE id='".$sid."'"));
                      }
                      $listsoc=$soc[$sid];
                      if ($listsoc['name'] == $char['society']) $classn="btn-info";
                      else $classn="btn-default";
                ?>
                    <tr>
                      <td width="25" valign='bottom'><?php echo ($x)."."; ?></td>
                      <td valign='bottom' align='center'><?php if ($y < 5) echo number_format($topClans[$x][$numStr]); else echo displayGold($topClans[$x][$numStr]);?></td>
                      <td valign='bottom'>
                        <a class='btn btn-xs btn-block btn-wrap <?php echo $classn; ?>' href="joinclan.php?name=<?php echo $listsoc['name'];?>"><?php echo $listsoc['name'];?>
                          <?php if ($stance[str_replace(" ","_",$listsoc[name])]) echo "<img src='images/".$img_ar[$stance[str_replace(" ","_",$listsoc[name])]-1].".gif'/>"; ?></a>
                      </td>
                    </tr>
                <?php
                    }
                  }
                ?>
                  </table>
                </div> 
              </div>
            </div>  
      <?php
        }
        echo "</div>";
        
        // Top Cities
        if ($tab == 9) $active='active'; else $active='';
        echo "<div class='tab-pane $active' id='city_tab'>";
        for ($y = 0; $y < count($city_rank_data); $y++)
        {
          $x=1;
      ?>
            <div class='col-sm-6 col-md-4'>
              <div class="panel panel-success">
                <div class="panel-heading">
                  <h3 class="panel-title"><?php echo "Top Ten by ".$city_rank_data[$y][1];?></h3>
                </div>
                <div class="panel-body solid-back">
                  <table class='table table-responsive table-hover table-condensed table-clear small'>
                <?php
                  $rank_by = $city_rank_data[$y][0];
                  $field = $rank_by;
                  $orderBy = $rank_by." DESC";
                  if (substr($rank_by, 0, 1) === "-") 
                  { 
                    $field = substr($rank_by, 1, strlen($rank_by));
                    $orderBy =$field." ASC";
                  }
                  $x=0;
                  $result = mysql_query("SELECT id, myOrder, pop, name FROM Locations WHERE 1 ORDER BY ".$orderBy." LIMIT 0,10");
                  while ($listloc = mysql_fetch_array($result))
                  {
                      $classn="btn-default";
                ?>
                    <tr>
                      <td width="25" valign='bottom'><?php echo ($x+1)."."; ?></td>
                      <td valign='bottom' align='center'><?php echo number_format($listloc[$field]); ?></td>
                      <td valign='bottom'>
                        <a class='btn btn-xs btn-block btn-wrap <?php echo $classn; ?>' href="#"><?php echo $listloc['name']; ?></a>
                      </td>
                    </tr>
                <?php
                    $x++;
                  }
                ?>
                  </table>
                </div> 
              </div>
            </div>  
      <?php
        }
        echo "</div>";
        // END LOOPS
      ?>
        </div>
      </div>
    </div>
  </div>

<?php
include('footer.htm');
?>