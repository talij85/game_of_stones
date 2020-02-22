<?php

function array_delel($array,$del)
{
  $arraycount1=count($array);
  $z = $del;
  while ($z < $arraycount1) {
    $array[$z] = $array[$z+1];
    $z++;
  }
  array_pop($array);
  return $array;
}

/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/itemFuncs.php");
include_once("admin/charFuncs.php");

$wikilink = "Achievements";

// update message check
mysql_query("UPDATE Users SET newachieve=0 WHERE id=$id");

$tab = mysql_real_escape_string($_GET['tab']);
if ($message == '')
{
  $message = "Achievements";
}

// Regrab messages as you may have some new ones depending on your actions
$aresult=mysql_query("SELECT * FROM Notes WHERE to_id='$id' AND del_to='0' AND type='5' ORDER BY sent DESC LIMIT 0, 20");
$abox = "";
$a_num = 0;
while ($anote = mysql_fetch_array($aresult))
{
  $abox[$a_num++] = $anote;
}

$stats = mysql_fetch_array(mysql_query("SELECT * FROM Users_stats WHERE id='$char[id]'"));

if (!$tab) $tab = 1;

include('header.htm');
?>
  <div class="row solid-back">
    <div class="col-sm-12">
      <div id="content">
        <ul id="achievtabs" class="nav nav-tabs" data-tabs="tabs">
          <li <?php if ($tab == 1) echo "class='active'";?>><a href="#recent_tab" data-toggle="tab">Recent</a></li>        
          <li <?php if ($tab == 2) echo "class='active'";?>><a href="#achieve_tab" data-toggle="tab">Achievements</a></li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane <?php if ($tab == 1) echo "active";?>" id="recent_tab">  
            <p class='h5'>        
          <?php
            if ($a_num==1) echo "One achievement stored";
            else echo "$a_num achievements stored";
            echo "</p>";
            $x=0;

            if ($a_num > 0)
            {
          ?>
            <div class="panel-group" id="accordion_ach">
            <?php
              while ($x < $a_num)
              {
                $y = $x; 
                if ($abox[$y][id])
                {
                  $currid = "diva".$y;
                  $cursub = nl2br($abox[$y][subject]);
                  $curnote=nl2br($abox[$y][body]);
                  $senttime = "Seconds ago";
                  $minpast = intval((time()-$abox[$y][sent])/60);
                  $senttime = displayTime($minpast,1,0);;
                  $sender = mysql_fetch_array(mysql_query("SELECT id,name,lastname FROM Users WHERE id='".$abox[$y][from_id]."'"));
                  $accord = "collapsea".$y;
            ?>
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <div class='row'>
                      <a class='col-xs-3 btn btn-primary btn-xs btn-wrap' href="bio.php?name=<?php echo $sender[name]."&last=".$sender[lastname]; ?>"><?php echo $sender[name]." ".$sender[lastname]." (".$senttime; ?>)</a>
                      <a class='col-xs-7 btn btn-default btn-xs btn-wrap' data-toggle="collapse" data-parent="#accordion_ach" href="#<?php echo $accord;?>"><h4 class="panel-title"><?php echo $cursub; ?></h4></a>
                    </div>
                  </div>
                  <div id="<?php echo $accord;?>" class="panel-collapse collapse">
                    <div class="panel-body" align='left'>
                      <?php echo $curnote; ?>
                    </div>
                  </div>
                </div>
            <?php
                }
                $x++;
              }
              // END DRAWING MESSAGES
            ?>        
              </div>
          <?php      
            }
            else echo "<p class='h4'>-No Achievements-</p>";
          ?>
          </div>
          <div class="tab-pane <?php if ($tab == 2) echo "active";?>" id="achieve_tab">  
            <p class='h5'>My Achievements: <?php echo $stats[achieved];?></p>
            <div class='row'>
              <div class='col-sm-10 col-sm-push-1 col-md-8 col-md-push-2 col-lg-6 col-lg-push-3'>
                <table class='table table-responsive table-striped table-hover table-clear small'>
                  <tr>
                    <th>Task</th>
                    <th>Progress</th>
                    <th>Reward</th>
                  </tr>
                <?php
                  $myAchieve= unserialize($char[achieve]);
                  foreach ($achievements as $branch => $ainfo)
                  {
                    $a= $myAchieve[$branch];
                    if (!$a) $a=0;
                    if ($a < count($ainfo[3]))
                    {
                      $x = $ainfo[3][$a];
                      if ($ainfo[1] == '' || ($myAchieve[$ainfo[1][0]]>=$ainfo[1][1]))
                      {
                        echo "<tr>";
                        echo "<td><a class='btn btn-info btn-sm btn-block btn-wrap' href='http://talij.com/goswiki/index.php?title=Achievements#".$ainfo[6]."_Related_Achievements' target='_blank'>".$ainfo[2][0];
                        if (count($ainfo[2]) > 1)
                        {
                          if ($ainfo[2][2])
                          {
                            echo displayGold($x);
                          }
                          else
                          {
                            echo $x;
                          }
                          echo $ainfo[2][1]."</a>";
                          if ($stats[$ainfo[0]] >= $x) echo "*";
                        }
                        else echo "</a>";
                        echo "</td>";
                        echo "<td align='center'>";
                        if ($ainfo[2][2])
                        {
                          echo displayGold($stats[$ainfo[0]]);
                        }
                        else
                        {
                          echo $stats[$ainfo[0]];
                        }
                        echo "</td>";
                        echo "<td>";
                        if ($ainfo[5] == 'cp') echo displayGold($ainfo[4][$a]);
                        elseif ($ainfo[5] == 'ji') echo $ainfo[4][$a]." Ji";
                        elseif ($ainfo[5] == 'sp') echo $ainfo[4][$a]." Skill Points";
                        elseif ($ainfo[5] == 'pp') echo $ainfo[4][$a]." Profession Points";
                        else echo $ainfo[4][$a];
                        echo "</td>";
                        echo "</tr>";
                      }
                    }
                  }
                ?>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php
include('footer.htm');
?>