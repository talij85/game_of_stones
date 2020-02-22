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

$iresult=mysql_query("SELECT * FROM Notes WHERE to_id='$id' AND del_to='0' AND type < 5 ORDER BY sent DESC LIMIT 0, 50");
$inbox = "";
$in_num = 0;
while ($inote = mysql_fetch_array($iresult))
{
  $inbox[$in_num++] = $inote;
}

$oresult=mysql_query("SELECT * FROM Notes WHERE from_id='$id' AND del_from='0' AND type < 4 ORDER BY sent DESC LIMIT 0, 50");
$outbox = "";
$out_num = 0;
while ($onote = mysql_fetch_array($oresult))
{
  $outbox[$out_num++] = $onote;
}

$wikilink = "Messages";

$resultnumb = mysql_real_escape_string($_GET['resultnumb']);
if ($resultnumb == "") $resultnumb = 0;
$resultnumbout = mysql_real_escape_string($_GET['resultnumbout']);
if ($resultnumbout == "") $resultnumbout = 0;
$resultnumblog = mysql_real_escape_string($_GET['resultnumblog']);
if ($resultnumblog == "") $resultnumblog = 0;

// update message check
mysql_query("UPDATE Users SET msgcheck='".time()."' WHERE id=$id");

$tab = mysql_real_escape_string($_GET['tab']);
$noteto = $_REQUEST['noteto'];
$tradeto = $_REQUEST['tradeto'];
$notesub = $_REQUEST['notesub'];
$note = $_REQUEST['note'];
$submitted = $_REQUEST['submitted'];
$rep_id = $_REQUEST['rep_id'];
$tid = mysql_real_escape_string($_GET['id']);
$acceptTrade = mysql_real_escape_string($_GET[accept]);
if ($rep_id == "") $rep_id=0;

// delete selected messages
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
  $message = "Deleted selected messages";
}

// delete all messages
if ( $_REQUEST['trashall'] )
{
  $x=0;
  while ($x < $in_num)
  {
    mysql_query("UPDATE Notes SET del_to='1' WHERE id='".$inbox[$x][id]."'");
    $x++;
  }
  $message = "All messages deleted";
}

// delete selected messages from outbox
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
  $message = "Deleted selected messages";
}

// delete all messages from outbox
if ( $_REQUEST['trashallout'] )
{
  $x=0;
  while ($x < $out_num)
  {
    mysql_query("UPDATE Notes SET del_from='1' WHERE id='".$outbox[$x][id]."'");
    $x++;
  }
}

// ACCEPT TRADE
if ($acceptTrade) 
{
  $tnote = mysql_fetch_array(mysql_query("SELECT * FROM Notes WHERE id='$note'"));
  if ($tnote)
  {
    $data=explode('|',$tnote[special]);
    if (intval($data[3])>time()) 
    {
      if ($char[gold]-$data[2]>=0) 
      {
        $x=0;
        $qitem = mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE id='".$data[1]."'"));
 
        if ($qitem[owner] == $data[0])
        {
          if ($qitem[society] == 0)
          {
            if ($qitem[istatus]==0 || $qitem[istatus]==-2) 
            {
              $listsize=mysql_num_rows(mysql_query("SELECT * FROM Items WHERE owner='$id' AND type<15"));
          
              if ($listsize<$inv_max) 
              {
                $qitem[owner]=$id;
                $result = mysql_query("UPDATE Items SET owner='$id', last_moved='".time()."' WHERE id='".$qitem[id]."'");
                $tnote[special]="";
                $tnote[body]=preg_replace('/\[.*\]/',"[trade completed]",$tnote[body]);
                $char[gold]-=$data[2];
                mysql_query("UPDATE Notes SET body='".$tnote[body]."', special='".$tnote[special]."' WHERE id=".$tnote[id]);
                mysql_query("UPDATE Users SET gold='".$char['gold']."' WHERE id=".$char[id]);
                mysql_query("UPDATE Users SET gold=gold+".$data[2]." WHERE id=".$data[0]);
                $message="Trade completed successfully";
              } else $message="You do not have enough space.";
            } else $message="The other character is using the item.";
          } else $message="The item now belongs to a clan and cannot be traded.";
        } else $message="The other character no longer has that item.";
      } else $message="You do not have enough gold.";
    } else $message="This trade offer expired ".number_format(intval((time()-$data[5])/3600)+1)." hours ago.";
  }
}

// SEND NOTE
else if ($note && time() - intval($char['lastpost']) > $wait_post && $char[id])
{
  $recipient = explode(" ", $noteto);
  $target = mysql_fetch_array(mysql_query("SELECT * FROM Users WHERE name = '$recipient[0]' AND lastname = '$recipient[1]'"));
  if ($target)  
  {
    if (strlen($note) < 1000)
    {
      if (!$bypass_check)
      { 
        $note = htmlspecialchars(stripslashes($note),ENT_QUOTES);
        $notesub = htmlspecialchars(stripslashes($notesub),ENT_QUOTES);        
      }
      if ((preg_match('/^[-a-z0-9+.,!@(){}<>^$\[\]*_&=#:\/%;?\s]*$/i',$note) && preg_match('/^[-a-z0-9+.,!@(){}<>^$\[\]*_&=#:\/%;?\s]*$/i',$notesub)) || $bypass_check)
      {
        $cid = $target[id];
        if ($notesub == '') $notesub= "(none)";
        if (!$bypass_check) $note=strip_tags($note);
        $root = 0;
        if ($rep_id)
        {
          $repnote = mysql_fetch_array(mysql_query("SELECT * FROM Notes WHERE id='$rep_id'"));
          if ($repnote[root] != 0) $root= $repnote[root];
          else $root= $repnote[id];
        }
        $result = mysql_query("INSERT INTO Notes (from_id,to_id, del_from,del_to,type,root,   sent,        cc,subject,   body,   special) 
                                          VALUES ('$id',  '$cid','0',     '0',   '0', '$root','".time()."','','$notesub','$note','$note_extra')");
        
        mysql_query("UPDATE Users SET lastpost='".time()."' WHERE id='".$char['id']."'");
        mysql_query("UPDATE Users SET newmsg=newmsg+1 WHERE id=$cid");
        $char['lastpost'] = time();
        if ($message == '') $message = "Messenger dispatched with note";
      }
      else $message="What strange characters you used. Officials refuse to post your note";
    }
    else
    { 
      $message="Message is too wordy, the messenger refuses to carry so much weight";
    }
  }
  else
  {
    $message="The messanger gives you a confused look. Who is that?";
  }
}
else if ($note) $message = "You must wait 30 seconds between messages";

if ($submitted && !$note) 
{
  $message="Why would you send a blank message?";
  $tab=3;
}

// SET UP TRADE
$bypass_check=0;

if ($_GET[trade] && $_REQUEST[item]) 
{
  $charip = unserialize($char[ip]);
  $alts = getAlts($charip);
  $recipient = explode(" ", $tradeto);
  $target = mysql_fetch_array(mysql_query("SELECT * FROM Users WHERE name = '$recipient[0]' AND lastname = '$recipient[1]'"));
  if ($target)  
  {
    $username = $target[name]."_".$target[lastname];
    
    if (!$alts[$username]) 
    {
      $cid = $target[id];
      $tradeval=$_REQUEST[gold]*10000+$_REQUEST[silver]*100+$_REQUEST[copper];
      if ($_REQUEST[gold] <= 25)
      {
        if ($tradeval>0) 
        {
          $bypass_check=1;
          $itm=mysql_fetch_array(mysql_query("SELECT * FROM Items WHERE id='".$_REQUEST[item]."'"));
          $item_name=iname($itm);
          $link="[<a href=messages.php?accept=1&name=".$char[name]."&last=".$char[lastname]."&note=>Accept Offer</a>]";
          $ilink="<a href=javascript:doPopUp(&#39;".$itm[base]."&#39;,&#39;".$itm[prefix]."&#39;,&#39;".$itm[suffix]."&#39;)>".$item_name."</a>";
          $note="<i><b>Trade offer:</b></i> $link<br/><br/>One $ilink in return for ".displayGold($tradeval,1)."<br/><br/><img src=\"items/".str_replace(" ","",$itm[base]).".gif\" />";
          $note_extra=$char[id]."|".$_REQUEST[item]."|".$tradeval."|".intval(time()+intval($_REQUEST[time]));
          $noteto = $_GET[name]." ".$_GET[last];
          $notesub = "Trade Offer";
          $message="Trade offer dispatched";
          $mytime=time();
 
          $result = mysql_query("INSERT INTO Notes (from_id,to_id, del_from,del_to,type,root,sent,         cc,subject,   body,   special) 
                                            VALUES ('$id',  '$cid','0',     '0',   '3', '0', '".$mytime."','','$notesub','$note','$note_extra')");
                                        
          // Update the message's body with the message's own id
          $request = mysql_fetch_array(mysql_query("SELECT * FROM Notes WHERE sent='".$mytime."' AND from_id='".$char[id]."'"));
          $link="[<a href=messages.php?accept=1&name=".$char[name]."&last=".$char[lastname]."&note=".$request[id].">Accept Offer</a>]";
          $request[body] = "<i><b>Trade offer:</b></i> $link<br/><br/>One $ilink in return for ".displayGold($tradeval,1)."<br/><br/><img src=\"items/".str_replace(" ","",$itm[base]).".gif\" />";
          mysql_query("UPDATE Notes SET body='".$request[body]."' WHERE id='".$request[id]."'");
        
          mysql_query("UPDATE Users SET lastpost='".time()."' WHERE id='".$char['id']."'");
          mysql_query("UPDATE Users SET newmsg=newmsg+1 WHERE id=$cid");
          $char['lastpost'] = time();
        } else $message="You must trade for some amount of coin";
      } else $message="You cannot ask for more than 25 gold for an item";
    } else {$message="The messenger gives you a confused look and hands the offer back to you.";}
  }
} 
else 
{ 
  $bypass_check=0; 
  if ($_REQUEST[gold]) $message="Invalid trade offer";
}


// SEND INVITE
if ($_GET['join'] != '' && $char[society])
{
  // Send Invite
  $societyo = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$char[society]' "));    

  if ($societyo[invite] == 1 || ($societyo[invite] == 2 && strtolower($societyo[leader]) == strtolower($char[name]) && strtolower($societyo[leaderlast]) == strtolower($char[lastname]))) 
  {
    $message = "Invitation Sent";
    $noteto = $_GET[name]." ".$_GET[last];
    $socname2 = str_replace(" ","+",$char[society]);
    $notesub="Clan Invitation";
    $link="<a href=joinclan.php?invite=1&name=$socname2>Accept Invitation</a>";
    $note="<b>Invitation to join $char[society]</b><br/><br/> $link";
    $note_extra=$char[society];
    
    $recipient = explode(" ", $noteto);
    $target = mysql_fetch_array(mysql_query("SELECT * FROM Users WHERE name = '$recipient[0]' AND lastname = '$recipient[1]'"));
    $mytime=time();
    if ($target)  
    {
      $cid = $target[id];
      $result = mysql_query("INSERT INTO Notes (from_id,to_id, del_from,del_to,type,root,sent,        cc,subject,   body,   special) 
                                        VALUES ('$id',  '$cid','0',     '0',   '0', '0', '".$mytime."','','$notesub','$note','$note_extra')");
                                        
      // Update the message's body with the message's own id
      $request = mysql_fetch_array(mysql_query("SELECT * FROM Notes WHERE sent='".$mytime."' AND from_id='".$char[id]."'"));
      $link="<a href=joinclan.php?invite=1&name=$socname2&note=".$request[id]."&invite=".floor($mytime/400).">Accept Invitation</a>";
      $request[body] = "<b>Invitation to join $char[society]</b><br/><br/> $link";
      mysql_query("UPDATE Notes SET body='".$request[body]."' WHERE id='".$request[id]."'");

      mysql_query("UPDATE Users SET newmsg=newmsg+1 WHERE id='$cid'");
    }
  }
  else $message = 'Why are you trying to send an invite?';
}

mysql_query("UPDATE Users SET newmsg=0 WHERE id=$id");

if ($message == '')
{
  $message = "Message Center";
}

$itmlist="";
$listsize=0;
$iresult=mysql_query("SELECT * FROM Items WHERE owner='$id' AND type<15 AND type>0");
while ($qitem = mysql_fetch_array($iresult))
{
  $itmlist[$listsize++] = $qitem;
}
  

// Regrab messages as you may have some new ones depending on your actions
$iresult=mysql_query("SELECT * FROM Notes WHERE to_id='$id' AND del_to='0' AND type < 5 ORDER BY sent DESC LIMIT 0, 50");
$inbox = "";
$in_num = 0;
while ($inote = mysql_fetch_array($iresult))
{
  $inbox[$in_num++] = $inote;
}

$oresult=mysql_query("SELECT * FROM Notes WHERE from_id='$id' AND del_from='0' AND type < 4 AND subject!='Welcome to GoS!' ORDER BY sent DESC LIMIT 0, 50");
$outbox = "";
$out_num = 0;
while ($onote = mysql_fetch_array($oresult))
{
  $outbox[$out_num++] = $onote;
}

if (!$tab) $tab = 1;

include('header.htm');
?>

  <div class="row solid-back">
    <div class='col-sm-12'>
      <div id="content">
        <ul id="msgtabs" class="nav nav-tabs" data-tabs="tabs">
          <li <?php if ($tab == 1) echo "class='active'";?>><a href="#in_tab" data-toggle="tab">Inbox</a></li>         
          <li <?php if ($tab == 2) echo "class='active'";?>><a href="#out_tab" data-toggle="tab">Outbox</a></li>
          <li <?php if ($tab == 3) echo "class='active'";?>><a href="#comp_tab" data-toggle="tab">Compose</a></li>
          <li <?php if ($tab == 4) echo "class='active'";?>><a href="#trade_tab" data-toggle="tab">Trade</a></li>
        </ul>

        <div class="tab-content">
          <div class="tab-pane <?php if ($tab == 1) echo "active";?>" id="in_tab">
            <p class='h5'>
          <?php
            if ($in_num==1) echo "One message stored  (".intval(100*$in_num/50)."%)";
            else echo "$in_num messages stored  (".intval(100*$in_num/50)."%)";
            echo "</p>";
            $x=0;
        
            if ($in_num > 0)
            {
          ?>
            <form name='delinform' id='delinform' method="post" action="messages.php">
              <div class="panel-group" id="accordion_in">
              <?php
                while ($x < $in_num)
                {
                  $y=$x;
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
                      <a class='col-xs-1 btn btn-xs btn-success' href='messages.php?rep_id=<?php echo $inbox[$y][id]; ?>&tab=3'>Reply</a>
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
            else echo "<p class='h4'>-No Messages-</p>";
          ?>
          </div>
          <div class="tab-pane <?php if ($tab == 2) echo "active";?>" id="out_tab">  
            <p class='h5'>
          <?php
            if ($out_num==1) echo "One message stored  (".intval(100*$out_num/50)."%)";
            else echo "$out_num messages stored  (".intval(100*$out_num/50)."%)";
            echo "</p>";
            $x=0;
  
            if ($out_num > 0)
            {
          ?>
            <form name='deloutform' id='deloutform' method="post" action="messages.php">
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
            else echo "<p class='h4'>-No Messages-</p>";
          ?>
          </div>
          <div class="tab-pane <?php if ($tab == 3) echo "active";?>" id="comp_tab">  
            <p class='h5'>Send Message:</p>
            <form class='form-horizontal' name='newmessage' action="messages.php" method="post">
            <?php
              $defaultsub = "";
              $repmessage="";
              $defaultto="";
              if ($tid)
              {
                $result = mysql_query("SELECT name, lastname FROM Users WHERE id='$tid'",$db);
                $target = mysql_fetch_array($result);
                $defaultto=$target['name']." ".$target['lastname'];
              } 
              if ($rep_id)
              {
                $repnote = mysql_fetch_array(mysql_query("SELECT * FROM Notes WHERE id='$rep_id'"));
                $defaultsub = "Re: ".str_replace("Re: ", "", $repnote[subject]);
                $sender = mysql_fetch_array(mysql_query("SELECT id,name,lastname FROM Users WHERE id='".$repnote[from_id]."'"));
                $defaultto = $sender[name]." ".$sender[lastname];
                $repmessage = $repnote[body];
              } 
            ?>      
              <div class="form-group form-group-sm">
                <label for='msgto' class='control-label col-sm-2'>To: </label>
                <div class='col-sm-10'>
                  <textarea id='msgto' class="form-control gos-form" name="noteto" rows="1" cols="100" wrap="soft" style="overflow:hidden"><?php echo $defaultto;?></textarea>
                </div>
              </div>
              <div class="form-group form-group-sm">
                <label for='msgsub' class='control-label col-sm-2'>Subject: </label>
                <div class='col-sm-10'>
                  <textarea id='msgsub' class="form-control gos-form" name="notesub" rows="1" cols="100" wrap="soft" style="overflow:hidden"><?php echo $defaultsub;?></textarea>
                </div>
              </div>
              <div class="form-group">
                <label for='msgnote' class='control-label col-sm-2'>Message: </label>
                <div class='col-sm-10'>
                  <textarea id='msgnote' class="form-control gos-form" name="note" rows="6"></textarea>
                </div>
              </div>
            <?php
              if ($rep_id)
              {
            ?>
              <div class='row'>
              <div class='col-sm-10 col-sm-push-2' align='left'>
                <p class='text-warning'>Original Message: <br/><i><?php echo $repmessage;?></i></p>
              </div>
              </div>
            <?php
              }
            ?>
              <input type="hidden" name="rep_id" value="<?php echo $rep_id;?>" />
              <input type="hidden" name="submitted" value="1" />
              <input type="submit" name="submit" id="showBut" value="Dispatch Message" class="btn btn-success" style="display: <?php if (time() - intval($charother['lastpost']) > $wait_post) echo "block"; else echo "none"; ?>"/>
            </form>
          </div>
          <div class="tab-pane <?php if ($tab == 4) echo "active";?>" id="trade_tab">
          <?php  
            $defaultto="";
            if ($tid)
            {
              $result = mysql_query("SELECT name, lastname FROM Users WHERE id='$tid'",$db);
              $target = mysql_fetch_array($result);
              $defaultto=$target['name']." ".$target['lastname'];
            } 
          ?>
            <p class='h5'>Send Trade Offer:</p>          
            <form class='form-horizontal' action="messages.php?trade=1" method="post">
              <div class="form-group form-group-sm">
                <label for='tradeto' class='control-label col-sm-2'>To: </label>
                <div class='col-sm-10'>
                  <textarea id='tradeto' class="form-control gos-form" name="tradeto" rows="1" cols="50" wrap="soft" style="overflow:hidden"><?php echo $defaultto;?></textarea>
                </div>
              </div>
              <div class="form-group form-group-sm">
                <label for='itmList' class='control-label col-sm-2'>Offer: </label>
                <div class='col-sm-10'>
                  <select name="item" id="itmList" size="1" class="form-control gos-form" onChange="updateItem(this.options[selectedIndex].value);">
                <?php
                  $have_item = 0;
                  for ($x=0; $x<$listsize; $x++) {
                    if (($itmlist[$x][istatus] == 0 || $itmlist[$x][istatus] == -2) && $itmlist[$x][society] == 0) 
                    {
                      echo "<option value='".$itmlist[$x][id]."'>".iname($itmlist[$x])."</option>\n";
                      $have_item=1; 
                    }
                  }
                  if (!$have_item) echo "<option value=''>No items</option>";
                ?>
                  </select>
                </div>
              </div>
              <div class="form-group form-group-sm">
                <label class='control-label col-sm-2'>For: </label>
                <div class='col-sm-10'>
                  <div class='form-inline' align='left'>
                    <img src='images/gold.gif'/>
                    <input type="text" name="gold" value="0" class="form-control gos-form" size="2" maxlength="2"/>
                    <img src='images/silver.gif'/>
                    <input type="text" name="silver" value="0" class="form-control gos-form" size="2" maxlength="2"/>
                    <img src='images/copper.gif'/>
                    <input type="text" name="copper" value="1" class="form-control gos-form" size="2" maxlength="2"/>
                  </div>
                </div>
              </div>
              <div class="form-group form-group-sm form-inline">
                <label for='tradetime' class='control-label col-sm-2'>Expires In: </label>
                <div class='col-sm-10' align='left'>
                  <select name="time" class="form-control gos-form">
                    <option value="43200">Five days</option>
                    <option selected value="259200">Three days</option>
                    <option value="86400">One day</option>
                    <option value="21600">Six hours</option>
                    <option value="3600">One hour</option>
                  </select>
                </div>
              </div>
              <input type="Submit" name="submit" value="Dispatch Offer" class="btn btn-primary btn-sm"/>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

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
    document.getElementById("trashall").value = 1;
    document.getElementById("delinform").submit();
  }
  
  function submitFormDelAllOut() 
  {
    document.getElementById("trashallout").value = 1;
    document.getElementById("deloutform").submit();
  }
  
  var inv = new Array();
<?php
  for ($i=0; $i<$listsize; ++$i)
  {
    echo "  inv[".$itmlist[$i][id]."] = '".str_replace(" ","",$itmlist[$i][base])."';";
  }
?>  

  function updateItem(id) 
  {
    document.getElementById('itemImg').alt="items/"+inv[id]+".gif";
    document.getElementById('itemImg').src="items/"+inv[id]+".gif";
  }
</script>
<?php
include('footer.htm');
?>