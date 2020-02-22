 <?php
$message=mysql_real_escape_string($_GET['message']);
$note=mysql_real_escape_string($_POST['note']);
$submitted=mysql_real_escape_string($_REQUEST[submitted]);
$wait_post = 30;

$id=$char['id'];

$soc_name = $char['society'];
$loc_name = str_replace('-ap-','&#39;',$char['location']);
$loc_query = $char['location'];
$surrounding_area = $map_data[$loc];

// LOAD LOCATION TABLE
$location = mysql_fetch_array(mysql_query("SELECT * FROM Locations WHERE name='$loc_query'"));

$upgrades = unserialize($location[upgrades]);

// UPDATE NUMBER OF MEMEBERS
$query = "SELECT COUNT(*) FROM Users WHERE location='$loc_query' ";
$resultf = mysql_fetch_array(mysql_query($query));
$numchar = $resultf[0];

// CHECK FOR LOCAL BUSINESSES
  $myLocBiz[999] = '';
  $query1 = "SELECT COUNT(*) FROM Profs WHERE owner='$id' AND location='$loc_query'";
  $resultf1 = mysql_fetch_array(mysql_query($query));
  if ($resultf1)
  {
    $result = mysql_query("SELECT id, type FROM Profs WHERE owner='$id' AND location='$loc_query'");  
    while ($bq = mysql_fetch_array( $result ) )
    {
      $myLocBiz[$bq[type]]=1;
    }
  }
?>

<div class='row'>

  <?php
    if ($location['ruler'] != 'No One' && !$location[isDestroyed])
    {
      $rulersoc = mysql_fetch_array(mysql_query("SELECT id, flag, sigil FROM Soc WHERE name='$location[ruler]' "));
      echo "<div class='col-sm-3 col-lg-2 hidden-xs'><div class='img-optional' style=\"background-image: url('images/Flags/".$rulersoc[flag]."'); background-repeat: no-repeat;\"><img src=\"images/Sigils/".$rulersoc['sigil']."\" align='top' width=160 height=197 class='img-optional'></div></div>";
    } 
    else 
    { 
      $town_img_name = str_replace(' ','_',strtolower($char['location']));
      $town_img_name = str_replace('&#39;','',strtolower($town_img_name));
      echo "<div class='col-sm-3 col-lg-2 hidden-xs'><img class='img-optional' src=\"images/Flags/".$town_img_name.".gif\" align='top' width=160/></div>";
    }

    $sealid = $location[id]+50000;
    $hasSeal = mysql_num_rows(mysql_query("SELECT id FROM Items WHERE type=0 && owner='$sealid'"));
  ?>
  <div class='col-sm-6 col-lg-8'>
      <?php
      if (!$location[isDestroyed])
      {
      ?>
        <?php
          if ($hasSeal)
            echo "<img src='images/classes/4.gif' height=18 width=18/>&nbsp;";
        ?>
        <p><b><?php echo "$loc_name"; ?></b> is controlled by<b>
        <?php 
          if ($location['ruler'] != 'No One')
          {
        ?>
        <a href="joinclan.php?name=<?php echo $location['ruler'];  echo "&time="; echo time(); ?>" target="_parent"><?php echo $location['ruler'];?></a>
        <?php
          }
          else { echo $location['ruler']; }
        ?></b></p> 

        <div class='row'>
              <div class="city-btn col-sm-4 col-md-3">
                <button class='btn btn-default btn-sm btn-block' onClick="parent.document.location='shop.php'">Shop</button>
              </div>
              <div class="city-btn col-sm-4 col-md-3">
                <button class='btn btn-default btn-sm btn-block' onClick="parent.document.location='blacksmith.php'">Blacksmith</button>
              </div>          
              <div class="city-btn col-sm-4 col-md-3">
                <button class='btn btn-default btn-sm btn-block' onClick="parent.document.location='market.php'">Market</button>
              </div> 
              <div class="city-btn col-sm-4 col-md-3">
                <button class='btn btn-default btn-sm btn-block' onClick="parent.document.location='business.php?shop=4'">Outfitter</button>
              </div>
              <div class="city-btn col-sm-4 col-md-3">
                <button class='btn btn-default btn-sm btn-block' onClick="parent.document.location='business.php?shop=1'">Inn</button>
              </div>
              <div class="city-btn col-sm-4 col-md-3">
                <button class='btn btn-default btn-sm btn-block' onClick="parent.document.location='business.php?shop=2'">Wise Woman</button>
              </div>     
              <div class="city-btn col-sm-4 col-md-3">
                <button class='btn btn-default btn-sm btn-block' onClick="parent.document.location='business.php?shop=3'">Tavern</button>
              </div>
              <div class="city-btn col-sm-4 col-md-3">
                <button class='btn btn-default btn-sm btn-block' onClick="parent.document.location='quests.php'">Quests</button>
              </div>                            
              <div class="city-btn col-sm-4 col-md-3">
                <button class='btn btn-default btn-sm btn-block' onClick="parent.document.location='rumors.php'">Rumors</button>
              </div>
              <div class="city-btn col-sm-4 col-md-3">
                <button class='btn btn-default btn-sm btn-block' onClick="parent.document.location='townhall.php'">City Hall</button>
              </div>              
          <?php 
            if (count($myLocBiz) >1)
            {
          ?>
              <div class="city-btn col-sm-4 col-md-3">
                <button class='btn btn-default btn-sm btn-block' onClick="parent.document.location='mybusinesses.php'">Businesses</button>
              </div>
          <?php
            }        
            $society = mysql_fetch_array(mysql_query("SELECT id, subleaders, subs, offices, area_score, leader, leaderlast FROM Soc WHERE name='$soc_name' "));
            $subleaders = unserialize($society['subleaders']);
            $subs = $society['subs'];
            $offices = unserialize($society['offices']);
            $areascore = unserialize($society['area_score']);
            $locscore = $areascore[$location[id]];
            $b = 0; 
  
            if (strtolower($name) == strtolower($society[leader]) && strtolower($lastname) == strtolower($society[leaderlast]) ) 
            {
              $b=1;
            }
            if ($subs > 0)
            {
              foreach ($subleaders as $c_n => $c_s)
              {
                if (strtolower($name) == strtolower($c_s[0]) && strtolower($lastname) == strtolower($c_s[1]) )
                {
                  $b=1;
                }
              }
            }    
            if ($offices[$location[id]][0] != '1')
            {
              if (strtolower($name) == strtolower($offices[$location[id]][0]) && strtolower($lastname) == strtolower($offices[$location[id]][1]) ) 
              {
                $b=1;  
              }
            }    
            if ($b==1)
            {
              if ($locscore >=100 || $offices[$location[id]][0])
              {
          ?>
              <div class="city-btn col-sm-4 col-md-3">
                <button class='btn btn-default btn-sm btn-block' onClick="parent.document.location='clanoffice.php'">Clan Office</button>
              </div>
          <?php
              }          
            }
          ?>
          </div>
          <div class='row'>
            <p><i><?php echo nl2br($location[info]); ?></i></p>
          </div>
            <?php
              $myTourney = 0;
              if ($location[last_tourney])
              {
                $myTourney = mysql_fetch_array(mysql_query("SELECT id, starts, ends, winner FROM Contests WHERE id='$location[last_tourney]' "));
              }
              if ($myTourney != 0) // Previous tournament
              {
                echo "<div class='row'><p><a href='townhall.php?tab=2' class='text-primary'>";
                if ($myTourney[starts] > intval(time()/3600))
                {
                  $tminus = intval(($myTourney[starts]*3600-time())/60);
                  echo "New Tournament starts in: ".displayTime($tminus,0,1);
                }
                else if ($myTourney[ends] > intval(time()/3600))
                {
                  $tminus = intval(($myTourney[ends]*3600-time())/60);
                  echo "Tournament ends in: ".displayTime($tminus,0,1);
                }
                else
                {
                  echo "Last Tournament won by: ".$myTourney[winner];
                }
                echo "</a></p></div>";
              }
            ?>
            <?php
              $lastBattle = 0;
              $myWar = 0;
              
              // Check if it's Last Battle Time. :)
              $lastBattle = mysql_fetch_array(mysql_query("SELECT id, starts, ends, winner FROM Contests WHERE type='99' "));
              if ($lastBattle == 0) // if not, look for clan battles
              {
                if ($location[last_war])
                {
                  $myWar = mysql_fetch_array(mysql_query("SELECT id, starts, ends, winner FROM Contests WHERE id='$location[last_war]' "));
                }
                if ($myWar != 0) // Previous tournament
                { 
                  echo "<div class='row'><p><a href='townhall.php?tab=3' class='text-warning'>";
                  if ($myWar[starts] > intval(time()/3600))
                  {
                    $tminus = intval(($myWar[starts]*3600-time())/60);
                    echo "Clan Battle Declared! Battle starts in: ".displayTime($tminus,0)."!";
                  }
                  else if ($myWar[ends] > intval(time()/3600))
                  {
                    $tminus = intval(($myWar[ends]*3600-time())/60);
                    echo "Clan Battle in Progress! Ends in: ".displayTime($tminus,0,1);
                  }
                  else
                  {
                    echo "Last Clan Battle won by: ".$myWar[winner];
                  }
                  echo "</a></p></div>";
                }
              }
              else // If so, let the fun begin...
              {
                echo "<div class='row'><p><a href='townhall.php?tab=3' class='text-warning'>";
                if ($lastBattle[starts] > intval(time()/3600))
                {
                  $tminus = intval(($lastBattle[starts]*3600-time())/60);
                  echo "The Last Battle is upon us! Battle starts in: ".displayTime($tminus,0)."!";
                }
                else if ($lastBattle[ends] > intval(time()/3600))
                {
                  $tminus = intval(($lastBattle[ends]*3600-time())/60);
                  echo "The Last Battle in Progress! Ends in: ".displayTime($tminus,0,1);
                }
                else
                {
                  echo "The Last battle is over. The ".$lastBattle[winner]." has prevailed!";
                }
                echo "</a></p></div>";
              }

              $result3 = mysql_query("SELECT id, npcs, done, ends, location FROM Hordes WHERE target='$location[name]' ORDER BY ends DESC LIMIT 1");
              while ($myHorde = mysql_fetch_array( $result3 ) )
              {
                $npc_info = unserialize($myHorde[npcs]);
                $hordemsg = "";
                for ($n=0; $n < 1; $n++)
                {
                  if ($n==0) $hordemsg .= "A "; else $hordemsg.= " and a ";
                  $hordemsg .= $horde_types[$npc_info[$n][0]]." of ".$npc_info[$n][0]."s";
                }
                if ($myHorde[done]==1 || $myHorde[done]==2)
                {
                  if (count($npc_info)==1) $hordemsg .= " was "; else $hordemsg.= " were ";              
                  $hordemsg .= "defeated in ".str_replace('-ap-','&#39;',$myHorde[location]).". Attack averted!"; 
                }
                elseif ($myHorde[done]==0)
                {
                  $tminus = intval(($myHorde[ends]*3600-time())/60);
                  if (count($npc_info)==1) $hordemsg .= " is "; else $hordemsg.= " are ";              
                  $hordemsg .= "gathering in ".str_replace('-ap-','&#39;',$myHorde[location]).". Attack in ".displayTime($tminus,0,1)."!";
                }
                elseif ($myHorde[done]==3)
                {
                  $hordemsg .= " was left unchecked and attacked the city! ";
                }
                if ($myHorde[done]<2) $hclass = 'text-danger';
                else $hclass = 'text-info';
                echo "<div class='row'><p><a href='hordeinfo.php' class='".$hclass."'>".$hordemsg."</a></p></div>";
                
              }
            ?>

      <?php
      }
      else
      {
      ?>
      The ruins of the destroyed city of <b><?php echo "$loc_name"; ?></b>
      <?php
      }
      ?>
    </div>
    <?php
      echo "<div class='col-sm-3 col-lg-2 hidden-xs'><img class='img-optional' src=\"images/Flags/".$town_img_name.".gif\" align='top' width=160/></div>";      
    ?>
  </div>
<script language="javascript">
// getXMLHttpRequest object
function getXMLHttpRequestObject(){
   var xmlobj;

    // check for existing requests
    if(xmlobj!=null&&xmlobj.readyState!=0&&xmlobj.readyState!=4){
        xmlobj.abort();
    }
    if (window.XMLHttpRequest) {
        // instantiate object for Mozilla, Nestcape, etc.
        xmlobj=new XMLHttpRequest();
    }
    else if ( window.ActiveXObject ) 
    {
      try 
      {
        xmlobj = new ActiveXObject("Microsoft.XMLHTTP");
      } 
      catch( e ) 
      {
        xmlobj = new ActiveXObject("Msxml2.XMLHTTP");
      }
    }
    
    if (xmlobj==null) {
        return false;
    }
    return xmlobj;
}  
</script>  
  <?php 
    if (!$location[isDestroyed])
    {
      include "bank.php";
    }
    $font_size="class='medtext' style='$classn'";
  ?>  
  <div class='row'>
    <div class="col-sm-4">
      <div class="panel panel-warning">
        <div class="panel-heading">
          <h3 class="panel-title" onClick="parent.document.location='viewtowns.php'">City Info</h3>
        </div>
        <div class="panel-body abox"> 
        <?php
          $total=0;
          $shoplvls=unserialize($location['shoplvls']);
          $total = number_format(count($shoplvls)*100/79)."%";
      
          $loco = $location['id'];
          updateSocScores();
          $location = mysql_fetch_array(mysql_query("SELECT * FROM Locations WHERE name='$loc_query'"));
          $clanscores = unserialize($location[clan_scores]);
          $supporting = unserialize($location[clan_support]);

          $topclans='';
          $x=0;
          
          if ($clanscores)
          {
            foreach ($clanscores as $key => $value)
            {
              $topclans[$x++] = $key;
            }
          }
          
          $h=0;

          $result =mysql_query("SELECT * FROM Users LEFT JOIN Users_stats ON Users.id=Users_stats.id WHERE 1 ORDER BY loc_ji".$loco." DESC, exp DESC LIMIT 0,5");
          while ( $listchar = mysql_fetch_array( $result ) )
          {
            $topheroes[$h][0]=$listchar[name]." ".$listchar[lastname];
            $topheroes[$h][1]=$listchar['loc_ji'.$loco];
            $h++;
          }
          $town_pop = getTownPop($upgrades,$numchar,$build_pop);
        ?>
          
          <table cellpadding='2' width='100%' class="table-clear">
            <tr><td align='left'>Players in City:</td><td align='left'><?php echo $numchar; ?></td></tr>
            <?php 
              if (!$location[isDestroyed])
              {
            ?>
            <tr><td align='left'>Population:</td><td align='left'><?php echo $town_pop; ?></td></tr>
            <tr><td align='left'>City Bank:</td><td align='left'><?php echo displayGold($location['bank']);?></td></tr>
            <tr><td align='left'>City Order:</td><td align='left'><?php echo $location['myOrder'];?></td></tr>
            <tr><td align='left'>City Defenses:</td><td align='left'><?php echo $location['army'];?></td></tr>
            <tr><td align='left'>Shop Upgrades:</td><td align='left'><?php echo $total; ?></td></tr>
            <tr><td colspan=2 align='left'>Bonuses:<br/>
            <?php
              echo itm_info($display_town_bonuses)."</td></tr>";
              }
            ?>             
          </table>
        </div>
      </div>
    </div> 
    <div class="col-sm-4">
      <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title" onClick="parent.document.location='viewclans.php'">Top Clans</h3>
        </div>
        <div class="panel-body abox"> 
          <table cellpadding='2' width='100%' class="table-clear">
          <?php
            if ($clanscores)
            {
              for ($x=0; $x<5; $x++)
              {
                if ($topclans[$x])
                {
                  $soc = mysql_fetch_array(mysql_query("SELECT id, name FROM `Soc` WHERE id = '$topclans[$x]'"));
                  echo "<tr><td align='left'>".$soc[name].":</td>";
                  if ($supporting[$topclans[$x]]==0) echo "<td align='left'>". number_format($clanscores[$topclans[$x]])."</td>";
                  else echo "<td align='left'>Supporting</td>";
                  echo "</tr>";
                }
              }
            }
          ?>
          </table>
        </div>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="panel panel-success">
        <div class="panel-heading">
          <h3 class="panel-title" onClick="parent.document.location='horn.php'">Heroes of <?php echo "$loc_name"; ?></h3>
        </div>
        <div class="panel-body abox">
          <table cellpadding='2' width='100%' class="table-clear">
          <?php
            for ($x=0; $x<5; $x++)
            {
              if ($topheroes[$x])
              {
                echo "<tr><td align='left'>".$topheroes[$x][0].":</td>";
                echo "<td align='left'>". number_format($topheroes[$x][1])."</td>";
                echo "</tr>";
              }
            }
          ?>
          </table>
        </div>
      </div>
    </div>
  </div>