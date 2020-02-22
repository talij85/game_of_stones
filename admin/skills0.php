<?php
$skills = unserialize($char['skills']);

$clan_skill_names = array(
  '0' => "vault size",
  '1' => "damage vs enemies",
  '2' => "gold steal vs enemies",
  '3' => "defense in ruled towns",
  '4' => "health gain in ruled towns",
);

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
      
    case '10':
      $skills = array(101,102,103);
      break;
    case '11':
      $skills = array(104,105,106);
      break;
    case '12':
      $skills = array(110,111,112);
      break;
    case '13':
      $skills = array(113,114,115);
      break;
    case '14':
      $skills = array(119,120,121);
      break;
    case '15':
      $skills = array(122,123,124);
      break;
    case '16':
      $skills = array(128,129,130);
      break;
    case '17':
      $skills = array(125,126,127);
      break;
    case '18':
      $skills = array(116,117,118);
      break;
    case '19':
      $skills = array(134,135,136);
      break;
    case '20':
      $skills = array(137,138,139);
      break;
    case '21':
      $skills = array(107,108,109);
      break;      
    case '22':
      $skills = array(131,132,133);
      break;
    case '23':
      $skills = array(140,141,142);
      break;
    case '24':
      $skills = array(143,144,145);
      break;
    case '25':
      $skills = array(149,150,151);
      break;
    case '26':
      $skills = array(152,153,154);
      break;
    case '27':
      $skills = array(146,147,148);
      break;      
    case '28':
      $skills = array(155,156,157);
      break;
    case '29':
      $skills = array(158,159,160);
      break;
    case '30':
      $skills = array(161,162,163);
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
    case '108':
      $skills = array(26,27,28,29,30);
      break;
    default:
      $skills = array(0,0,0);
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
case 'Cat Crosses the Courtyard':
$stats = "N".$mult2;
break;
case 'Sword Finess':
$stats = "sC".$neg;
break;
case 'Heron Spreads its Wings':
$stats = "A".$slvl." B".$mult2." O".$slvl." N".$slvl;
break;
case 'Sword Mastery':
$stats = "sA".$slvl;
break;
case 'Parting the Silk':
$stats = "O".$mult2;
break;
case 'Mighty Chop':
$stats = "V".$slvl." Y".$slvl;
break;
case 'Axe Finess':
$stats = "xC".$neg;
break;
case 'Hacking through Brambles':
$stats = "A".$slvl." B".$mult2." Y".$slvl." O".$slvl;
break;
case 'Axe Mastery':
$stats = "xA".$slvl;
break;
case 'Cleave':
$stats = "O".$slvl." S".$slvl;
break;
case 'Reach':
$stats = "V".$slvl." F".$slvl;
break;
case 'Spear Finess':
$stats = "pC".$neg;
break;
case 'Dance the Spears':
$stats = "A".$slvl." B".$mult2." Y".$slvl." V".$slvl;
break;
case 'Spear Mastery':
$stats = "pA".$slvl;
break;
case 'Wash the Spears':
$stats = "N".$slvl." Y".$slvl;
break;
case 'Quick Draw':
$stats = "O".$slvl." F".$slvl;
break;
case 'Bow Finess':
$stats = "oC".$neg;
break;
case 'Rapid Fire':
$stats = "A".$slvl." B".$mult2." Y".$slvl." X".$div3;
break;
case 'Bow Mastery':
$stats = "oA".$slvl;
break;
case 'Focused Shot':
$stats = "Y".$mult2;
break;
case 'Deflection':
$stats = "V".$slvl." G".$slvl;
break;
case 'Bludgeon Finess':
$stats = "bC".$neg;
break;
case 'Shattering Strike':
$stats = "A".$slvl." B".$mult2." N".$slvl." W".$div3;
break;
case 'Bludgeon Mastery':
$stats = "bA".$slvl;
break;
case 'Stunning Blow':
$stats = "N".$slvl." S".$slvl;
break;

case 'Strength of Fire':
if ($gender==0) $slvl++;
$stats = "wA".$slvl." fP".$slvl;
break;
case 'Strength of Water':
if ($gender==1) $slvl++;
$stats = "wA".$slvl." wP".$slvl;
break;
case 'Strength of Spirit':
$stats = "G".$slvl." sP".$slvl;
break;
case 'Strength of Air':
if ($gender==1) {$slvl++; $neg--;}
$stats = "wC".$neg." aP".$slvl;
break;
case 'Strength of Earth':
if ($gender==0) {$slvl++; $neg--;}
$stats = "wC".$neg." eP".$slvl;
break;

case 'Poised to Strike':
$stats = "O".$slvl." Y".$slvl;
break;
case 'Offensive Prowess':
$stats = "O".$slvl;
break;
case 'Combat Readiness':
$stats = "O".$slvl." N".$slvl." F".$slvl;
break;
case 'Defensive Prowess':
$stats = "N".$slvl;
break;
case 'On Guard':
$stats = "N".$slvl." V".$slvl;
break;

case 'An Extra Bit':
$stats = "P".$mult2;
break;
case 'Resourcefulness':
$stats = "L".$slvl;
break;
case 'What is Needed':
$stats = "A".$slvl." B".$mult2." Y".$slvl." P".$slvl;
break;
case 'Awareness':
$stats = "V".$slvl;
break;
case 'Watching your Back':
$stats = "N".$slvl." F".$slvl;
break;
case 'Using Your Surroundings':
$stats = "O".$slvl." P".$slvl;
break;
case 'Presicion':
$stats = "Y".$slvl;
break;
case 'Tracking the Wind':
$stats = "A".$slvl." B".$mult2." N".$slvl." V".$slvl;
break;
case 'Reflexes':
$stats = "F".$slvl;
break;
case 'Blending In':
$stats = "V".$mult2;
break;
case 'Fear and Respect':
$stats = "O".$slvl." V".$slvl;
break;
case 'Long Life':
$stats = "G".$slvl;
break;
case 'Embracing the Source':
$stats = "A".$slvl." B".$mult2." L".$slvl." F".$slvl;
break;
case 'Awe Inspiring':
$stats = "S".$slvl;
break;
case 'Ward Weaving':
$stats = "G".$slvl." S".$slvl;
break;

case 'Hammer and Anvil':
$stats = "O".$slvl." Y".$slvl." V".$slvl;
break;
case 'Veteran':
$stats = "A".$slvl." B".$mult2." N".$slvl;
break;
case 'The Lesser Sadness':
$stats = "O".$slvl." N".$slvl." W".$div3;
break;
case 'Time to Toss the Dice':
$stats = "A".$slvl." B".$mult2." V".$slvl." L".$slvl;
break;
case 'Toughness':
$stats = "D".$mult2." E".$mult4;
break;
case 'Maintaining Civility':
$stats = "N".$mult2." V".$slvl;
break;
case 'Purging the Shadow':
$stats = "A".$slvl." B".$mult2." P".$slvl." G".$slvl;
break;
case 'Defender of the Light':
$stats = "D".$slvl." E".$mult2." N".$slvl;
break;
case 'Justice':
$stats = "O".$mult2." W".$div3;
break;
case 'Heavier than a Mountain':
$stats = "A".$mult2." B".$mult4." O".$mult2." H".$slvl;
break;
case 'Benefits of the Bond':
$stats = "G".$slvl." F".$slvl;
break;
case 'Legendary Skill':
$stats = "A".$slvl." B".$mult2." D".$slvl." E".$mult2." S".$slvl;
break;
case 'Expendable':
$stats = "A".$mult2." B".$mult4." N".$mult2." H".$slvl;
break;
case 'Soldier for Hire':
$stats = "O".$slvl." L".$slvl;
break;
case 'Higher Pay':
$stats = "N".$slvl." L".$mult2;
break;
case 'Urban Tracking':
$stats = "A".$slvl." B".$mult2." N".$slvl." Y".$slvl;
break;
case 'Catching':
$stats = "V".$slvl." S".$slvl;
break;
case 'Ambush':
$stats = "N".$slvl." F".$slvl." X".$div3;
break;
case 'Unexpected Opponent':
$stats = "O".$slvl." F".$slvl." S".$slvl;
break;
case 'Nimbleness':
$stats = "V".$slvl." X".$div3;
break;
case 'Extra Flourish':
$stats = "A".$slvl." B".$mult2." Y".$slvl." S".$slvl;
break;
case 'Great Roll':
$stats = "N".$slvl." V".$mult2;
break;
case 'Good Fortune':
$stats = "L".$mult2;
break;
case 'Lucky Shot':
$stats = "O".$slvl." Y".$mult2;
break;
case 'Ruthlessness':
$stats = "O".$slvl." P".$mult2;
break;
case 'Thievery':
$stats = "V".$slvl." L".$slvl;
break;
case 'Mug':
$stats = "Y".$slvl." L".$slvl." F".$slvl;
break;
case 'Knowing their Weakness':
$stats = "A".$slvl." B".$mult2." Y".$mult2;
break;
case 'Straight Shooter':
$stats = "Y".$slvl." F".$slvl;
break;
case 'Stalking Prey':
$stats = "A".$slvl." B".$mult2." V".$slvl." W".$div3;
break;
case 'The Beast Within':
$stats = "A".$slvl." B".$mult2." O".$mult3." H".$slvl;
break;
case 'Eyes of the Wolf':
$stats = "Y".$slvl." S".$slvl;
break;
case 'One with the Pack':
$stats = "N".$slvl." V".$slvl." X".$div3;
break;
case 'Fanatical Devotion':
$stats = "A".$slvl." B".$mult2." O".$slvl." T".$mult2." H".$slvl;
break;
case 'Light-Blinded':
$stats = "T".$slvl." G".$slvl;
break;
case 'Mob Mentality':
$stats = "A".$slvl." B".$mult2." O".$mult3." N-".$slvl;
break;
case 'Corruption':
$stats = "P".$slvl." T".$slvl." W".$div3;
break;
case 'Mark of the Shadow':
$stats = "T".$mult2;
break;
case 'Leech':
$stats = "P".$mult2." G".$slvl;
break;
case 'Incapacite':
$stats = "D".$slvl." E".$mult2." F".$slvl." W".$div3;
break;
case 'Bound by the Oaths':
$stats = "O-".$slvl." N".$mult2." G".$slvl;
break;
case 'Legendary Reputation':
$stats = "A".$slvl." B".$mult2." F".$slvl." S".$slvl;
break;
case 'Living Weapon':
$stats = "A".$slvl." B".$mult2." O".$slvl." G".$slvl;
break;
case 'Forced Learning':
$stats = "O".$mult2." Y".$slvl." H".$slvl;
break;
case 'Residual of Madness':
$stats = "T".$mult2." G".$slvl;
break;
case 'Shade and Water':
$stats = "D".$slvl." E".$mult2." V".$mult2;
break;
case 'Dedicated':
$stats = "N".$slvl." G".$slvl;
break;
case 'Of the Three-fold Land':
$stats = "D".$slvl." E".$mult2." O".$slvl." N".$slvl;
break;
case 'Til Shade is Gone':
$stats = "O".$slvl." V".$slvl." X".$div3;
break;
case 'Raid Tactics':
$stats = "L".$slvl." F".$slvl;
break;
case 'Taking an Opening':
$stats = "A".$mult2." B".$mult4." W".$div3;
break;
case 'The Gift of Passage':
$stats = "L".$mult2." X".$div3;
break;
case 'Art of the Deal':
$stats = "D".$slvl." E".$mult2." S".$slvl;
break;
case 'To Uphold the Bargain':
$stats = "A".$slvl." B".$mult2." P".$slvl." F".$slvl;
break;
case 'Master of Trade':
$stats = "D".$slvl." E".$mult2." V".$slvl." L".$slvl;
break;
case 'Disciplined':
$stats = "F".$slvl." S".$slvl;
break;
case 'Defender of the Ship':
$stats = "A".$slvl." B".$mult2." N".$mult2;
break;
case 'Defense of Knowledge':
$stats = "D".$mult2." E".$mult4." Y".$slvl;
break;
case 'One with the Trees':
$stats = "G".$mult2;
break;
case 'Walking Legend':
$stats = "N".$slvl." G".$slvl." S".$slvl;
break;
case 'Pride of the Empire':
$stats = "A".$mult2." B".$mult4." D".$slvl." E".$mult2;
break;
case 'Resilant Fighter':
$stats = "A".$slvl." B".$mult2." G".$slvl;
break;
case 'Fearsome':
$stats = "O".$slvl." S".$slvl." W".$div3;
break;

case 'Go Team':
$stats = "A999 B999 D999 E999 V200 Y200 X-15 L100";
break;

}
if ($slvl == 0) $stats = "";
return $stats;
}
// SKILL DATA
// data (cost, reqs); all reqs = 0 for now.
$skill_name = array(
'1' => "Cat Crosses the Courtyard",
'2' => "Sword Finess",
'3' => "Heron Spreads its Wings",
'4' => "Sword Mastery",
'5' => "Parting the Silk",
'6' => "Mighty Chop",
'7' => "Axe Finess",
'8' => "Hacking through Brambles",
'9' => "Axe Mastery",
'10' => "Cleave",
'11' => "Reach",
'12' => "Spear Finess",
'13' => "Dance the Spears",
'14' => "Spear Mastery",
'15' => "Wash the Spears",
'16' => "Quick Draw",
'17' => "Bow Finess",
'18' => "Rapid Fire",
'19' => "Bow Mastery",
'20' => "Focused Shot",
'21' => "Deflection",
'22' => "Bludgeon Finess",
'23' => "Shattering Strike",
'24' => "Bludgeon Mastery",
'25' => "Stunning Blow",
'26' => "Strength of Fire",
'27' => "Strength of Water",
'28' => "Strength of Spirit",
'29' => "Strength of Air",
'30' => "Strength of Earth",

'51' => "Poised to Strike",
'52' => "Offensive Prowess",
'53' => "Combat Readiness",
'54' => "Defensive Prowess",
'55' => "On Guard",
'56' => "An Extra Bit",
'57' => "Resourcefulness",
'58' => "What is Needed",
'59' => "Awareness",
'60' => "Watching your Back",
'61' => "Using Your Surroundings",
'62' => "Presicion",
'63' => "Tracking the Wind",
'64' => "Reflexes",
'65' => "Blending In",
'66' => "Fear and Respect",
'67' => "Long Life",
'68' => "Embracing the Source",
'69' => "Awe Inspiring",
'70' => "Ward Weaving",

'101' => "Hammer and Anvil",
'102' => "Veteran",
'103' => "The Lesser Sadness",
'104' => "Time to Toss the Dice",
'105' => "Toughness",
'106' => "Maintaining Civility",
'107' => "Purging the Shadow",
'108' => "Defender of the Light",
'109' => "Justice",
'110' => "Heavier than a Mountain",
'111' => "Benefits of the Bond",
'112' => "Legendary Skill",
'113' => "Expendable",
'114' => "Soldier for Hire",
'115' => "Higher Pay",
'116' => "Urban Tracking",
'117' => "Catching",
'118' => "Ambush",
'119' => "Unexpected Opponent",
'120' => "Nimbleness",
'121' => "Extra Flourish",
'122' => "Great Roll",
'123' => "Good Fortune",
'124' => "Lucky Shot",
'125' => "Ruthlessness",
'126' => "Thievery",
'127' => "Mug",
'128' => "Knowing their Weakness",
'129' => "Straight Shooter",
'130' => "Stalking Prey",
'131' => "The Beast Within",
'132' => "Eyes of the Wolf",
'133' => "One with the Pack",
'134' => "Fanatical Devotion",
'135' => "Light-Blinded",
'136' => "Mob Mentality",
'137' => "Corruption",
'138' => "Mark of the Shadow",
'139' => "Leech",
'140' => "Incapacite",
'141' => "Bound by the Oaths",
'142' => "Legendary Reputation",
'143' => "Living Weapon",
'144' => "Forced Learning",
'145' => "Residual of Madness",
'146' => "The Gift of Passage",
'147' => "Art of the Deal",
'148' => "To Uphold the Bargain",
'149' => "Shade and Water",
'150' => "Dedicated",
'151' => "Of the Three-fold Land",
'152' => "Til Shade is Gone",
'153' => "Raid Tactics",
'154' => "Taking an Opening",
'155' => "Master of Trade",
'156' => "Disciplined",
'157' => "Defender of the Ship",
'158' => "Defense of Knowledge",
'159' => "One with the Trees",
'160' => "Walking Legend",
'161' => "Pride of the Empire",
'162' => "Resilant Fighter",
'163' => "Fearsome",

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
    default:
      break;
  }
  $skill_tmp = cparse($skills, 0);
  if ($skill_tmp[$bonus]) $mod = $mod + $skill_tmp[$bonus];
  
  return $mod;
}

?>