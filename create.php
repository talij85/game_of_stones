<?php
include_once("admin/charFuncs.php");
?>
<script language="Javascript">
var tinfos=new Array(5);
tinfos[0]="";
tinfos[1]="<br/><br/><center><i>Armsmen are your basic soldier types, trained in the ways of fighting. They are well-balanced in offense and defense.</i></center>";
tinfos[2]="<br/><br/><center><i>Wanderers travel the world, taking in the sites the world has to offer. They are used to watching their back and trust their luck when they have to.</i></center>";
tinfos[3]="<br/><br/><center><i>Outdoorsmen are used to living off the land, building shelters in the wild. They use their skills to get the jump on their prey and make their hits count.</i></center>";
tinfos[4]="<br/><br/><center><i>Healers are knowledgeable in the ways of medicine, using herbs help themselves and others. They also know the weaknesses of the human body and can use that to their advantage.</i></center>";
tinfos[5]="<br/><br/><center><i>Darkfriends are followers of the Dark One, many of which have sworn to him for the promise of power and immortality.<br/><u>Special</u>: Alignment cannot become positive, regardless of actions.</i></center>";

var ninfos=new Array(19);
ninfos[0]="";
<?php
  for ($n=1; $n<19; $n++)
  {
    $nat = $nationalities[$n];
    echo "ninfos[$n]=\"<center><i>".$nat_info[$nat][0]."<br/><br/><u>Favored Terrain</u>: ".$nat_info[$nat][1]."<br/><u>Favored Opponents</u>: ".$nat_info[$nat][2]."<br/><u>Unfavored Opponents</u>: ".$nat_info[$nat][3]."<br/><u>Special</u>: ".$nat_info[$nat][4];
    if ($nat_info[$nat][5] != "")
      echo "<br/><u>Restrictions</u>: ".$nat_info[$nat][5];
    echo "</i></center>\";";
  }
?>
var nimgs= new Array(19);
<?php
  for ($n=0; $n<19; $n++)
  {
    echo "nimgs[$n]= new Array(2);";
    for ($nsex=0; $nsex<2; $nsex++)
    {
      if ($n==0)
      {
        echo "nimgs[$n][$nsex]= \"\";";
      }
      else
      {
        $nat = str_replace(" ","_",$nationalities[$n]);
        $sexChar = "M";
        if ($nsex) $sexChar = "F";    
        echo "nimgs[$n][$nsex]= \"<center><img id='avi' witdh='211' height='375' src=\\\"char/".$nat.$sexChar."2.jpg\\\"/></center>\";";
      }
    }
  }
?>

function swapimg()
{
  var n = document.getElementById("nation");
  var nv = n.options[n.selectedIndex].value;
  var s = document.getElementsByName('sex');
  var sv = 0;
  if (s[1].checked) {sv = 1;}
 
  document.getElementById('myimg').innerHTML=nimgs[nv][sv];
}

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
  var crange = 4;
  var ctypes = new Array('-Select-','Armsman','Wanderer','Outdoorsman','Healer','Darkfriend');
  
  for (var i=0; i<=crange; i++) 
  {
    tmp = ctypes[i];
    newElem.options[newElem.options.length] = new Option(tmp,i);
  }
  newElem.options[newElem.options.length] = new Option(ctypes[5],5);
  swapinfo();
  swapimg();
}

function setItem()
{
  var newElem = document.getElementById('item');
  newElem.options.length = 0;
  var type = document.getElementById('type').value;
  var nat = document.getElementById('nation').value;
  var irange = 8;
  if (nat == 13) irange = 7;
  var items = new Array('-Select-','Sword','Axe','Spear','Bow','Bludgeon','Knives','Shield','Weaves');
  
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
$title = "Create a Character";
$message = "Create a Character";
$skipVerify = 1;
include('header.htm');
?>

<form role="form" action="adduser.php" method="post">
  <div class="row">
    <div class="col-sm-4">
      <div class="form-group form-group-sm">
        <label for="userid">Character Name:</label>
        <input type="text" name="userid" id="userid" class="form-control gos-form" maxlength="15" size="25" />
      </div>
      <div class="form-group form-group-sm">
        <label for="last">House Name:</label>
        <input type="text" name="last" id="last" class="form-control gos-form" maxlength="15" size="25" />
      </div>
      <div class="form-group form-group-sm">
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" class="form-control gos-form" maxlength="15" size="25" />
      </div>
      <div class="form-group form-group-sm">       
        <label for="pass2">Confirm Password:</label>
        <input type="password" name="pass2" id="pass2" class="form-control gos-form" maxlength="15" size="25" />
      </div>
      <div class="form-group form-group-sm">        
        <label for="email">E-Mail:</label>
        <input type="text" name="email" id="email" maxlength="40" size="25" class="form-control gos-form" />
      </div>
      <div class="form-group form-group-sm" align='center'>
        <label for="sex">Gender:</label><br/>
        <label class="radio-inline"><input type="radio" name="sex" id="sex" VALUE="0" onchange="javascript:swapimg();" CHECKED/>Male</label>
        <label class="radio-inline"><input type="radio" name="sex" id="sex" VALUE="1" onchange="javascript:swapimg();" />Female</label>
      </div><br/>
      <div class="form-group form-group-sm">
        <label for="nation">Nationality:</label>
        <select id="nation" name="nation" class="form-control gos-form" onChange="javascript:setClass();">
          <option value='0'>-Select-</option>
          <?php          
            for ($i=1; $i <= 18; $i++)
            {
              echo "<option value='".$i."'>".$nationalities[$i]."</option>";
            }
          ?>
        </select>
      </div>
      <div class="form-group form-group-sm">        
        <label for="type">Class:</label>
        <select id="type" name="type" class="form-control gos-form" onchange="javascript:setItem();"></select>
      </div>
      <div class="form-group form-group-sm">        
        <label for="item">Item Focus:</label>
        <select id="item" name="item" class="form-control gos-form" onchange="javascript:swapinfo();"></select>
      </div> 
    </div>
    <div class="col-sm-8 solid-back">       
      <div id='myimg'>&nbsp;</div>
      <div id='info'>&nbsp;</div>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12">
      <div class="checkbox">
        <label><input type="checkbox" name="nocrap">
        <b>I have read and agree to the following rules (<a href='http://talij.com/goswiki/index.php?title=Official_Rules' target="_blank">More details these rules can be found here</a>):</b></label>
        <p>1. I will act considerately towards all players.<br/>
        2. <strike>I will not create more than 2 characters (4 for donors) at once.</strike><br/>
        3. I will not create characters for the purpose of furthering other characters or clans.<br/>
        4. I will report any bugs or exploits to The Creator immediately.<br/>
        </p>
      </div>
      <input type="Submit" name="submit" value="Create New Character" class="btn btn-success">
    </div>
  </div> 
</form>  

<script language="Javascript">
swapinfo();
</script>
<?php

include('footer.htm');
?>