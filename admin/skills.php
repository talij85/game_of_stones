<?php
$skills = unserialize($char['skills']);

$clan_hires = array(
  array("Supply Caravan","lS","10"),
  array("Raiders","eL","2"),
  array("Watch","nR","2"),
  array("Medics","gR","2"),
  array("Scout","vR","1"),
  array("Seige Weapons","oW","2"),
  array("Saboteurs","pW","1"),
  array("Spies","yW","1"),
);

function getMaxClanHire ($ji)
{
  $max = 3;
  $clan_hires_req = array(0,0,0,5000,10000,15000,20000,25000,35000,50000,10000000);
  for ($i=3; $i<10; $i++)
  {
    if ($ji >= $clan_hires_req[$i]) $max = $i+1;
  }
  return $max;
}



function show_sign($thing) {
  if ($thing < 0) return $thing; else return "+".$thing;
}

function getSkills($mySkills, $type)
{
  $skills=typeSkills($type);
  
  for ($i=0; $i<count($skills); $i++)
  { 
    $mySkills[$skills[$i]]=0;
  }
  
  return serialize($mySkills);
}

function typeSkills($type)
{
  $skills = '';
  switch ($type)
  {
    case '0': 
      $skills = array(999);
      break;
      
    case '1':
      $skills = array(51,52,53,54,55);
      break;
    case '2':
      $skills = array(56,57,58,59,60);
      break;
    case '3':
      $skills = array(61,62,63,64,65);
      break;
    case '4':
      $skills = array(66,67,68,69,70);
      break;
    case '5':
      $skills = array(71,72,73,74,75);
      break;
            
    case '10':
      $skills = array(101,102,103);
      break;
    case '11':
      $skills = array(104,105,106);
      break;
    case '12':
      $skills = array(107,108,109);
      break;
    case '13':
      $skills = array(110,111,112);
      break;      
    case '14':
      $skills = array(113,114,115);
      break;
    case '15':
      $skills = array(116,117,118);
      break;
    case '16':
      $skills = array(119,120,121);
      break;
    case '17':
      $skills = array(122,123,124);
      break;
    case '18':
      $skills = array(125,126,127);
      break;
    case '19':
      $skills = array(128,129,130);
      break;
    case '20':
      $skills = array(131,132,133);
      break;
    case '21':
      $skills = array(134,135,136);
      break;
      
    case '31':
      $skills = array(164,165,166);
      break;
    case '32':
      $skills = array(167,168,169);
      break;      
    case '33':
      $skills = array(170,171,172);
      break;
    case '34':
      $skills = array(173,174,175);
      break;
    case '35':
      $skills = array(176,177,178);
      break;
    case '36':
      $skills = array(179,180,181);
      break;
    case '37':
      $skills = array(182,183,184);
      break;      
    case '38':
      $skills = array(185,186,187);
      break;
    case '39':
      $skills = array(188,189,190);
      break;
    case '40':
      $skills = array(191,192,193);
      break;
    case '41':
      $skills = array(194,195,196);
      break;
    case '42':
      $skills = array(197,198,199);
      break;      
    case '43':
      $skills = array(200,201,202);
      break;
    case '44':
      $skills = array(203,204,205);
      break;
    case '45':
      $skills = array(206,207,208);
      break;
    case '46':
      $skills = array(209,210,211);
      break;
    case '47':
      $skills = array(212,213,214);
      break;      
    case '48':
      $skills = array(215,216,217);
      break;
    case '49':
      $skills = array(218,219,220);
      break;
    case '50':
      $skills = array(221,222,223);
      break;
    case '51':
      $skills = array(224,225,226);
      break;
    case '52':
      $skills = array(227,228,229);
      break; 
    case '101':
      $skills = array(1,2,3,4,5);
      break;
    case '102':
      $skills = array(6,7,8,9,10);
      break;
    case '103':
      $skills = array(11,12,13,14,15);
      break;
    case '104':
      $skills = array(16,17,18,19,20);
      break;
    case '105':
      $skills = array(21,22,23,24,25);
      break;
    case '106':
      $skills = array(26,27,28,29,30);
      break;
    case '107':
      $skills = array(31,32,33,34,35);
      break;
    case '108':
      $skills = array(36,37,38,39,40);
      break;
    case '109':
      $skills = array(41,42,43,44,45);
      break;
    case '110':
      $skills = array(46,47,48,49,50);
      break;
      
    case '900':
      $skills = array(901,902,903,904,905,906,907);
      break;  
    
    default:
      $skills = array(0,0,0,0,0);
      break;
  }
  
  return $skills;
}
function getSkillEffect($sname,$slvl,$gender=0)
{
$stats = '';
$mult2=2*$slvl;
$mult3=3*$slvl;
$mult4=4*$slvl;
$div3= floor(($slvl+2)/3);
$neg = -1*$slvl;

switch($sname)
{
// Sword
  case 'Heron Spreads its Wings':
    $stats = "A".$slvl." B".$mult2." O".$slvl." X".$div3;
    break;
  case 'Sword Finesse':
    $stats = "sC".$neg;
    break;
  case 'Cat Crosses the Courtyard':
    $stats = "sA".$mult2." O".$slvl." S".$slvl;
    break;
  case 'Sword Mastery':
    $stats = "sA".$slvl;
    break;
  case 'Parting the Silk':
    $stats = "sO".$mult2." V".$slvl;
    break;
// Axe
  case 'Hacking through Brambles':
    $stats = "A".$slvl." B".$mult2." S".$slvl." W".$div3;
    break;
  case 'Axe Finesse':
    $stats = "xC".$neg;
    break;
  case 'Mighty Chop':
    $stats = "xA".$mult2." O".$slvl." S".$slvl;
    break;
  case 'Axe Mastery':
    $stats = "xA".$slvl;
    break;
  case 'Cleave': 
    $stats = "xO".$mult2." Y".$slvl;
    break;
// Spear
  case 'Wash the Spears':
    $stats = "A".$slvl." B".$mult2." Y".$slvl." W".$div3;
    break;
  case 'Spear Finesse': 
    $stats = "pC".$neg;
    break;
  case 'Dance the Spears':
    $stats = "pA".$mult2." V".$slvl." F".$slvl;
    break;
  case 'Spear Mastery':
    $stats = "pA".$slvl;
    break;
  case 'Reach': 
    $stats = "pO".$mult2." F".$slvl;
    break;
// Bow
  case 'Rapid Fire':
    $stats = "A".$slvl." B".$mult2." V".$slvl." X".$div3;
    break;
  case 'Bow Finesse':
    $stats = "oC".$neg;
    break;
  case 'Focused Shot':
    $stats = "oA".$mult2." Y".$slvl." F".$slvl;
    break;
  case 'Bow Mastery':
    $stats = "oA".$slvl;
    break;
  case 'Tipped Arrows':
    $stats = "oO".$mult2." P".$slvl;
    break;
// Bludgeon
  case 'Shattering Strike':
    $stats = "A".$slvl." B".$mult2." N".$slvl." W".$div3;
    break;
  case 'Bludgeon Finesse':
    $stats = "bC".$neg;
    break;
  case 'Stunning Blow':
    $stats = "bA".$mult2." N".$slvl." S".$slvl;
    break;
  case 'Bludgeon Mastery':
    $stats = "bA".$slvl;
    break;
  case 'Hard Knocks':
    $stats = "bO".$mult2." S".$slvl;
    break;
// Knife
  case 'Quick Toss':
    $stats = "A".$slvl." B".$mult2." L".$slvl." X".$div3;
    break;
  case 'Knife Finesse':
    $stats = "kC".$neg;
    break;
  case 'Trick Throw':
    $stats = "kA".$mult2." Y".$slvl." V".$slvl;
    break;
  case 'Knife Mastery':
    $stats = "kA".$slvl;
    break;
  case 'Concealed Strike':
    $stats = "kO".$mult2." T".$slvl;
    break;    
// Shield
  case 'Shield Bash':
    $stats = "D".$slvl." E".$mult2." S".$slvl." W".$div3;
    break;
  case 'Shield Finesse':
    $stats = "hC".$neg;
    break;
  case 'Cover':
    $stats = "hA".$mult2." Y".$slvl." G".$slvl;
    break;
  case 'Shield Mastery':
    $stats = "hA".$slvl;
    break;
  case 'Parry':
    $stats = "hO".$mult2." L".$slvl;
    break;
// Weave    
  case 'Strength of Fire':
    if ($gender==0) $slvl++;
    $stats = "wA".$slvl." fP".$slvl." Y".$slvl;
    break;
  case 'Strength of Water':
    if ($gender==1) $slvl++;
    $stats = "wA".$slvl." wP".$slvl." V".$slvl;
    break;
  case 'Strength of Spirit':
    $stats = "wC".$neg." wO".$slvl." sP".$slvl;
    break;
  case 'Strength of Air':
    if ($gender==1) {$slvl++; $neg--;}
    $stats = "wC".$neg." aP".$slvl." F".$slvl;
    break;
  case 'Strength of Earth':
    if ($gender==0) {$slvl++; $neg--;}
    $stats = "wC".$neg." eP".$slvl." S".$slvl;
    break;
// Body Armor
  case 'Moment of Silence':
    $stats = "D".$mult2." E".$mult4." N".$slvl;
    break;
  case 'Body Armor Finesse':
    $stats = "aC".$neg;
    break;
  case 'Deflection':
    $stats = "aA".$mult2." N".$slvl." V".$slvl;
    break;    
  case 'Body Armor Mastery':
    $stats = "aA".$slvl;
    break;
  case 'Protection':
    $stats = "aO".$mult3;
    break;
// Leg Armor
  case 'Armored Agility':
    $stats = "D".$slvl." E".$mult2." V".$slvl." X".$div3;
    break;
  case 'Leg Armor Finesse':
    $stats = "lC".$neg;
    break;
  case 'Stability':
    $stats = "lA".$mult2." N".$slvl." Y".$slvl;
    break;
  case 'Leg Armor Mastery':
    $stats = "lA".$slvl;
    break;
  case 'Maneuverability':
    $stats = "lO".$mult2." G".$slvl;
    break;

// Armsman
  case 'Combat Prowess':
    $stats = "O".$slvl." N".$slvl." X".$div3;
    break;
  case 'Poised to Strike':
    $stats = "O".$slvl." V".$slvl;
    break;
  case 'Battle Tested':
    $stats = "O".$slvl." N".$slvl." V".$slvl." G".$slvl;
    break;
  case 'On Guard':
    $stats = "N".$slvl." G".$slvl;
    break;
  case 'Under the Banner':
    $stats = "A".$slvl." B".$mult2." V".$slvl." G".$slvl;
    break;
// Wanderer
  case 'Take No Chances':
    $stats = "Y".$slvl." L".$slvl." W".$div3;
    break;
  case 'Shoot First':
    $stats = "Y".$slvl." F".$slvl;
    break;
  case 'Resourceful':
    $stats = "N".$slvl." Y".$slvl." L".$slvl." F".$slvl;
    break;
  case 'Questions Later':
    $stats = "N".$slvl." L".$slvl;
    break;
  case 'Trust No One':
    $stats = "D".$slvl." E".$mult2." N".$slvl." F".$slvl;
    break;    
// Outdoorsman
  case 'Stealth Strike':
    $stats = "V".$slvl." P".$slvl." X".$div3;
    break;
  case 'Precise Movement':
    $stats = "Y".$slvl." V".$slvl;
    break;
  case 'Natural Awareness':
    $stats = "Y".$slvl." V".$slvl." P".$slvl." L".$slvl;
    break;
  case 'One with the Land':
    $stats = "P".$slvl." L".$slvl;
    break;
  case 'Trapping':
    $stats = "A".$slvl." B".$mult2." Y".$slvl." L".$slvl;
    break;    
// Healer
  case 'Quick and Painless':
    $stats = "O".$slvl." G".$slvl." X".$div3;
    break;
  case 'Correct Dosage':
    $stats = "P".$slvl." G".$slvl;
    break;
  case 'The Best Medicine':
    $stats = "O".$slvl." P".$slvl." G".$slvl." S".$slvl;
    break;
  case 'Apply Pressure':
    $stats = "O".$slvl." S".$slvl;
    break;
  case 'Patience':
    $stats = "D".$slvl." E".$mult2." P".$slvl." S".$slvl;
    break;
// Darkfriend ( class icon: Skull )
  case 'Proactive':
    $stats = "P".$slvl." F".$slvl." W".$div3;
    break;
  case 'Corruption':
    $stats = "N".$slvl." P".$slvl;
    break; 
  case 'Mark of the Shadow':
    $stats = "N".$slvl." P".$slvl." T".$slvl." F".$slvl;
    break;
  case 'Deadly Surprise':
    $stats = "T".$slvl." F".$slvl;
    break;    
  case 'For the Great Lord':
    $stats = "A".$slvl." B".$mult2." N".$slvl." T".$slvl;
    break;

// Commander ( class icon: Star )
  case 'Hammer and Anvil':
    $stats = "D".$slvl." E".$mult2." N".$slvl." Y".$slvl;
    break;
  case 'Veteran':
    $stats = "A".$slvl." B".$mult2." D".$slvl." E".$mult2;
    break;
  case 'The Lesser Sadness':
    $stats = "A".$slvl." B".$mult2." O".$slvl." W".$div3;
    break;
// Redarm ( class icon: Red Hand )
  case 'Time to Toss the Dice':
    $stats = "A".$slvl." B".$mult2." V".$slvl." L".$slvl;
    break;
  case 'Toughness':
    $stats = "D".$mult2." E".$mult4;
    break;
  case 'Maintaining Civility':
    $stats = "D".$slvl." E".$mult2." V".$slvl." S".$slvl;
    break;
// Warder ( class icon: Blue Heron )
  case 'Heavier than a Mountain':
    $stats = "A".$mult2." B".$mult4." O".$mult2." H".$slvl;
    break;
  case 'Benefits of the Bond':
    $stats = "G".$slvl." F".$slvl;
    break;
  case 'Legendary Skill':
    $stats = "A".$slvl." B".$mult2." D".$slvl." E".$mult2." G".$slvl;
    break;
// Mercenary ( class icon: Coin Sack )
  case 'Seasoned Fighter':
    $stats = "A".$slvl." B".$mult2." O".$slvl." N".$slvl;
    break;
  case 'Soldier for Hire':
    $stats = "O".$slvl." L".$slvl;
    break;
  case 'Higher Pay':
    $stats = "Y".$slvl." L".$mult2;
    break;
// Gleeman ( class icon: Horn )
  case 'Unexpected Opponent':
    $stats = "V".$slvl." F".$slvl." S".$slvl;
    break;
  case 'Nimbleness':
    $stats = "V".$slvl." L".$slvl;
    break;
  case 'Extra Flourish':
    $stats = "Y".$slvl." L".$slvl." S".$slvl;
    break;
// Gambler ( class icon: Single Die )
  case 'Great Roll':
    $stats = "N".$slvl." V".$mult2;
    break;
  case 'Good Fortune':
    $stats = "L".$mult2;
    break;
  case 'Lucky Shot':
    $stats = "O".$slvl." Y".$mult2;
    break;
// Hunter ( class icon: Stag )
  case 'Knowing their Weakness':
    $stats = "A".$slvl." B".$mult2." Y".$mult2;
    break;
  case 'Bring It Down':
    $stats = "Y".$slvl." P".$slvl;
    break;
  case 'Stalking Prey':
    $stats = "V".$slvl." P".$slvl." W".$div3;
    break;
// Bandit ( class icon: Rags)
  case 'Ruthlessness':
    $stats = "O".$slvl." T".$slvl." P".$slvl;
    break;
  case 'Thievery':
    $stats = "T".$slvl." L".$slvl;
    break;
  case 'Mug':
    $stats = "O".$slvl." L".$slvl." F".$slvl;
    break;
// Thief Catcher ( class icon: Scales )
  case 'Urban Tracking':
    $stats = "D".$slvl." E".$mult2." Y".$slvl." V".$slvl;
    break;
  case 'Catching':
    $stats = "D".$slvl." E".$mult2." L".$slvl;
    break;
  case 'Ambush':
    $stats = "L".$slvl." F".$slvl." W".$div3;
    break;
// Fanatic ( class icon: Great Serpent/Infinity )
  case 'Fanatical Devotion':
    $stats = "A".$slvl." B".$mult2." T".$mult2;
    break;
  case 'Light-Blinded':
    $stats = "T".$slvl." G".$slvl;
    break;
  case 'Strength in Numbers':
    $stats = "A".$slvl." B".$mult2." O".$slvl." G".$slvl;
    break;
// Dragonsworn
  case 'Mob Justice':
    $stats = "O".$slvl." P".$slvl." L".$slvl;
    break;
  case 'Misguided Loyalty':
    $stats = "P".$slvl." S".$slvl;
    break;
  case 'For the Lord Dragon':
    $stats = "S".$mult2." W".$div3;
    break;
// Assassin
  case 'Unseen Assailant':
    $stats = "N".$slvl." V".$slvl." F".$slvl;
    break;
  case 'To Kill':
    $stats = "P".$mult2;
    break;
  case 'Lethal Precision':
    $stats = "N".$slvl." Y".$slvl." P".$slvl;
    break;
// Whitecloak ( class icon: Sun )
  case 'Purging the Shadow':
    $stats = "A".$slvl." B".$mult2." P".$slvl." W".$div3;
    break;
  case 'Defender of the Light':
    $stats = "O".$slvl." P".$slvl;
    break;
  case 'Justice':
    $stats = "O".$slvl." N".$slvl." W".$div3;
    break;
// Wolfbrother ( class icon: Red Wolf )
  case 'Eyes of the Wolf':
    $stats = "O".$mult2." Y".$slvl;
    break;
  case 'The Beast Within':
    $stats = "A".$mult2." B".$mult4;
    break;
  case 'One with the Pack':
    $stats = "A".$slvl." B".$mult2." G".$slvl." X".$div3;
    break;
// Noble
  case 'Deflecting Accusations':
    $stats = "N".$slvl." V".$mult2;
    break;
  case "Daes Dae'mar":
    $stats = "V".$slvl." P".$slvl;
    break;
  case 'Eliminate your Rivals':
    $stats = "O".$slvl." Y".$slvl." P".$slvl;
    break;
// Aes Sedai ( class icon: White Flame )
  case 'Subdue':
    $stats = "F".$slvl." S".$slvl." W".$div3;
    break;
  case 'Fear and Respect':
    $stats = "N".$slvl." S".$slvl;
    break;
  case 'Prevention':
    $stats = "D".$slvl." E".$mult2." N".$slvl." F".$slvl;
    break;
// Asha'man ( class icon: Black Fang )
  case 'Living Weapon':
    $stats = "A".$slvl." B".$mult2." O".$slvl." Y".$slvl;
    break;
  case 'Fast and Furious':
    $stats = "O".$slvl." F".$slvl;
    break;
  case 'Residual of Madness':
    $stats = "T".$mult2." F".$slvl;
    break;
// Bargainer
  case 'Terms of Negotiation':
    $stats = "L".$slvl." F".$slvl." S".$slvl;
    break;
  case 'The Best Deal':
    $stats = "L".$slvl." G".$slvl;
    break;
  case 'Bleed Dry':
    $stats = "P".$slvl." G".$slvl." S".$slvl;
    break;
// Blight Guard
  case 'Vigilant':
    $stats = "D".$slvl." E".$mult2." Y".$slvl." X".$div3;
    break;
  case 'Shadowspawn Hunter':
    $stats = "Y".$mult2;
    break;
  case 'Blooded':
    $stats = "O".$slvl." T".$slvl." S".$slvl;
    break;
// Duelist
  case 'Block and Counter':
    $stats = "N".$slvl." Y".$slvl." S".$slvl;
    break;
  case 'Quick Reflexes':
    $stats = "F".$mult2;
    break;
  case 'One Step Ahead':
    $stats = "V".$slvl." F".$slvl." W".$div3;
    break;
// Militia
  case 'Fight with Heart':
    $stats = "O".$slvl." G".$slvl." S".$slvl;
    break;
  case 'Defend your Home':
    $stats = "Y".$slvl." G".$slvl;
    break;
  case 'Untrained Attack':
    $stats = "O".$mult2." H".$slvl." Y".$slvl." L".$slvl;
    break;
// Smuggler
  case 'Bribes':
    $stats = "L".$slvl." F".$slvl." X".$div3;
    break;
  case 'Undetected':
    $stats = "V".$mult2;
    break;
  case 'Avoid Suspicion':
    $stats = "V".$slvl." L".$slvl." S".$slvl;
    break;
// Wise One ( class icon: Heart )
  case 'Shade and Water':
    $stats = "D".$slvl." E".$mult2." V".$mult2;
    break;
  case 'Dedicated':
    $stats = "D".$slvl." E".$mult2." G".$slvl;
    break;
  case 'Of the Three-Fold Land':
    $stats = "N".$mult2." G".$slvl;
    break;
// Chief ( class icon: Buckler )
  case 'Til Shade is Gone':
    $stats = "A".$slvl." B".$mult2." V".$slvl." F".$slvl;
    break;
  case 'Raid Tactics':
    $stats = "L".$slvl." F".$slvl;
    break;
  case 'Taking an Opening':
    $stats = "A".$slvl." B".$mult2." L".$slvl." W".$div3;
    break;
// Sailmistress ( class icon: Wave )
  case 'The Gift of Passage':
    $stats = "L".$slvl." S".$slvl." X".$div3;
    break;
  case 'Art of the Deal':
    $stats = "D".$slvl." E".$mult2." S".$slvl;
    break;
  case 'To Uphold the Bargain':
    $stats = "D".$slvl." E".$mult2." P".$slvl." L".$slvl;
    break;
// Cargomaster ( class icon: Anchor )
  case 'Master of Trade':
    $stats = "D".$slvl." E".$mult2." L".$slvl." S".$slvl;
    break;
  case 'Disciplined':
    $stats = "F".$slvl." S".$slvl;
    break;
  case 'Defender of the Ship':
    $stats = "D".$slvl." E".$mult2." Y".$slvl." F".$slvl;
    break;
// Tree Singer ( class icon: Trefoil Leaf )
  case 'Defense of Knowledge':
    $stats = "D".$mult2." E".$mult4." Y".$slvl;
    break;
  case 'One with the Trees':
    $stats = "G".$mult2;
    break;
  case 'Walking Legend':
    $stats = "N".$slvl." G".$slvl." S".$slvl;
    break;
// Elder
  case 'Bring Down the Mountain':
    $stats = "O".$slvl." Y".$slvl." F".$slvl;
    break;
  case 'Living History':
    $stats = "O".$slvl." G".$slvl;
    break;
  case 'Skilled Speaker':
    $stats = "V".$slvl." G".$slvl." F".$slvl;
    break;
// Deathwatch Guard ( class icon: Double-Bit Axe )
  case 'Pride of the Empire':
    $stats = "A".$mult2." B".$mult4." D".$slvl." E".$mult2;
    break;
  case 'Resilient Fighter':
    $stats = "A".$slvl." B".$mult2." G".$slvl;
    break;
  case 'Fearsome':
    $stats = "G".$slvl." S".$slvl." W".$div3;
    break;
// Seeker
  case 'Subterfuge':
    $stats = "N".$slvl." V".$slvl." S".$slvl;
    break;
  case 'Thorough Search':
    $stats = "L".$slvl." S".$slvl;
    break;
  case 'Property of the Empress':
    $stats = "G".$slvl." L".$slvl." S".$slvl;
    break;
// Companion
  case 'Loyalty to the Crown':
    $stats = "Y".$mult2." G".$slvl;
    break;
  case 'Elite Training':
    $stats = "O".$mult2;
    break;
  case 'Shock and Awe':
    $stats = "O".$slvl." F".$slvl." S".$slvl;
    break;
// Legion
  case 'Wall of Jehannah':
    $stats = "Y".$slvl." V".$slvl." S".$slvl;
    break;
  case 'Protect and Serve':
    $stats = "A".$slvl." B".$mult2." S".$slvl;
    break;
  case 'Called to Arms':
    $stats = "A".$slvl." B".$mult2." O".$slvl." L".$slvl;
    break;
// Queen's Guard
  case "The Queen's Peace":
    $stats = "D".$slvl." E".$mult2." N".$slvl." S".$slvl;
    break;
  case 'Rally to the Queen':
    $stats = "D".$slvl." E".$mult2." F".$slvl;
    break;
  case 'Forward the White Lion':
    $stats = "O".$slvl." N".$slvl." F".$slvl;
    break;
// Defender
  case 'Shield Against Prophecy':
    $stats = "D".$slvl." E".$mult2." V".$slvl." F".$slvl;
    break;
  case 'The Stone Stands':
    $stats = "N".$slvl." F".$slvl;
    break;
  case 'Heart of Stone':
    $stats = "N".$slvl." G".$slvl." Y".$slvl;
    break;

// Alignment
  case 'Among the Chosen':
    $stats = "D".$slvl." E".$mult2." T".$slvl." P".$slvl;
    break;
  case 'From the Shadows':
    $stats = "O".$slvl." V".$slvl." W".$div3;
    break;
  case 'Just Business':
    $stats = "P".$slvl." F".$slvl;
    break;
  case 'As the Wheel Weaves':
    $stats = "D".$slvl." E".$mult2." O".$slvl;
    break;
  case 'Avoiding Trouble':
    $stats = "G".$slvl." S".$slvl;
    break;
  case 'Against the Shadow':
    $stats = "N".$slvl." Y".$slvl." X".$div3;
    break;
  case 'Taveren':
    $stats = "A".$slvl." B".$mult2." G".$slvl." L".$slvl;
    break;
       
// Supreme Being
  case 'Go Team':
    $stats = "A0 B0 D999 E999 V200 Y200 L100 W10 P1 T1 G100 S1";
    break;
}
if ($slvl == 0) $stats = "";
return $stats;
}
// SKILL DATA
// data (cost, reqs); all reqs = 0 for now.
$skill_name = array(
'1' => "Heron Spreads its Wings",
'2' => "Sword Finesse",
'3' => "Cat Crosses the Courtyard",
'4' => "Sword Mastery",
'5' => "Parting the Silk",
'6' => "Hacking through Brambles",
'7' => "Axe Finesse",
'8' => "Mighty Chop",
'9' => "Axe Mastery",
'10' => "Cleave",
'11' => "Wash the Spears",
'12' => "Spear Finesse",
'13' => "Dance the Spears",
'14' => "Spear Mastery",
'15' => "Reach",
'16' => "Rapid Fire",
'17' => "Bow Finesse",
'18' => "Focused Shot",
'19' => "Bow Mastery",
'20' => "Tipped Arrows",
'21' => "Shattering Strike",
'22' => "Bludgeon Finesse",
'23' => "Stunning Blow",
'24' => "Bludgeon Mastery",
'25' => "Hard Knocks",
'26' => "Quick Toss",
'27' => "Knife Finesse",
'28' => "Trick Throw",
'29' => "Knife Mastery",
'30' => "Concealed Strike",
'31' => "Shield Bash",
'32' => "Shield Finesse",
'33' => "Cover",
'34' => "Shield Mastery",
'35' => "Parry",
'36' => "Strength of Fire",
'37' => "Strength of Water",
'38' => "Strength of Spirit",
'39' => "Strength of Air",
'40' => "Strength of Earth",
'41' => "Moment of Silence",
'42' => "Body Armor Finesse",
'43' => "Deflection",
'44' => "Body Armor Mastery",
'45' => "Protection",
'46' => "Armored Agility",
'47' => "Leg Armor Finesse",
'48' => "Stability",
'49' => "Leg Armor Mastery",
'50' => "Maneuverability",

'51' => "Combat Prowess",
'52' => "Poised to Strike",
'53' => "Battle Tested",
'54' => "On Guard",
'55' => "Under the Banner",
'56' => "Take No Chances",
'57' => "Shoot First",
'58' => "Resourceful",
'59' => "Questions Later",
'60' => "Trust No One",
'61' => "Stealth Strike",
'62' => "Precise Movement",
'63' => "Natural Awareness",
'64' => "One with the Land",
'65' => "Trapping",
'66' => "Quick and Painless",
'67' => "Correct Dosage",
'68' => "The Best Medicine",
'69' => "Apply Pressure",
'70' => "Patience",
'71' => "Proactive",
'72' => "Corruption",
'73' => "Mark of the Shadow",
'74' => "Deadly Surprise",
'75' => "For the Great Lord",


'101' => "Hammer and Anvil",
'102' => "Veteran",
'103' => "The Lesser Sadness",
'104' => "Time to Toss the Dice",
'105' => "Toughness",
'106' => "Maintaining Civility",
'107' => "Heavier than a Mountain",
'108' => "Benefits of the Bond",
'109' => "Legendary Skill",
'110' => "Seasoned Fighter",
'111' => "Soldier for Hire",
'112' => "Higher Pay",
'113' => "Unexpected Opponent",
'114' => "Nimbleness",
'115' => "Extra Flourish",
'116' => "Great Roll",
'117' => "Good Fortune",
'118' => "Lucky Shot",
'119' => "Knowing their Weakness",
'120' => "Bring It Down",
'121' => "Stalking Prey",
'122' => "Ruthlessness",
'123' => "Thievery",
'124' => "Mug",
'125' => "Urban Tracking",
'126' => "Catching",
'127' => "Ambush",
'128' => "Fanatical Devotion",
'129' => "Light-Blinded",
'130' => "Strength in Numbers",
'131' => "Mob Justice",
'132' => "Misguided Loyalty",
'133' => "For the Lord Dragon",
'134' => "Unseen Assailant",
'135' => "To Kill",
'136' => "Lethal Precision",

'164' => "Purging the Shadow",
'165' => "Defender of the Light",
'166' => "Justice",
'167' => "Eyes of the Wolf",
'168' => "The Beast Within",
'169' => "One with the Pack",
'170' => "Deflecting Accusations",
'171' => "Daes Dae'mar",
'172' => "Eliminate your Rivals",
'173' => "Subdue",
'174' => "Fear and Respect",
'175' => "Prevention",
'176' => "Living Weapon",
'177' => "Fast and Furious",
'178' => "Residual of Madness",
'179' => "Terms of Negotiation",
'180' => "The Best Deal",
'181' => "Bleed Dry",
'182' => "Vigilant",
'183' => "Shadowspawn Hunter",
'184' => "Blooded",
'185' => "Block and Counter",
'186' => "Quick Reflexes",
'187' => "One Step Ahead",
'188' => "Fight with Heart",
'189' => "Defend your Home",
'190' => "Untrained Attack",
'191' => "Bribes",
'192' => "Undetected",
'193' => "Avoid Suspicion",
'194' => "Shade and Water",
'195' => "Dedicated",
'196' => "Of the Three-Fold Land",
'197' => "Til Shade is Gone",
'198' => "Raid Tactics",
'199' => "Taking an Opening",
'200' => "The Gift of Passage",
'201' => "Art of the Deal",
'202' => "To Uphold the Bargain",
'203' => "Master of Trade",
'204' => "Disciplined",
'205' => "Defender of the Ship",
'206' => "Defense of Knowledge",
'207' => "One with the Trees",
'208' => "Walking Legend",
'209' => "Bring Down the Mountain",
'210' => "Living History",
'211' => "Skilled Speaker",
'212' => "Pride of the Empire",
'213' => "Resilient Fighter",
'214' => "Fearsome",
'215' => "Subterfuge",
'216' => "Thorough Search",
'217' => "Property of the Empress",
'218' => "Loyalty to the Crown",
'219' => "Elite Training",
'220' => "Shock and Awe",
'221' => "Wall of Jehannah",
'222' => "Protect and Serve",
'223' => "Called to Arms",
'224' => "The Queen's Peace",
'225' => "Rally to the Queen",
'226' => "Forward the White Lion",
'227' => "Shield Against Prophecy",
'228' => "The Stone Stands",
'229' => "Heart of Stone",

'901' => "Among the Chosen",
'902' => "From the Shadows",
'903' => "Just Business",
'904' => "As the Wheel Weaves",
'905' => "Avoiding Trouble",
'906' => "Against the Shadow",
'907' => "Taveren",

'999' => "Go Team",
); 

function getTypeMod($skills, $type)
{
  $mod = 100;
  $bonus = '';
  switch ($type)
  {
    case '0':
      $bonus = 'rC';
      break;
    case '1':
      $bonus = 'sC';
      break;
    case '2':
      $bonus = 'xC';
      break;
    case '3':
      $bonus = 'pC';
      break;
    case '4':
      $bonus = 'oC';
      break;
    case '5':
      $bonus = 'bC';
      break;
    case '6':
      $bonus = 'kC';
      break;
    case '7':
      $bonus = 'hC';
      break;
    case '8':
      $bonus = 'wC';
      break;
    case '9':
      $bonus = 'aC';
      break;
    case '10':
      $bonus = 'lC';
      break;
    default:
      break;
  }
  $skill_tmp = cparse($skills, 0);
  if ($skill_tmp[$bonus]) $mod = $mod + $skill_tmp[$bonus];
  
  return $mod;
}

?>