<script language="Javascript">
var tinfos=new Array(5);
tinfos[0]="";
tinfos[1]="<br><br><center><font class='littletext'><i>Armsmen are your basic soldier types, trained in the ways of fighting. They are well-balanced in offense and defense.</i>";
tinfos[2]="<br><br><center><font class='littletext'><i>Wanderers travel the world, taking in the sites the world has to offer. They are used to watching their back and trust their luck when they have to.</i>";
tinfos[3]="<br><br><center><font class='littletext'><i>Woodsman are used to living off the land, building shelters in the woods. They use their skills to get the jump on their prey and make their hits count.</i>";
tinfos[4]="<br><br><center><font class='littletext'><i>Channelers are able to use the One Power, using weaves to do many wonders. They are blessed with a longer lifespan and others are often in awe of them, which has it's advantages.</i>";


var ninfos=new Array(13);
ninfos[0]="";
ninfos[1]="<center><font class='littletext'><i>Aiel are skilled and fierce warriors from the Wastes, who follow the ways of Ji'e'toh: Honor and Obligation.<br><br><u>Favored Terrain</u>: Wastelands<br><u>Favored Opponents</u>: Military><br><u>Unfavored Opponents</u>: Channelers<br><u>Unique</u>: Can become Chiefs (Male) or Wise Ones (Female)<br><u>Restrictions</u>: Cannot use swords, Cannot become Wolfkin or Whitecloak.</i>";
ninfos[2]="<center><font class='littletext'><i>Andorans are hard-working people, who are open, friendly, and willing to help out even strangers. But betray their friendliness or wrong them at your on risk!.<br><br><u>Favored Terrain</u>: Forests<br><u>Favored Opponents</u>: Shadowspawn<br><u>Unfavored Opponents</u>: Ruffians</i>";
ninfos[3]="<center><font class='littletext'><i>The Atha'an Miere, or Sea Folk, spend almost all their lives at sea, making their living mainly through trade and transport.<br><br><u>Favored Terrain</u>: Oceans<br><u>Favored Opponents</u>: Ruffians<br><u>Unfavored Opponents</u>: Military<br><u>Unique</u>: Can be Cargomaster (Male) or Sailmistress (Female)<br><u>Restrictions</u>: Cannot become Wolfkin or Whitecloak</i>";
ninfos[4]="<center><font class='littletext'><i>Borderlanders are hard, fearsome warriors, shaped by their close proximity to Shadow and their duty to protect the defense against the hordes of Shadowspawn that flow from the Blight.<br><br><u>Favored Terrain</u>: Wastelands<br><u>Favored Opponents</u>: Shadowspawn<br><u>Unfavored Opponents</u>: Exotic Animals</i>";
ninfos[5]="<center><font class='littletext'><i>Cairhienin have a tendancy to seek and create order, planning out their actions carefully. This also tends to lend to scheming and plotting to help keep themselves in control of situations.<br><br><u>Favored Terrain</u>: Mountains<br><u>Favored Opponents</u>: Animals<br><u>Unfavored Opponents</u>: Military</i>";
ninfos[6]="<center><font class='littletext'><i>Domani enjoy pleasure for the sake of pleasure, and know how to use the desires of others to their own advantage, making them skilled bargainers. Beware of their fierce tempers!<br><br><u>Favored Terrain</u>: Hills<br><u>Favored Opponents</u>: Exotic Animals<br><u>Unfavored Opponents</u>: Animal</i>";
ninfos[7]="<center><font class='littletext'><i>Ebou Dari are polite and easygoing, but follow a strict code of conduct and etiquette that can bewilder those unfamiliar with it. Offend them and they will be quick to pull a knife on you, especially women.<br><br><u>Favored Terrain</u>: Mountains<br><u>Favored Opponents</u>: Ruffians<br><u>Unfavored Opponents</u>: Channelers</i>";
ninfos[8]="<center><font class='littletext'><i>Illianers have a distaste for tyrants, but enjoy the luxuries and traditions typical among nobility. They take great pride in their history and their civilized society.<br><br><u>Favored Terrain</u>: Hills<br><u>Favored Opponents</u>: Military<br><u>Unfavored Opponents</u>: Shadowspawn</i>";;
ninfos[9]="<center><font class='littletext'><i>Ogier are non-humans with a deep connection with nature. Their longer lifespans lead to a more measured temper compared to humans, but as the saying goes: 'Anger the Ogier and bring mountains down on your head'.<br><br><u>Favored Terrain</u>: Forests<br><u>Favored Opponents</u>: Animals<br><u>Unfavored Opponents</u>: Exotic Animals<br><u>Unique</u>: Can be Treesingers or Deathwatch Guard<br><u>Restrictions</u>: Cannot Channel, Cannot become Wolfkin or Whitecloak</i>";
ninfos[10]="<center><font class='littletext'><i>Seanchan have a rigid class structure with the belief that everyone has a place in which to serve and they belong in their place. They have a strong sense of honor, where a word of honor once given is considered absolute.<br><br><u>Favored Terrain</u>: Oceans<br><u>Favored Opponents</u>: Channelers<br><u>Unfavored Opponents</u>: Shadowspawn<br><u>Unique</u>: Can be Deathwatch Guard<br><u>Restrictions</u>: Cannot become Whitecloak</i>";
ninfos[11]="<center><font class='littletext'><i>Tairens have a strong conviction and self-esteem that guide them through their lives. This is particularly strong among the nobility, who often believe they have the right to take whatever they desire.<br><br><u>Favored Terrain</u>: Plains<br><u>Favored Opponents</u>: Channelers<br><u>Unfavored Opponents</u>: Animals</i>";
ninfos[12]="<center><font class='littletext'><i>Taraboners have a proud heritage, which they claim dates back to the Age of Legends. They are well known for their custom of concealing their faces and their complex government leads them to enjoy political and social intrigue.<br><br><u>Favored Terrain</u>: Plains<br><u>Favored Opponents</u>: Exotic Animals<br><u>Unfavored Opponents</u>: Ruffians</i>"; 

function swapinfo()
{
  var n = document.getElementById("nation");
  var t = document.getElementById("type");
  var nv = n.options[n.selectedIndex].value;
  var tv = t.options[t.selectedIndex].value;
 

  document.getElementById('info').innerHTML=ninfos[nv]+tinfos[tv];
}

function setClass()
{
  var newElem = document.getElementById('type');
  newElem.options.length = 0;
  var nat = document.getElementById('nation').value;
  var crange = 3;
  if (nat == 9) crange =3;
  var ctypes = new Array('-Select-','Armsman','Wanderer','Woodsman','Channeler');
  
  for (var i=0; i<=crange; i++) 
  {
    tmp = ctypes[i];
    newElem.options[newElem.options.length] = new Option(tmp,i);
  }
  swapinfo();
}

function setItem()
{
  var newElem = document.getElementById('item');
  newElem.options.length = 0;
  var type = document.getElementById('type').value;
  var nat = document.getElementById('nation').value;
  var irange = 5;
  if (type == 4) irange =6;
  var items = new Array('-Select-','Sword','Axe','Spear','Bow','Bludgeon','Weaves');
  
  for (var i=0; i<=irange; i++) 
  {
    if (!(nat==1 && i==1))
    {
      tmp = items[i];
      newElem.options[newElem.options.length] = new Option(tmp,i);
    }
  }
  swapinfo();
}

</script>
<?php
if (!$_GET['message']) $title = "Create a Character";
else $title = $_GET['message'];
include('headerno.htm');
include('admin/charFuncs.php');
?>

<center>
<form method="post" action="adduser.php">
  <table border="0" cellspacing="25" width='500'>
    <tr>
      <td><label for="userid"><font class="littletext"><b>Character Name:<br></b></label></td>
      <td><input type="text" name="userid" id="userid" class="form" maxlength="10" /></td>
    </tr>
    <tr>
      <td><label for="last"><font class="littletext"><b>House Name:<br></b></label></td>
      <td><input type="text" name="last" id="last" class="form" maxlength="10" /></td>
    </tr>
    <tr> 
     <td><label for="password"><font class="littletext"><b>Password:<br></b></label></td>
     <td><input type="password" name="password" id="password" class="form" maxlength="10" /></td>
    </tr>
    <tr> 
      <td><label for="pass2"><font class="littletext"><b>Re-enter Password:<br></b></label></td>
      <td><input type="password" name="pass2" id="pass2" class="form" maxlength="10" /></td>
    </tr>
    <tr>
      <td><label for="email"><font class="littletext"><b>E-Mail:<br></b></label></td>
      <td><input type="text" name="email" id="email" maxlength="40" class="form" /></td>
    </tr>
<!--         INPUT CHARACTER SEX -->
    <tr>
      <td><label for="sex"><font class="littletext"><b>Gender:</b></label><br></b></td>
      <td>
        <input type="radio" name="sex" id="sex" VALUE="0" CHECKED/><font class="littletext">Male<br>
        <input type="radio" name="sex" id="sex" VALUE="1" /><font class="littletext">Female
      </td>
    </tr>
    <tr>
      <td><label for="dark"><font class="littletext"><b>Nationality:</b></label><br></b></td>
      <td>
        <select id="nation" name="nation" onChange="javascript:setClass();">
          <option value='0'>-Select-</option>
          <?php          
            for ($i=1; $i <= 12; $i++)
            {
              echo "<option value='".$i."'>".$nationalities[$i]."</option>";
            }
          ?>
        </select>
      </td>
    </tr>
    <tr>
      <td><label for="type"><font class="littletext"><b>Class:</b></label><br></b></td>
      <td><select id="type" name="type" onchange="javascript:setItem();"></select></td>
    </tr>
    <tr>
      <td><label for="item"><font class="littletext"><b>Item Focus:</b></label><br></b></td>
      <td><select id="item" name="item" onchange="javascript:swapinfo();"></select></td>
    </tr>    
    <tr>
      <td colspan=2>
        <div id='info'>&nbsp;</div>
      </td>
    </tr>
  </table>
<!-- -->
<br>
<center>
<table class='blank'><tr><td>
<input type="checkbox" name="nocrap">&nbsp;
</td><td class='littletext'>
I will act considerately towards all players.<br><font class='littletext_f'>(no foul language, cheating, stealing clans/characters, etc)
</td>
</tr><tr>
<td></td><td><br></td>
</tr>
<tr>
<td>
<input type="checkbox" name="noalt">&nbsp;
</td>
<td class='littletext'>
I will not create more than 5 characters at once in a single version,<br>nor will create characters for the purpose of furthering any other character. <br><font class='littletext_f'>(and realize that if I get caught doing so could cost me all my characters)
</td></tr>
</table>
<br>
<!-- -->
<br>
<input type="Submit" name="submit" value="Create New Character" class="form">
<!--&nbsp;&nbsp; <b>or</b> &nbsp;&nbsp;
<input type="Submit" name="transfer" value="Transfer Old Character" class="form">-->
</form>
</p>
</center>

<br><br><br><br><br><br>

<script language="Javascript">
swapinfo();
</script>
<?php

include('footer.htm');
?>