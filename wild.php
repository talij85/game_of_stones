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
    parent.document.location='npc.php?row='+rowIndex+'&col='+cellIndex;
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

  onload=mapTable;

</script>

<?php

$message=mysql_real_escape_string($_GET['message']);
$isHorde=0;

$soc_name = $char['society'];
$loc_name = str_replace('-ap-','&#39;',$char['location']);
$loc_query = $char['location'];
 
$surrounding_area=$map_data[$char['location']];

// LOAD SOCIETY TABLE
$location = mysql_fetch_array(mysql_query("SELECT * FROM Locations WHERE name='$loc_query'"));

// UPDATE NUMBER OF MEMEBERS
$resultf = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM Users WHERE location='$loc_query' "));
$numchar = $resultf[0];
$myHorde=0;
?>
  <div class='row'>
    <div class='col-md-4 col-md-push-8'>
      <div class="panel panel-warning">
        <div class="panel-heading">
          <h3 class="panel-title"><?php echo "$loc_name"; ?></h3>
        </div>
        <div class="panel-body abox">  
          <p>Number of characters: <?php echo "$numchar"; ?></p>

  <?php
    $result3 = mysql_query("SELECT * FROM Hordes WHERE done='0' AND location='$char[location]'");
    while ($myHorde = mysql_fetch_array( $result3 ) )
    {
      $npc_info = unserialize($myHorde[npcs]);
      $hordemsg = "";
      for ($n=0; $n < 1; $n++)
      {
        if ($n==0) $hordemsg .= "A "; else $hordemsg.= " and a ";
        $hordemsg .= $horde_types[$npc_info[$n][0]]." of ".$npc_info[$n][0]."s";
      }
      $tminus = intval(($myHorde[ends]*3600-time())/60);
      if (count($npc_info)==1) $hordemsg .= " is "; else $hordemsg.= " are ";              
      $hordemsg .= "heading towards ".str_replace('-ap-','&#39;',$myHorde[target]).". Attack in ".displayTime($tminus,0,1)."!";
      echo "<p><a href='hordeinfo.php' class='evil'>".$hordemsg."</a></p>";
      $isHorde=$myHorde;
    }
  ?>
        </div>
      </div>
    </div>
    <div class='col-md-8 col-md-pull-4'>
      <div class='row'>
      <?php
        $hunt =0;
        $myquests= unserialize($char[quests]);
        if ($myquests)
        {
          foreach ($myquests as $c_n => $c_s)
          {
            if ($c_s[0] == 1)
            {
              $quest = mysql_fetch_array(mysql_query("SELECT * FROM Quests WHERE id='$c_n'"));
              if ($quest[expire] > (time()/3600) || $quest[expire] == -1)
              { 
                if ($quest[type] == 1)
                {
                  $goals = unserialize($quest[goals]);
                  if (strtolower($goals[2]) == strtolower($char[location]) && $myquests[$c_n][1]< $goals[0])
                  {
                    $hunt = 1;
                  }
                }
                else if ($quest[type] == $quest_type_num["Horde"])
                {
                  $goals = unserialize($quest[goals]);
                  if (strtolower($goals[2]) == strtolower($char[location]) && $myquests[$c_n][1] > 0)
                  {
                     $hunt = 1;
                  }
                }
              }
            }
          }
        }
  
        // add estate check
        $isEstate=1;
          
        $links=1+$hunt+$isEstate;
        if ($isHorde[id])
        {
          $links++;
          if ($isHorde[army_done] == 0) $links++;
        }
      ?>
<table cellpadding=0 cellspacing=0 width='98%' class='table-clear'>
  <tr>
<?php
  if ($links < 4)
  {  
?>  
    <td width='4%'><img src="images/cmpctpillar.jpg" class='hidden-xs img-optional'></td>
    <td width='15%'>
      &nbsp;
    </td>
<?php
  }
?>
    <td width='4%'><img src="images/extpillar.jpg" class='hidden-xs img-optional'></td>
    <td width='15%'>
      <div align='center' onClick="parent.document.location='npc.php'">
        <img class='img-responsive' src="images/explore1.gif" alt="Explore" onMouseover="this.src='images/explore2.gif'" onMouseout="this.src='images/explore1.gif'">
      </div>
    </td>
    <td width='4%'><img src="images/extpillar.jpg" class='hidden-xs img-optional'></td>
<?php
  if ($hunt)
  { 
?>
    <td width='15%'>
      <div align='center' onClick="parent.document.location='npc.php?hunt=1'">
        <img class='img-responsive' src="images/hunt1.gif" alt="Hunt" onMouseover="this.src='images/hunt2.gif'" onMouseout="this.src='images/hunt1.gif'">
      </div>
    </td>
    <td width='4%'><img src="images/extpillar.jpg" class='hidden-xs img-optional'></td>
<?php
  }  
  if ($isHorde[id])
  {
?>
    <td width='15%'>
      <div align='center' onClick="parent.document.location='npc.php?horde=<?php echo $isHorde[id];?>'">
        <img class='img-responsive' src="images/horde1.gif" alt="Horde" onMouseover="this.src='images/horde2.gif'" onMouseout="this.src='images/horde1.gif'">
      </div>
    </td>
    <td width='4%'><img src="images/extpillar.jpg" class='hidden-xs img-optional'></td>
<?php
    if ($isHorde[army_done] == 0)
    {
?>
    <td width='15%'>
      <div align='center' onClick="parent.document.location='npc.php?horde=<?php echo $isHorde[id];?>&army=1'">
        <img class='img-responsive' src="images/armies1.gif" alt="Army" onMouseover="this.src='images/armies2.gif'" onMouseout="this.src='images/armies1.gif'">
      </div>
    </td>
    <td width='4%'><img src="images/extpillar.jpg" class='hidden-xs img-optional'></td>
<?php
    }
  }
  if ($links < 3)
  {
?>
    <td width='15%'>
      &nbsp;
    </td>
    <td width='4%'><img src="images/extpillar.jpg" class='hidden-xs img-optional'></td>
<?php
  }
?>
    <td width='15%'>
      <div align='center' onClick="parent.document.location='estate.php'">
        <img class='img-responsive' src="images/estates1.gif" name="6" border="0" alt="Estates" onMouseover="this.src='images/estates2.gif'" onMouseout="this.src='images/estates1.gif'">
      </div>
    </td>
    <td width='4%'><img src="images/extpillar.jpg" class='hidden-xs img-optional'></td>
<?php
  if ($links < 5)
  {
?>
    <td width='15%'>
      &nbsp;
    </td>
    <td width='4%'><img src="images/cmpctpillar.jpg" class='hidden-xs img-optional'></td>
<?php
  }
?>
  </tr>
</table>
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
  echo "<tr style='height:20%;'><td width='20%'>&nbsp;</td><td width='20%'>&nbsp;</td><td width='20%'>&nbsp;</td><td width='20%'>&nbsp;</td><td width='20%'>&nbsp;</td></tr>";
  echo "<tr style='height:20%;'><td width='20%'>&nbsp;</td><td width='20%'>&nbsp;</td><td width='20%'>&nbsp;</td><td width='20%'>&nbsp;</td><td width='20%'>&nbsp;</td></tr>";
  echo "<tr style='height:20%;'><td width='20%'>&nbsp;</td><td width='20%'>&nbsp;</td><td width='20%'>&nbsp;</td><td width='20%'>&nbsp;</td><td width='20%'>&nbsp;</td></tr>";
  echo "<tr style='height:20%;'><td width='20%'>&nbsp;</td><td width='20%'>&nbsp;</td><td width='20%'>&nbsp;</td><td width='20%'>&nbsp;</td><td width='20%'>&nbsp;</td></tr>";
  echo "<tr style='height:20%;'><td width='20%'>&nbsp;</td><td width='20%'>&nbsp;</td><td width='20%'>&nbsp;</td><td width='20%'>&nbsp;</td><td width='20%'>&nbsp;</td></tr>";
  echo "</table>";
  echo "</td></tr></table>";
?>
</div>
</div>