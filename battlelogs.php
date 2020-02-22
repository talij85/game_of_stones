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

$iresult=mysql_query("SELECT * FROM Notes WHERE to_id='$id' AND del_to='0' AND type = 9 AND sent > '".(time()-432000)."' ORDER BY sent DESC");
$inbox = "";
$in_num = 0;
while ($inote = mysql_fetch_array($iresult))
{
  $inbox[$in_num++] = $inote;
}

$oresult=mysql_query("SELECT * FROM Notes WHERE from_id='$id' AND del_from='0' AND type < 9 ORDER BY sent DESC LIMIT 0, 30");
$outbox = "";
$out_num = 0;
while ($onote = mysql_fetch_array($oresult))
{
  $outbox[$out_num++] = $onote;
}

$wikilink = "Battle+Logs";

// update log check
mysql_query("UPDATE Users SET newlog=0 WHERE id=$id");

$tab = mysql_real_escape_string($_GET['tab']);

// delete selected logs
if ( $_REQUEST['ifsubmitted'] == 1)
{
  $x=0;
  while ($x < $in_num)
  {
    if (isset($_REQUEST[$x]))
    {
      $nid = $_REQUEST[$x];
      mysql_query("UPDATE Notes SET del_to='1' WHERE id='$nid'");     
    }
    $x++;
  }
  $message = "Deleted selected defense logs";
}

// delete all logs
if ( $_REQUEST['trashall'] )
{
  $x=0;
  while ($x < $in_num)
  {
    mysql_query("UPDATE Notes SET del_to='1' WHERE id='".$inbox[$x][id]."'");
    $x++;
  }
  $message = "All defense logs deleted";
}

// delete selected logs from outbox
if ( $_REQUEST['ifoutsubmitted'] == 1)
{
  $x=0;
  while ($x < $out_num)
  {
    if (isset($_REQUEST[$x]))
    {
      $nid = $_REQUEST[$x];
      mysql_query("UPDATE Notes SET del_from='1' WHERE id='$nid'");     
    }
    $x++;
  }
  $message = "Deleted selected offense logs";
}

// delete all logs from outbox
if ( $_REQUEST['trashallout'] )
{
  $x=0;
  while ($x < $out_num)
  {
    mysql_query("UPDATE Notes SET del_from='1' WHERE id='".$outbox[$x][id]."'");
    $x++;
  }
  $message = "All offense logs deleted";
}

if ($message == '')
{
  $message = "Duel Logs";
}

// Regrab logs as you may have some new ones depending on your actions
$iresult=mysql_query("SELECT * FROM Notes WHERE to_id='$id' AND del_to='0' AND type = 9 AND sent > '".(time()-432000)."' ORDER BY sent DESC");
$inbox = "";
$in_num = 0;
while ($inote = mysql_fetch_array($iresult))
{
  $inbox[$in_num++] = $inote;
}

$oresult=mysql_query("SELECT * FROM Notes WHERE from_id='$id' AND del_from='0' AND type = 9 ORDER BY sent DESC LIMIT 0, 30");
$outbox = "";
$out_num = 0;
while ($onote = mysql_fetch_array($oresult))
{
  $outbox[$out_num++] = $onote;
}

?>
<script type="text/javascript">
  
  function submitFormDelIn() 
  {
    document.getElementById("delinform").submit();
  }
  
  function submitFormDelOut() 
  {
    document.getElementById("deloutform").submit();
  }
   
  function submitFormDelAllIn() 
  {
    document.getElementById("delall").submit();
  }
  
  function submitFormDelAllOut() 
  {
    document.getElementById("delallout").submit();
  }
</script>
<?php
if (!$tab) $tab = 1;
include('header.htm');
?>

  <div class="row solid-back">
    <div class='col-sm-12'>
      <div id="content">
        <ul id="logtabs" class="nav nav-tabs" data-tabs="tabs">
          <li <?php if ($tab == 1) echo "class='active'";?>><a href="#def_tab" data-toggle="tab">Defense Logs</a></li>
          <li <?php if ($tab == 2) echo "class='active'";?>><a href="#off_tab" data-toggle="tab">Offensive Logs</a></li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane <?php if ($tab == 1) echo "active";?>" id="def_tab">
            <p class='h5'>
            <?php
              if ($in_num==1) echo "One log stored (".intval(100*$in_num/50)."%)";
              else echo "$in_num logs stored (".intval(100*$in_num/50)."%)";
              echo "</p>";
              $x=0;

              if ($in_num > 0)
              {
            ?>
            <form name='delinform' id='delinform' method="post" action="battlelogs.php">
              <div class="panel-group" id="accordion_in">
              <?php
                while ($x < $in_num)
                {
                  $y = $x;
                  if ($inbox[$y][id])
                  {
                    $currid = "div".$y;
                    $cursub = nl2br($inbox[$y][subject]);
                    $curnote=nl2br($inbox[$y][body]);
                    $senttime = "Seconds ago";
                    $minpast = intval((time()-$inbox[$y][sent])/60);
                    $senttime = displayTime($minpast,1,0);
                    $sender = mysql_fetch_array(mysql_query("SELECT id,name,lastname FROM Users WHERE id='".$inbox[$y][from_id]."'"));
                    $accord = "collapse".$y;                    
              ?>
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <div class='row'>
                      <input class='col-xs-1' type="checkbox" name="<?php echo "$y"; ?>" value="<?php echo $inbox[$y][id];?>"/>
                      <a class='col-xs-3 btn btn-primary btn-xs btn-wrap' href="bio.php?name=<?php echo $sender[name]."&last=".$sender[lastname]; ?>"><?php echo $sender[name]." ".$sender[lastname]." (".$senttime; ?>)</a>
                      <a class='col-xs-7 btn btn-default btn-xs btn-wrap' data-toggle="collapse" data-parent="#accordion_in" href="#<?php echo $accord;?>"><h4 class="panel-title"><?php echo $cursub; ?></h4></a>
                      <a class='col-xs-1 btn btn-xs btn-danger' href='replay.php?log=<?php echo $inbox[$y][id];?>&tab=3'>Replay</a>
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
                // DELETE MESSAGE FORMS
              ?>
              </div>              
              <input type="hidden" name="ifsubmitted" value="1"/>
              <a data-href="javascript:submitFormDelIn();" data-toggle="confirmation" data-placement="top" title="Discard selected messages from your inbox?" class="btn btn-danger btn-sm btn-wrap">Discard Checked</a>
              <input type="hidden" id='trashall' name="trashall" value="0"/>
              <a data-href="javascript:submitFormDelAllIn();" data-toggle="confirmation" data-placement="top" title="Discard all messages in your inbox?" class="btn btn-danger btn-sm btn-wrap">Discard All</a>
            </form>
          <?php               
              }
              else echo "<p class='h4'>-No Defense Logs-</p>";
          ?>
          </div>                
          <div class="tab-pane <?php if ($tab == 2) echo "active";?>" id="off_tab">  
            <p class='h5'>
          <?php
            if ($out_num==1) echo "One log stored  (".intval(100*$out_num/50)."%)";
            else echo "$out_num logs stored  (".intval(100*$out_num/50)."%)";
            echo "</p>";
            $x=0;
  
            if ($out_num > 0)
            {
          ?>
            <form name='deloutform' id='deloutform' method="post" action="battlelogs.php">
              <div class="panel-group" id="accordion_out">
              <?php
                while ($x < $out_num)
                {
                  $y = $x;
                  if ($outbox[$y][id])
                  {
                    $currid = "divo".$y;
                    $cursub = nl2br($outbox[$y][subject]);
                    $curnote=nl2br($outbox[$y][body]);
                    $senttime = "Seconds ago";
                    $minpast = intval((time()-$outbox[$y][sent])/60);
                    $senttime = displayTime($minpast,1,0);
                    $receiver = mysql_fetch_array(mysql_query("SELECT id,name,lastname FROM Users WHERE id='".$outbox[$y][to_id]."'"));
                    $accord = "collapseo".$y;
              ?>
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <div class='row'>
                      <input class='col-xs-1' type="checkbox" name="<?php echo "$y"; ?>" value="<?php echo $outbox[$y][id];?>"/>
                      <a class='col-xs-3 btn btn-primary btn-xs btn-wrap' href="bio.php?name=<?php echo $receiver[name]."&last=".$receiver[lastname]; ?>"><?php echo $receiver[name]." ".$receiver[lastname]." (".$senttime; ?>)</a>
                      <a class='col-xs-7 btn btn-default btn-xs btn-wrap' data-toggle="collapse" data-parent="#accordion_out" href="#<?php echo $accord;?>"><h4 class="panel-title"><?php echo $cursub; ?></h4></a>
                      <a class='col-xs-1 btn btn-xs btn-danger' href='replay.php?log=<?php echo $outbox[$y][id];?>&tab=3'>Replay</a>
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
                // DELETE MESSAGE FORMS
              ?>
              </div>
              <input type="hidden" name="ifoutsubmitted" value="1"/>  
              <a data-href="javascript:submitFormDelOut();" data-toggle="confirmation" data-placement="top" title="Discard selected messages from your outbox?" class="btn btn-danger btn-sm btn-wrap">Discard Checked</a>
              <input type="hidden" name="trashallout" value="0"/>
              <a data-href="javascript:submitFormDelAllOut();" data-toggle="confirmation" data-placement="top" title="Discard all messages in your outbox?" class="btn btn-danger btn-sm btn-wrap">Discard All</a>
            </form>
          <?php               
            }
            else echo "<p class='h4'>-No Offensive Logs-</p>";
          ?>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php
include('footer.htm');
?>