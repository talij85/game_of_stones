<?php
/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");
$doit=mysql_real_escape_string($_GET['doit']);
$clan=trim(mysql_real_escape_string($_POST['clan']));
$flagStyle = mysql_real_escape_string($_POST['style']);
$flagColor = mysql_real_escape_string($_POST['color']);
$flagSigil = mysql_real_escape_string($_POST['sigil']);
$declared = mysql_real_escape_string($_POST['declared']);
$founded = mysql_real_escape_string($_POST['found']);
$id=$char[id];
$message = "Form a new Clan";

$wikilink = "Clan";

// MAKE CLAN

if ($doit == 1)
{

  // CHECK IF CLAN EXISTS ALREADY
  $query = "SELECT * FROM Soc WHERE name='$clan'";
  $resultb = mysql_query($query, $db);

  // IF CLAN DOESNT EXIST
  if ( !mysql_fetch_row($resultb)  && strlen($clan) <= 30 && !preg_match("/[^a-z ]+/i",$clan) && strlen($clan) > 2 && strtolower($clan) != strtolower("No One") && $flagStyle != "0" && $flagSigil != "0")
  {
    // SET DATABASE TABLES
    $ally = array ('0');
    $ally[str_replace(" ","_",$clan)] = 1;
    $array = serialize($array);
    $ally = serialize($ally);
    $flag = "Flag".$flagStyle."-".$flagColor.".gif";
    $sigil = $flagSigil.".gif";
    $area_score = serialize(array('0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0'));
    $area_rep = array('0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0');
    $area_rep[$founded] = 100;
    $area_reps = serialize($area_rep);
    $upgrades = serialize(array('0','0','0','0','0'));
    $office = array(0 => array('0'));
    $office[$founded][0] = '1';
    $offices = serialize($office);
    $querya = "INSERT INTO Soc (name,   leader, leaderlast, about,private_info,invite,members,allow,stance, blocked,score,area_score,   area_rep,    bank,inactivity,flag,   sigil,   upgrades,   offices,   align,declared) 
                        VALUES ('$clan','$name','$lastname','',   '',          '0',   '1',    '0',  '$ally','',     '0',  '$area_score','$area_reps','0', '5',       '$flag','$sigil','$upgrades','$offices',0,    '$declared')";
    $result = mysql_query($querya);
    $query = "SELECT * FROM Soc WHERE name='$clan'";
    $result = mysql_query($query);
    $society = mysql_fetch_array($result);
    $querya = "INSERT INTO messages (id, checktime, message) VALUES ('$society[id]','0','a:0:{}')";
    $result = mysql_query($querya);
    $querya = "UPDATE Users SET society='$clan', soc_rank='1' WHERE id='$id'";
    $result = mysql_query($querya);
    $ustats = mysql_fetch_array(mysql_query("SELECT * FROM Users_stats WHERE id='$id'"));
    $ustats[clans_joined]++;
    mysql_query("UPDATE Users_stats SET clans_joined='$ustats[clans_joined]' WHERE id='$id' ");  
    header("Location: $server_name/clan.php?time=$curtime");
    exit;
  }
}

if ($doit && strtolower($clan) == strtolower("No One")) 
  $message = "You're pretty smart thinking of that. Too bad I thought of it first.";
elseif ($doit && (strlen($clan) > 30 || preg_match("/[^a-z ]+/i",$clan) || strlen($clan) < 2) )
  $message = "Problem with Clan Name";
elseif ($doit && ($flagStyle =="0" || $flagSigil== "0"))
  $message = "You must select both a flag and sigil";
elseif ($doit) 
  $message = "Clan already exists";

include('header.htm');

?>
  <div class="row solid-back">
    <br/>
<?php
// MAKE CLAN SCREEN
if ($char['society'] == '')
{
?>
<!-- CLAN FORM -->
    <form method="post" action="makeclan.php?doit=1">
      <div class="col-sm-6">
        <div class="form-group form-group-sm">
          <label for="clan">Clan Name:</label>
          <input type="text" name="clan" id="clan" class="form-control gos-form"  maxlength="30" />
        </div>
        <div class="form-group form-group-sm">
          <label for="declared">Declare Alignment:</label>
          <select id="declared" name='declared' class="form-control gos-form">
            <option value="0">Neutral</option>
            <option value="1">Light</option>
            <option value="-1">Shadow</option>
          </select>
        </div>
        <div class="form-group form-group-sm">
          <label for="found">Founding City:</label>
          <select id="found" name='found' class="form-control gos-form">
            <option value="0">-Select-</option>
          <?php
            $result = mysql_query("SELECT id, name FROM Locations ORDER BY name"); 
            while ($city = mysql_fetch_array( $result ) )
            {
              echo "<option value='$city[id]'>$city[name]</option>";
            }
          ?>
          </select>
        </div>
        <div class="form-group form-group-sm">
          <label for="style">Clan Flag:</label>
          <select id="style" name='style' class="form-control gos-form" onChange="javascript:setColors();">
            <option value="0">-Select-</option>
          <?php
            for ($i=1; $i<18; $i++)
            {
              echo "<option value='$i'>Style $i</option>";
            }
          ?>
          </select>
          <select id="color" name='color' class="form-control gos-form" onChange="javascript:setFlag();"></select>
        </div>
        <div class="form-group form-group-sm">
          <label for="sigil">Sigil:</label>
          <select id="sigil" name='sigil' class="form-control gos-form" onChange="javascript:setSigil();">
            <option value="0">-Select-</option>
          <?php
          $sigilList = array(
            "Adam", "Aes Sedai", "Ale", "Anchor 1", "Anchor 2", "Anvil", "Badger 1", "Badger 2", "Bear 1", "Bear 2", "Buckler 1", "Buckler 2", "Buckler 3", "Buckler 4", "Buckler 5",
            "Bull 1", "Bull 2", "Bull 3", "Crane", "Crescents", "Crossed 1", "Crossed 2", "Crossed 3", "Crossed 4", "Crossed 5", "Crossed 6", "Crossed 7", "Crow", 
            "Crown 1", "Crown 2", "Crown 3", "Crown 4", "Cyclone", "Dice", "Dragon 1", "Dragon 2", "Eagle 1", "Eagle 2", "Eagle 3", "Fang", "Fireball", "Flame", 
            "Gate 1", "Gate 2", "Gauntlet 1", "Gauntlet 2", "Gold Bags", "Great Serpent", "Hammer", "Hand 1", "Hand 2", "Hand 3", "Hawk 1", "Hawk 2", "Hawk 3", "Hawk 4",
            "Heart Dagger", "Heron 1", "Heron 2", "Horn", "Horse 1", "Horse 2", "Horse 3", "Horse 4", "Horse 5", "Horse 6", "Horse 7", "Leaf 1", "Leaf 2", "Leopard",
            "Lightning Bolt", "Lion 1", "Lion 2", "Lion 3", "Lion 4", "Lion 5", "Lion 6", "Rat", "Raven", "Roses 1", "Roses 2", "Scales", "Serpent 1", "Serpent 2",
            "Shield", "Ship", "Skull 1", "Skull 2", "Skull 3", "Snakes Foxes", "Stars", "Sun", "Sword 1", "Sword 2", "Sword 3", "Sword 4", "Sword 5", "Sword 6",
            "Tablets", "Tower 1", "Tower 2", "Tower 3", "Tree 1", "Tree 2", "Volcano", "War Horse 1", "War Horse 2", "War Horse 3", "Wheel", "Wolf 1", "Wolf 2", "Wolf 3",);
          foreach ($sigilList as $sigil)
          {
            echo "<option value='".str_replace(' ', '', $sigil)."'>".$sigil."</option>";
          }
          ?>
          </select>
        </div>
      </div>
      <div class='col-sm-6'>
        <table height=197 width=160 class="table-clear">
          <tr><td id='display'>&nbsp;</td></tr>
        </table>
        <input type="Submit" name="submit" value="Form Clan" class="btn btn-sm btn-success">        
      </div>
    </form>
<?php 
}
else
{
?>
<center><p class='text-danger'>You must leave your current clan first before you can create a new one!</p></center>
<?php
}
?>
  </div>
<script type="text/javascript">
function setColors() 
{
<?php
$colorArray = array(
'1' => array('Black','Blue','Brown','DkBlue','DkRed','Green','GreenBlue','OliveGreen','Purple','Red','Yellow'),
'2' => array('Aqua','Black','Blue','Brown','Green','Purple','Red','Yellow'),
'3' => array('Black','Blue','Brown','DkPurple','Green','Purple','Red','Yellow'),
'4' => array('Blue','Brown','Green','LtBlue','Purple','Red','Yellow'),
'5' => array('Blue','Green','GreenGold','Purple','WhiteRed'),
'6' => array('BlueBrown','BlueGreen','BlueOrange','BlueRed','BlueYellow','GreenPurple'),
'7' => array('Aqua','Blue','Brown','Green','Purple','Red','Yellow'),
'8' => array('Black','Blue','Brown','Green','OliveGreen','Red','White'),
'9' => array('Aqua','Black','Blue','DkGreen','DkPurple','Green','LtBlue','LtGreen','OliveGreen','Purple','Red'),
'10' => array('Aqua','Black','Blue','Brown','DkPurple','Green','LtBlue','LtGreen','LtPurple','Orange','Red','Yellow'),
'11' => array('Blue','Green','Purple','Red'),
'12' => array('Black','Brown','DkBlue','Green','LtGreen','Purple','Red'),
'13' => array('BlueRed','BlueYellow','Green','GreenOrange','Purple'),
'14' => array('BlueGreen','Gray','GreenYellow','OrangeRed','PurpleBlue','RedBlue','White','YellowPurple'),
'15' => array('BlueGreen','BlueRed','Gray','Green','MaroonBlue','PurpleGreen','RedBlue','WhiteBlue','YellowBrown'),
'16' => array('Black','Blue','Brown','GreenYellow','Purple','Red','White'),
'17' => array('Blue','Gray','Green','Purple','Red','Yellow'),
);
echo 'var c1 = new Array("', join($colorArray[1],'","'), '");'; 
echo 'var c2 = new Array("', join($colorArray[2],'","'), '");';
echo 'var c3 = new Array("', join($colorArray[3],'","'), '");';
echo 'var c4 = new Array("', join($colorArray[4],'","'), '");';
echo 'var c5 = new Array("', join($colorArray[5],'","'), '");';
echo 'var c6 = new Array("', join($colorArray[6],'","'), '");';
echo 'var c7 = new Array("', join($colorArray[7],'","'), '");';
echo 'var c8 = new Array("', join($colorArray[8],'","'), '");';
echo 'var c9 = new Array("', join($colorArray[9],'","'), '");';
echo 'var c10 = new Array("', join($colorArray[10],'","'), '");';
echo 'var c11 = new Array("', join($colorArray[11],'","'), '");';
echo 'var c12 = new Array("', join($colorArray[12],'","'), '");';
echo 'var c13 = new Array("', join($colorArray[13],'","'), '");';
echo 'var c14 = new Array("', join($colorArray[14],'","'), '");';
echo 'var c15 = new Array("', join($colorArray[15],'","'), '");';
echo 'var c16 = new Array("', join($colorArray[16],'","'), '");';
echo 'var c17 = new Array("', join($colorArray[17],'","'), '");';

?>
  var selElem = document.getElementById('style');
  var index = selElem.selectedIndex; 
  var newElem = document.getElementById('color');
  var tmp = '';
  var arr = '';
  newElem.options.length = 0;
  if (index == 1) arr = c1;
  else if (index == 2) arr = c2.slice();
  else if (index == 3) arr = c3.slice();
  else if (index == 4) arr = c4.slice();
  else if (index == 5) arr = c5.slice();
  else if (index == 6) arr = c6.slice();
  else if (index == 7) arr = c7.slice();
  else if (index == 8) arr = c8.slice();
  else if (index == 9) arr = c9.slice();
  else if (index == 10) arr = c10.slice();
  else if (index == 11) arr = c11.slice();
  else if (index == 12) arr = c12.slice();
  else if (index == 13) arr = c13.slice();
  else if (index == 14) arr = c14.slice();
  else if (index == 15) arr = c15.slice();
  else if (index == 16) arr = c16.slice();
  else if (index == 17) arr = c17.slice();  
  
  for (var i=0; i<arr.length; i++) 
  {
    tmp = arr[i];
    newElem.options[newElem.options.length] = new Option(tmp,tmp);
  }
  setFlag();
}

function setFlag()
{
  var style = document.getElementById('style').value;
  var color = document.getElementById('color').value;

  document.getElementById('display').style.backgroundImage = "url(images/Flags/Flag"+style+"-"+color+".gif)";
}

function setSigil()
{
  var sigil = document.getElementById('sigil').value;
  if (sigil != "0") document.getElementById('display').innerHTML = "<img src='images/Sigils/"+sigil+".gif' width=160 height=197/>";
  else document.getElementById('display').innerHTML = "&nbsp;";
}
</script>  
<?php
include('footer.htm');
?>
