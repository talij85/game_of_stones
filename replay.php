<?php

include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/charFuncs.php");
include_once("admin/duelFuncs.php");
include_once("admin/locFuncs.php");
include_once('map/mapdata/coordinates.inc');

$wikilink = "Duels";
$redirect=0;
$rmsg='';
$battle_view = 1000;

$log_id = intval($_REQUEST[log]);
$dlog = mysql_fetch_array(mysql_query("SELECT * FROM Notes WHERE id='$log_id'"));

if (!$dlog[id] || $dlog[type] != 9)  
{
  $redirect=1;
  $rmsg='Invalid log selected!';
}
else if ($dlog[to_id] != $char[id] && $dlog[from_id] != $char[id])  
{
  $redirect=1;
  $rmsg='You can only replay duels you were a part of!';
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////

if (!$redirect)
{
  $bresult = unserialize($dlog[special]);
  $defend = mysql_fetch_array(mysql_query("SELECT id, name, lastname FROM Users WHERE id='".$dlog[to_id]."'"));
  $offend = mysql_fetch_array(mysql_query("SELECT id, name, lastname FROM Users WHERE id='".$dlog[from_id]."'"));
  // THE ACTUAL PAGE DRAWING
  $message = "<b>".$offend[name]." ".$offend[lastname]."</b> vs <b>".$defend[name]." ".$defend[lastname]."</b> - Replay";
  $array_gen = generate_duel_text($bresult);
}

include('header.htm');
?>
<form name="redirectForm" action="battlelogs.php" method="post">
  <input type='hidden' name='message' id='message' value=''>
</form>
<?php
if ($redirect)
{
?>
<SCRIPT LANGUAGE="JavaScript">
<!-- Begin
redirectMessage('<?php echo $rmsg;?>');
// End -->
</SCRIPT>
<?php
}

?>
  <div class="row solid-back">
    <div class='col-sm-12'>
      <table border=0 cellpadding=0 cellspacing=0 width='100%'>
        <tr>
          <td class='hidden-xs' rowspan=3 width='64'><img width='64' src='images/BattleBox/Left2.jpg' class='img hidden-xs img-optional'/></td>
          <td class='visible-md img-optional' width='110' style='background-image:url(images/BattleBox/TopLeftmdspcr.jpg); background-repeat: repeat-x;'></td>
          <td class='visible-lg img-optional' width='210' style='background-image:url(images/BattleBox/TopLeftwidespcr.jpg); background-repeat: repeat-x;'></td>
          <td class='hidden-xs' align='center' height='38' width='592'>
            <img src='images/BattleBox/topmid2.jpg' class='img hidden-xs img-optional' width='592' />
          </td>
          <td class='visible-md img-optional' width='110' style='background-image:url(images/BattleBox/TopRtmdspcr.jpg); background-repeat: repeat-x;'></td>
          <td class='visible-lg img-optional' width='210' style='background-image:url(images/BattleBox/TopRtwidespcr.jpg); background-repeat: repeat-x;'></td>
          <td class='hidden-xs' rowspan=3 width='64'><img width='64' src='images/BattleBox/Right2.jpg' class='img hidden-xs img-optional'/></td>
          <td class='visible-xs' rowspan=3></td>
          <td class='visible-xs' style='border-style: solid;' colspan=3></td>
          <td class='visible-xs' rowspan=3></td>
        </tr>
        <tr>
          <td class='solid-back' style='vertical-align: text-top;' height='155' colspan=3>
            <div class='row'>
              <div class='col-sm-3 col-md-2'>
                <div id="battleP1H" style="font-family: Verdana; font-size: 10px;"></div>
              </div>
              <div class='col-sm-6 col-md-8 hidden-xs'>
                <br/>
              </div>
              <div class='col-sm-3 col-md-2'>
                <div id="battleP2H" style="font-family: Verdana; font-size: 10px;"></div>
              </div>        
            </div>
            <div class='row'>
              <div class='col-md-2 hidden-sm hidden-xs'>
                <img src='images/BattleBox/OP.gif' class='img img-responsive img-optional'/>
              </div>
              <div class='col-sm-9 col-md-8'>
                <div id='battleBox2' style="font-family: Verdana; font-size: 11px;"></div>
              </div>
              <div class='col-sm-3 col-md-2'>
                <div id="battleImg" class='hidden-xs'></div>
              </div>        
            </div>
        </tr>
        <tr>
          <td class='visible-md img-optional' width='110' style='background-image:url(images/BattleBox/BLeftmdspcr.jpg); background-repeat: repeat-x;'></td>
          <td class='visible-lg img-optional' width='210' style='background-image:url(images/BattleBox/BLeftwidespcr.jpg); background-repeat: repeat-x;'></td>   
        <?php
          if ($mode != 1)
          {
        ?>
          <td align='center' height='37' style='background-image:url(images/BattleBox/botspacerleft.jpg); background-repeat: repeat-x;'><img width='256' src='images/BattleBox/botleft.jpg' class='img hidden-xs'/><a href="npc.php?hunt=<?php echo $rehunt;?>&horde=<?php echo $horde;?>&army=<?php echo $army;?>"><img width='80' src="images/BattleBox/botmid1.jpg" border="0" alt="Again" onMouseover="this.src='images/BattleBox/botmid2.jpg'" onMouseout="this.src='images/BattleBox/botmid1.jpg'"/></a><img width='256' src='images/BattleBox/botright.jpg' class='img hidden-xs'/>
          </td>
        <?php
          }
          else
          {
        ?>          
          <td align='center' height='37'>
            <a href="npc.php?hunt=<?php echo $rehunt;?>&horde=<?php echo $horde;?>&army=<?php echo $army;?>">
              <img width='80' src="images/BattleBox/botmid1.jpg" border="0" alt="Again" onMouseover="this.src='images/BattleBox/botmid2.jpg'" onMouseout="this.src='images/BattleBox/botmid1.jpg'"/>
            </a>
          </td>
        <?php
          }
        ?>                

          <td class='visible-md img-optional' width='110' style='background-image:url(images/BattleBox/BRtmdspcr.jpg); background-repeat: repeat-x;'></td>
          <td class='visible-lg img-optional' width='210' style='background-image:url(images/BattleBox/BRtwidespcr.jpg); background-repeat: repeat-x;'></td>          
        </tr>
      </table>
      <div class='panel-body battlebox'>
        <div class='row'>
          <div class='col-xs-10 col-xs-push-1'>
            <div id='battleBox' style="font-family: Verdana; font-size: 9px; color: #000000; "></div>
          </div>
        </div>
        <div class='row'>
          <div class='col-sm-12'>
          </div>
        </div>
      </div>      
    </div>    
  </div>
<?php
// JAVASCRIPT
?>
<SCRIPT LANGUAGE="JavaScript">
<!-- Begin

var myTurn = 0;
var myBattle = new Array();
<?php
echo $array_gen;

echo "var looping = 1;\n";

if ($redirect==0)
{
echo "var tlog = '".$bresult[0][tlog]."';\n";
echo "var maxHealth0= ".$bresult[0][ahp].";\n";
echo "var maxHealth1= ".$bresult[0][dhp].";\n";
?>
  window.onLoad = startBattle();
<?php
}
?>
function startBattle() 
{
  window.self.setInterval("updateBattle()",<?php echo $battle_view;?>);
}

function redirectMessage(msg)
{
  document.getElementById('message').value=msg;
  alert(msg);
  document.redirectForm.submit();
}

function updateBattle() 
{
  if (myBattle.length > myTurn) 
  {
    if (myTurn > 0) document.getElementById("battleBox").innerHTML = document.getElementById("battleBox2").innerHTML.replace(/battletext/g, 'battledtext')+document.getElementById("battleBox").innerHTML;
    document.getElementById("battleBox2").innerHTML = myBattle[myTurn][0];
    document.getElementById("battleImg").innerHTML = "<img class='img-optional' src='"+myBattle[myTurn][1]+"'>";
    document.getElementById("battleP1H").innerHTML = "<img src='images/health.gif' style='vertical-align:middle'>: "+myBattle[myTurn][2]+"/"+maxHealth0;
    document.getElementById("battleP2H").innerHTML = "<img src='images/health.gif' style='vertical-align:middle'>: "+myBattle[myTurn][3]+"/"+maxHealth1;
  }
  else if (looping)
  {
    document.getElementById("battleBox2").innerHTML = document.getElementById("battleBox2").innerHTML+tlog;
    looping = 0;
  }
  myTurn++;
}
// End -->
</SCRIPT>
<?php
$no_show_footer = 1;

include('footer.htm');
?>