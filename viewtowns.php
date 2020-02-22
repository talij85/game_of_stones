<?php

/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/locFuncs.php");

$wikilink = "Controlling+Cities";

$time=time();
$sort= $_REQUEST['sort'];

if ($message == '') $message = "City Information";

//HEADER
include('header.htm');
?>
  <div class="row solid-back">
    <div class="row">
      <form name="sortForm" action="viewtowns.php" method="post">
        <input type="hidden" name="sort" id="sort" value="" />
      </form>
<table class='table table-responsive table-hover table-clear small'>
  <tr>
    <th><a class="btn btn-success btn-sm btn-block" href="javascript:resortForm('1');">City</a></th>
    <th><a class="btn btn-success btn-sm btn-block" href="javascript:resortForm('2');">Order</a></th> 
    <th class='hidden-xs'><a class="btn btn-default btn-sm btn-block" href="#">Defense</a></th>
    <th class='hidden-xs'><a class="btn btn-default btn-sm btn-block" href="#">Pop.</a></th>     
    <th class='hidden-xs'><a class="btn btn-default btn-sm btn-block" href="#">Shop</a></th>
    <th><a class="btn btn-success btn-sm btn-block" href="javascript:resortForm('3');">Ruler</a></th>
    <th><a class="btn btn-default btn-sm btn-block" href="#">Rival</a></th>
  </tr>
<?php
  updateSocScores();
  $y=0;  

  $sortBy = 'id';
  if ($sort == 1) $sortBy='name';
  else if ($sort == 2) $sortBy='myOrder';
  else if ($sort == 3) $sortBy='ruler';
  
  $result = mysql_query("SELECT id, name, shoplvls, clan_scores, ruler, myOrder, army, isDestroyed FROM Locations ORDER BY $sortBy");  
  while ($loc = mysql_fetch_array( $result ) )
  {
    if (!$loc[isDestroyed])
    {
      $himage = "";
      $result3 = mysql_query("SELECT id, target, location FROM Hordes WHERE done='0'");
      while ($myHorde = mysql_fetch_array( $result3 ) )  
      {
        if ($myHorde[target] == $loc[name]) 
        {
          $himage = "<img src='images/hunt_l.gif' border='0' alt='Horde: ' title='Horde gathering in ".$myHorde[location]."' width='20' style='vertical-align:middle'/> ";
        }
      }
    
      $sealid = $loc[id]+50000;
      $hasSeal = mysql_num_rows(mysql_query("SELECT id FROM Items WHERE type=0 && owner='$sealid'"));
?>
      <tr>
        <td align='center'>
        <?php
          $sealImg = "";
          if ($hasSeal)
            $sealImg = "&nbsp;<img src='images/classes/4.gif' height=15 width=15/>";
        ?>
          <a href='#' class='btn btn-xs btn-default btn-block btn-wrap'><?php echo $himage.str_replace('-ap-','&#39;',$loc[name]).$sealImg; ?></a>
        </td>
        <td align='center'><?php echo $loc[myOrder]; ?></td>
        <td align='center' class='hidden-xs'><?php echo $loc[army]; ?></td>  
        <?php
          $resultf = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM Users WHERE location='$loc[name]' "));
          $numchar = $resultf[0];    
        ?>
        <td align='center' class='hidden-xs'><?php echo number_format($numchar); ?></td>          
        <?php
          $total=0;
          $shoplvls=unserialize($loc['shoplvls']);
      
          $total = count($shoplvls);
        ?>
        <td align='center' class='hidden-xs'><?php echo ($total); ?></td>
        <?php
          $clanscores = unserialize($loc[clan_scores]);

          $ruler=$loc[ruler];
          $ruler_wins = 0;
          $rival = "No One";
          $rival_wins = 0;
          if ($clanscores)
          {
            foreach ($clanscores as $key => $value)
            {
              $tsoc = mysql_fetch_array(mysql_query("SELECT id, name FROM `Soc` WHERE id = '$key'"));
              if ($ruler=='No One' && $rival == 'No One')
              { 
                $ruler_wins = 100;
                $rival = $tsoc[name];
                $rival_wins = $value; 
              }
              else if ($ruler == $tsoc[name])
              {
                $ruler_wins = $value;
              }
              else if ($rival == "No One")
              {
                $rival = $tsoc[name];
                $rival_wins = $value;
              }
            }
          }  
        ?>
        <td align='center'>
        <?php  
          if ($ruler != "No One") 
            echo "<a class='btn btn-xs btn-wrap btn-block btn-primary' href='joinclan.php?name=".$ruler."'>".$ruler." <span class='badge'>".number_format($ruler_wins)."</span></a>";
          else 
            echo "<a class='btn btn-xs btn-wrap btn-block btn-default' href='#'>".$ruler."</a>";
        ?>
        </td>
        <td align='center'>
        <?php  
          if ($rival != "No One") 
            echo "<a class='btn btn-xs btn-wrap btn-block btn-danger' href='joinclan.php?name=".$rival."'>".$rival." <span class='badge'>".number_format($rival_wins)."</span></a>";
          else 
            echo "<a class='btn btn-xs btn-wrap btn-block btn-default' href='#'>".$rival."</a>";
        ?>
        </td>
      </tr>
<?php
    } // !isDestroyed
  } // while loc
?>
  <tr><td colspan='8' style='border-width: 0px; border-top: 1px solid #333333'></td></tr>
</table>
<?php
include('footer.htm');

?>