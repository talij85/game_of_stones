<?php

/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");
$numbofresults = 25;
$search=mysql_real_escape_string($_POST['search']);
$slast=mysql_real_escape_string($_POST['slast']);
$order=intval(mysql_real_escape_string($_GET['order']));
$time=time();
$clansearch = mysql_real_escape_string($_GET[clan]);
$world = mysql_real_escape_string($_GET[world]);
include_once("admin/charFuncs.php");
include_once("admin/locFuncs.php");

$wikilink = "Nearby";

// See if character is in a City
$isCity = 0;
$loc = mysql_fetch_array(mysql_query("SELECT id, name FROM Locations WHERE name='$char[location]'"));
if ($loc[id]) $isCity = 1;

$dw=0;
if ($isCity)
{ 
  $dw= $town_bonuses[dW];
  $query = "SELECT Users.id, Users.name, Users.lastname, Users.type, Users.level, Users.align, Users.gold, Users.location, Users.goodevil, Users.exp, Users.society, Users.soc_rank, Users.lastonline, Users.used_pts, Users.equip_pts, Users.stamina, Users.stamaxa, Users_stats.loc_ji".$loc[id]." FROM Users LEFT JOIN Users_stats ON Users.id=Users_stats.id";
}
else
{
  $query = "SELECT id, name, lastname, type, level, align, gold, location, goodevil, exp, society, soc_rank, lastonline, used_pts, equip_pts, stamina, stamaxa FROM Users";
}
if ($world) $query .= " WHERE 1";
elseif ($clansearch) $query .= " WHERE Users.society = '$clansearch'";
else $query .= " WHERE Users.location='".$char['location']."'";

// See if character is in a City
$isCity = 0;
$loc = mysql_fetch_array(mysql_query("SELECT id, name FROM Locations WHERE name='$char[location]'"));
if ($loc[id]) $isCity = 1;

//  HOW TO ORDER
if ($order==1) $query .= " ORDER BY gold DESC, exp DESC";
elseif ($order==3) $query .= " ORDER BY lastonline DESC, exp DESC";
elseif ($order==4 && $isCity)  $query .= " ORDER BY loc_ji".$loc[id]." DESC, exp DESC";
else $query .= " ORDER BY level DESC, exp DESC";

$searchpage = mysql_real_escape_string($_POST['pagenumber']);
$resultnumb = intval(mysql_real_escape_string($_GET['pagenumb']));
if ($resultnumb == '') $resultnumb = 0;

if (!$clansearch)
{
$soc_name = $char[society];
$society = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$soc_name'"));
$stance = unserialize($society['stance']);
}

$searchpage = intval($searchpage);
if ($searchpage > 0) $resultnumb = ($searchpage - 1) * $numbofresults;

// Unserialize battled array 
$find_battle = unserialize($char[find_battle]);

// SEARCH FOR CHAR

if ($search || $slast)
{
// SEARCH
$query1 = "SELECT name, lastname FROM Users WHERE name = '$search' AND lastname = '$slast'";

/* query the database */
$result5 = mysql_query($query1);
if (!($searchchar = mysql_fetch_array($result5)))
{
$query1 = "SELECT name, lastname FROM Users WHERE name LIKE '%$search%' AND lastname LIKE '%$slast%'";
$result5 = mysql_query($query1);
$searchchar = mysql_fetch_array($result5);
}
/* Allow access if a matching record was found*/
if ($searchchar)
{
$search = $searchchar[name];
$slast = $searchchar[lastname];
$message = "Asking around, you hear of someone named <a href='bio.php?name=$search&last=$slast&time=$time'>$search $slast</a>";
}
else $message = "Asking around for $search $slast, you are met only by blank stares and muttered curses.";
}

// DISPLAY LIST

// FIND WHERE YOU ARE ON LIST
$resultf = mysql_query($query);
$numchar2 = mysql_num_rows($resultf);
if ($resultnumb > $numchar2) {$resultnumb = intval($numchar2/$numbofresults) * $numbofresults; $resultf = mysql_query($query);}

if ($_GET['first'] == 1)
{
$y = 0;
while ($y < $numchar2 && ($thechar[name] != $name || $thechar[lastname] != $lastname) )
{
$thechar = mysql_fetch_array($resultf);
$y++;
}
$resultnumb = intval(($y-1)/$numbofresults) * $numbofresults;
}

$query .= " LIMIT $resultnumb,$numbofresults";
$result = mysql_query($query);
$numchar = mysql_num_rows($result);

if ($world) $message = "The World has a population of ".number_format($numchar2);

if ($message == '')
{
$message = str_replace('-ap-','&#39;',$char['location'])." has a population of ".number_format($numchar2);
if ($clansearch) $message = $clansearch." contains ".number_format($numchar2)." members";
}

//HEADER
include('header.htm');

$style="style='border-width: 0px; border-bottom: 1px solid #333333'";
?>

  <div class='row'>
    <div class="col-sm-12">
      <form name='duelform' action='duel.php' method='post'>
        <input type='hidden' name='enemyid' value='0' id='enemyid' />
      </form>
      <table class="table table-condensed table-striped table-clear table-responsive solid-back">
        <tr>
          <th width="40">&nbsp;</th>
          <th style='vertical-align: bottom;'><a class="btn btn-primary btn-md btn-block btn-wrap" href="look.php?first=1&order=3&clan=<?php echo $clansearch."&world=".$world; ?>">Name</a></th>
          <th style='vertical-align: bottom;' class='hidden-xs'><a class="btn btn-primary btn-md btn-block btn-wrap" href="#">Classes</a></td>
          <th style='vertical-align: bottom;' class='hidden-xs'><a class="btn btn-primary btn-md btn-block btn-wrap" href="#">Equipped</a></th>
          <th style='vertical-align: bottom;' class='visible-xs'><a class="btn btn-primary btn-md btn-block btn-wrap" href="#">Equip & Align</a></th>            
          <th style='vertical-align: bottom;' class='hidden-xs'><a class="btn btn-primary btn-md btn-block btn-wrap" href="#">Alignment</a></th>

    <?php 
      if ($isCity)
      {
    ?> 
          <th style='vertical-align: bottom;' class='hidden-xs' ><a class="btn btn-primary btn-md btn-block btn-wrap" href="look.php?first=1&order=4&clan=<?php echo $clansearch."&world=".$world; ?>">Local Ji</a></th>
          <th style='vertical-align: bottom;' class='visible-xs' ><a class="btn btn-primary btn-md btn-block btn-wrap" href="look.php?first=1&order=4&clan=<?php echo $clansearch."&world=".$world; ?>">Ji</a></th>          
    <?php 
      }
    ?>
          <th style='vertical-align: bottom;'><a class="btn btn-primary btn-md btn-block btn-wrap" href="look.php?first=1&order=1&clan=<?php echo $clansearch."&world=".$world; ?>">Coin</a></th>
          <th style='vertical-align: bottom;' class='hidden-xs'><a class="btn btn-primary btn-md btn-block btn-wrap" href="look.php?first=1&order=0&clan=<?php echo $clansearch."&world=".$world; ?>">Level</a></th>
          <th style='vertical-align: bottom;' class='visible-xs'><a class="btn btn-primary btn-md btn-block btn-wrap" href="look.php?first=1&order=0&clan=<?php echo $clansearch."&world=".$world; ?>">Lvl</a></th>          
        </tr>

<!-- LIST ALL CHARACTERS ON PAGE -->
<?php
  $img_ar = Array("s1","w1");
  $x=0;

  while ( $listchar = mysql_fetch_array( $result ) )
  {
    if ($listchar['id'] == $id) $classn="info";
    elseif ($listchar['lastonline'] >= time()-900) $classn="success";
    else $classn="default";

?>
        <tr>
          <td>
          <?php
            if ($find_battle[$listchar[id]] <= time()-intval(300*(100+$dw)/100) && !$is_creator && ($listchar[id] != $char[id])) // DUEL FIX: 0->300
            {
              $duel_link = "javascript:submitFormLook(".$listchar[id].")";
              echo "<a class='btn btn-danger btn-xs btn-block btn-wrap' href=\"$duel_link\">Duel</a>";
            }
            elseif ($find_battle[$listchar['id']] > time() - intval(300*(100+$dw)/100)) 
            {
              $time_to_battle=intval((intval(300*(100+$dw)/100) - (time() - $find_battle[$listchar['id']]))/60 + 1);
              echo "<a class='btn btn-danger btn-xs btn-block btn-wrap disabled' href='#'>".$time_to_battle."m</a>";
            }
          ?>              
          </td>
          <td align='center'>
            <a class="btn btn-<?php echo $classn;?> btn-xs btn-block btn-wrap" href="bio.php?name=<?php echo $listchar['name']."&last=".$listchar['lastname']."&time=".time(); ?>">
          <?php
            $hasSeal = mysql_num_rows(mysql_query("SELECT id FROM Items WHERE type=0 && owner='$listchar[id]'"));
            if ($hasSeal)
              echo "<img src='images/classes/4.gif' height=18 width=18/>&nbsp;"; 
            $lastBattle = mysql_fetch_array(mysql_query("SELECT id, starts, ends, winner FROM Contests WHERE type='99' "));
            if ($lastBattle != 0)
            {
              if ($listchar[align] > 0)
                echo "<img src=images/1.gif>&nbsp;";
              else
                echo "<img src=images/0.gif>&nbsp;";
            }
            else if ($stance[str_replace(" ","_",$listchar[society])] && $listchar[id] != $id) 
              echo "<img src=images/".$img_ar[$stance[str_replace(" ","_",$listchar[society])]-1].".gif>&nbsp;"; 
            echo $listchar['name']." ".$listchar['lastname']; 
            if ($listchar[soc_rank] == 1) 
              echo "<img src='images/SocLead.gif' height=20 width=28/>";
            elseif ($listchar[soc_rank] == 2) 
              echo "<img src='images/SocSub.gif' height=20 width=28/>";
          ?>
            </a>
          </td>  
          <td class="hidden-xs" align='center'>
            <?php
              $classes=unserialize($listchar[type]);
              for ($i = 0; $i<count($classes); $i++)
              {
                echo "<img src='images/classes/".$classes[$i].".gif' alt='".$classes[$i]."' title='".$char_class[$classes[$i]]."' height='15' width='15'>";
              }
            ?>
          </td>          
          <td align='center' class='hidden-xs'>
          <?php
            $cstr = getStrength($listchar[used_pts], $listchar[equip_pts], $listchar[stamina], $listchar[stamaxa]);
            $slvl = getStrengthLevel($cstr);
            echo $slvl;
          ?>                
          </td>
          <td align='center' class='hidden-xs'>  
          <?php   
            $alignnum = getAlignment($listchar[align]);
            $lalign = getAlignmentText($alignnum);
            echo $lalign;
          ?>                
          </td>
          <td align='center' class='visible-xs'>
          <?php
            echo $slvl." ".$lalign;
          ?>                
          </td>          
          <?php
            if ($isCity)
            {
          ?>
          <td align='center'>  
          <?php
            $jivar = 'loc_ji'.$loc[id];
            echo $listchar[$jivar];
          ?>                
          </td>
          <?php      
            }
          ?> 
          <td align='center'>
            <?php echo displayGold($listchar['gold']);?>
          </td>
          <td align='center'>
            <?php echo $listchar['level']; ?>
          </td>
        </tr>   
<?php
  $x++;
  if ($x == $numbofresults) {break;}
}

// SWITCH PAGES AND SEARCH
?>
      </table>
      <div class='row'>
        <?php
          $resultnumb1=$resultnumb + $numbofresults;
          $resultnumb2=$resultnumb - $numbofresults;
          $resultnumbh=$resultnumb + 1;
          if ($resultnumb > 0)
          {
        ?>
        <a class="btn btn-default btn-sm btn-wrap" href="look.php?clan=<?php echo $clansearch."&world=".$world; ?>&order=<?php echo "$order"; ?>&pagenumb=<?php echo "$resultnumb2&time=$time"; ?>">&#60;&#60; Previous</a>
        <?php
          }
        ?>
        <?php echo "$resultnumbh - $resultnumb1"; ?>
        <?php
          if ($resultnumb + $numbofresults < $numchar2)
          {
        ?>
          <a class="btn btn-default btn-sm btn-wrap" href="look.php?clan=<?php echo $clansearch."&world=".$world; ?>&order=<?php echo "$order"; ?>&pagenumb=<?php echo "$resultnumb1&time=$time"; ?>">Next &#62;&#62;</a></p>
        <?php
          }
        ?>
      </div>
      <div class='row'>
        <form class='form-inline' action="look.php?clan=<?php echo $clansearch."&world=".$world; ?>&order=<?php echo "$order"; ?>" method="post">
          <div class="form-group form-group-sm">
            <label for="pagenumber">Page:</label>
            <input type="text" name="pagenumber" id="pagenumber" value="<?php echo (intval($resultnumb/$numbofresults)+1); ?>" maxlength="3" size="2" class="form-control gos-form"/>
            <input type="submit" name="submit" value="Goto" class="btn btn-sm btn-primary" >
          </div>
        </form>
      </div>	
      <div class='row'>
        <form class='form-inline' action="look.php?first=1&order=<?php echo "$order"; ?>&clan=<?php echo $clansearch."&world=".$world; ?>" method="post">
          <div class="form-group form-group-sm">
            <input type="submit" name="submit" value="Find" class="btn btn-sm btn-default"/>
            <input type="text" name="search" id="search" class="form-control gos-form" style="text-align:right;"/>
            <label for="slast"> of house</label>
            <input type="text" name="slast" id="slast" class="form-control gos-form"/>
          </div>
        </form>
      </div>
    </div>
  </div>

<script type='text/javascript'>
  function submitFormLook(id)
  {
    document.duelform.enemyid.value = id;
    document.duelform.submit();
  }
</script>
<?php
include('footer.htm');
?>