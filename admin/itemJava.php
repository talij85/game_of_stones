<?php
include_once("itemFuncs.php");
include_once('displayFuncs.php');
?>              
<script type="text/javascript">
  var item_base = new Object();
<?php
  foreach ($item_base as $iname => $istats)
  {
    echo "  item_base.".str_replace(" ","_",$iname)." = new Array();\n";
    for ($i=0; $i < count($istats); $i++)
      echo "  item_base.".str_replace(" ","_",$iname)."[".$i."] = '".$istats[$i]."';\n";
  }
?>

  var item_ix = new Object();

<?php
  foreach ($item_ix as $iname => $istats)
  {
    echo "  item_ix.".str_replace(" ","_",$iname)." = new Array('".$istats."');\n";
  }

?>

  var ter_bonuses = new Array();
<?php
  foreach ($ter_bonuses as $itype => $ibonus)
  {
    if ($itype < 12)
      echo "  ter_bonuses[".$itype."] = new Array('".$ibonus[0]."','".$ibonus[1]."');\n";
    else
    {
      echo "  ter_bonuses[".$itype."] = new Array();\n";
      foreach ($ibonus as $bt => $bbonus)
      {
        echo "  ter_bonuses[".$itype."][".$bt."] = new Array('".$bbonus[0]."','".$bbonus[1]."');\n";
      }
    }
  }
?>  
  
  var stat_msg = new Object();
<?php
  foreach ($stat_msg as $isym => $imsg)
  {
    echo "  stat_msg.".$isym." = new Array();\n";
    for ($i=0; $i < count($imsg); $i++)
      echo "  stat_msg.".$isym."[".$i."] = '".$imsg[$i]."';\n";
  }
?>

function ucwords (str) 
{  
    return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
        return $1.toUpperCase();
    });
}


function initObj(obj)
{
<?php
  foreach ($stat_msg as $isym => $imsg)
  {
    echo "obj.".$isym." = 0;";
  }
  // These aren't in $stat_msg as they are paired with another stat.
?>
  return obj;
}

function cparse (stats)
{
  var resolve = stats.split(" ");
  var rc= resolve.length;
  var output = new Object();
  output = initObj(output);
  for (var i=0; i < rc; ++i)
  {
    // two letter code
    if ((resolve[i].substring(1,2) != "-" ) && (resolve[i].substring(1,2) != "0" ) && (resolve[i].substring(1,2) != "1" ) && (resolve[i].substring(1,2) != "2" ) && (resolve[i].substring(1,2) != "3" ) 
     && (resolve[i].substring(1,2) != "4" ) && (resolve[i].substring(1,2) != "5" ) && (resolve[i].substring(1,2) != "6" ) && (resolve[i].substring(1,2) != "7" ) && (resolve[i].substring(1,2) != "8" )
     && (resolve[i].substring(1,2) != "9" ))
    { 
      output[resolve[i].substring(0,2)] += parseInt(resolve[i].substring(2));
    }
    else // one letter code
    {
      output[resolve[i].substring(0,1)] += parseInt(resolve[i].substring(1));
    }
  }
  return output;
}  

function iparse(itm,pre,suf,cond)
{
  var rval = item_base[itm][0];
  if (pre)
  {
    rval += " " + item_ix[pre];
    if (item_base[itm][1] < 12) rval += " " + ter_bonuses[item_base[itm][1]][0];
    else if (item_base[itm][1] < 14) rval += " " + ter_bonuses[item_base[itm][1]][item_base[itm][2]][0];
  }
  if (suf) 
  {
    rval += " " +item_ix[suf];
    if (item_base[itm][1] < 12) rval += " " + ter_bonuses[item_base[itm][1]][1];
    else if (item_base[itm][1] < 14) rval += " " + ter_bonuses[item_base[itm][1]][item_base[itm][2]][1];
  }
  if (item_base[itm][1] >= 7 )
    rval += " N-"+ Math.floor((100-cond)/3);
  else 
    rval += " O-"+ Math.floor((100-cond)/3);
  return rval;
}

function show_sign1(thing) {
  if (thing > 0) thing = "+" + thing; 
  return thing;
}

function display_stat(sym, char_stats, old_stats)
{
  var msg = stat_msg[sym];
  var bonus='';
  if (char_stats[sym] != 0 || (old_stats != null && old_stats[sym] != 0 && old_stats[sym] != null))
  {
    // Create info string
    if (msg[2]==0)
    {
      bonus = msg[0]+(char_stats[sym])+msg[1];
    }
    else if (msg[2]==1)
    {
      bonus = msg[0]+show_sign1(char_stats[sym])+msg[1];
    }
    else if (msg[2]==2)
    {
      bonus = msg[0]+show_sign1(char_stats[sym])+"-"+(char_stats[sym]*2)+msg[1];
    }
    else if (msg[2]==3)
    {
      bonus = msg[0]+msg[1];
    }
    else
    {
      bonus = msg[0]+char_stats[sym]+"-"+char_stats[msg[2]]+msg[1];
    }
    
    // Color stat changes
    if (old_stats != null && msg[2]!=3)
    {
      var font = "";
      if (char_stats[sym] > old_stats[sym])
      {
        font = "<font class='text-success'>";
      }
      else if (char_stats[sym] < old_stats[sym])
      {
        font = "<font class='text-danger'>";
      }
      
      if (font != "")
      {
        bonus = font+bonus+"</font>";
      }
    }
    if (bonus != "") bonus += "<br/>";
  }

//  return bonus.replace(" ", "&nbsp;");
  return bonus;
}

function itm_info(char_stats, old_stats)
{
  var bonuses = "";
<?php
  foreach ($stat_msg as $sym => $msg)
  {
    echo "  bonuses += display_stat('".$sym."', char_stats, old_stats);\n";
  }
?>
  
  return bonuses;
}

function old_itm_info(char_stats)
{

  var bonus="";
  if (char_stats['A'] < 0) char_stats['A'] = 0;
  if (char_stats['B'] < char_stats['A']) char_stats['B'] = char_stats['A'];
  if (char_stats['D'] < 0) char_stats['D'] = 0;
  if (char_stats['E'] < char_stats['D']) char_stats['E'] = char_stats['D'];

  if (char_stats['A'] || char_stats['B']) bonus += char_stats['A']+"-"+char_stats['B']+" Damage<br>";
  if (char_stats['D'] || char_stats['E']) bonus += char_stats['D']+"-"+char_stats['E']+" Block<br>";
  if (char_stats['O']) bonus += show_sign1(char_stats['O'])+"% Damage<br>";
  if (char_stats['N']) bonus += show_sign1(char_stats['N'])+"% Defense<br>";
  if (char_stats['H']) bonus += char_stats['H']+"% Damage to Self<br>";
  if (char_stats['G']) bonus += char_stats['G']+"% Health Gain<br>";
  if (char_stats['L']) bonus += show_sign1(char_stats['L'])+" Luck<br>";
  if (char_stats['P']) bonus += char_stats['P']+" Poison<br>";
  if (char_stats['T']) bonus += show_sign1(char_stats['T'])+" Taint<br>";
  if (char_stats['X']) bonus += show_sign1(char_stats['X'])+" Speed<br>";
  if (char_stats['W']) bonus += show_sign1(char_stats['W'])+" Wound<br>";    
  if (char_stats['F']) bonus += show_sign1(char_stats['F'])+" First Strike<br>";
  if (char_stats['S']) bonus += show_sign1(char_stats['S'])+" Stun<br>";
  if (char_stats['Y']) bonus += show_sign1(char_stats['Y'])+" Accuracy<br>";
  if (char_stats['V']) bonus += show_sign1(char_stats['V'])+" Dodge<br>";
  if (char_stats['M']) bonus += show_sign1(char_stats['M'])+" Stamina<br>";
  if (char_stats['J']) bonus += show_sign1(char_stats['J'])+" Ji in Duels<br>";
  if (char_stats['Q']) bonus += show_sign1(char_stats['Q'])+" Experience in Duels<br>";
  
  if (char_stats['sA']) bonus += show_sign1(char_stats['sA'])+"-"+(2*char_stats['sA'])+" Sword Damage<br>";
  if (char_stats['xA']) bonus += show_sign1(char_stats['xA'])+"-"+(2*char_stats['xA'])+" Axe Damage<br>";
  if (char_stats['pA']) bonus += show_sign1(char_stats['pA'])+"-"+(2*char_stats['pA'])+" Spear Damage<br>";
  if (char_stats['oA']) bonus += show_sign1(char_stats['oA'])+"-"+(2*char_stats['oA'])+" Bow Damage<br>";
  if (char_stats['bA']) bonus += show_sign1(char_stats['bA'])+"-"+(2*char_stats['bA'])+" Bludgeon Damage<br>";
  if (char_stats['kA']) bonus += show_sign1(char_stats['kA'])+"-"+(2*char_stats['kA'])+" Knife Damage<br>";
  if (char_stats['hA']) bonus += show_sign1(char_stats['hA'])+"-"+(2*char_stats['hA'])+" Shield Damage<br>";
  if (char_stats['wA']) bonus += show_sign1(char_stats['wA'])+"-"+(2*char_stats['wA'])+" Weave Damage<br>";
  if (char_stats['aA']) bonus += show_sign1(char_stats['aA'])+"-"+(2*char_stats['aA'])+" Body Armor Defense<br>";
  if (char_stats['lA']) bonus += show_sign1(char_stats['lA'])+"-"+(2*char_stats['lA'])+" Leg Armor Defense<br>"; 
  if (char_stats['sO']) bonus += show_sign1(char_stats['sO'])+"% Sword Damage<br>";
  if (char_stats['xO']) bonus += show_sign1(char_stats['xO'])+"% Axe Damage<br>";
  if (char_stats['pO']) bonus += show_sign1(char_stats['pO'])+"% Spear Damage<br>";
  if (char_stats['oO']) bonus += show_sign1(char_stats['oO'])+"% Bow Damage<br>";
  if (char_stats['bO']) bonus += show_sign1(char_stats['bO'])+"% Bludgeon Damage<br>";
  if (char_stats['kO']) bonus += show_sign1(char_stats['kO'])+"% Knife Damage<br>";
  if (char_stats['hO']) bonus += show_sign1(char_stats['hO'])+"% Shield Defense<br>";
  if (char_stats['wO']) bonus += show_sign1(char_stats['wO'])+"% Weave Damage<br>";
  if (char_stats['aO']) bonus += show_sign1(char_stats['aO'])+"% Body Armor Defense<br>";
  if (char_stats['lO']) bonus += show_sign1(char_stats['lO'])+"% Leg Armor Defense<br>";    
  if (char_stats['sC']) bonus += show_sign1(char_stats['sC'])+"% Sword Usage cost<br>";
  if (char_stats['xC']) bonus += show_sign1(char_stats['xC'])+"% Axe Usage Cost<br>";
  if (char_stats['pC']) bonus += show_sign1(char_stats['pC'])+"% Spear Usage Cost<br>";
  if (char_stats['oC']) bonus += show_sign1(char_stats['oC'])+"% Bow Usage Cost<br>";
  if (char_stats['bC']) bonus += show_sign1(char_stats['bC'])+"% Bludgeon Usage Cost<br>";
  if (char_stats['kC']) bonus += show_sign1(char_stats['kC'])+"% Knife Usage Cost<br>";
  if (char_stats['hC']) bonus += show_sign1(char_stats['hC'])+"% Shield Usage Cost<br>";
  if (char_stats['wC']) bonus += show_sign1(char_stats['wC'])+"% Weave Usage Cost<br>";
  if (char_stats['aC']) bonus += show_sign1(char_stats['aC'])+"% Body Armor Usage Cost<br>";
  if (char_stats['lC']) bonus += show_sign1(char_stats['lC'])+"% Leg Armor Usage Cost<br>";
  
  if (char_stats['rV']) bonus += show_sign1(char_stats['rV'])+"% Repair Cost<br>";
  if (char_stats['hV']) bonus += show_sign1(char_stats['hV'])+"% Herb Cost<br>";
  if (char_stats['dV']) bonus += show_sign1(char_stats['dV'])+"% Drink Cost<br>";
  if (char_stats['fV']) bonus += show_sign1(char_stats['fV'])+"% Food Cost<br>";
  if (char_stats['iV']) bonus += show_sign1(char_stats['iV'])+"% Inn Cost<br>";
  if (char_stats['pL']) bonus += show_sign1(char_stats['pL'])+" Luck vs NPCs in Plains<br>";
  if (char_stats['hL']) bonus += show_sign1(char_stats['hL'])+" Luck vs NPCs in Hills<br>";
  if (char_stats['fL']) bonus += show_sign1(char_stats['fL'])+" Luck vs NPCs in Forest<br>";
  if (char_stats['mL']) bonus += show_sign1(char_stats['mL'])+" Luck vs NPCs in Mountains<br>";
  if (char_stats['oL']) bonus += show_sign1(char_stats['oL'])+" Luck vs NPCs in Oceans<br>";
  if (char_stats['wL']) bonus += show_sign1(char_stats['wL'])+" Luck vs NPCs in Wastelands<br>";
  if (char_stats['dL']) bonus += show_sign1(char_stats['dL'])+" Luck in Duels<br>";
  if (char_stats['hU']) bonus += "Herb effects last an extra "+show_sign1(char_stats['hU'])+" Turns<br>";
  if (char_stats['dU']) bonus += "Drink effects last an extra "+show_sign1(char_stats['dU'])+" Turns<br>";
  if (char_stats['fU']) bonus += "Food effects last an extra "+show_sign1(char_stats['fU'])+" Turns<br>";
  if (char_stats['fS']) bonus += show_sign1(char_stats['fS'])+" Stamina recovered from Food<br>";
  if (char_stats['eS']) bonus += show_sign1(char_stats['eS'])+" Equipment Storage<br>";
  if (char_stats['cS']) bonus += show_sign1(char_stats['cS'])+" Consumable Storage<br>";
  if (char_stats['eF']) bonus += (char_stats['eF'])+"% less damaged Equipment Found<br>";
  if (char_stats['eV']) bonus += "Entertainment bonus coin (level "+(char_stats['eV'])+")<br>";
  if (char_stats['cV']) bonus += "Purse Cutting bonus coin (level "+(char_stats['cV'])+")<br>";
  if (char_stats['fQ']) bonus += "Find Quest bonus coin (level "+(char_stats['fQ'])+")<br>";
  if (char_stats['nQ']) bonus += "NPC Quest bonus coin (level "+(char_stats['nQ'])+")<br>";
  if (char_stats['fB']) bonus += "Can own a level "+(char_stats['fB'])+" Farm<br>";
  if (char_stats['mB']) bonus += "Can own a level "+(char_stats['mB'])+" Mine<br>";
  if (char_stats['sB']) bonus += "Can own a level "+(char_stats['sB'])+" Shipping Office<br>";
  if (char_stats['kB']) bonus += "Can own a level "+(char_stats['kB'])+" Kitchen<br>";
  if (char_stats['gB']) bonus += "Can own a level "+(char_stats['gB'])+" Herb Garden<br>";
  if (char_stats['bB']) bonus += "Can own a level "+(char_stats['bB'])+" Brewery<br>";
  if (char_stats['fT']) bonus += show_sign1(char_stats['fT'])+"% Crop Grow Time<br>";
  if (char_stats['mT']) bonus += show_sign1(char_stats['mT'])+"% Mining Time<br>";
  if (char_stats['sT']) bonus += show_sign1(char_stats['sT'])+"% Sailing Time<br>";
  if (char_stats['kT']) bonus += show_sign1(char_stats['kT'])+"% Cook Time<br>";
  if (char_stats['gT']) bonus += show_sign1(char_stats['gT'])+"% Herb Grow Time<br>";
  if (char_stats['bT']) bonus += show_sign1(char_stats['bT'])+"% Brewing Time<br>";
  if (char_stats['xV']) bonus += show_sign1(char_stats['xV'])+"% Crop Value<br>";
  if (char_stats['mV']) bonus += show_sign1(char_stats['mV'])+"% Mineral Value<br>";
  if (char_stats['sV']) bonus += show_sign1(char_stats['sV'])+"% Cargo Value<br>";
  if (char_stats['kQ']) bonus += show_sign1(char_stats['kQ'])+" Food Quality<br>";
  if (char_stats['gQ']) bonus += show_sign1(char_stats['gQ'])+" Herb Quality<br>";
  if (char_stats['bQ']) bonus += show_sign1(char_stats['bQ'])+" Drink Quality<br>"; 
  if (char_stats['sR']) bonus += show_sign1(char_stats['sR'])+" Ship Range<br>";
  if (char_stats['kS']) bonus += show_sign1(char_stats['kS'])+" Food Storage<br>";
  if (char_stats['gS']) bonus += show_sign1(char_stats['gS'])+" Herb Storage<br>";
  if (char_stats['bS']) bonus += show_sign1(char_stats['bS'])+" Drink Storage<br>";     

  return bonus.replace(" ", "&nbsp;");
}

function getTypeMod(skills, type)
{
  var mod = 100;
  var bonus = '';
  switch (type)
  {
    case '0':
      bonus = 'rC';
      break;
    case '1':
      bonus = 'sC';
      break;
    case '2':
      bonus = 'xC';
      break;
    case '3':
      bonus = 'pC';
      break;
    case '4':
      bonus = 'oC';
      break;
    case '5':
      bonus = 'bC';
      break;
    case '6':
      bonus = 'kC';
      break;
    case '7':
      bonus = 'hC';
      break;
    case '8':
      bonus = 'wC';
      break;
    case '9':
      bonus = 'aC';
      break;
    case '10':
      bonus = 'lC';
      break;
    default:
      break;
  }
  var skill_tmp = cparse(skills, 0);
  if (skill_tmp[bonus]) mod = parseInt(mod) + parseInt(skill_tmp[bonus]);
  
  return mod;
}

// ITEM VALUE PRODUCER
function item_val(str)
{
  var worth = Math.round(Math.pow(lvl_req(str,100),2)/3);

  if (worth < 0) worth = 0;
  return worth;
}

function lvl_req(str, mod) {
  var char_stat = cparse(str,0);

  var num =((parseInt(char_stat['A'])+parseInt(char_stat['B'])+parseInt(char_stat['D'])+parseInt(char_stat['E'])+parseInt(char_stat['X'])+parseInt(char_stat['W']))*3 + 
                        (parseInt(char_stat['P']))*2 + (parseInt(char_stat['T'])+parseInt(char_stat['L']))*5 + 
                        (parseInt(char_stat['O'])+parseInt(char_stat['N']))/3 +(parseInt(char_stat['F'])+parseInt(char_stat['S'])+
                         parseInt(char_stat['V'])+parseInt(char_stat['Y'])+parseInt(char_stat['G'])-parseInt(char_stat['H'])));
       
  var per = (((parseInt(char_stat['X'])+parseInt(char_stat['W']))*6 + (parseInt(char_stat['F'])+parseInt(char_stat['S']))*3 + 
                        (parseInt(char_stat['P'])+parseInt(char_stat['O'])+parseInt(char_stat['N'])-parseInt(char_stat['H']))+
                        (parseInt(char_stat['V'])+parseInt(char_stat['Y'])+parseInt(char_stat['G']))*2)/100+1);
  var lvl = num*per;
  
  lvl = Math.round((lvl * mod)/100);
  
   
  return lvl < 1 ? 1 : lvl;
}

function displayGold (money, txt)
{
  var c;
  var m;
  var p;
  var neg = 0;
  var rval="<font class=littletext>";
  if (money < 0)
  {
    neg = 1;
    rval = "<font class='littletext_r'>";    
    money = Math.abs(money);
  }
  var crown = Math.floor(money/10000);
  var mark = Math.floor((money-crown*10000)/100);
  var penny = Math.floor ((money-crown*10000-mark*100));
  if (txt==0)
    rval += "<img src='images/gold.gif' width='15' style='vertical-align:middle' alt='g:'>"+crown+"<img src='images/silver.gif' width='15' style='vertical-align:middle' alt='s:'>"+mark+"<img src='images/copper.gif' width='15' style='vertical-align:middle' alt='c:'>"+penny+"</font>";
  else
  {
    if (crown != 1)c = "crowns"; else c = "crown";
    if (mark != 1)m = "marks"; else m = "mark";
    if (penny != 1)p = "pennies"; else p = "penny";
    rval += crown+" gold "+c+", "+mark+" silver "+m+", and "+penny+" copper "+p+".</font>";
  }
  
  return rval;
}

function iname(item)
{
  var name = ucwords(item[1]);
  if (name) name += " ";
  name += ucwords(item[0]);
  if (item[2]) name += " "+ucwords(item[2]);
  return name.replace(/_/g, " ");
}

function weaveStats (sname, skills)
{
  sname = sname.replace(/_/g, " ");
  var c_skills = cparse(skills,0);
  
  var fp = c_skills['fP'];
  var wp = c_skills['wP'];
  var sp = c_skills['sP'];
  var ap = c_skills['aP'];
  var ep = c_skills['eP'];
  var stats = '';

  switch(sname)
  {
    case 'firespark':
    stats = calcWeaveParts("O",fp);
    break;
    case 'cantrips':
    stats = calcWeaveParts("N",ep);
    break;
    case 'water spike':
    stats = calcWeaveParts("V",wp);
    break;
    case 'air razor':
    stats = calcWeaveParts("Y",ap);
    break;
    case 'ward':
    stats = calcWeaveParts("G",sp,1);
    break;

    case 'arrows of fire':
    stats = calcWeaveParts("F",fp)+" "+calcWeaveParts("Y",ap);
    break;
    case 'boil':
    stats = calcWeaveParts("O",fp)+" "+calcWeaveParts("V",wp);
    break;
    case 'fireball':
    stats = calcWeaveParts("O",fp)+" "+calcWeaveParts("N",ep);
    break;
    case 'compel':
    stats = calcWeaveParts("S",fp,1)+" "+calcWeaveParts("P",sp,1);
    break;
    case 'mudslide':
    stats = calcWeaveParts("V",wp,1)+" "+calcWeaveParts("N",ep,1);
    break;
    case 'upheaval':
    stats = calcWeaveParts("T",ap)+" "+calcWeaveParts("F",ep);
    break;
    case 'corrupt':
    stats = calcWeaveParts("L",sp)+" "+calcWeaveParts("T",ep);
    break;
    case 'ice missiles':
    stats = calcWeaveParts("X",wp)+" "+calcWeaveParts("Y",ap);
    break;
    case 'drown':
    stats = calcWeaveParts("P",wp)+" "+calcWeaveParts("G",sp);
    break;
    case 'crush':
    stats = calcWeaveParts("G",sp,1)+" "+calcWeaveParts("W",ap,1);
    break;
    
    case 'lava flow':
    stats = calcWeaveParts("S",fp)+" "+calcWeaveParts("V",wp)+" "+calcWeaveParts("L",ep);
    break;
    case 'blossoms of fire':
    stats = calcWeaveParts("O",fp)+" "+calcWeaveParts("W",ap)+" "+calcWeaveParts("N",ep);
    break;
    case 'deathgate':
    stats = calcWeaveParts("O",fp)+" "+calcWeaveParts("G",sp)+" "+calcWeaveParts("F",ep);
    break;
    case 'cyclone':
    stats = calcWeaveParts("O",fp)+" "+calcWeaveParts("V",wp)+" "+calcWeaveParts("Y",ap);
    break;
    case 'compulsion':
    stats = calcWeaveParts("S",fp,1)+" "+calcWeaveParts("T",wp,1)+" "+calcWeaveParts("P",sp,1);
    break;
    case 'lightning strike':
    stats = calcWeaveParts("F",fp)+" "+calcWeaveParts("L",sp)+" "+calcWeaveParts("X",ap);
    break;
    case 'hailstorm':
    stats = calcWeaveParts("X",sp,1)+" "+calcWeaveParts("T",ap,1)+" "+calcWeaveParts("N",ep,1);
    break;
    case 'tsunami':
    stats = calcWeaveParts("S",wp)+" "+calcWeaveParts("G",sp)+" "+calcWeaveParts("Y",ap);
    break;
    case 'power shield':
    stats = calcWeaveParts("V",wp,1)+" "+calcWeaveParts("G",sp,1)+" "+calcWeaveParts("N",ep,1);
    break;
    case 'rend':
    stats = calcWeaveParts("W",wp)+" "+calcWeaveParts("Y",ap)+" "+calcWeaveParts("F",ep);
    break;
 
    case 'fire storm':
    stats = calcWeaveParts("O",fp)+" "+calcWeaveParts("Y",sp)+" "+calcWeaveParts("X",ap)+" "+calcWeaveParts("F",ep);
    break;
    case 'earthquake':
    stats = calcWeaveParts("F",fp,1)+" "+calcWeaveParts("S",wp,1)+" "+calcWeaveParts("L",ap)+" "+calcWeaveParts("N",ep);
    break;
    case 'decay':
    stats = calcWeaveParts("P",fp)+" "+calcWeaveParts("W",wp)+" "+calcWeaveParts("T",sp)+" "+calcWeaveParts("S",ep);
    break;
    case 'lightning storm':
    stats = calcWeaveParts("W",fp)+" "+calcWeaveParts("L",wp)+" "+calcWeaveParts("V",sp)+" "+calcWeaveParts("Y",ap);
    break;
    case 'refresh':
    stats = calcWeaveParts("V",wp,1)+" "+calcWeaveParts("G",sp,1)+" "+calcWeaveParts("L",ap,1)+" "+calcWeaveParts("X",ep,1);
    break;
    default:
    stats = "A1 B1";
    break;
  }

  return stats;
}


function calcWeaveParts(stat, p)
{
  return calcWeaveParts(stat, p, 0);
}

function calcWeaveParts(stat, p, def)
{
  var stats='';
  var ad=new Array("A","B");
  if (def) ad=new Array("D","E");
  switch(stat)
  {
    case 'O':
    stats = ad[0]+parseInt(2*p)+" "+ad[1]+parseInt(3*p)+" O"+parseInt(2*p);
    break;
    case 'N':
    stats = ad[0]+parseInt(2*p)+" "+ad[1]+parseInt(3*p)+" N"+parseInt(2*p);
    break;
    case 'Y':
    stats = ad[0]+parseInt(2*p)+" "+ad[1]+parseInt(3*p)+" Y"+parseInt(p);
    break;
    case 'V':
    stats = ad[0]+parseInt(2*p)+" "+ad[1]+parseInt(3*p)+" V"+parseInt(p);
    break;
    case 'G':
    stats = ad[0]+parseInt(2*p)+" "+ad[1]+parseInt(3*p)+" G"+parseInt(p);
    break;
    case 'L':
    stats = ad[0]+parseInt(1*p)+" "+ad[1]+parseInt(3*p)+" L"+parseInt(p);
    break;
    case 'P':
    stats = ad[0]+parseInt(1*p)+" "+ad[1]+parseInt(3*p)+" P"+parseInt(p);
    break;
    case 'T':
    stats = ad[0]+parseInt(1*p)+" "+ad[1]+parseInt(3*p)+" T"+parseInt(p);
    break;
    case 'F':
    stats = ad[0]+parseInt(1*p)+" "+ad[1]+parseInt(3*p)+" F"+parseInt(p);
    break;
    case 'S':
    stats = ad[0]+parseInt(1*p)+" "+ad[1]+parseInt(3*p)+" S"+parseInt(p);
    break;
    case 'X':
    stats = ad[0]+parseInt(1*p)+" "+ad[1]+parseInt(2*p)+" X"+parseInt(Math.round(0.5*p));
    break;
    case 'W':
    stats = ad[0]+parseInt(1*p)+" "+ad[1]+parseInt(2*p)+" W"+parseInt(Math.round(0.5*p));
    break;
  }
  return stats;
}

</script> 